<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Curriculum\EducationLevel;
use App\Models\Curriculum\Subject;
use App\Services\Practice\Leaderboard;
use Illuminate\Http\Request;
use Illuminate\View\View;

/** Top students by points, this month or all time, overall or for one subject. */
class LeaderboardController extends Controller
{
    public function __invoke(Request $request, Leaderboard $leaderboard): View
    {
        $filters = $request->validate([
            'subject' => ['nullable', 'string', 'max:120'],
            'period' => ['nullable', 'in:month,all'],
        ]);
        $subject = ! empty($filters['subject']) ? Subject::active()->where('slug', $filters['subject'])->first() : null;
        $period = $filters['period'] ?? 'month';

        $rows = $leaderboard->top($subject?->id, $period === 'month' ? now()->startOfMonth() : null);
        $user = $request->user();

        return view('public.leaderboard', [
            'rows' => $rows,
            'subject' => $subject,
            'period' => $period,
            'mine' => $user ? $rows->search(fn ($r) => $r->user_id === $user->id) : false,
            'levels' => EducationLevel::active()->ordered()->where('code', '!=', EducationLevel::PROFESSIONAL)
                ->with(['subjects' => fn ($q) => $q->active()->orderBy('sort')->orderBy('name')])->get(),
        ]);
    }
}
