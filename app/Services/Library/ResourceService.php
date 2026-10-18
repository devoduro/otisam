<?php

namespace App\Services\Library;

use App\Jobs\GenerateResourcePreview;
use App\Models\Curriculum\CurriculumNode;
use App\Models\Library\Resource;
use App\Models\User;
use App\Services\Audit;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

/** Creating, editing, publishing and linking library items. */
class ResourceService
{
    public function __construct(private readonly ResourceStorage $storage) {}

    /** @param array<string, mixed> $attributes validated fillable attributes */
    public function create(array $attributes, ?InspectedUpload $main, User $by): Resource
    {
        return DB::transaction(function () use ($attributes, $main, $by) {
            $resource = new Resource($attributes);
            $resource->status = 'draft';
            $resource->author_id = $by->id;
            $resource->save();

            if ($main) {
                $this->storage->storeMain($resource, $main, $by);
            }

            return $resource;
        });
    }

    /** @param array<string, mixed> $attributes */
    public function update(Resource $resource, array $attributes, ?InspectedUpload $main, ?InspectedUpload $preview, User $by): Resource
    {
        return DB::transaction(function () use ($resource, $attributes, $main, $preview, $by) {
            $resource->fill($attributes);
            $previewLengthChanged = $resource->isDirty('preview_pages');
            $resource->save();

            if ($main) {
                $this->storage->storeMain($resource, $main, $by);
                Audit::record('library', 'resource.file-replaced', "Replaced the document of \"{$resource->title}\"", $resource);
            }
            if ($preview) {
                $this->storage->storeManualPreview($resource->fresh(), $preview, $by);
            }

            $current = $resource->mainFile()->first();
            if (! $main && ! $preview && $previewLengthChanged && $current?->isPdf() && $current->preview_status !== 'manual') {
                $current->forceFill(['preview_status' => 'pending'])->save();
                GenerateResourcePreview::dispatch($current->id)->afterCommit();
            }

            return $resource;
        });
    }

    /**
     * Move through the workflow. Publishing needs a document; `publishAt` in the future schedules it.
     */
    public function setStatus(Resource $resource, string $status, ?\DateTimeInterface $publishAt = null): void
    {
        if (! array_key_exists($status, Resource::STATUSES)) {
            throw new InvalidArgumentException('Unknown status.');
        }
        if ($status === 'published' && ! $resource->mainFile()->exists()) {
            throw new InvalidArgumentException('Upload the document before publishing.');
        }

        $old = $resource->status;
        $resource->status = $status;
        $resource->publish_at = $status === 'published' ? $publishAt : null;
        if ($status === 'published' && $resource->published_at === null) {
            $resource->published_at = $publishAt ?? now();
        }
        $resource->save();

        if ($old !== $status) {
            Audit::record('library', "resource.{$status}", 'Resource '.Resource::STATUSES[$status].": {$resource->title}".($publishAt ? ' (from '.$publishAt->format('j M Y H:i').')' : ''),
                $resource, ['status' => $old], ['status' => $status, 'publish_at' => $publishAt?->format(DATE_ATOM)]);
        }
    }

    public function link(Resource $resource, Resource $linked, string $relation): void
    {
        if ($resource->is($linked)) {
            throw new InvalidArgumentException('A resource cannot be linked to itself.');
        }
        if (! array_key_exists($relation, Resource::RELATIONS)) {
            throw new InvalidArgumentException('Unknown link type.');
        }

        if (! $resource->linkedResources()->where('resources.id', $linked->id)->wherePivot('relation', $relation)->exists()) {
            $resource->linkedResources()->attach($linked->id, ['relation' => $relation]);
        }
    }

    /**
     * Tag the resource to curriculum items of its own subject.
     *
     * @param  array<int, int>  $nodeIds
     */
    public function syncCurriculum(Resource $resource, array $nodeIds): void
    {
        $valid = $resource->subject_id
            ? CurriculumNode::where('subject_id', $resource->subject_id)->whereIn('id', $nodeIds)->pluck('id')->all()
            : [];

        $resource->curriculumNodes()->sync($valid);
    }
}
