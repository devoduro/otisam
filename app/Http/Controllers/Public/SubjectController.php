<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Curriculum\CurriculumNode;
use App\Models\Curriculum\CurriculumVersion;
use App\Models\Curriculum\EducationLevel;
use App\Models\Curriculum\Subject;
use App\Models\Library\Resource;
use App\Services\Curriculum\CurriculumTree;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Public browsing: subject → topic. Only active subjects of active levels, and only versions that
 * are not drafts, are visible.
 */
class SubjectController extends Controller
{
    public function index(): View
    {
        $levels = EducationLevel::active()->ordered()
            ->with(['subjects' => fn ($q) => $q->where('is_active', true)])
            ->get()
            ->filter(fn ($level) => $level->subjects->isNotEmpty());

        return view('public.subjects.index', compact('levels'));
    }

    public function show(Request $request, Subject $subject, CurriculumTree $tree): View
    {
        $this->assertVisible($subject);

        $versions = CurriculumVersion::published()->currentFirst()
            ->where('education_level_id', $subject->education_level_id)
            ->whereHas('nodes', fn ($q) => $q->where('subject_id', $subject->id))
            ->get();

        $version = $versions->firstWhere('id', $request->integer('version')) ?? $versions->first();

        return view('public.subjects.show', [
            'subject' => $subject->load('educationLevel'),
            'versions' => $versions,
            'version' => $version,
            'roots' => $version ? $tree->load($version, $subject) : collect(),
        ]);
    }

    public function topic(Subject $subject, CurriculumNode $node): View
    {
        $this->assertVisible($subject);
        abort_unless($node->subject_id === $subject->id, 404);

        $node->load('version', 'classLevel', 'children.classLevel');
        abort_unless($node->version->isPublic(), 404);

        $ancestors = CurriculumNode::whereIn('id', $node->ancestorIds())->orderBy('depth')->get();

        // Items tagged to this topic or anything under it.
        $resources = Resource::visible()->where('audience', '!=', 'teacher')
            ->whereHas('curriculumNodes', fn ($q) => $q->where('curriculum_nodes.id', $node->id)->orWhere('curriculum_nodes.path', 'like', $node->childPath().'%'))
            ->with(['category', 'subject', 'examType', 'classLevel'])
            ->orderByDesc('exam_year')->limit(30)->get();

        return view('public.subjects.topic', [
            'subject' => $subject->load('educationLevel'),
            'node' => $node,
            'ancestors' => $ancestors,
            'resources' => $resources,
        ]);
    }

    private function assertVisible(Subject $subject): void
    {
        abort_unless($subject->is_active && $subject->educationLevel?->is_active, 404);
    }
}
