<?php

namespace App\Services\Curriculum;

use App\Models\Curriculum\CurriculumNode;
use App\Models\Curriculum\CurriculumVersion;
use App\Models\Curriculum\Subject;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

/**
 * Owns the structure of a subject's curriculum tree (one tree per version + subject):
 * parent, materialised `path`, `depth` and sibling `sort`. Every structural change goes through
 * here so the path column can be trusted for subtree queries.
 */
class CurriculumTree
{
    /**
     * Whole tree in one query, nested in memory: each node gets a `tree_children` collection.
     *
     * @return Collection<int, CurriculumNode> root nodes
     */
    public function load(CurriculumVersion $version, Subject $subject): Collection
    {
        $nodes = CurriculumNode::inTree($version->id, $subject->id)
            ->with('classLevel')
            ->orderBy('depth')->orderBy('sort')->orderBy('id')
            ->get();

        $byParent = $nodes->groupBy(fn (CurriculumNode $n) => (int) $n->parent_id);

        foreach ($nodes as $node) {
            $node->setRelation('tree_children', $byParent->get($node->id, collect())->values());
        }

        return $byParent->get(0, collect())->values();
    }

    /** @param array<string, mixed> $attributes validated fillable attributes */
    public function add(CurriculumVersion $version, Subject $subject, ?CurriculumNode $parent, array $attributes): CurriculumNode
    {
        $this->assertSameLevel($version, $subject);

        if ($parent) {
            $this->assertInTree($parent, $version, $subject);
            if ($parent->depth + 1 > CurriculumNode::MAX_DEPTH) {
                throw new InvalidArgumentException('The tree cannot go deeper than '.(CurriculumNode::MAX_DEPTH + 1).' levels.');
            }
        }

        return DB::transaction(function () use ($version, $subject, $parent, $attributes) {
            $node = new CurriculumNode($attributes);
            $node->curriculum_version_id = $version->id;
            $node->subject_id = $subject->id;
            $node->parent_id = $parent?->id;
            $node->depth = $parent ? $parent->depth + 1 : 0;
            $node->path = $parent ? $parent->childPath() : '/';
            $node->sort = $this->nextSort($version->id, $subject->id, $parent?->id);
            $node->save();

            return $node;
        });
    }

    /**
     * Update a node's content and, if $newParentId differs, move it (with its whole subtree)
     * under another node of the same tree, or to the top level when $newParentId is null.
     *
     * @param  array<string, mixed>  $attributes  validated fillable attributes
     */
    public function update(CurriculumNode $node, array $attributes, ?int $newParentId): CurriculumNode
    {
        return DB::transaction(function () use ($node, $attributes, $newParentId) {
            $node->fill($attributes);

            if ($newParentId !== $node->parent_id) {
                $this->move($node, $newParentId);
            }

            $node->save();

            return $node;
        });
    }

    /** Swap a node with its previous or next sibling. */
    public function shift(CurriculumNode $node, string $direction): void
    {
        DB::transaction(function () use ($node, $direction) {
            $this->normaliseSort($node->curriculum_version_id, $node->subject_id, $node->parent_id);
            $node->refresh();

            $sibling = CurriculumNode::inTree($node->curriculum_version_id, $node->subject_id)
                ->where('parent_id', $node->parent_id)
                ->where('sort', $direction === 'up' ? '<' : '>', $node->sort)
                ->orderBy('sort', $direction === 'up' ? 'desc' : 'asc')
                ->first();

            if ($sibling) {
                [$node->sort, $sibling->sort] = [$sibling->sort, $node->sort];
                $node->save();
                $sibling->save();
            }
        });
    }

    /** Delete a node; its descendants go with it (foreign key cascade). Returns how many were removed. */
    public function delete(CurriculumNode $node): int
    {
        return DB::transaction(function () use ($node) {
            $count = 1 + $this->descendants($node)->count();
            $node->delete();

            return $count;
        });
    }

    public function descendants(CurriculumNode $node)
    {
        return CurriculumNode::inTree($node->curriculum_version_id, $node->subject_id)
            ->where('path', 'like', $node->childPath().'%');
    }

    private function move(CurriculumNode $node, ?int $newParentId): void
    {
        $newParent = null;

        if ($newParentId !== null) {
            $newParent = CurriculumNode::find($newParentId);

            if (! $newParent || $newParent->curriculum_version_id !== $node->curriculum_version_id || $newParent->subject_id !== $node->subject_id) {
                throw new InvalidArgumentException('The new parent must be in the same subject and curriculum version.');
            }
            if ($newParent->id === $node->id || str_contains($newParent->path, '/'.$node->id.'/')) {
                throw new InvalidArgumentException('An item cannot be moved inside itself.');
            }
        }

        $oldPrefix = $node->childPath();
        $newDepth = $newParent ? $newParent->depth + 1 : 0;
        $delta = $newDepth - $node->depth;

        // Built now, while the node still has its old path.
        $subtree = $this->descendants($node);

        $deepest = (int) (clone $subtree)->max('depth');
        if (max($deepest, $node->depth) + $delta > CurriculumNode::MAX_DEPTH) {
            throw new InvalidArgumentException('Moving it there would make the tree deeper than '.(CurriculumNode::MAX_DEPTH + 1).' levels.');
        }

        $node->parent_id = $newParent?->id;
        $node->path = $newParent ? $newParent->childPath() : '/';
        $node->depth = $newDepth;
        $node->sort = $this->nextSort($node->curriculum_version_id, $node->subject_id, $newParent?->id);

        $newPrefix = $node->childPath();

        // Rewrite the path prefix and depth of the whole subtree in one statement.
        $subtree->update([
            'path' => DB::raw('CONCAT('.DB::getPdo()->quote($newPrefix).', SUBSTRING(path, '.(strlen($oldPrefix) + 1).'))'),
            'depth' => DB::raw('depth + ('.(int) $delta.')'),
        ]);
    }

    private function nextSort(int $versionId, int $subjectId, ?int $parentId): int
    {
        return (int) CurriculumNode::inTree($versionId, $subjectId)->where('parent_id', $parentId)->max('sort') + 1;
    }

    /** Renumber siblings 1..n so up/down swaps are always well defined. */
    private function normaliseSort(int $versionId, int $subjectId, ?int $parentId): void
    {
        CurriculumNode::inTree($versionId, $subjectId)->where('parent_id', $parentId)
            ->orderBy('sort')->orderBy('id')->get(['id', 'sort'])
            ->each(function (CurriculumNode $sibling, int $i) {
                if ($sibling->sort !== $i + 1) {
                    CurriculumNode::whereKey($sibling->id)->update(['sort' => $i + 1]);
                }
            });
    }

    private function assertSameLevel(CurriculumVersion $version, Subject $subject): void
    {
        if ($version->education_level_id !== $subject->education_level_id) {
            throw new InvalidArgumentException('The subject and the curriculum version belong to different education levels.');
        }
    }

    private function assertInTree(CurriculumNode $node, CurriculumVersion $version, Subject $subject): void
    {
        if ($node->curriculum_version_id !== $version->id || $node->subject_id !== $subject->id) {
            throw new InvalidArgumentException('The parent belongs to a different tree.');
        }
    }
}
