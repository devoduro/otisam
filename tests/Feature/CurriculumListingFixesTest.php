<?php

namespace Tests\Feature;

use App\Models\Curriculum\ClassLevel;
use App\Models\Curriculum\CurriculumNode;
use App\Models\Curriculum\EducationLevel;
use App\Models\Curriculum\Subject;
use App\Models\QuestionBank\Question;
use Database\Seeders\CurriculumSubjectsSeeder;
use Database\Seeders\DemoQuestionSeeder;
use Tests\TestCase;

/** Year order, programme links for the 2023 subjects, and the library's link to practice questions. */
class CurriculumListingFixesTest extends TestCase
{
    public function test_class_levels_take_their_year_from_the_name(): void
    {
        $shs = EducationLevel::where('code', 'SHS')->firstOrFail();
        ClassLevel::where('education_level_id', $shs->id)->where('name', 'SHS 2')->update(['sort' => 0]);

        (require database_path('migrations/2026_10_22_100001_fix_class_level_sort_order.php'))->up();

        $this->assertSame(['SHS 1', 'SHS 2', 'SHS 3'], $shs->classLevels()->pluck('name')->all());
        $this->assertSame([1, 2, 3], $shs->classLevels()->pluck('sort')->map(fn ($s) => (int) $s)->all());
    }

    public function test_2023_subjects_are_linked_to_programmes(): void
    {
        $this->seed(CurriculumSubjectsSeeder::class);

        $robotics = Subject::where('slug', 'robotics')->firstOrFail();
        $this->assertEqualsCanonicalizing(['General Science', 'Technical'], $robotics->programmes()->pluck('name')->all());

        // New core subjects belong to every programme, as core.
        $rme = Subject::where('slug', 'religious-and-moral-education')->firstOrFail();
        $this->assertSame(7, $rme->programmes()->wherePivot('is_core', true)->count());

        // Running it again links nothing twice.
        $this->seed(CurriculumSubjectsSeeder::class);
        $this->assertSame(2, $robotics->programmes()->count());
    }

    public function test_practice_questions_are_grouped_by_topic_and_year(): void
    {
        $this->seed([CurriculumSubjectsSeeder::class, DemoQuestionSeeder::class]);

        $robotics = Subject::where('slug', 'robotics')->firstOrFail();
        $topic = CurriculumNode::where('subject_id', $robotics->id)->where('title', 'PID control and troubleshooting')->firstOrFail();
        $shs2 = ClassLevel::where('name', 'SHS 2')->firstOrFail();
        $this->assertSame($shs2->id, $topic->class_level_id);
        $this->assertFalse($topic->is_official);

        // Every Robotics question has a topic; the SHS 2 topic's questions are filed under SHS 2.
        $questions = Question::where('subject_id', $robotics->id)->get();
        $this->assertSame(0, $questions->filter(fn ($q) => ! $q->curriculumNodes()->exists())->count());
        $this->assertSame(10, Question::whereHas('curriculumNodes', fn ($n) => $n->whereKey($topic->id))->where('class_level_id', $shs2->id)->count());

        $this->get(route('practice.index', ['subject' => 'robotics']))->assertOk()
            ->assertDontSee('none tagged yet')->assertSee('SHS 2 · PID control and troubleshooting');
        $this->get(route('practice.index', ['subject' => 'robotics', 'topic' => $topic->id]))->assertOk()
            ->assertSee('10 questions match');

        // Running the seeder again neither duplicates topics nor re-tags questions.
        $nodes = CurriculumNode::count();
        $this->seed(DemoQuestionSeeder::class);
        $this->assertSame($nodes, CurriculumNode::count());
        $this->assertSame(1, $questions->first()->curriculumNodes()->count());
    }

    public function test_library_points_to_practice_questions_for_the_subject(): void
    {
        $this->seed([CurriculumSubjectsSeeder::class, DemoQuestionSeeder::class]);

        $this->get(route('library.index', ['subject' => 'robotics']))->assertOk()
            ->assertSee('practice questions with answers for Robotics')
            ->assertSee(route('practice.index', ['subject' => 'robotics']), false);

        $this->get(route('library.index', ['subject' => 'spanish']))->assertOk()->assertDontSee('Practise now');
    }
}
