<?php

namespace Tests\Unit;

use App\Enums\TeacherVerificationStatus;
use App\Models\Classroom\Assignment;
use App\Models\Classroom\Classroom;
use App\Models\Curriculum\ClassLevel;
use App\Models\Curriculum\Subject;
use App\Models\QuestionBank\Question;
use App\Models\QuestionBank\QuestionKey;
use App\Models\User;
use App\Services\Classroom\AssignmentService;
use Database\Seeders\CurriculumSubjectsSeeder;
use Illuminate\Validation\ValidationException;
use Tests\Concerns\CreatesTopicQuestions;
use Tests\TestCase;

/** Picking quiz questions from one topic of the question bank. */
class TopicQuizBankTest extends TestCase
{
    use CreatesTopicQuestions;

    private AssignmentService $service;

    private Subject $maths;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(CurriculumSubjectsSeeder::class);
        $this->service = app(AssignmentService::class);
        $this->maths = Subject::where('slug', 'jhs-mathematics')->firstOrFail();
    }

    private function quiz(): Assignment
    {
        $teacher = User::factory()->teacher(TeacherVerificationStatus::Approved)->create();
        $classroom = new Classroom(['name' => 'JHS 1A']);
        $classroom->teacher_id = $teacher->id;
        $classroom->save();
        $quiz = new Assignment(['type' => 'quiz', 'title' => 'Topic test']);
        $quiz->forceFill(['classroom_id' => $classroom->id, 'teacher_id' => $teacher->id])->save();

        return $quiz;
    }

    /** @return list<string> */
    private function stems(Assignment $quiz): array
    {
        return $quiz->questions()->with('currentRevision')->get()->map(fn ($q) => $q->currentRevision->stem)->sort()->values()->all();
    }

    public function test_only_questions_from_the_chosen_topic_are_added(): void
    {
        $fractions = $this->topicWithQuestions($this->maths, 'Fractions', 5);
        $this->topicWithQuestions($this->maths, 'Angles', 5);
        $quiz = $this->quiz();

        $this->assertSame(3, $this->service->addFromBank($quiz, $this->maths, 3, null, $fractions));
        $this->assertCount(3, $this->stems($quiz));
        foreach ($this->stems($quiz) as $stem) {
            $this->assertStringStartsWith('Fractions', $stem);
        }
    }

    public function test_a_topic_includes_its_subtopics(): void
    {
        $number = $this->topicWithQuestions($this->maths, 'Number', 1);
        $this->topicWithQuestions($this->maths, 'Decimals', 2, $number);
        $this->topicWithQuestions($this->maths, 'Angles', 2);
        $quiz = $this->quiz();

        $this->assertSame(3, $this->service->addFromBank($quiz, $this->maths, 10, null, $number));
        $this->assertSame(['Decimals question 1', 'Decimals question 2', 'Number question 1'], $this->stems($quiz));
    }

    public function test_adding_again_never_repeats_a_question_and_stops_when_the_topic_runs_out(): void
    {
        $fractions = $this->topicWithQuestions($this->maths, 'Fractions', 4);
        $quiz = $this->quiz();

        $this->assertSame(3, $this->service->addFromBank($quiz, $this->maths, 3, null, $fractions));
        $this->assertSame(1, $this->service->addFromBank($quiz, $this->maths, 3, null, $fractions));
        $this->assertSame(0, $this->service->addFromBank($quiz, $this->maths, 3, null, $fractions));
        $this->assertCount(4, array_unique($this->stems($quiz)));
    }

    public function test_difficulty_and_topic_filters_combine(): void
    {
        $fractions = $this->topicWithQuestions($this->maths, 'Fractions', 3, question: ['difficulty' => 1]);
        $this->bankQuestion($this->maths, 'Hard fractions question', ['difficulty' => 5])->curriculumNodes()->attach($fractions->id);
        $quiz = $this->quiz();

        $this->assertSame(1, $this->service->addFromBank($quiz, $this->maths, 10, [4, 5], $fractions));
        $this->assertSame(['Hard fractions question'], $this->stems($quiz));
    }

    public function test_unpublished_premium_safe_and_non_mcq_questions_are_left_out(): void
    {
        $fractions = $this->topicWithQuestions($this->maths, 'Fractions', 1);
        foreach ([['status' => 'draft'], ['confidentiality' => 'exam'], ['type' => 'theory']] as $i => $attributes) {
            $this->bankQuestion($this->maths, "Excluded {$i}", $attributes)->curriculumNodes()->attach($fractions->id);
        }
        $quiz = $this->quiz();

        $this->assertSame(1, $this->service->addFromBank($quiz, $this->maths, 10, null, $fractions));
        $this->assertSame(['Fractions question 1'], $this->stems($quiz));
    }

    public function test_bank_topics_lists_topics_with_usable_question_counts(): void
    {
        $this->topicWithQuestions($this->maths, 'Fractions', 3);
        $angles = $this->topicWithQuestions($this->maths, 'Angles', 2);
        $this->bankQuestion($this->maths, 'Draft angle question', ['status' => 'draft'])->curriculumNodes()->attach($angles->id);
        $this->topicWithQuestions($this->maths, 'Empty topic', 0);
        $this->topicWithQuestions(Subject::where('slug', 'jhs-science')->firstOrFail(), 'Cells', 4);

        $topics = $this->service->bankTopics($this->maths)->keyBy('title');

        $this->assertSame(['Angles', 'Fractions'], $topics->keys()->sort()->values()->all());
        $this->assertSame(3, $topics['Fractions']['count']);
        $this->assertSame(2, $topics['Angles']['count'], 'Draft questions are not counted.');
    }

    public function test_search_finds_words_in_the_question_and_treats_wildcards_literally(): void
    {
        $this->bankQuestion($this->maths, 'Simplify 50% of 30');
        $this->bankQuestion($this->maths, 'Simplify 5 of 30');
        $this->bankQuestion($this->maths, 'Find the angle_sum of a triangle');

        $stems = fn (string $q) => $this->service->bankQuestions($this->maths, search: $q)->with('currentRevision')->get()->map(fn ($x) => $x->currentRevision->stem)->sort()->values()->all();

        $this->assertSame(['Simplify 5 of 30', 'Simplify 50% of 30'], $stems('simplify'));
        $this->assertSame(['Simplify 50% of 30'], $stems('50%'));
        $this->assertSame(['Find the angle_sum of a triangle'], $stems('angle_sum'));
        $this->assertSame([], $stems('angle%sum'));
        $this->assertCount(3, $stems('  '));
    }

    public function test_add_selected_keeps_the_order_and_skips_repeats_and_unusable_questions(): void
    {
        $a = $this->bankQuestion($this->maths, 'Question A');
        $b = $this->bankQuestion($this->maths, 'Question B');
        $c = $this->bankQuestion($this->maths, 'Question C');
        $draft = $this->bankQuestion($this->maths, 'Draft', ['status' => 'draft']);
        $theory = $this->bankQuestion($this->maths, 'Theory', ['type' => 'theory']);
        $quiz = $this->quiz();

        $this->assertSame(2, $this->service->addSelected($quiz, [$c->id, $a->id, $c->id, $draft->id, $theory->id, 999999]));
        $this->assertSame([$c->id, $a->id], $quiz->questions()->pluck('questions.id')->all());

        // Adding more puts them after the existing ones; ones already in the quiz are skipped.
        $this->assertSame(1, $this->service->addSelected($quiz, [$a->id, $b->id]));
        $this->assertSame([$c->id, $a->id, $b->id], $quiz->questions()->pluck('questions.id')->all());
        $this->assertSame([1, 2, 3], $quiz->questions()->get()->pluck('pivot.sort')->map(fn ($s) => (int) $s)->all());
    }

    public function test_add_selected_pins_the_current_revision_and_one_mark(): void
    {
        $question = $this->bankQuestion($this->maths, 'Question A');
        $quiz = $this->quiz();

        $this->service->addSelected($quiz, [$question->id]);

        $pivot = $quiz->questions()->first()->pivot;
        $this->assertSame($question->current_revision_id, (int) $pivot->question_revision_id);
        $this->assertSame(1.0, (float) $pivot->marks);
    }

    public function test_add_selected_refuses_to_go_over_fifty_questions(): void
    {
        $quiz = $this->quiz();
        $ids = collect(range(1, 51))->map(fn ($i) => $this->bankQuestion($this->maths, "Question {$i}")->id);

        $this->assertSame(50, $this->service->addSelected($quiz, $ids->take(50)->all()));
        $this->expectException(ValidationException::class);
        $this->service->addSelected($quiz, [$ids->last()]);
    }

    public function test_year_filter_includes_questions_for_any_year(): void
    {
        $jhs1 = ClassLevel::where('name', 'JHS 1')->firstOrFail();
        $jhs2 = ClassLevel::where('name', 'JHS 2')->firstOrFail();
        $this->bankQuestion($this->maths, 'One', ['class_level_id' => $jhs1->id]);
        $this->bankQuestion($this->maths, 'Two', ['class_level_id' => $jhs2->id]);
        $this->bankQuestion($this->maths, 'Any');

        $stems = fn (?ClassLevel $year) => $this->service->bankQuestions($this->maths, year: $year)->with('currentRevision')->get()->map(fn ($x) => $x->currentRevision->stem)->sort()->values()->all();

        $this->assertSame(['Any', 'Two'], $stems($jhs2));
        $this->assertSame(['Any', 'One'], $stems($jhs1));
        $this->assertSame(['Any', 'One', 'Two'], $stems(null));
    }

    public function test_bank_topics_for_a_year_count_only_that_years_questions(): void
    {
        $jhs1 = ClassLevel::where('name', 'JHS 1')->firstOrFail();
        $jhs2 = ClassLevel::where('name', 'JHS 2')->firstOrFail();
        $topic = $this->topicWithQuestions($this->maths, 'Fractions', 2);
        $this->bankQuestion($this->maths, 'JHS 1 fractions', ['class_level_id' => $jhs1->id])->curriculumNodes()->attach($topic->id);
        $jhs1Only = $this->topicWithQuestions($this->maths, 'Place value', 1);
        $jhs1Only->update(['class_level_id' => $jhs1->id]);

        $this->assertSame(['Fractions' => 3, 'Place value' => 1], $this->service->bankTopics($this->maths, $jhs1)->pluck('count', 'title')->all());
        $this->assertSame(['Fractions' => 2], $this->service->bankTopics($this->maths, $jhs2)->pluck('count', 'title')->all());
    }

    private function teacherOf(Assignment $quiz): User
    {
        return User::findOrFail($quiz->teacher_id);
    }

    public function test_editing_a_bank_question_swaps_in_a_teacher_copy_at_the_same_place(): void
    {
        $quiz = $this->quiz();
        [$a, $b, $c] = [$this->bankQuestion($this->maths, 'A'), $this->bankQuestion($this->maths, 'B'), $this->bankQuestion($this->maths, 'C')];
        $this->service->addSelected($quiz, [$a->id, $b->id, $c->id]);

        $copy = $this->service->editQuestion($quiz, $b, $this->teacherOf($quiz), 'B edited', ['1', '2', '3', '4'], 'D', 'Because.', 2);

        $this->assertSame([$a->id, $copy->id, $c->id], $quiz->questions()->pluck('questions.id')->all());
        $this->assertSame(['teacher', 'exam', $quiz->teacher_id], [$copy->owner_scope, $copy->confidentiality, $copy->author_id]);
        $this->assertSame($b->subject_id, $copy->subject_id);
        $this->assertSame((int) $b->difficulty, (int) $copy->difficulty);
        $revision = $copy->currentRevision;
        $this->assertSame('B edited', $revision->stem);
        $this->assertSame('Because.', $revision->explanation);
        $this->assertSame([['id' => 'A', 'text' => '1'], ['id' => 'B', 'text' => '2'], ['id' => 'C', 'text' => '3'], ['id' => 'D', 'text' => '4']], $revision->payload['options']);
        $this->assertSame('D', QuestionKey::where('question_revision_id', $revision->id)->value('key')['correct']);
        $pivot = $quiz->questions()->whereKey($copy->id)->first()->pivot;
        $this->assertSame(2, (int) $pivot->sort);
        $this->assertSame(2.0, (float) $pivot->marks);
        $this->assertSame('B', $b->fresh('currentRevision')->currentRevision->stem, 'The bank question is unchanged.');
    }

    public function test_editing_another_teachers_question_also_makes_a_copy(): void
    {
        $quiz = $this->quiz();
        $others = $this->bankQuestion($this->maths, 'Theirs', ['owner_scope' => 'teacher', 'author_id' => User::factory()->teacher()->create()->id, 'confidentiality' => 'practice']);
        $this->service->addSelected($quiz, [$others->id]);

        $copy = $this->service->editQuestion($quiz, $others, $this->teacherOf($quiz), 'Mine now', ['1', '2', '3', '4'], 'A', null, 1);

        $this->assertNotSame($others->id, $copy->id);
        $this->assertSame('Theirs', $others->fresh('currentRevision')->currentRevision->stem);
    }

    public function test_a_teachers_own_question_gets_a_new_revision_and_other_quizzes_keep_the_old_one(): void
    {
        $quiz = $this->quiz();
        $own = $this->service->addOwnQuestion($quiz, $this->teacherOf($quiz), $this->maths, 'Original', ['1', '2', '3', '4'], 'A', null);
        $firstRevision = $own->current_revision_id;
        // The same question in a second quiz of the same teacher.
        $second = new Assignment(['type' => 'quiz', 'title' => 'Second']);
        $second->forceFill(['classroom_id' => $quiz->classroom_id, 'teacher_id' => $quiz->teacher_id])->save();
        $second->questions()->attach($own->id, ['question_revision_id' => $firstRevision, 'sort' => 1, 'marks' => 1]);

        $same = $this->service->editQuestion($quiz, $own, $this->teacherOf($quiz), 'Changed', ['1', '2', '3', '4'], 'B', null, 1);

        $this->assertSame($own->id, $same->id);
        $this->assertSame(2, $own->revisions()->count());
        $this->assertNotSame($firstRevision, $own->fresh()->current_revision_id);
        $this->assertSame((int) $own->fresh()->current_revision_id, (int) $quiz->questions()->first()->pivot->question_revision_id);
        $this->assertSame((int) $firstRevision, (int) $second->questions()->first()->pivot->question_revision_id);
    }

    public function test_changing_only_marks_updates_the_marks_alone(): void
    {
        $quiz = $this->quiz();
        $question = $this->bankQuestion($this->maths, 'A');
        $this->service->addSelected($quiz, [$question->id]);

        $result = $this->service->editQuestion($quiz, $question, $this->teacherOf($quiz), ' A ', ['wrong one', 'right one', 'wrong two', 'wrong three'], 'B', '  ', 4.5);

        $this->assertSame($question->id, $result->id);
        $this->assertSame(1, Question::count());
        $this->assertSame(4.5, (float) $quiz->questions()->first()->pivot->marks);
    }

    public function test_a_question_outside_the_quiz_cannot_be_edited_through_it(): void
    {
        $quiz = $this->quiz();
        $question = $this->bankQuestion($this->maths, 'A');

        $this->expectException(ValidationException::class);
        $this->service->editQuestion($quiz, $question, $this->teacherOf($quiz), 'B', ['1', '2', '3', '4'], 'A', null, 1);
    }
}
