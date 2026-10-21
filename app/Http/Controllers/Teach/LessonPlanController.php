<?php

namespace App\Http\Controllers\Teach;

use App\Http\Controllers\Controller;
use App\Models\Curriculum\ClassLevel;
use App\Models\Curriculum\CurriculumNode;
use App\Models\Curriculum\EducationLevel;
use App\Models\Curriculum\Subject;
use App\Models\LessonPlans\LessonPlan;
use App\Services\Audit;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

/**
 * Teachers write weekly lesson plans. A plan stays private until the teacher shares it; shared
 * plans from teachers trusted with "teach.publish-tutorials" go live at once, everyone else's
 * wait for an administrator (Admin → Lesson plans).
 */
class LessonPlanController extends Controller
{
    public function index(Request $request): View
    {
        return view('teach.lesson-plans.index', [
            'plans' => LessonPlan::where('created_by', $request->user()->id)->with('subject.educationLevel', 'classLevel')
                ->latest('updated_at')->get(),
            'canPublish' => $request->user()->can('teach.publish-tutorials'),
        ]);
    }

    public function create(Request $request): View
    {
        $plan = new LessonPlan(['lessons_per_week' => 3, 'duration_minutes' => 60]);

        // "Use as a starting point": copy another visible plan into a fresh private draft.
        if ($request->filled('from')) {
            $source = LessonPlan::where('slug', $request->string('from'))->first();
            if ($source && $source->canBeViewedBy($request->user())) {
                $plan->fill($source->only($plan->getFillable()));
                $plan->title = $source->title.' (my copy)';
            }
        }

        return view('teach.lesson-plans.form', $this->formData($request, $plan));
    }

    public function store(Request $request): RedirectResponse
    {
        $plan = new LessonPlan($this->validated($request));
        $plan->created_by = $request->user()->id;
        $this->setStatus($request, $plan);
        $plan->save();

        Audit::record('content', 'lesson-plan.created', "Lesson plan added: {$plan->title}", $plan);

        return redirect()->route('teach.lesson-plans.index')->with('success', $this->savedMessage($plan));
    }

    public function edit(Request $request, LessonPlan $lessonPlan): View
    {
        $this->authorizeOwner($request, $lessonPlan);

        return view('teach.lesson-plans.form', $this->formData($request, $lessonPlan));
    }

    public function update(Request $request, LessonPlan $lessonPlan): RedirectResponse
    {
        $this->authorizeOwner($request, $lessonPlan);
        $lessonPlan->fill($this->validated($request));
        $wasShared = in_array($lessonPlan->getOriginal('status'), ['pending', 'published'], true);
        if ($lessonPlan->isDirty() || $wasShared !== $request->boolean('share')) {
            $this->setStatus($request, $lessonPlan);
        }
        $lessonPlan->save();

        return redirect()->route('teach.lesson-plans.index')->with('success', $this->savedMessage($lessonPlan));
    }

    public function destroy(Request $request, LessonPlan $lessonPlan): RedirectResponse
    {
        $this->authorizeOwner($request, $lessonPlan);
        $lessonPlan->delete();

        return redirect()->route('teach.lesson-plans.index')->with('success', 'Lesson plan deleted.');
    }

    private function setStatus(Request $request, LessonPlan $plan): void
    {
        if (! $request->boolean('share')) {
            $plan->status = 'draft';

            return;
        }
        $plan->status = $request->user()->can('teach.publish-tutorials') ? 'published' : 'pending';
        $plan->review_note = null;
        if ($plan->status === 'published' && ! $plan->published_at) {
            $plan->published_at = now();
        }
    }

    private function savedMessage(LessonPlan $plan): string
    {
        return match ($plan->status) {
            'published' => 'Saved and published in Lesson plans.',
            'pending' => 'Saved and sent for review. It goes public once an administrator approves it.',
            default => 'Saved as a private draft. Only you can see it.',
        };
    }

    private function authorizeOwner(Request $request, LessonPlan $plan): void
    {
        abort_unless($plan->created_by === $request->user()->id, 404);
    }

    private function validated(Request $request): array
    {
        $text = ['nullable', 'string', 'max:5000'];
        $data = $request->validate([
            'title' => ['required', 'string', 'max:200'],
            'subject_id' => ['required', 'integer', Rule::exists('subjects', 'id')->where('is_active', true)],
            'class_level_id' => ['nullable', 'integer', 'exists:class_levels,id'],
            'curriculum_node_id' => ['nullable', 'integer', 'exists:curriculum_nodes,id'],
            'term' => ['nullable', 'integer', 'min:1', 'max:3'],
            'week' => ['nullable', 'integer', 'min:1', 'max:40'],
            'strand' => ['nullable', 'string', 'max:200'],
            'sub_strand' => ['nullable', 'string', 'max:200'],
            'content_standard' => $text,
            'indicators' => $text,
            'core_competencies' => $text,
            'key_vocabulary' => ['nullable', 'string', 'max:500'],
            'lessons_per_week' => ['nullable', 'integer', 'min:1', 'max:10'],
            'duration_minutes' => ['nullable', 'integer', 'min:10', 'max:300'],
            'class_size' => ['nullable', 'integer', 'min:1', 'max:300'],
            'resources' => $text,
            'starter' => $text,
            'main_activities' => ['required', 'string', 'max:10000'],
            'reflection' => $text,
            'assessment' => $text,
            'differentiation' => $text,
            'homework' => $text,
            'teacher_notes' => $text,
            'share' => ['nullable', 'boolean'],
        ]);
        unset($data['share']);

        // The topic must belong to the chosen subject, and the year to the subject's level.
        if (! empty($data['curriculum_node_id']) && ! CurriculumNode::whereKey($data['curriculum_node_id'])->where('subject_id', $data['subject_id'])->exists()) {
            throw ValidationException::withMessages(['curriculum_node_id' => 'Choose a topic from the selected subject.']);
        }
        if (! empty($data['class_level_id'])) {
            $levelId = Subject::whereKey($data['subject_id'])->value('education_level_id');
            if (! ClassLevel::whereKey($data['class_level_id'])->where('education_level_id', $levelId)->exists()) {
                throw ValidationException::withMessages(['class_level_id' => 'Choose a year from the same level as the subject.']);
            }
        }

        return $data;
    }

    private function formData(Request $request, LessonPlan $plan): array
    {
        $levels = EducationLevel::active()->ordered()->where('code', '!=', EducationLevel::PROFESSIONAL)
            ->with(['subjects' => fn ($q) => $q->where('is_active', true)->orderBy('sort'), 'classLevels'])->get();

        // Topics for every subject, so the topic list can follow the subject choice without a round trip.
        $topics = CurriculumNode::whereIn('subject_id', $levels->flatMap->subjects->pluck('id'))
            ->whereHas('version', fn ($q) => $q->where('status', 'active'))
            ->where('depth', '<=', 1)
            ->orderBy('path')->orderBy('sort')->get(['id', 'subject_id', 'title', 'code', 'depth'])
            ->groupBy('subject_id')
            ->map(fn ($nodes) => $nodes->map(fn ($n) => ['id' => $n->id, 'label' => str_repeat('— ', $n->depth).($n->code ? $n->code.' ' : '').$n->title])->values());

        return [
            'plan' => $plan,
            'levels' => $levels,
            'topics' => $topics,
            'canPublish' => $request->user()->can('teach.publish-tutorials'),
        ];
    }
}
