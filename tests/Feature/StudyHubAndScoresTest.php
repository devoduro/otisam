<?php

namespace Tests\Feature;

use App\Enums\TeacherVerificationStatus;
use App\Models\Curriculum\ClassLevel;
use App\Models\Curriculum\CurriculumNode;
use App\Models\Curriculum\CurriculumVersion;
use App\Models\Curriculum\Subject;
use App\Models\QuestionBank\ExamPaper;
use App\Models\QuestionBank\Question;
use App\Models\QuestionBank\QuestionKey;
use App\Models\QuestionBank\QuestionRevision;
use App\Models\QuestionBank\QuizAttempt;
use App\Models\User;
use App\Services\Practice\Leaderboard;
use App\Services\Practice\PaperBuilder;
use Database\Seeders\CurriculumSubjectsSeeder;
use Tests\TestCase;

class StudyHubAndScoresTest extends TestCase
{
    private Subject $maths;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(CurriculumSubjectsSeeder::class);
        $this->maths = Subject::where('slug', 'core-mathematics')->firstOrFail();
    }

    /** $count free practice MCQs whose right answer is B. */
    private function questions(int $count): void
    {
        for ($i = 0; $i < $count; $i++) {
            $question = new Question;
            $question->forceFill(['type' => 'mcq', 'education_level_id' => $this->maths->education_level_id, 'subject_id' => $this->maths->id, 'difficulty' => 3, 'status' => 'published', 'access_level' => 'free', 'confidentiality' => 'practice'])->save();
            $revision = new QuestionRevision;
            $revision->forceFill(['question_id' => $question->id, 'revision_no' => 1, 'stem' => "Question {$question->id}", 'payload' => ['options' => [['id' => 'A', 'text' => 'a'], ['id' => 'B', 'text' => 'b']]], 'explanation' => 'B.'])->save();
            (new QuestionKey)->forceFill(['question_revision_id' => $revision->id, 'key' => ['correct' => 'B']])->save();
            $question->forceFill(['current_revision_id' => $revision->id])->save();
        }
    }

    /** Sits paper 1 in exam mode with $right correct answers. */
    private function sit(User $user, int $right): void
    {
        $questions = ExamPaper::where('number', 1)->firstOrFail()->questions()->get();
        $this->actingAs($user)->get(route('papers.take', [$this->maths, 1, 'mode' => 'exam']))->assertOk();
        $answers = $questions->values()->mapWithKeys(fn ($q, $i) => [$q->ulid => $i < $right ? 'B' : 'A'])->all();
        $this->actingAs($user)->post(route('papers.mark', [$this->maths, 1]), ['answers' => $answers])->assertOk();
    }

    public function test_study_pages_lead_from_level_to_the_subject_hub(): void
    {
        $shs1 = ClassLevel::where('name', 'SHS 1')->firstOrFail();

        $this->get(route('study.index'))->assertOk()->assertSee('Senior High School')->assertSee('SHS 1');
        $this->get(route('study.level', ['shs', 'class' => $shs1->id]))->assertOk()->assertSee('Core Mathematics')->assertDontSee('Robotics');
        $this->get(route('study.level', ['shs', 'category' => 'elective']))->assertOk()->assertSee('Robotics');
        $this->get('/study/pro')->assertNotFound();

        $this->questions(50);
        app(PaperBuilder::class)->build($this->maths);
        $this->get(route('study.subject', ['shs', $this->maths, 'class' => $shs1->id]))->assertOk()
            ->assertSee('Practice papers')->assertSee('1 paper')->assertSee('50 questions')
            ->assertSee(route('papers.subject', $this->maths))
            ->assertSee(route('lesson-plans.index', ['subject' => 'core-mathematics', 'year' => $shs1->id]))
            ->assertSee(route('leaderboard', ['subject' => 'core-mathematics']));

        // The level in the URL must be the subject's own.
        $this->get(route('study.subject', ['jhs', $this->maths]))->assertNotFound();
    }

    public function test_student_scores_are_saved_and_best_paper_score_counts_once(): void
    {
        $this->questions(50);
        app(PaperBuilder::class)->build($this->maths);
        $ama = User::factory()->student()->create(['name' => 'Ama Serwaa Owusu']);
        $kofi = User::factory()->student()->create(['name' => 'Kofi Boateng']);

        $this->sit($ama, 30);
        $this->sit($ama, 40); // best of the two counts
        $this->sit($kofi, 35);

        $this->assertSame(3, QuizAttempt::where('kind', 'paper')->count());
        $this->actingAs($ama)->get(route('papers.subject', $this->maths))->assertSee('Best: 40/50');

        $top = app(Leaderboard::class)->top();
        $this->assertSame(['Ama O.', 'Kofi B.'], $top->pluck('name')->all());
        $this->assertSame([40, 35], $top->pluck('points')->all());

        $this->get(route('leaderboard', ['subject' => 'core-mathematics']))->assertOk()
            ->assertSee('Ama O.')->assertSee('Kofi B.')->assertDontSee('Owusu');
        $this->actingAs($ama)->get(route('learn.progress'))->assertOk()->assertSee('Core Mathematics Paper 1')->assertSee('40/50')->assertSee('#1');
    }

    public function test_only_students_are_recorded_and_ranked(): void
    {
        $this->questions(50);
        app(PaperBuilder::class)->build($this->maths);

        $this->sit(User::factory()->teacher(TeacherVerificationStatus::Approved)->create(), 50);
        auth()->logout();
        // Guests can sit a paper but nothing is saved.
        $this->get(route('papers.take', [$this->maths, 1, 'mode' => 'exam']));
        $this->post(route('papers.mark', [$this->maths, 1]), ['answers' => []])->assertOk()->assertSee('Create a free student account');

        $this->assertSame(0, QuizAttempt::count());
        $this->get(route('leaderboard'))->assertOk()->assertSee('No scores yet');
    }

    public function test_practice_sets_add_correct_answers_to_points(): void
    {
        $this->questions(10);
        $student = User::factory()->student()->create(['name' => 'Esi Mensah']);

        $this->actingAs($student)->get(route('practice.start', ['subject' => 'core-mathematics', 'count' => 10]))->assertOk();
        $ids = session('practice.set.ids');
        $answers = Question::whereIn('id', $ids)->get()->mapWithKeys(fn ($q) => [$q->ulid => 'B'])->all();
        $this->actingAs($student)->post(route('practice.mark'), ['answers' => $answers])->assertOk();

        $attempt = QuizAttempt::firstOrFail();
        $this->assertSame(['practice', 10, 10], [$attempt->kind, $attempt->score, $attempt->total]);
        $this->assertSame(10, app(Leaderboard::class)->top()->first()->points);
    }

    public function test_quick_practice_by_topic_checks_answers_one_at_a_time(): void
    {
        $this->questions(12);
        $version = CurriculumVersion::create(['education_level_id' => $this->maths->education_level_id, 'name' => 'Topics', 'status' => 'active']);
        $topic = new CurriculumNode(['node_type' => 'topic', 'title' => 'Fractions']);
        $topic->forceFill(['curriculum_version_id' => $version->id, 'subject_id' => $this->maths->id, 'path' => '/'])->save();
        $tagged = Question::orderBy('id')->limit(4)->get();
        $topic->questions()->attach($tagged->pluck('id'));

        $this->get(route('study.subject', ['shs', $this->maths]))->assertOk()
            ->assertSee('Quick practice')->assertSee('Practice by topic')->assertSee('Fractions')->assertSee('4 questions');

        $this->get(route('study.quick', ['shs', $this->maths, 'topic' => $topic->id]))->assertOk()
            ->assertSee('Fractions')->assertSee('Question 1 of 4')->assertDontSee('Question 5 of')
            ->assertSee(route('study.check', ['shs', $this->maths]));
        $this->get(route('study.quick', ['shs', $this->maths]))->assertOk()->assertSee('Question 1 of 10');

        $this->postJson(route('study.check', ['shs', $this->maths]), ['question' => $tagged->first()->ulid, 'answer' => 'B'])
            ->assertOk()->assertJson(['right' => true, 'correct' => 'B']);

        // Questions of another subject, or not free practice ones, cannot be checked here.
        $tagged->first()->forceFill(['access_level' => 'premium'])->save();
        $this->postJson(route('study.check', ['shs', $this->maths]), ['question' => $tagged->first()->ulid, 'answer' => 'B'])->assertNotFound();
    }

    public function test_short_names_hide_surnames(): void
    {
        $this->assertSame('Ama O.', Leaderboard::shortName('Ama Serwaa Owusu'));
        $this->assertSame('Kwame', Leaderboard::shortName('Kwame'));
        $this->assertSame('Yaa A.', Leaderboard::shortName('  Yaa   asantewaa '));
    }
}
