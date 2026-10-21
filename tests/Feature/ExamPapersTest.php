<?php

namespace Tests\Feature;

use App\Models\Curriculum\Subject;
use App\Models\QuestionBank\ExamPaper;
use App\Models\QuestionBank\Question;
use App\Models\QuestionBank\QuestionKey;
use App\Models\QuestionBank\QuestionRevision;
use App\Models\User;
use App\Services\Practice\PaperBuilder;
use Database\Seeders\CurriculumSubjectsSeeder;
use Tests\TestCase;

class ExamPapersTest extends TestCase
{
    private Subject $maths;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(CurriculumSubjectsSeeder::class);
        $this->maths = Subject::where('slug', 'core-mathematics')->firstOrFail();
    }

    /** $count free practice MCQs; the right answer is always B. */
    private function questions(Subject $subject, int $count, int $difficulty = 3, array $overrides = []): void
    {
        for ($i = 1; $i <= $count; $i++) {
            $question = new Question;
            $question->forceFill($overrides + [
                'type' => 'mcq', 'education_level_id' => $subject->education_level_id, 'subject_id' => $subject->id,
                'difficulty' => $difficulty, 'status' => 'published', 'access_level' => 'free', 'confidentiality' => 'practice',
            ])->save();
            $revision = new QuestionRevision;
            $revision->forceFill([
                'question_id' => $question->id, 'revision_no' => 1, 'stem' => "{$subject->name} question {$question->id}",
                'payload' => ['options' => [['id' => 'A', 'text' => 'one'], ['id' => 'B', 'text' => 'two'], ['id' => 'C', 'text' => 'three'], ['id' => 'D', 'text' => 'four']]],
                'explanation' => "Because two is right for {$question->id}.",
            ])->save();
            (new QuestionKey)->forceFill(['question_revision_id' => $revision->id, 'key' => ['correct' => 'B']])->save();
            $question->forceFill(['current_revision_id' => $revision->id])->save();
        }
    }

    public function test_builder_makes_non_overlapping_wassce_papers_with_a_difficulty_mix(): void
    {
        $this->questions($this->maths, 40, 1);
        $this->questions($this->maths, 50, 3);
        $this->questions($this->maths, 40, 5);
        // Not eligible: premium, draft, or exam-only questions.
        $this->questions($this->maths, 5, 3, ['access_level' => 'premium']);
        $this->questions($this->maths, 5, 3, ['status' => 'draft']);
        $this->questions($this->maths, 5, 3, ['confidentiality' => 'exam']);

        $this->artisan('papers:build', ['subject' => ['core-mathematics']])->assertSuccessful();

        $papers = ExamPaper::where('subject_id', $this->maths->id)->orderBy('number')->get();
        $this->assertSame([1, 2], $papers->pluck('number')->all()); // 130 eligible → two papers of 50
        $this->assertSame('Core Mathematics Paper 1', $papers[0]->title);
        $this->assertSame('WASSCE', $papers[0]->exam_style);
        $this->assertSame(60, $papers[0]->duration_minutes);

        $first = $papers[0]->questions()->get();
        $this->assertCount(50, $first);
        $this->assertSame([15, 20, 15], [$first->where('difficulty', 1)->count(), $first->where('difficulty', 3)->count(), $first->where('difficulty', 5)->count()]);
        $this->assertSame(range(1, 50), $first->pluck('pivot.position')->all());
        $this->assertTrue($first->every(fn ($q) => $q->access_level === 'free' && $q->status === 'published' && $q->confidentiality === 'practice'));

        $used = $papers->load('questions')->flatMap->questions->pluck('id');
        $this->assertSame($used->count(), $used->unique()->count());

        // Running again adds nothing until there are enough new questions.
        app(PaperBuilder::class)->build($this->maths);
        $this->assertSame(2, ExamPaper::count());
        $this->questions($this->maths, 30, 3);
        $this->assertSame(1, app(PaperBuilder::class)->build($this->maths));
        $this->assertSame(3, ExamPaper::max('number'));
    }

    public function test_jhs_papers_are_bece_style(): void
    {
        $science = Subject::where('slug', 'jhs-science')->firstOrFail();
        $this->questions($science, 40);
        app(PaperBuilder::class)->build($science);

        $paper = ExamPaper::where('subject_id', $science->id)->firstOrFail();
        $this->assertSame('BECE', $paper->exam_style);
        $this->assertCount(40, $paper->questions()->get());
    }

    public function test_paper_pages_and_study_mode_check(): void
    {
        $this->questions($this->maths, 50);
        app(PaperBuilder::class)->build($this->maths);
        $paper = ExamPaper::firstOrFail();
        $question = $paper->questions()->with('currentRevision')->first();

        $this->get(route('papers.index'))->assertOk()->assertSee('Core Mathematics')->assertSee('1 paper');
        $this->get(route('papers.subject', $this->maths))->assertOk()->assertSee('Core Mathematics Paper 1')->assertSee('50 questions');
        $this->get(route('papers.show', [$this->maths, 1]))->assertOk()->assertSee('Study mode')->assertSee('Exam mode');
        $this->get(route('papers.show', [$this->maths, 9]))->assertNotFound();

        // Answer keys are never in the page.
        $this->get(route('papers.take', [$this->maths, 1, 'mode' => 'study']))->assertOk()
            ->assertSee($question->currentRevision->stem)->assertDontSee('Because two is right');

        $this->postJson(route('papers.check', [$this->maths, 1]), ['question' => $question->ulid, 'answer' => 'A'])
            ->assertOk()->assertJson(['right' => false, 'correct' => 'B', 'explanation' => "Because two is right for {$question->id}."]);
        $this->postJson(route('papers.check', [$this->maths, 1]), ['question' => $question->ulid, 'answer' => 'B'])->assertJson(['right' => true]);

        // A question from outside the paper cannot be checked through it.
        $this->questions($this->maths, 1);
        $outside = Question::latest('id')->first();
        $this->postJson(route('papers.check', [$this->maths, 1]), ['question' => $outside->ulid, 'answer' => 'B'])->assertNotFound();
    }

    public function test_exam_mode_is_marked_on_the_server(): void
    {
        $this->questions($this->maths, 50);
        app(PaperBuilder::class)->build($this->maths);
        $questions = ExamPaper::firstOrFail()->questions()->get();

        // Submitting without starting is refused.
        $this->post(route('papers.mark', [$this->maths, 1]), ['answers' => []])->assertStatus(419);

        $this->get(route('papers.take', [$this->maths, 1, 'mode' => 'exam']))->assertOk()->assertSee('Submit paper')->assertSee('60:00');
        $answers = $questions->take(30)->mapWithKeys(fn ($q, $i) => [$q->ulid => $i < 20 ? 'B' : 'C'])->all();

        $this->post(route('papers.mark', [$this->maths, 1]), ['answers' => $answers])->assertOk()->assertSee('20 / 50')->assertSee('40%');
        // The attempt is used up.
        $this->post(route('papers.mark', [$this->maths, 1]), ['answers' => $answers])->assertStatus(419);
    }

    public function test_solutions_need_an_account(): void
    {
        $this->questions($this->maths, 50);
        app(PaperBuilder::class)->build($this->maths);
        $question = ExamPaper::firstOrFail()->questions()->with('currentRevision')->first();

        $this->get(route('papers.solutions', [$this->maths, 1]))->assertRedirect(route('login'));
        $this->actingAs(User::factory()->student()->create())->get(route('papers.solutions', [$this->maths, 1]))
            ->assertOk()->assertSee($question->currentRevision->stem)->assertSee("Because two is right for {$question->id}.");
    }

    public function test_past_questions_page_lists_bece_and_wassce_subjects_and_every_year(): void
    {
        $this->questions($this->maths, 50);
        app(PaperBuilder::class)->build($this->maths);

        $this->get(route('library.index'))->assertOk()
            ->assertSee('Past Questions')->assertSee('BECE · Junior High School')->assertSee('WASSCE · Senior High School')
            ->assertSee('Core Mathematics')->assertSee('1 practice paper')->assertSee('Past papers coming');
        $this->get(route('library.index', ['level' => 'jhs']))->assertOk()->assertDontSee('WASSCE · Senior High School');

        $this->get(route('library.index', ['subject' => 'core-mathematics']))->assertOk()
            ->assertSee('WASSCE past papers by year')->assertSee((string) now()->year)->assertSee('1993')->assertDontSee('WASSCE 1992')
            ->assertSee('WASSCE-style practice papers')->assertSee('Core Mathematics Paper 1')->assertSee('not WAEC papers')
            ->assertSee(route('papers.take', [$this->maths, 1, 'mode' => 'exam']), false);

        $this->get(route('library.index', ['subject' => 'jhs-science']))->assertOk()->assertSee('BECE past papers by year')->assertSee('1990');
    }

    public function test_inactive_subjects_and_draft_papers_are_hidden(): void
    {
        $this->questions($this->maths, 50);
        app(PaperBuilder::class)->build($this->maths);

        ExamPaper::query()->update(['status' => 'draft']);
        $this->get(route('papers.show', [$this->maths, 1]))->assertNotFound();
        $this->get(route('papers.index'))->assertDontSee('Core Mathematics');

        ExamPaper::query()->update(['status' => 'published']);
        $this->maths->update(['is_active' => false]);
        $this->get(route('papers.subject', $this->maths))->assertNotFound();
    }
}
