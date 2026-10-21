<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LessonPlans\LessonPlan;
use App\Services\Audit;
use App\Services\Notifications\Notifier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Staff review of lesson plans teachers share (permission tutorials.manage): approve, send back
 * with a reason, or take a published plan down. Every decision is audited and the teacher is told.
 */
class LessonPlanController extends Controller
{
    public function __construct(private readonly Notifier $notifier) {}

    public function index(Request $request): View
    {
        $status = $request->validate(['status' => ['nullable', 'in:pending,published,archived']])['status'] ?? 'pending';

        return view('admin.lesson-plans.index', [
            'status' => $status,
            'counts' => LessonPlan::whereIn('status', ['pending', 'published', 'archived'])->selectRaw('status, count(*) as n')->groupBy('status')->pluck('n', 'status'),
            'plans' => LessonPlan::where('status', $status)->with(['creator', 'subject.educationLevel', 'classLevel'])
                ->when($status === 'pending', fn ($q) => $q->oldest('updated_at'), fn ($q) => $q->latest('updated_at'))
                ->paginate(25)->withQueryString(),
        ]);
    }

    public function approve(LessonPlan $plan): RedirectResponse
    {
        abort_unless($plan->status === 'pending', 404);

        $plan->status = 'published';
        $plan->published_at ??= now();
        $plan->review_note = null;
        $plan->save();

        Audit::record('content', 'lesson-plan.approved', "Lesson plan approved: {$plan->title}", $plan, ['status' => 'pending'], ['status' => 'published']);
        $this->tell($plan, 'lesson-plan.approved', 'Your lesson plan is live',
            "your lesson plan \"{$plan->title}\" was approved. It is now in ".config('app.name').' Lesson plans.');

        return back()->with('success', "\"{$plan->title}\" published.");
    }

    public function reject(Request $request, LessonPlan $plan): RedirectResponse
    {
        abort_unless(in_array($plan->status, ['pending', 'published'], true), 404);
        $reason = $request->validate(['reason' => ['required', 'string', 'max:500']])['reason'];
        $old = $plan->status;

        // Back to a private draft: the teacher edits it and shares it again.
        $plan->status = 'draft';
        $plan->review_note = $reason;
        $plan->save();

        Audit::record('content', 'lesson-plan.rejected', "Lesson plan sent back: {$plan->title}", $plan, ['status' => $old], ['status' => 'draft', 'reason' => $reason]);
        $this->tell($plan, 'lesson-plan.rejected', 'Your lesson plan was not approved',
            "your lesson plan \"{$plan->title}\" was not approved. Reason: {$reason} You can edit it and share it again.");

        return back()->with('success', "\"{$plan->title}\" sent back. The teacher has been told why.");
    }

    public function archive(LessonPlan $plan): RedirectResponse
    {
        abort_unless($plan->status === 'published', 404);
        $plan->status = 'archived';
        $plan->save();

        Audit::record('content', 'lesson-plan.archived', "Lesson plan archived: {$plan->title}", $plan, ['status' => 'published'], ['status' => 'archived']);

        return back()->with('success', "\"{$plan->title}\" archived. It is no longer public.");
    }

    private function tell(LessonPlan $plan, string $event, string $subject, string $body): void
    {
        if (! $plan->creator) {
            return;
        }
        $app = config('app.name');
        $this->notifier->toUser($plan->creator, $event, 'transactional', "{$app}: {$subject}",
            "Hello {$plan->creator->name}, {$body}", "{$event}:{$plan->id}:".$plan->updated_at->timestamp);
    }
}
