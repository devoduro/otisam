<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Curriculum\CurriculumNode;
use App\Models\Curriculum\EducationLevel;
use App\Models\Curriculum\Subject;
use App\Models\LessonPlans\LessonPlan;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Illuminate\View\View;

/**
 * Public lesson plans: browse by level, year, subject, topic and week; read, print or save a
 * plan. Free to everyone, like the subject pages.
 */
class LessonPlanController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'level' => ['nullable', 'string', 'max:20'],
            'year' => ['nullable', 'integer'],
            'subject' => ['nullable', 'string', 'max:120'],
            'topic' => ['nullable', 'integer'],
            'week' => ['nullable', 'integer', 'min:1', 'max:40'],
            'sort' => ['nullable', 'in:week,newest,popular'],
        ]);

        $levels = EducationLevel::active()->ordered()->where('code', '!=', EducationLevel::PROFESSIONAL)->with('classLevels')->get();
        $level = $levels->firstWhere('code', strtoupper($filters['level'] ?? ''));
        $subject = ! empty($filters['subject']) ? Subject::where('slug', $filters['subject'])->first() : null;
        $level ??= $subject ? $levels->firstWhere('id', $subject->education_level_id) : null;
        $year = ! empty($filters['year']) ? $levels->flatMap->classLevels->firstWhere('id', (int) $filters['year']) : null;

        // Only subjects that have published plans, grouped by level.
        $subjectIds = LessonPlan::visible()->distinct()->pluck('subject_id');
        $subjects = Subject::active()->whereIn('id', $subjectIds)
            ->when($level, fn ($q) => $q->where('education_level_id', $level->id))
            ->orderBy('sort')->orderBy('name')->get(['id', 'name', 'slug', 'education_level_id'])
            ->groupBy('education_level_id');

        $topics = $subject
            ? CurriculumNode::where('subject_id', $subject->id)
                ->whereIn('id', LessonPlan::visible()->where('subject_id', $subject->id)->whereNotNull('curriculum_node_id')->select('curriculum_node_id'))
                ->orderBy('path')->orderBy('sort')->get(['id', 'title', 'code', 'path'])
            : collect();
        $topic = ! empty($filters['topic']) ? CurriculumNode::find($filters['topic']) : null;

        $plans = LessonPlan::visible()->with(['subject.educationLevel', 'classLevel', 'topic', 'creator'])
            ->when($filters['q'] ?? null, fn ($q, $s) => $q->where(fn ($w) => $w->where('title', 'like', "%{$s}%")
                ->orWhere('strand', 'like', "%{$s}%")->orWhere('sub_strand', 'like', "%{$s}%")->orWhere('key_vocabulary', 'like', "%{$s}%")))
            ->when($level && ! $subject, fn ($q) => $q->whereHas('subject', fn ($s) => $s->where('education_level_id', $level->id)))
            ->when($subject, fn ($q) => $q->where('subject_id', $subject->id))
            // A plan tagged to no year counts for every year of its level.
            ->when($year, fn ($q) => $q->where(fn ($w) => $w->whereNull('class_level_id')->orWhere('class_level_id', $year->id)))
            ->when($topic, fn ($q) => $q->whereIn('curriculum_node_id', CurriculumNode::where('id', $topic->id)->orWhere('path', 'like', $topic->childPath().'%')->select('id')))
            ->when($filters['week'] ?? null, fn ($q, $w) => $q->where('week', $w))
            ->tap(fn ($q) => match ($filters['sort'] ?? 'week') {
                'newest' => $q->latest('published_at'),
                'popular' => $q->orderByDesc('view_count'),
                default => $q->orderByRaw('term is null')->orderBy('term')->orderByRaw('week is null')->orderBy('week')->orderBy('title'),
            })
            ->paginate(18)->withQueryString();

        return view('public.lesson-plans.index', [
            'plans' => $plans,
            'filters' => $filters,
            'levels' => $levels,
            'level' => $level,
            'subjects' => $subjects,
            'subject' => $subject,
            'topics' => $topics,
        ]);
    }

    public function show(Request $request, LessonPlan $plan): View
    {
        abort_unless($plan->canBeViewedBy($request->user()), 404);

        // Authors reading their own plan do not count as views.
        if ($plan->isVisible() && ! ($request->user() && $plan->created_by === $request->user()->id)) {
            LessonPlan::whereKey($plan->id)->increment('view_count');
        }

        return view('public.lesson-plans.show', [
            'plan' => $plan->load(['subject.educationLevel', 'classLevel', 'topic.version', 'creator']),
            'related' => LessonPlan::visible()->whereKeyNot($plan->id)->where('subject_id', $plan->subject_id)
                ->when($plan->class_level_id, fn ($q) => $q->where(fn ($w) => $w->whereNull('class_level_id')->orWhere('class_level_id', $plan->class_level_id)))
                ->orderByRaw('week is null')->orderBy('week')->limit(6)->get(),
        ]);
    }

    /** Clean printable page; the browser's "Save as PDF" turns it into a PDF. */
    public function print(Request $request, LessonPlan $plan): View
    {
        abort_unless($plan->canBeViewedBy($request->user()), 404);

        return view('public.lesson-plans.print', ['plan' => $plan->load(['subject.educationLevel', 'classLevel', 'topic', 'creator']), 'forWord' => false]);
    }

    /** The printable page as a .doc file, so teachers can edit it in Word or LibreOffice. */
    public function download(Request $request, LessonPlan $plan): Response
    {
        abort_unless($plan->canBeViewedBy($request->user()), 404);

        $html = view('public.lesson-plans.print', ['plan' => $plan->load(['subject.educationLevel', 'classLevel', 'topic', 'creator']), 'forWord' => true])->render();
        $name = Str::limit($plan->slug, 120, '').'.doc';

        return response($html, 200, [
            'Content-Type' => 'application/msword; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="'.$name.'"',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }
}
