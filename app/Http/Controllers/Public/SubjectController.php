<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Curriculum\CurriculumNode;
use App\Models\Curriculum\CurriculumVersion;
use App\Models\Curriculum\EducationLevel;
use App\Models\Curriculum\Subject;
use App\Models\Library\Resource;
use App\Models\QuestionBank\Question;
use App\Models\Videos\VideoLesson;
use App\Services\Curriculum\CurriculumTree;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * Public browsing: subject → topic. Only active subjects of active levels, and only versions that
 * are not drafts, are visible.
 */
class SubjectController extends Controller
{
    public const HAS = ['library' => 'Past questions & notes', 'videos' => 'Video lessons', 'practice' => 'Practice questions', 'topics' => 'Curriculum topics'];

    public function index(Request $request): View
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'level' => ['nullable', 'string', 'max:20'],
            'category' => ['nullable', 'in:core,elective'],
            'programme' => ['nullable', 'integer'],
            'has' => ['nullable', Rule::in(array_keys(self::HAS))],
            'sort' => ['nullable', 'in:curriculum,name,content'],
        ]);

        $allLevels = EducationLevel::active()->ordered()->with('programmes')->get();
        $level = $allLevels->firstWhere('code', strtoupper($filters['level'] ?? ''));

        // How much there is to study in each subject.
        $counts = [
            'library' => Resource::visible()->where('audience', '!=', 'teacher')->whereNotNull('subject_id')->selectRaw('subject_id, count(*) n')->groupBy('subject_id')->pluck('n', 'subject_id'),
            'videos' => VideoLesson::visible()->whereNotNull('subject_id')->selectRaw('subject_id, count(*) n')->groupBy('subject_id')->pluck('n', 'subject_id'),
            'practice' => Question::where('status', 'published')->where('confidentiality', 'practice')->selectRaw('subject_id, count(*) n')->groupBy('subject_id')->pluck('n', 'subject_id'),
            'topics' => CurriculumNode::whereHas('version', fn ($v) => $v->published())->selectRaw('subject_id, count(*) n')->groupBy('subject_id')->pluck('n', 'subject_id'),
        ];

        $subjects = Subject::query()
            ->where('is_active', true)
            ->whereIn('education_level_id', ($level ? collect([$level]) : $allLevels)->pluck('id'))
            ->when($filters['q'] ?? null, fn ($q, $s) => $q->where(fn ($w) => $w->where('name', 'like', "%{$s}%")->orWhere('description', 'like', "%{$s}%")))
            ->when($filters['category'] ?? null, fn ($q, $c) => $q->where('category', $c))
            ->when($filters['programme'] ?? null, fn ($q, $p) => $q->whereHas('programmes', fn ($pq) => $pq->where('programmes.id', $p)))
            ->when($filters['has'] ?? null, fn ($q, $h) => $q->whereIn('id', $counts[$h]->keys()))
            ->get();

        $subjects = match ($filters['sort'] ?? 'curriculum') {
            'name' => $subjects->sortBy('name'),
            'content' => $subjects->sortByDesc(fn ($s) => collect($counts)->sum(fn ($c) => $c[$s->id] ?? 0)),
            default => $subjects->sortBy([['sort', 'asc'], ['name', 'asc']]),
        };

        $levels = ($level ? collect([$level]) : $allLevels)->map(function ($l) use ($subjects) {
            $l->setRelation('subjects', $subjects->where('education_level_id', $l->id)->values());

            return $l;
        })->filter(fn ($l) => $l->subjects->isNotEmpty());

        return view('public.subjects.index', [
            'levels' => $levels,
            'allLevels' => $allLevels,
            'level' => $level,
            'filters' => $filters,
            'counts' => $counts,
            'total' => $subjects->count(),
        ]);
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
