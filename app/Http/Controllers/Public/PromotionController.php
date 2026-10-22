<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\QuestionBank\Question;
use App\Models\QuestionBank\QuestionKey;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Teacher promotion exam practice. Anyone can read the list of papers and a few sample questions
 * with answers; taking a paper (and seeing the rest of the bank) needs an approved teacher account.
 */
class PromotionController extends Controller
{
    public const SUBJECT = 'ges-promotion-general';

    /** Numerical, verbal and logical reasoning: its own question set so it does not swamp the general paper. */
    public const APTITUDE = 'ges-promotion-aptitude';

    public const SAMPLES = 5;

    /**
     * Papers on offer. "count" questions are drawn from the general bank; rank-specific papers open
     * as their question sets are written.
     */
    public const PAPERS = [
        ['title' => 'General paper: all ranks', 'for' => 'Every teacher sitting a GES promotion exam', 'count' => 40, 'mode' => 'timed', 'ready' => true],
        ['title' => 'Quick mock: 20 questions', 'for' => 'A 20-minute warm-up across the whole syllabus', 'count' => 20, 'mode' => 'timed', 'ready' => true],
        ['title' => 'Practice: 10 questions, untimed', 'for' => 'Learn from the explanations at your own pace', 'count' => 10, 'mode' => 'practice', 'ready' => true],
        ['title' => 'Aptitude paper: numerical, verbal and logical reasoning', 'for' => 'Number work, word skills and reasoning puzzles', 'count' => 40, 'mode' => 'timed', 'ready' => true, 'subject' => self::APTITUDE],
        ['title' => 'Quick aptitude set: 20 questions', 'for' => 'A short reasoning warm-up', 'count' => 20, 'mode' => 'timed', 'ready' => true, 'subject' => self::APTITUDE],
        ['title' => 'Superintendent II to Superintendent I', 'for' => 'Rank-specific paper', 'count' => 40, 'mode' => 'timed', 'ready' => false],
        ['title' => 'Senior Superintendent to Principal Superintendent', 'for' => 'Rank-specific paper', 'count' => 40, 'mode' => 'timed', 'ready' => false],
        ['title' => 'Principal Superintendent to Assistant Director II', 'for' => 'Rank-specific paper', 'count' => 40, 'mode' => 'timed', 'ready' => false],
        ['title' => 'Assistant Director II to Assistant Director I', 'for' => 'Rank-specific paper', 'count' => 40, 'mode' => 'timed', 'ready' => false],
        ['title' => 'Assistant Director I to Deputy Director', 'for' => 'Rank-specific paper', 'count' => 40, 'mode' => 'timed', 'ready' => false],
    ];

    public function __invoke(Request $request): View
    {
        $user = $request->user();
        $bank = Question::where('confidentiality', 'promotion')->where('status', 'published')->whereNotNull('current_revision_id')
            ->whereHas('subject', fn ($q) => $q->where('slug', self::SUBJECT));

        $totals = [
            self::SUBJECT => (clone $bank)->count(),
            self::APTITUDE => Question::where('confidentiality', 'promotion')->where('status', 'published')->whereNotNull('current_revision_id')
                ->whereHas('subject', fn ($q) => $q->where('slug', self::APTITUDE))->count(),
        ];

        $samples = (clone $bank)->with('currentRevision')->orderBy('id')->limit(self::SAMPLES)->get();
        $keys = QuestionKey::whereIn('question_revision_id', $samples->pluck('current_revision_id'))->pluck('key', 'question_revision_id');

        return view('public.promotion', [
            'user' => $user,
            'profile' => $user?->isTeacher() ? $user->teacherProfile : null,
            'canPractise' => $user && ($user->isApprovedTeacher() || $user->isStaff()),
            'total' => $totals[self::SUBJECT],
            'totals' => $totals,
            'allTotal' => array_sum($totals),
            'samples' => $samples,
            'keys' => $keys,
            'papers' => self::PAPERS,
        ]);
    }
}
