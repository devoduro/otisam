<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Curriculum\Subject;
use App\Models\QuestionBank\Question;
use App\Models\QuestionBank\QuestionKey;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
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

    /** Leadership, administration, HR, finance and policy: harder questions drawn on by the senior-rank papers. */
    public const LEADERSHIP = 'ges-promotion-leadership';

    public const SAMPLES = 5;

    /**
     * Papers on offer. "count" questions are drawn from the general bank. A paper with a "mix" is a
     * practice set: percentages of general, aptitude and leadership questions (in that order), with
     * leadership questions no harder than "maxDifficulty". These are Otisam's own practice sets, not
     * official GES rank syllabi.
     */
    public const PAPERS = [
        ['title' => 'General paper: all ranks', 'for' => 'Every teacher sitting a GES promotion exam', 'count' => 40, 'mode' => 'timed', 'ready' => true],
        ['title' => 'Quick mock: 20 questions', 'for' => 'A 20-minute warm-up across the whole syllabus', 'count' => 20, 'mode' => 'timed', 'ready' => true],
        ['title' => 'Practice: 10 questions, untimed', 'for' => 'Learn from the explanations at your own pace', 'count' => 10, 'mode' => 'practice', 'ready' => true],
        ['title' => 'Aptitude paper: numerical, verbal and logical reasoning', 'for' => 'Number work, word skills and reasoning puzzles', 'count' => 40, 'mode' => 'timed', 'ready' => true, 'subject' => self::APTITUDE],
        ['title' => 'Quick aptitude set: 20 questions', 'for' => 'A short reasoning warm-up', 'count' => 20, 'mode' => 'timed', 'ready' => true, 'subject' => self::APTITUDE],
        ['title' => 'Superintendent II to Superintendent I', 'for' => 'Practice set weighted towards this rank', 'count' => 40, 'mode' => 'timed', 'ready' => true, 'key' => 'sup-ii-to-i', 'mix' => [60, 40, 0], 'maxDifficulty' => 5],
        ['title' => 'Senior Superintendent to Principal Superintendent', 'for' => 'Practice set weighted towards this rank', 'count' => 40, 'mode' => 'timed', 'ready' => true, 'key' => 'senior-to-principal', 'mix' => [50, 30, 20], 'maxDifficulty' => 3],
        ['title' => 'Principal Superintendent to Assistant Director II', 'for' => 'Practice set weighted towards this rank', 'count' => 40, 'mode' => 'timed', 'ready' => true, 'key' => 'principal-to-ad-ii', 'mix' => [40, 20, 40], 'maxDifficulty' => 3],
        ['title' => 'Assistant Director II to Assistant Director I', 'for' => 'Practice set weighted towards this rank', 'count' => 40, 'mode' => 'timed', 'ready' => true, 'key' => 'ad-ii-to-ad-i', 'mix' => [30, 20, 50], 'maxDifficulty' => 5],
        ['title' => 'Assistant Director I to Deputy Director', 'for' => 'Practice set weighted towards this rank', 'count' => 40, 'mode' => 'timed', 'ready' => true, 'key' => 'ad-i-to-deputy', 'mix' => [20, 10, 70], 'maxDifficulty' => 5],
    ];

    public function __invoke(Request $request): View
    {
        $user = $request->user();
        $bank = Question::where('confidentiality', 'promotion')->where('status', 'published')->whereNotNull('current_revision_id')
            ->whereHas('subject', fn ($q) => $q->where('slug', self::SUBJECT));

        $totals = collect([self::SUBJECT, self::APTITUDE, self::LEADERSHIP])
            ->mapWithKeys(fn ($slug) => [$slug => $this->bankFor($slug)->count()])->all();

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

    /** A rank practice set: a shuffled mix of general, aptitude and leadership questions. */
    public function paper(Request $request, string $paper): View|RedirectResponse
    {
        $config = collect(self::PAPERS)->firstWhere('key', $paper);
        abort_unless($config, 404);

        $user = $request->user();
        if (! $user) {
            return redirect()->guest(route('teachers.register'))->with('status', 'Create a free teacher account to take promotion practice papers.');
        }
        if (! ($user->isApprovedTeacher() || $user->isStaff())) {
            return redirect()->route('promotion')->with('error', 'Promotion practice is for approved teacher accounts.');
        }

        $slugs = [self::SUBJECT, self::APTITUDE, self::LEADERSHIP];
        $picked = collect();
        $wanted = 0;
        foreach ($slugs as $i => $slug) {
            $share = $i === 2 ? $config['count'] - $wanted : (int) round($config['count'] * $config['mix'][$i] / 100);
            $wanted += $share;
            if ($share < 1) {
                continue;
            }
            $query = $this->bankFor($slug);
            if ($slug === self::LEADERSHIP) {
                $query->where('difficulty', '<=', $config['maxDifficulty']);
            }
            $picked = $picked->concat($query->with('currentRevision')->inRandomOrder()->limit($share)->get());
        }

        // A thin bank is topped up from the general set so the paper keeps its length.
        if ($picked->count() < $config['count']) {
            $extra = $this->bankFor(self::SUBJECT)->whereNotIn('id', $picked->pluck('id'))->with('currentRevision')
                ->inRandomOrder()->limit($config['count'] - $picked->count())->get();
            $picked = $picked->concat($extra);
        }
        $questions = $picked->shuffle()->values();

        $subject = Subject::where('slug', self::SUBJECT)->with('educationLevel')->firstOrFail();
        $minutes = max(1, $questions->count() * PracticeController::MINUTES_PER_QUESTION);
        $filters = ['subject' => self::SUBJECT, 'count' => 20, 'mode' => 'timed'];

        $request->session()->put('practice.set', [
            'subject_id' => $subject->id,
            'ids' => $questions->pluck('id')->all(),
            'started_at' => now()->timestamp,
            'minutes' => $minutes,
            'filters' => $filters,
        ]);

        return view('public.practice.show', ['subject' => $subject, 'questions' => $questions, 'minutes' => $minutes, 'filters' => $filters]);
    }

    private function bankFor(string $slug): Builder
    {
        return Question::query()->where('confidentiality', 'promotion')->where('status', 'published')->where('type', 'mcq')
            ->whereNotNull('current_revision_id')
            ->whereHas('subject', fn ($q) => $q->where('slug', $slug));
    }
}
