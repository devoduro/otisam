<?php

namespace App\Services\Practice;

use App\Models\Curriculum\EducationLevel;
use App\Models\Curriculum\Subject;
use App\Models\QuestionBank\ExamPaper;
use App\Models\QuestionBank\Question;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Turns a subject's free, published practice questions into numbered objective papers laid out
 * like the exam the subject leads to. Each question goes into at most one paper, so papers never
 * repeat each other, and every paper mixes easy, medium and hard questions in roughly the same
 * proportions. Running it again only adds papers from questions not yet used.
 */
class PaperBuilder
{
    /** Questions and minutes per paper, by exam style. Modelled on objective papers, not copied timings. */
    public const SHAPES = [
        'WASSCE' => ['questions' => 50, 'minutes' => 60],
        'BECE' => ['questions' => 40, 'minutes' => 50],
    ];

    /** Share of each paper drawn from each difficulty band (difficulty 1–2, 3, 4–5). */
    private const MIX = ['easy' => 0.3, 'medium' => 0.4, 'hard' => 0.3];

    /** @return int papers created */
    public function build(Subject $subject, ?int $limit = null): int
    {
        $subject->loadMissing('educationLevel');
        $style = $subject->examStyle();
        $shape = self::SHAPES[$style] ?? null;
        if (! $shape || $subject->educationLevel?->code === EducationLevel::PROFESSIONAL) {
            return 0;
        }

        $size = $shape['questions'];
        $pools = $this->unusedQuestions($subject)->groupBy(fn ($q) => $q->difficulty <= 2 ? 'easy' : ($q->difficulty === 3 ? 'medium' : 'hard'));
        $pools = collect(self::MIX)->map(fn ($share, $band) => $pools->get($band, collect())->values());

        $created = 0;
        $number = (int) ExamPaper::where('subject_id', $subject->id)->max('number');
        while ($pools->sum(fn ($p) => $p->count()) >= $size && ($limit === null || $created < $limit)) {
            $picked = $this->pick($pools, $size);
            $number++;
            DB::transaction(function () use ($subject, $style, $shape, $number, $picked) {
                $paper = new ExamPaper(['number' => $number, 'title' => ExamPaper::titleFor($subject->name, $number), 'exam_style' => $style, 'duration_minutes' => $shape['minutes']]);
                $paper->subject_id = $subject->id;
                $paper->status = 'published';
                $paper->published_at = now();
                $paper->save();
                // Easier questions first, as on a real paper, keeping a stable order within each band.
                $paper->questions()->attach($picked->sortBy([['difficulty', 'asc'], ['id', 'asc']])->values()
                    ->mapWithKeys(fn ($q, $i) => [$q->id => ['position' => $i + 1]])->all());
            });
            $created++;
        }

        return $created;
    }

    /** Free published practice MCQs of the subject that no paper uses yet, oldest first. */
    private function unusedQuestions(Subject $subject): Collection
    {
        return Question::query()
            ->where('subject_id', $subject->id)
            ->where('status', 'published')
            ->where('confidentiality', 'practice')
            ->where('type', 'mcq')
            ->where('access_level', 'free')
            ->whereNotNull('current_revision_id')
            ->whereNotIn('id', DB::table('exam_paper_question')->select('question_id'))
            ->orderBy('id')
            ->get(['id', 'difficulty']);
    }

    /** Takes $size questions off the front of the pools following MIX, topping up from whichever bands still have questions. */
    private function pick(Collection $pools, int $size): Collection
    {
        $picked = collect();
        foreach (self::MIX as $band => $share) {
            $take = min((int) round($size * $share), $pools[$band]->count());
            $picked = $picked->concat($pools[$band]->splice(0, $take));
        }
        foreach (array_keys(self::MIX) as $band) {
            $missing = $size - $picked->count();
            if ($missing <= 0) {
                break;
            }
            $picked = $picked->concat($pools[$band]->splice(0, min($missing, $pools[$band]->count())));
        }

        return $picked;
    }
}
