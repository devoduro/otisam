<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Community\Answer;
use App\Models\Community\Question;
use App\Models\Community\Report;
use App\Services\Audit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Staff review of community Q&A (permission tutorials.manage): see reported and hidden posts,
 * keep or restore, hide, or delete. Acting on a post resolves its open reports. Audited.
 */
class QaModerationController extends Controller
{
    private const TYPES = ['question' => Question::class, 'answer' => Answer::class];

    public function index(Request $request): View
    {
        $tab = $request->validate(['tab' => ['nullable', 'in:reported,hidden']])['tab'] ?? 'reported';

        // One row per reported post, most recently reported first.
        $reported = Report::whereNull('resolved_at')
            ->selectRaw('reportable_type, reportable_id, count(*) as open_count, max(created_at) as last_reported_at')
            ->groupBy('reportable_type', 'reportable_id')
            ->orderByDesc('last_reported_at')
            ->paginate(25, ['*'], 'page')->withQueryString();
        $reported->getCollection()->load(['reportable' => fn ($q) => $q->with('author')]);
        $reasons = Report::whereNull('resolved_at')
            ->whereIn('reportable_id', $reported->pluck('reportable_id'))
            ->with('reporter')->latest()->get()
            ->groupBy(fn (Report $r) => $r->reportable_type.':'.$r->reportable_id);

        return view('admin.qa.index', [
            'tab' => $tab,
            'reported' => $reported,
            'reasons' => $reasons,
            'openCount' => Report::openPostCount(),
            'hiddenQuestions' => $tab === 'hidden' ? Question::where('status', 'hidden')->with('author')->latest('updated_at')->paginate(25, ['*'], 'qpage')->withQueryString() : null,
            'hiddenAnswers' => $tab === 'hidden' ? Answer::where('status', 'hidden')->with(['author', 'question'])->latest('updated_at')->paginate(25, ['*'], 'apage')->withQueryString() : null,
            'hiddenCount' => Question::where('status', 'hidden')->count() + Answer::where('status', 'hidden')->count(),
            'labels' => Report::REASONS,
        ]);
    }

    public function hide(Request $request, string $type, int $id): RedirectResponse
    {
        $post = $this->find($type, $id);
        $old = $post->status;
        $post->forceFill(['status' => 'hidden', 'hidden_reason' => 'moderator'])->save();
        $this->resolveReports($request, $post);

        Audit::record('content', "qa.{$type}.hidden", 'Q&A post hidden: '.$post->label(), $post, ['status' => $old], ['status' => 'hidden']);

        return back()->with('success', ucfirst($type).' hidden.');
    }

    /** Keep a reported post up, or bring back a hidden one. */
    public function restore(Request $request, string $type, int $id): RedirectResponse
    {
        $post = $this->find($type, $id);
        $old = $post->status;
        $post->forceFill(['status' => 'visible', 'hidden_reason' => null])->save();
        $this->resolveReports($request, $post);

        Audit::record('content', "qa.{$type}.restored", 'Q&A post kept or restored: '.$post->label(), $post, ['status' => $old], ['status' => 'visible']);

        return back()->with('success', ucfirst($type).' is visible. Its reports are closed.');
    }

    public function destroy(string $type, int $id): RedirectResponse
    {
        $post = $this->find($type, $id);
        Audit::record('content', "qa.{$type}.deleted", 'Q&A post deleted: '.$post->label(), $post, ['body' => $post->body, 'user_id' => $post->user_id]);
        $post->delete();

        return back()->with('success', ucfirst($type).' deleted.');
    }

    private function find(string $type, int $id): Model
    {
        $class = self::TYPES[$type] ?? abort(404);

        return $class::findOrFail($id);
    }

    private function resolveReports(Request $request, Model $post): void
    {
        $post->openReports()->update(['resolved_at' => now(), 'resolved_by' => $request->user()->id]);
    }
}
