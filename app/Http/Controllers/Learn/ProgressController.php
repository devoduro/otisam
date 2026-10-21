<?php

namespace App\Http\Controllers\Learn;

use App\Http\Controllers\Controller;
use App\Models\QuestionBank\QuizAttempt;
use App\Services\Practice\Leaderboard;
use Illuminate\Http\Request;
use Illuminate\View\View;

/** A student's saved scores: totals, per-subject averages and recent attempts. */
class ProgressController extends Controller
{
    public function __invoke(Request $request, Leaderboard $leaderboard): View
    {
        $user = $request->user();
        $attempts = QuizAttempt::where('user_id', $user->id);

        $bySubject = (clone $attempts)->with('subject:id,name,slug,education_level_id')
            ->selectRaw('subject_id, count(*) as attempts, sum(score) as score, sum(total) as total')
            ->groupBy('subject_id')->get()
            ->sortByDesc('attempts')->values();

        $month = $leaderboard->top(null, now()->startOfMonth(), 1000);

        return view('learn.progress', [
            'recent' => (clone $attempts)->with(['subject:id,name,slug', 'paper:id,subject_id,number,title'])->latest('created_at')->limit(20)->get(),
            'bySubject' => $bySubject,
            'totals' => ['attempts' => (clone $attempts)->count(), 'papers' => (clone $attempts)->where('kind', 'paper')->distinct()->count('exam_paper_id')],
            'rank' => ($i = $month->search(fn ($r) => $r->user_id === $user->id)) === false ? null : $i + 1,
            'points' => $month->firstWhere('user_id', $user->id)?->points ?? 0,
        ]);
    }
}
