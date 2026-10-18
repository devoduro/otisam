<?php

namespace App\Http\Controllers\Admin\Library;

use App\Http\Controllers\Controller;
use App\Models\Curriculum\ClassLevel;
use App\Models\Curriculum\CurriculumVersion;
use App\Models\Curriculum\EducationLevel;
use App\Models\Curriculum\Programme;
use App\Models\Curriculum\Subject;
use App\Models\Library\ExamType;
use App\Models\Library\Resource;
use App\Models\Library\ResourceCategory;
use App\Services\Audit;
use App\Services\Curriculum\CurriculumTree;
use App\Services\Library\InspectedUpload;
use App\Services\Library\PdfPreviewGenerator;
use App\Services\Library\ResourceService;
use App\Services\Library\UploadInspector;
use App\Services\Library\UploadRejected;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use InvalidArgumentException;

class ResourceController extends Controller
{
    /** Exam types whose papers belong to WAEC and so may only be published free (owner decision D2). */
    public const WAEC_EXAM_CODES = ['WASSCE-MJ', 'WASSCE-ND', 'BECE'];

    public function __construct(
        private readonly ResourceService $service,
        private readonly UploadInspector $inspector,
    ) {}

    public function index(Request $request): View
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'category' => ['nullable', 'integer'],
            'subject' => ['nullable', 'integer'],
            'status' => ['nullable', Rule::in(array_keys(Resource::STATUSES))],
            'access' => ['nullable', Rule::in(array_keys(Resource::ACCESS_LEVELS))],
            'year' => ['nullable', 'integer'],
        ]);

        $resources = Resource::with(['category', 'subject', 'examType', 'mainFile'])
            ->when($filters['q'] ?? null, fn ($q, $s) => $q->where('title', 'like', "%{$s}%"))
            ->when($filters['category'] ?? null, fn ($q, $c) => $q->where('category_id', $c))
            ->when($filters['subject'] ?? null, fn ($q, $s) => $q->where('subject_id', $s))
            ->when($filters['status'] ?? null, fn ($q, $s) => $q->where('status', $s))
            ->when($filters['access'] ?? null, fn ($q, $a) => $q->where('access_level', $a))
            ->when($filters['year'] ?? null, fn ($q, $y) => $q->where('exam_year', $y))
            ->latest('updated_at')
            ->paginate(25)->withQueryString();

        return view('admin.library.resources.index', [
            'resources' => $resources,
            'filters' => $filters,
            'categories' => ResourceCategory::ordered()->get(),
            'subjects' => Subject::with('educationLevel')->orderBy('education_level_id')->orderBy('name')->get(['id', 'name', 'education_level_id']),
        ]);
    }

    public function create(): View
    {
        return view('admin.library.resources.form', $this->formData(new Resource(['access_level' => 'premium', 'audience' => 'student'])));
    }

    public function store(Request $request): RedirectResponse
    {
        $attributes = $this->validated($request);
        $upload = $this->inspect($request, 'file');

        $resource = $this->service->create($attributes, $upload, $request->user());
        Audit::record('library', 'resource.created', "Resource created: {$resource->title}", $resource);

        return redirect()->route('admin.library.resources.edit', $resource)->with('success', 'Saved as a draft. Check the preview, link any solution or marking scheme, then publish.');
    }

    public function edit(Resource $resource): View
    {
        $resource->load(['mainFile', 'previewFile', 'linkedResources.category', 'linkedFrom.category', 'curriculumNodes', 'author']);

        return view('admin.library.resources.form', $this->formData($resource));
    }

    public function update(Request $request, Resource $resource): RedirectResponse
    {
        $attributes = $this->validated($request, $resource);
        $main = $this->inspect($request, 'file');
        $preview = $this->inspect($request, 'preview_file');

        try {
            $this->service->update($resource, $attributes, $main, $preview, $request->user());
        } catch (UploadRejected $e) {
            throw ValidationException::withMessages(['preview_file' => $e->getMessage()]);
        }

        if ($request->has('curriculum_nodes_present')) {
            $this->service->syncCurriculum($resource, array_map('intval', (array) $request->input('curriculum_nodes', [])));
        }

        return redirect()->route('admin.library.resources.edit', $resource)->with('success', 'Saved.');
    }

    /** Workflow: submit for review, publish (now or scheduled), archive, back to draft. */
    public function status(Request $request, Resource $resource): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', Rule::in(array_keys(Resource::STATUSES))],
            'publish_at' => ['nullable', 'date'],
        ]);

        // Making something public, or taking it down, is a publishing decision.
        if (in_array($data['status'], ['published', 'archived'], true) || $resource->status === 'published') {
            abort_unless($request->user()->can('resources.publish'), 403);
        }

        if ($data['status'] === 'published') {
            $problems = $this->publishProblems($resource);
            if ($problems) {
                return back()->with('error', 'Not published: '.implode(' ', $problems));
            }
        }

        $publishAt = ! empty($data['publish_at']) ? Carbon::parse($data['publish_at']) : null;

        try {
            $this->service->setStatus($resource, $data['status'], $publishAt && $publishAt->isFuture() ? $publishAt : null);
        } catch (InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', 'Status: '.Resource::STATUSES[$data['status']].($publishAt && $publishAt->isFuture() ? ', goes live '.$publishAt->format('j M Y, H:i') : '').'.');
    }

    public function destroy(Resource $resource): RedirectResponse
    {
        $resource->delete(); // soft delete: files are kept so it can be restored
        Audit::record('library', 'resource.deleted', "Resource deleted: {$resource->title}", $resource);

        return redirect()->route('admin.library.resources.index')->with('success', "\"{$resource->title}\" deleted.");
    }

    public function link(Request $request, Resource $resource): RedirectResponse
    {
        $data = $request->validate([
            'linked_resource_id' => ['required', 'integer', Rule::exists('resources', 'id')->whereNull('deleted_at')],
            'relation' => ['required', Rule::in(array_keys(Resource::RELATIONS))],
        ]);

        $linked = Resource::findOrFail($data['linked_resource_id']);

        try {
            $this->service->link($resource, $linked, $data['relation']);
        } catch (InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage());
        }

        Audit::record('library', 'resource.linked', "Linked \"{$linked->title}\" as ".Resource::RELATIONS[$data['relation']]." of \"{$resource->title}\"", $resource);

        return back()->with('success', 'Linked.');
    }

    public function unlink(Resource $resource, Resource $linked, string $relation): RedirectResponse
    {
        $resource->linkedResources()->wherePivot('relation', $relation)->detach($linked->id);

        return back()->with('success', 'Link removed.');
    }

    public function bulkCreate(): View
    {
        return view('admin.library.resources.bulk', $this->formData(new Resource(['access_level' => 'premium', 'audience' => 'student'])));
    }

    /** Many files with the same details; each becomes a draft named after its file. */
    public function bulkStore(Request $request): RedirectResponse
    {
        $request->validate([
            'files' => ['required', 'array', 'min:1', 'max:20'],
            'files.*' => ['file'],
        ]);
        $attributes = $this->validated($request, null, titleRequired: false);

        $created = 0;
        $rejected = [];
        foreach ($request->file('files') as $file) {
            try {
                $upload = $this->inspector->inspect($file);
            } catch (UploadRejected $e) {
                $rejected[] = $file->getClientOriginalName().': '.$e->getMessage();

                continue;
            }

            $title = Str::of(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME))->replace(['_', '-'], ' ')->squish()->title()->limit(200, '')->toString();
            DB::transaction(fn () => $this->service->create(['title' => $title ?: 'Untitled'] + $attributes, $upload, $request->user()));
            $created++;
        }

        Audit::record('library', 'resource.bulk-upload', "Bulk upload: {$created} draft(s) created".($rejected ? ', '.count($rejected).' rejected' : ''));

        $redirect = redirect()->route('admin.library.resources.index', ['status' => 'draft'])->with('success', "{$created} draft(s) created. Review each one before publishing.");

        return $rejected ? $redirect->with('error', 'Not uploaded: '.implode(' | ', $rejected)) : $redirect;
    }

    /** @return array<int, string> reasons the resource cannot be published yet */
    private function publishProblems(Resource $resource): array
    {
        $problems = [];
        $main = $resource->mainFile()->first();

        if (! $main) {
            $problems[] = 'Upload the document first.';
        }
        if (! $resource->copyright_holder) {
            $problems[] = 'Record who holds the copyright.';
        }
        if ($main && $main->isPdf() && ! $resource->isFree() && $main->preview_status === 'pending') {
            $problems[] = 'The preview is still being generated. Try again in a minute.';
        }

        return $problems;
    }

    private function inspect(Request $request, string $field): ?InspectedUpload
    {
        if (! $request->hasFile($field)) {
            return null;
        }

        try {
            return $this->inspector->inspect($request->file($field));
        } catch (UploadRejected $e) {
            throw ValidationException::withMessages([$field => $e->getMessage()]);
        }
    }

    /** @return array<string, mixed> */
    private function validated(Request $request, ?Resource $resource = null, bool $titleRequired = true): array
    {
        $maxKb = config('platform.library.max_upload_mb') * 1024;

        $data = $request->validate([
            'title' => [$titleRequired ? 'required' : 'nullable', 'string', 'max:200'],
            'category_id' => ['required', 'integer', Rule::exists('resource_categories', 'id')],
            'description' => ['nullable', 'string', 'max:5000'],
            'audience' => ['required', Rule::in(array_keys(ResourceCategory::AUDIENCES))],
            'subject_id' => ['nullable', 'integer', Rule::exists('subjects', 'id')],
            'class_level_id' => ['nullable', 'integer', Rule::exists('class_levels', 'id')],
            'programme_id' => ['nullable', 'integer', Rule::exists('programmes', 'id')],
            'exam_type_id' => ['nullable', 'integer', Rule::exists('exam_types', 'id')],
            'exam_year' => ['nullable', 'integer', 'min:1950', 'max:'.(now()->year + 1)],
            'paper_number' => ['nullable', 'string', 'max:20'],
            'curriculum_version_id' => ['nullable', 'integer', Rule::exists('curriculum_versions', 'id')],
            'access_level' => ['required', Rule::in(array_keys(Resource::ACCESS_LEVELS))],
            'preview_pages' => ['nullable', 'integer', 'min:1', 'max:20'],
            'copyright_holder' => ['nullable', 'string', 'max:200'],
            'licence' => ['nullable', 'string', 'max:120'],
            'permission_reference' => ['nullable', 'string', 'max:255'],
            'file' => ['nullable', 'file', "max:{$maxKb}"],
            'preview_file' => ['nullable', 'file', "max:{$maxKb}"],
        ]);

        // Level comes from the subject (or class); the pieces must agree with each other.
        $subject = ! empty($data['subject_id']) ? Subject::find($data['subject_id']) : null;
        $class = ! empty($data['class_level_id']) ? ClassLevel::find($data['class_level_id']) : null;
        $levelId = $subject?->education_level_id ?? $class?->education_level_id;

        $errors = [];
        if ($class && $levelId !== $class->education_level_id) {
            $errors['class_level_id'] = 'This class is not in the same education level as the subject.';
        }
        if (! empty($data['programme_id']) && Programme::find($data['programme_id'])?->education_level_id !== $levelId) {
            $errors['programme_id'] = 'This programme is not in the same education level as the subject.';
        }
        if (! empty($data['curriculum_version_id']) && CurriculumVersion::find($data['curriculum_version_id'])?->education_level_id !== $levelId) {
            $errors['curriculum_version_id'] = 'This curriculum version is for a different education level.';
        }

        $examType = ! empty($data['exam_type_id']) ? ExamType::find($data['exam_type_id']) : null;
        if ($examType && in_array($examType->code, self::WAEC_EXAM_CODES, true)) {
            if ($data['access_level'] !== 'free') {
                $errors['access_level'] = 'WAEC papers may only be published free; premium content must be your own solutions and practice material.';
            }
            if (empty($data['copyright_holder'])) {
                $errors['copyright_holder'] = 'Record the copyright holder (e.g. WAEC) for examination papers.';
            }
        }

        if ($errors) {
            throw ValidationException::withMessages($errors);
        }

        unset($data['file'], $data['preview_file']);
        $data['education_level_id'] = $levelId;
        if (! $titleRequired) {
            unset($data['title']); // bulk upload names each draft after its file
        }

        return $data;
    }

    /** @return array<string, mixed> */
    private function formData(Resource $resource): array
    {
        // Curriculum items of the resource's subject, per version, in tree order (for tagging).
        $nodes = [];
        if ($resource->exists && $resource->subject) {
            $tree = app(CurriculumTree::class);
            $versions = CurriculumVersion::where('education_level_id', $resource->subject->education_level_id)
                ->whereHas('nodes', fn ($q) => $q->where('subject_id', $resource->subject_id))->currentFirst()->get();
            foreach ($versions as $version) {
                $flat = [];
                $walk = function ($items) use (&$walk, &$flat) {
                    foreach ($items as $item) {
                        $flat[] = $item;
                        $walk($item->tree_children);
                    }
                };
                $walk($tree->load($version, $resource->subject));
                $nodes[$version->name.($version->status === 'draft' ? ' (draft)' : '')] = $flat;
            }
        }

        return [
            'resource' => $resource,
            'categories' => ResourceCategory::ordered()->get(),
            'examTypes' => ExamType::ordered()->get(),
            'levels' => EducationLevel::ordered()->with(['subjects', 'classLevels', 'programmes'])->get(),
            'versions' => CurriculumVersion::with('educationLevel')->currentFirst()->get(),
            'curriculumNodes' => $nodes,
            'linkCandidates' => $resource->exists
                ? Resource::where('id', '!=', $resource->id)
                    ->when($resource->subject_id, fn ($q) => $q->where('subject_id', $resource->subject_id))
                    ->with('category')->orderBy('title')->limit(300)->get()
                : collect(),
            'defaultPreviewPages' => PdfPreviewGenerator::defaultPages(),
        ];
    }
}
