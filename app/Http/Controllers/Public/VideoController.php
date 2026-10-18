<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Curriculum\CurriculumNode;
use App\Models\Curriculum\EducationLevel;
use App\Models\Curriculum\Subject;
use App\Models\Videos\VideoLesson;
use App\Services\Access\Entitlements;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VideoController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'level' => ['nullable', 'string', 'max:20'],
            'year' => ['nullable', 'integer'],
            'subject' => ['nullable', 'string', 'max:120'],
            'topic' => ['nullable', 'integer'],
            'sort' => ['nullable', 'in:newest,oldest,popular,shortest'],
        ]);

        $levels = EducationLevel::active()->ordered()->with('classLevels')->get();
        $level = $levels->firstWhere('code', strtoupper($filters['level'] ?? ''));
        $subject = ! empty($filters['subject']) ? Subject::where('slug', $filters['subject'])->first() : null;
        $level ??= $subject ? $levels->firstWhere('id', $subject->education_level_id) : null;
        $year = ! empty($filters['year']) ? $levels->flatMap->classLevels->firstWhere('id', (int) $filters['year']) : null;

        // Only subjects that actually have published videos, grouped by level so "English Language"
        // (JHS) and "English Language" (SHS) are told apart.
        $subjectIds = VideoLesson::visible()->whereNotNull('subject_id')->distinct()->pluck('subject_id');
        $subjects = Subject::active()->whereIn('id', $subjectIds)
            ->when($level, fn ($q) => $q->where('education_level_id', $level->id))
            ->orderBy('sort')->orderBy('name')->get(['id', 'name', 'slug', 'education_level_id'])
            ->groupBy('education_level_id');

        // Topics with videos in the chosen subject (or year).
        $topics = $subject
            ? CurriculumNode::where('subject_id', $subject->id)
                ->whereIn('id', VideoLesson::visible()->where('subject_id', $subject->id)->whereNotNull('curriculum_node_id')->select('curriculum_node_id'))
                ->when($year, fn ($q) => $q->where(fn ($w) => $w->whereNull('class_level_id')->orWhere('class_level_id', $year->id)))
                ->orderBy('path')->orderBy('sort')->get(['id', 'title', 'code', 'path'])
            : collect();
        $topic = ! empty($filters['topic']) ? CurriculumNode::find($filters['topic']) : null;

        $videos = VideoLesson::visible()->with(['subject.educationLevel', 'classLevel', 'topic'])
            ->when($filters['q'] ?? null, fn ($q, $s) => $q->where(fn ($w) => $w->where('title', 'like', "%{$s}%")->orWhere('description', 'like', "%{$s}%")))
            ->when($level && ! $subject, fn ($q) => $q->whereHas('subject', fn ($s) => $s->where('education_level_id', $level->id)))
            ->when($subject, fn ($q) => $q->where('subject_id', $subject->id))
            // A video tagged to no year counts for every year of its level.
            ->when($year, fn ($q) => $q->where(fn ($w) => $w->whereNull('class_level_id')->orWhere('class_level_id', $year->id)))
            ->when($topic, fn ($q) => $q->whereIn('curriculum_node_id', CurriculumNode::where('id', $topic->id)->orWhere('path', 'like', $topic->childPath().'%')->select('id')))
            ->tap(fn ($q) => match ($filters['sort'] ?? 'newest') {
                'oldest' => $q->oldest('published_at'),
                'popular' => $q->orderByDesc('view_count'),
                'shortest' => $q->orderByRaw('duration_minutes is null')->orderBy('duration_minutes'),
                default => $q->latest('published_at'),
            })
            ->paginate(18)->withQueryString();

        return view('public.videos.index', [
            'videos' => $videos,
            'filters' => $filters,
            'levels' => $levels,
            'level' => $level,
            'subjects' => $subjects,
            'subject' => $subject,
            'topics' => $topics,
        ]);
    }

    public function show(Request $request, VideoLesson $video, Entitlements $entitlements): View
    {
        $user = $request->user();
        $isEditor = $user?->isStaff() && $user->can('tutorials.manage');
        abort_unless($video->isVisible() || $isEditor, 404);

        $canWatch = $isEditor || $video->isFree() || $entitlements->hasPremium($user);
        if ($video->isVisible()) {
            VideoLesson::whereKey($video->id)->increment('view_count');
        }

        return view('public.videos.show', [
            'video' => $video->load(['subject', 'classLevel', 'topic.version']),
            'canWatch' => $canWatch,
            'related' => VideoLesson::visible()->where('id', '!=', $video->id)
                ->when($video->subject_id, fn ($q) => $q->where('subject_id', $video->subject_id))
                ->latest('published_at')->limit(6)->get(),
        ]);
    }
}
