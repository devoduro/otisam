<?php

namespace Tests\Feature;

use App\Enums\TeacherVerificationStatus;
use App\Models\Classroom\Assignment;
use App\Models\Classroom\Classroom;
use App\Models\Classroom\Submission;
use App\Models\Curriculum\ClassLevel;
use App\Models\Curriculum\Subject;
use App\Models\QuestionBank\Question;
use App\Models\User;
use Database\Seeders\CurriculumSubjectsSeeder;
use Illuminate\Testing\TestResponse;
use Tests\Concerns\CreatesTopicQuestions;
use Tests\TestCase;

/** Teachers browse the question bank and tick the questions they want in a quiz. */
class QuizBankPickerTest extends TestCase
{
    use CreatesTopicQuestions;

    private User $teacher;

    private Subject $maths;

    private Assignment $quiz;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(CurriculumSubjectsSeeder::class);
        $this->teacher = User::factory()->teacher(TeacherVerificationStatus::Approved)->create();
        $this->maths = Subject::where('slug', 'jhs-mathematics')->firstOrFail();

        $this->actingAs($this->teacher)->post(route('teach.classes.store'), ['name' => 'JHS 1A', 'subject_id' => $this->maths->id, 'joining_open' => '1']);
        $this->post(route('teach.assignments.store', Classroom::firstOrFail()), ['type' => 'quiz', 'title' => 'Fractions test']);
        $this->quiz = Assignment::firstOrFail();
    }

    private function idOf(string $stem): int
    {
        return Question::whereHas('currentRevision', fn ($r) => $r->where('stem', $stem))->value('id');
    }

    public function test_the_quiz_page_offers_choosing_random_and_writing_questions(): void
    {
        $this->get(route('teach.assignments.show', $this->quiz))->assertOk()
            ->assertSee('Choose from the question bank')->assertSee(route('teach.assignments.bank', $this->quiz))
            ->assertSee('Add random questions from the bank')->assertSee('Write your own question');
    }

    public function test_browse_page_lists_the_class_subject_with_answers_and_filters(): void
    {
        $fractions = $this->topicWithQuestions($this->maths, 'Fractions', 3);
        $this->topicWithQuestions($this->maths, 'Angles', 2);
        $this->topicWithQuestions(Subject::where('slug', 'jhs-science')->firstOrFail(), 'Cells', 2);

        // Defaults to the class's subject; shows every usable question with its answer.
        $page = $this->get(route('teach.assignments.bank', $this->quiz))->assertOk();
        $page->assertSee('5 question(s) in Mathematics')->assertSee('Fractions question 1')->assertSee('Angles question 2')
            ->assertDontSee('Cells question 1')->assertSee('right one')->assertSee('aria-label="correct"', false);

        $this->get(route('teach.assignments.bank', [$this->quiz, 'topic_id' => $fractions->id]))
            ->assertSee('3 question(s)')->assertSee('Fractions question 2')->assertDontSee('Angles question 1');
        $this->get(route('teach.assignments.bank', [$this->quiz, 'q' => 'angles question 2']))
            ->assertSee('1 question(s)')->assertSee('Angles question 2')->assertDontSee('Angles question 1');
        $this->get(route('teach.assignments.bank', [$this->quiz, 'subject_id' => Subject::where('slug', 'jhs-science')->value('id')]))
            ->assertSee('Cells question 1')->assertDontSee('Fractions question 1');
        $this->get(route('teach.assignments.bank', [$this->quiz, 'difficulty' => 'hard']))->assertSee('0 question(s)')->assertSee('No questions match');
    }

    public function test_teacher_ticks_questions_and_they_are_added_in_order(): void
    {
        $this->topicWithQuestions($this->maths, 'Fractions', 4);
        $pick = [$this->idOf('Fractions question 3'), $this->idOf('Fractions question 1')];

        $this->from(route('teach.assignments.bank', $this->quiz))
            ->post(route('teach.assignments.questions.selected', $this->quiz), ['question_ids' => $pick])
            ->assertRedirect(route('teach.assignments.bank', $this->quiz))->assertSessionHas('success', '2 question(s) added to the quiz.');

        $this->assertSame($pick, $this->quiz->questions()->pluck('questions.id')->all());

        // Added questions show as "In quiz" and cannot be ticked again; ticking one again adds nothing.
        $page = $this->get(route('teach.assignments.bank', $this->quiz))->assertSee('In quiz')->assertSee('of 50 questions');
        $this->assertSame(2, substr_count($page->getContent(), 'type="checkbox" name="question_ids[]"'));
        $this->post(route('teach.assignments.questions.selected', $this->quiz), ['question_ids' => [$pick[0]]])->assertSessionHas('error');
        $this->assertSame(2, $this->quiz->questions()->count());

        // Picked questions sit alongside written ones on the quiz page.
        $this->get(route('teach.assignments.show', $this->quiz))->assertSeeInOrder(['Fractions question 3', 'Fractions question 1']);
    }

    public function test_nothing_ticked_is_an_error(): void
    {
        $this->post(route('teach.assignments.questions.selected', $this->quiz), [])->assertSessionHasErrors(['question_ids' => 'Tick at least one question to add.']);
    }

    public function test_questions_that_cannot_go_in_a_quiz_are_ignored(): void
    {
        $fractions = $this->topicWithQuestions($this->maths, 'Fractions', 1);
        $draft = $this->bankQuestion($this->maths, 'Draft question', ['status' => 'draft']);
        $exam = $this->bankQuestion($this->maths, 'Another teacher private question', ['confidentiality' => 'exam']);

        $this->post(route('teach.assignments.questions.selected', $this->quiz), ['question_ids' => [$draft->id, $exam->id, $this->idOf('Fractions question 1'), 999999]]);

        $this->assertSame([$this->idOf('Fractions question 1')], $this->quiz->questions()->pluck('questions.id')->all());
        $this->get(route('teach.assignments.bank', $this->quiz))->assertDontSee('Draft question')->assertDontSee('Another teacher private question');
        $this->assertNotNull($fractions);
    }

    public function test_the_fifty_question_limit_is_kept(): void
    {
        $this->topicWithQuestions($this->maths, 'Fractions', 52);
        $ids = Question::where('subject_id', $this->maths->id)->orderBy('id')->pluck('id');

        $this->post(route('teach.assignments.questions.selected', $this->quiz), ['question_ids' => $ids->take(48)->all()]);
        $this->post(route('teach.assignments.questions.selected', $this->quiz), ['question_ids' => $ids->slice(48)->values()->all()])
            ->assertSessionHasErrors(['question_ids' => 'A quiz can have at most 50 questions. It has 48 now, so you can add 2 more.']);
        $this->assertSame(48, $this->quiz->questions()->count());
    }

    public function test_no_questions_can_be_added_once_students_have_taken_the_quiz(): void
    {
        $this->topicWithQuestions($this->maths, 'Fractions', 3);
        $this->post(route('teach.assignments.questions.selected', $this->quiz), ['question_ids' => [$this->idOf('Fractions question 1')]]);
        $this->post(route('teach.assignments.publish', $this->quiz));
        $student = User::factory()->student()->create();
        Classroom::firstOrFail()->students()->attach($student->id, ['joined_at' => now()]);
        $this->actingAs($student)->get(route('learn.assignments.take', $this->quiz));
        $this->post(route('learn.assignments.submit-quiz', $this->quiz), ['answers' => []]);

        $this->actingAs($this->teacher)->get(route('teach.assignments.bank', $this->quiz))->assertSee('no more questions can be added');
        $this->post(route('teach.assignments.questions.selected', $this->quiz), ['question_ids' => [$this->idOf('Fractions question 2')]])->assertSessionHasErrors('stem');
        $this->assertSame(1, $this->quiz->questions()->count());
    }

    public function test_only_the_quiz_teacher_can_browse_or_add(): void
    {
        $this->topicWithQuestions($this->maths, 'Fractions', 1);
        $other = User::factory()->teacher(TeacherVerificationStatus::Approved)->create();

        $this->actingAs($other)->get(route('teach.assignments.bank', $this->quiz))->assertNotFound();
        $this->post(route('teach.assignments.questions.selected', $this->quiz), ['question_ids' => [$this->idOf('Fractions question 1')]])->assertNotFound();
        $this->assertSame(0, $this->quiz->questions()->count());

        $student = User::factory()->student()->create();
        $this->actingAs($student)->get(route('teach.assignments.bank', $this->quiz))->assertRedirect();
    }

    public function test_written_assignments_have_no_question_bank_page(): void
    {
        $this->post(route('teach.assignments.store', Classroom::firstOrFail()), ['type' => 'assignment', 'title' => 'Essay']);
        $essay = Assignment::where('type', 'assignment')->firstOrFail();

        $this->get(route('teach.assignments.bank', $essay))->assertNotFound();
    }

    // Level and class year

    public function test_browse_opens_on_the_class_level_year_and_subject(): void
    {
        $jhs2 = ClassLevel::where('name', 'JHS 2')->firstOrFail();
        Classroom::firstOrFail()->update(['class_level_id' => $jhs2->id]);

        $page = $this->get(route('teach.assignments.bank', $this->quiz))->assertOk();
        $page->assertSee('<option value="jhs" selected', false)
            ->assertSee('<option value="'.$jhs2->id.'" selected>JHS 2</option>', false)
            ->assertSee('<option value="'.$this->maths->id.'" selected>Mathematics</option>', false)
            ->assertDontSee('Core Mathematics');
    }

    public function test_year_filter_shows_that_year_and_questions_for_any_year(): void
    {
        $jhs1 = ClassLevel::where('name', 'JHS 1')->firstOrFail();
        $jhs2 = ClassLevel::where('name', 'JHS 2')->firstOrFail();
        $this->bankQuestion($this->maths, 'JHS one question', ['class_level_id' => $jhs1->id]);
        $this->bankQuestion($this->maths, 'JHS two question', ['class_level_id' => $jhs2->id]);
        $this->bankQuestion($this->maths, 'Any year question');

        $this->get(route('teach.assignments.bank', [$this->quiz, 'level' => 'jhs', 'year' => $jhs2->id, 'subject_id' => $this->maths->id]))
            ->assertSee('2 question(s) in Mathematics · JHS 2')->assertSee('JHS two question')->assertSee('Any year question')->assertDontSee('JHS one question');
        $this->get(route('teach.assignments.bank', [$this->quiz, 'level' => 'jhs', 'year' => '', 'subject_id' => $this->maths->id]))
            ->assertSee('3 question(s) in Mathematics.');
    }

    public function test_topics_follow_the_chosen_year(): void
    {
        $jhs1 = ClassLevel::where('name', 'JHS 1')->firstOrFail();
        $jhs3 = ClassLevel::where('name', 'JHS 3')->firstOrFail();
        $fractions = $this->topicWithQuestions($this->maths, 'Fractions', 2);
        $fractions->update(['class_level_id' => $jhs1->id]);
        $this->topicWithQuestions($this->maths, 'Probability', 2);

        $this->get(route('teach.assignments.bank', [$this->quiz, 'level' => 'jhs', 'year' => $jhs3->id, 'subject_id' => $this->maths->id]))
            ->assertSee('>Probability · 2</option>', false)->assertDontSee('>Fractions (JHS 1)', false);
        $this->get(route('teach.assignments.bank', [$this->quiz, 'level' => 'jhs', 'year' => $jhs1->id, 'subject_id' => $this->maths->id]))
            ->assertSee('>Fractions (JHS 1) · 2</option>', false);
    }

    public function test_choosing_another_level_lists_its_subjects_and_years(): void
    {
        $this->bankQuestion(Subject::where('slug', 'core-mathematics')->firstOrFail(), 'SHS question');

        // A subject from another level is ignored until one from the chosen level is picked.
        $page = $this->get(route('teach.assignments.bank', [$this->quiz, 'level' => 'shs', 'subject_id' => $this->maths->id]))->assertOk();
        $page->assertSee('Core Mathematics')->assertSee('>SHS 2</option>', false)->assertDontSee('>JHS 2</option>', false)
            ->assertSee('Choose a subject to see its questions.');

        $this->get(route('teach.assignments.bank', [$this->quiz, 'level' => 'shs', 'subject_id' => Subject::where('slug', 'core-mathematics')->value('id')]))
            ->assertSee('SHS question');
    }

    // Editing questions

    public function test_quiz_page_has_an_edit_link_for_each_question(): void
    {
        $this->topicWithQuestions($this->maths, 'Fractions', 1);
        $id = $this->idOf('Fractions question 1');
        $this->post(route('teach.assignments.questions.selected', $this->quiz), ['question_ids' => [$id]]);

        $this->get(route('teach.assignments.show', $this->quiz))->assertSee(route('teach.assignments.questions.edit', [$this->quiz, Question::find($id)]))->assertSee('1 mark');
        $this->get(route('teach.assignments.questions.edit', [$this->quiz, Question::find($id)]))->assertOk()
            ->assertSee('Edit question 1')->assertSee('Fractions question 1')->assertSee('value="right one"', false)
            ->assertSee('value="B" required class="accent-[#28409e]" checked', false)->assertSee('your own copy for this quiz');
    }

    private function edit(Question $question, array $changes = []): TestResponse
    {
        return $this->put(route('teach.assignments.questions.update', [$this->quiz, $question]), $changes + [
            'stem' => 'Fractions question 1', 'options' => ['wrong one', 'right one', 'wrong two', 'wrong three'], 'correct' => 'B', 'explanation' => '', 'marks' => 1,
        ]);
    }

    public function test_editing_a_bank_question_puts_the_teachers_copy_in_its_place(): void
    {
        $this->topicWithQuestions($this->maths, 'Fractions', 3);
        $ids = [$this->idOf('Fractions question 1'), $this->idOf('Fractions question 2'), $this->idOf('Fractions question 3')];
        $this->post(route('teach.assignments.questions.selected', $this->quiz), ['question_ids' => $ids]);

        $this->edit(Question::find($ids[1]), ['stem' => 'What is half of 10?', 'options' => ['2', '5', '10', '20'], 'correct' => 'B', 'explanation' => 'Half of 10 is 5.', 'marks' => 2])
            ->assertRedirect(route('teach.assignments.show', $this->quiz))->assertSessionHas('success', 'Question saved.');

        $quizIds = $this->quiz->questions()->pluck('questions.id')->all();
        $this->assertSame($ids[0], $quizIds[0]);
        $this->assertSame($ids[2], $quizIds[2]);
        $copy = Question::with('currentRevision')->findOrFail($quizIds[1]);
        $this->assertNotSame($ids[1], $copy->id);
        $this->assertSame('teacher', $copy->owner_scope);
        $this->assertSame($this->teacher->id, $copy->author_id);
        $this->assertSame('What is half of 10?', $copy->currentRevision->stem);
        $this->assertSame(2.0, (float) $this->quiz->questions()->whereKey($copy->id)->first()->pivot->marks);

        // The shared bank question is unchanged and still offered to other teachers.
        $this->assertSame('Fractions question 2', Question::with('currentRevision')->find($ids[1])->currentRevision->stem);
        $this->get(route('teach.assignments.show', $this->quiz))->assertSeeInOrder(['Fractions question 1', 'What is half of 10?', 'Fractions question 3'])->assertSee('2 marks');
    }

    public function test_editing_the_teachers_own_question_keeps_it_and_adds_a_revision(): void
    {
        $this->post(route('teach.assignments.questions.store', $this->quiz), [
            'subject_id' => $this->maths->id, 'stem' => 'What is 2 + 2?', 'options' => ['3', '4', '5', '6'], 'correct' => 'B',
        ]);
        $own = $this->quiz->questions()->firstOrFail();

        $this->put(route('teach.assignments.questions.update', [$this->quiz, $own]), ['stem' => 'What is 2 + 3?', 'options' => ['3', '4', '5', '6'], 'correct' => 'C', 'marks' => 1])
            ->assertSessionHasNoErrors();

        $this->assertSame([$own->id], $this->quiz->questions()->pluck('questions.id')->all());
        $this->assertSame(2, $own->revisions()->count());
        $this->assertSame('What is 2 + 3?', $own->fresh('currentRevision')->currentRevision->stem);
        $this->assertSame((int) $own->fresh()->current_revision_id, (int) $this->quiz->questions()->first()->pivot->question_revision_id);
    }

    public function test_changing_only_the_marks_keeps_the_bank_question(): void
    {
        $this->topicWithQuestions($this->maths, 'Fractions', 1);
        $id = $this->idOf('Fractions question 1');
        $this->post(route('teach.assignments.questions.selected', $this->quiz), ['question_ids' => [$id]]);

        $this->edit(Question::find($id), ['marks' => 3])->assertSessionHasNoErrors();

        $this->assertSame([$id], $this->quiz->questions()->pluck('questions.id')->all());
        $this->assertSame(3.0, (float) $this->quiz->questions()->first()->pivot->marks);
        $this->assertSame(1, Question::count());
    }

    public function test_edited_questions_and_marks_are_used_when_marking(): void
    {
        $this->topicWithQuestions($this->maths, 'Fractions', 2);
        $ids = [$this->idOf('Fractions question 1'), $this->idOf('Fractions question 2')];
        $this->post(route('teach.assignments.questions.selected', $this->quiz), ['question_ids' => $ids]);
        // Question 1 now has C as its answer and is worth 3 marks.
        $this->edit(Question::find($ids[0]), ['correct' => 'C', 'marks' => 3]);
        $this->post(route('teach.assignments.publish', $this->quiz));
        $student = User::factory()->student()->create();
        Classroom::firstOrFail()->students()->attach($student->id, ['joined_at' => now()]);

        $questions = $this->quiz->questions()->get();
        $this->actingAs($student)->get(route('learn.assignments.take', $this->quiz))->assertOk();
        $this->post(route('learn.assignments.submit-quiz', $this->quiz), ['answers' => [$questions[0]->ulid => 'C', $questions[1]->ulid => 'B']]);

        $submission = Submission::firstOrFail();
        $this->assertSame(4.0, (float) $submission->score);
        $this->assertSame(4.0, (float) $submission->max_score);
    }

    public function test_edit_form_is_validated(): void
    {
        $this->topicWithQuestions($this->maths, 'Fractions', 1);
        $question = Question::find($this->idOf('Fractions question 1'));
        $this->post(route('teach.assignments.questions.selected', $this->quiz), ['question_ids' => [$question->id]]);

        $this->edit($question, ['stem' => ''])->assertSessionHasErrors('stem');
        $this->edit($question, ['options' => ['1', '2', '2', '3']])->assertSessionHasErrors(['options.2' => 'Two options are the same.']);
        $this->edit($question, ['options' => ['1', '2', '3']])->assertSessionHasErrors('options');
        $this->edit($question, ['correct' => 'E'])->assertSessionHasErrors('correct');
        $this->edit($question, ['marks' => 0])->assertSessionHasErrors('marks');
        $this->edit($question, ['marks' => 1.3])->assertSessionHasErrors('marks');
        $this->assertSame([$question->id], $this->quiz->questions()->pluck('questions.id')->all());

        // Five options are allowed, and E can then be the answer.
        $this->edit($question, ['options' => ['1', '2', '3', '4', '5'], 'correct' => 'E'])->assertSessionHasNoErrors();
    }

    public function test_questions_cannot_be_edited_after_students_take_the_quiz_or_by_others(): void
    {
        $this->topicWithQuestions($this->maths, 'Fractions', 2);
        $question = Question::find($this->idOf('Fractions question 1'));
        $notInQuiz = Question::find($this->idOf('Fractions question 2'));
        $this->post(route('teach.assignments.questions.selected', $this->quiz), ['question_ids' => [$question->id]]);

        $this->get(route('teach.assignments.questions.edit', [$this->quiz, $notInQuiz]))->assertNotFound();
        $this->edit($notInQuiz, ['stem' => 'Changed'])->assertSessionHasErrors('stem');

        $other = User::factory()->teacher(TeacherVerificationStatus::Approved)->create();
        $this->actingAs($other)->get(route('teach.assignments.questions.edit', [$this->quiz, $question]))->assertNotFound();
        $this->edit($question, ['stem' => 'Changed'])->assertNotFound();

        $this->actingAs($this->teacher)->post(route('teach.assignments.publish', $this->quiz));
        $student = User::factory()->student()->create();
        Classroom::firstOrFail()->students()->attach($student->id, ['joined_at' => now()]);
        $this->actingAs($student)->get(route('learn.assignments.take', $this->quiz));
        $this->post(route('learn.assignments.submit-quiz', $this->quiz), ['answers' => []]);

        $this->actingAs($this->teacher)->get(route('teach.assignments.questions.edit', [$this->quiz, $question]))->assertSee('can no longer change');
        $this->get(route('teach.assignments.show', $this->quiz))->assertDontSee(route('teach.assignments.questions.edit', [$this->quiz, $question]));
        $this->edit($question, ['stem' => 'Changed'])->assertSessionHasErrors('stem');
        $this->assertSame('Fractions question 1', $question->fresh('currentRevision')->currentRevision->stem);
    }
}
