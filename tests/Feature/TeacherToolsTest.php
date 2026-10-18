<?php

namespace Tests\Feature;

use App\Enums\TeacherVerificationStatus;
use App\Models\Classroom\Assignment;
use App\Models\Classroom\Classroom;
use App\Models\Classroom\Submission;
use App\Models\Classroom\TeacherMaterial;
use App\Models\Curriculum\Subject;
use App\Models\NotificationMessage;
use App\Models\QuestionBank\Question;
use App\Models\QuestionBank\QuestionKey;
use App\Models\User;
use Database\Seeders\CurriculumSubjectsSeeder;
use Database\Seeders\DemoQuestionSeeder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class TeacherToolsTest extends TestCase
{
    private User $teacher;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('library');
        $this->seed([CurriculumSubjectsSeeder::class, DemoQuestionSeeder::class]);
        $this->teacher = User::factory()->teacher(TeacherVerificationStatus::Approved)->create();
    }

    private function makeClass(): Classroom
    {
        $this->actingAs($this->teacher)->post(route('teach.classes.store'), [
            'name' => 'SHS 2 Science A',
            'subject_id' => Subject::where('slug', 'core-mathematics')->value('id'),
            'joining_open' => '1',
        ])->assertRedirect();

        return Classroom::firstOrFail();
    }

    private function join(User $student, Classroom $classroom): void
    {
        // Codes are forgiving about case and spaces.
        $code = strtolower(substr($classroom->join_code, 0, 3).' '.substr($classroom->join_code, 3));
        $this->actingAs($student)->post(route('learn.classes.join'), ['code' => $code])->assertRedirect(route('learn.classes.show', $classroom));
    }

    public function test_pending_teachers_cannot_use_classes_or_materials(): void
    {
        $pending = User::factory()->teacher()->create();

        $this->actingAs($pending)->get(route('teach.classes.index'))->assertRedirect(route('teach.dashboard'));
        $this->actingAs($pending)->get(route('teach.materials.index'))->assertRedirect(route('teach.dashboard'));
    }

    public function test_teacher_creates_a_class_and_students_join_with_the_code(): void
    {
        $classroom = $this->makeClass();
        $this->assertMatchesRegularExpression('/^[2-9A-HJKMNP-Z]{6}$/', $classroom->join_code);

        $student = User::factory()->student()->create();
        $this->actingAs($student)->post(route('learn.classes.join'), ['code' => 'NOPE00'])->assertSessionHasErrors('code');
        $this->join($student, $classroom);
        $this->assertTrue($classroom->hasStudent($student));

        $this->actingAs($this->teacher)->get(route('teach.classes.show', $classroom))->assertOk()->assertSee($student->name)->assertSee($classroom->join_code);

        // Another teacher cannot see it; closing joining stops new students.
        $other = User::factory()->teacher(TeacherVerificationStatus::Approved)->create();
        $this->actingAs($other)->get(route('teach.classes.show', $classroom))->assertNotFound();

        $classroom->update(['joining_open' => false]);
        $late = User::factory()->student()->create();
        $this->actingAs($late)->post(route('learn.classes.join'), ['code' => $classroom->join_code])->assertSessionHasErrors('code');
    }

    public function test_quiz_from_bank_and_own_question_is_marked_automatically(): void
    {
        $classroom = $this->makeClass();
        $student = User::factory()->student()->create();
        $this->join($student, $classroom);
        $maths = Subject::where('slug', 'core-mathematics')->first();

        $this->actingAs($this->teacher)->post(route('teach.assignments.store', $classroom), ['type' => 'quiz', 'title' => 'Quiz 1', 'time_limit_minutes' => 10, 'show_answers' => '1']);
        $quiz = Assignment::firstOrFail();

        // Publishing an empty quiz is refused.
        $this->post(route('teach.assignments.publish', $quiz))->assertSessionHas('error');

        $this->post(route('teach.assignments.questions.bank', $quiz), ['subject_id' => $maths->id, 'count' => 5])->assertSessionHas('success');
        $this->post(route('teach.assignments.questions.store', $quiz), [
            'subject_id' => $maths->id, 'stem' => 'What is 7 × 8?', 'options' => ['54', '56', '58', '64'], 'correct' => 'B', 'explanation' => '7 × 8 = 56.',
        ])->assertSessionHas('success');
        $this->assertSame(6, $quiz->questions()->count());

        // The teacher's own question never reaches public practice.
        $own = Question::where('owner_scope', 'teacher')->firstOrFail();
        $this->assertSame('exam', $own->confidentiality);

        $this->post(route('teach.assignments.publish', $quiz))->assertSessionHas('success');
        $this->assertTrue(NotificationMessage::where('user_id', $student->id)->where('event_key', 'assignment.published')->exists());

        // Student takes it: no answers on the paper.
        $this->actingAs($student)->get(route('learn.classes.index'))->assertSee('Quiz 1')->assertSee('To do');
        $paper = $this->get(route('learn.assignments.take', $quiz))->assertOk()->assertSee('What is 7 × 8?')->assertSee('Time left');
        $paper->assertDontSee('7 × 8 = 56.');

        $answers = [];
        foreach ($quiz->questions()->get() as $i => $q) {
            $correct = QuestionKey::find($q->pivot->question_revision_id)->key['correct'];
            $answers[$q->ulid] = $i === 0 ? ($correct === 'A' ? 'B' : 'A') : $correct;
        }
        $this->post(route('learn.assignments.submit-quiz', $quiz), ['answers' => $answers])->assertRedirect(route('learn.assignments.show', $quiz));

        $submission = Submission::firstOrFail();
        $this->assertSame(5.0, (float) $submission->score);
        $this->assertSame(6.0, (float) $submission->max_score);
        $this->get(route('learn.assignments.show', $quiz))->assertSee('5 / 6')->assertSee('7 × 8 = 56.');

        // One attempt only, and questions are locked once someone has submitted.
        $this->post(route('learn.assignments.submit-quiz', $quiz), ['answers' => []]);
        $this->assertSame(5.0, (float) $submission->fresh()->score);
        $this->actingAs($this->teacher)->delete(route('teach.assignments.questions.destroy', [$quiz, $own]))->assertSessionHasErrors('stem');
        $this->get(route('teach.assignments.show', $quiz))->assertOk()->assertSee('5/6');
    }

    public function test_timed_quiz_is_closed_when_time_runs_out(): void
    {
        $classroom = $this->makeClass();
        $student = User::factory()->student()->create();
        $this->join($student, $classroom);
        $this->actingAs($this->teacher)->post(route('teach.assignments.store', $classroom), ['type' => 'quiz', 'title' => 'Speed test', 'time_limit_minutes' => 5]);
        $quiz = Assignment::firstOrFail();
        $this->post(route('teach.assignments.questions.bank', $quiz), ['subject_id' => Subject::where('slug', 'core-mathematics')->value('id'), 'count' => 3]);
        $this->post(route('teach.assignments.publish', $quiz));

        $this->actingAs($student)->get(route('learn.assignments.take', $quiz))->assertOk();
        $this->travel(6)->minutes();
        $this->get(route('learn.assignments.take', $quiz))->assertRedirect(route('learn.assignments.show', $quiz));
        $this->assertNotNull(Submission::firstOrFail()->submitted_at);
    }

    public function test_written_assignment_hand_in_and_marking(): void
    {
        $classroom = $this->makeClass();
        $student = User::factory()->student()->create();
        $outsider = User::factory()->student()->create();
        $this->join($student, $classroom);

        $this->actingAs($this->teacher)->post(route('teach.assignments.store', $classroom), [
            'type' => 'assignment', 'title' => 'Essay on photosynthesis', 'max_marks' => 20, 'due_at' => now()->addDay()->format('Y-m-d H:i'),
            'handout' => UploadedFile::fake()->image('diagram.png', 400, 300),
        ]);
        $work = Assignment::firstOrFail();
        $this->assertNotNull($work->file_path);
        $this->post(route('teach.assignments.publish', $work));

        $this->actingAs($outsider)->get(route('learn.assignments.show', $work))->assertNotFound();

        $this->actingAs($student)->get(route('learn.assignments.handout', $work))->assertOk();
        $this->post(route('learn.assignments.submit-work', $work), [])->assertSessionHasErrors('body');
        $this->post(route('learn.assignments.submit-work', $work), ['body' => 'Plants make food using sunlight.', 'file' => UploadedFile::fake()->image('page1.jpg', 800, 600)])->assertSessionHas('success');

        $submission = Submission::firstOrFail();
        $this->assertNotNull($submission->file_path);

        $this->actingAs($this->teacher)->get(route('teach.assignments.submission-file', [$work, $submission]))->assertOk();
        $this->put(route('teach.assignments.grade', [$work, $submission]), ['score' => 25])->assertSessionHasErrors('score');
        $this->put(route('teach.assignments.grade', [$work, $submission]), ['score' => 16, 'feedback' => 'Good, add the equation.'])->assertSessionHas('success');

        $this->actingAs($student)->get(route('learn.assignments.show', $work))->assertSee('16 / 20')->assertSee('Good, add the equation.');
        $this->post(route('learn.assignments.submit-work', $work), ['body' => 'changed'])->assertSessionHasErrors('body');
    }

    public function test_teachers_share_lesson_materials(): void
    {
        $this->actingAs($this->teacher)->post(route('teach.materials.store'), [
            'title' => 'Week 3 lesson plan: Motion', 'kind' => 'lesson_plan', 'visibility' => 'teachers',
            'subject_id' => Subject::where('slug', 'physics')->value('id'),
            'file' => UploadedFile::fake()->image('plan.png', 600, 800),
        ])->assertRedirect();
        $this->post(route('teach.materials.store'), [
            'title' => 'My private notes', 'kind' => 'lesson_note', 'visibility' => 'private',
            'file' => UploadedFile::fake()->image('notes.png', 600, 800),
        ]);

        $shared = TeacherMaterial::where('visibility', 'teachers')->firstOrFail();
        $private = TeacherMaterial::where('visibility', 'private')->firstOrFail();

        $colleague = User::factory()->teacher(TeacherVerificationStatus::Approved)->create();
        $this->actingAs($colleague)->get(route('teach.materials.index'))->assertOk()->assertSee('Week 3 lesson plan: Motion')->assertDontSee('My private notes');
        $this->get(route('teach.materials.download', $shared))->assertOk();
        $this->assertSame(1, $shared->fresh()->download_count);
        $this->get(route('teach.materials.download', $private))->assertNotFound();
        $this->get(route('teach.materials.edit', $shared))->assertNotFound();

        $student = User::factory()->student()->create();
        $this->actingAs($student)->get(route('teach.materials.index'))->assertRedirect();
    }

    public function test_referral_link_credits_the_inviting_teacher(): void
    {
        $code = $this->teacher->referralCode();
        $this->actingAs($this->teacher)->get(route('teach.dashboard'))->assertOk()->assertSee(route('referral', $code));
        auth()->logout();

        $this->get(route('referral', $code))->assertRedirect(route('teachers.register'));
        $this->post(route('teachers.register.store'), ['phone' => '0207778888', 'password' => 'teach1234', 'password_confirmation' => 'teach1234']);

        $newTeacher = User::where('phone', '0207778888')->firstOrFail();
        $this->assertSame($this->teacher->id, $newTeacher->referred_by);

        $this->actingAs($this->teacher)->get(route('teach.dashboard'))->assertSee('joined');
        $this->assertSame(1, $this->teacher->referrals()->count());

        // Unknown codes do nothing.
        auth()->logout();
        $this->get('/r/unknown1')->assertRedirect(route('register'));
    }

    public function test_promotion_practice_is_for_approved_teachers(): void
    {
        $this->get(route('practice.start', ['subject' => 'ges-promotion-general']))->assertRedirect(route('teachers.register'));
        $this->actingAs(User::factory()->teacher()->create())->get(route('practice.start', ['subject' => 'ges-promotion-general']))->assertRedirect(route('promotion'));

        $this->actingAs($this->teacher)->get(route('promotion'))->assertSee('Timed mock (20 questions)');
        $this->get(route('practice.start', ['subject' => 'ges-promotion-general', 'count' => 10]))->assertOk()->assertSee('GES promotion style');
        $this->assertCount(10, session('practice.set')['ids']);

        // Promotion subjects never show in public lists.
        $this->get(route('practice.index'))->assertDontSee('GES Promotion (General)');
        $this->get(route('subjects.index'))->assertDontSee('GES Promotion (General)');
    }
}
