<?php

namespace Tests\Feature;

use App\Models\Curriculum\EducationLevel;
use App\Models\Curriculum\Subject;
use App\Models\QuestionBank\Question;
use App\Models\QuestionBank\QuestionKey;
use App\Models\Videos\VideoLesson;
use Database\Seeders\CurriculumStructureSeeder;
use Database\Seeders\CurriculumSubjectsSeeder;
use Database\Seeders\DemoQuestionSeeder;
use Tests\TestCase;

class PracticeTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(CurriculumStructureSeeder::class);
        $this->seed(CurriculumSubjectsSeeder::class);
    }

    public function test_subject_seeder_adds_jhs_and_2023_curriculum_subjects_once(): void
    {
        $jhs = EducationLevel::where('code', 'JHS')->first();
        $this->assertTrue($jhs->is_active);
        $this->assertTrue(Subject::where('slug', 'jhs-mathematics')->where('education_level_id', $jhs->id)->exists());
        $this->assertTrue(Subject::where('slug', 'computing')->exists());
        $this->assertTrue(Subject::where('slug', 'robotics')->exists());
        $this->assertSame('Called Additional Mathematics in the 2023 SHS curriculum.', Subject::where('slug', 'elective-mathematics')->value('description'));

        $count = Subject::count();
        $this->seed(CurriculumSubjectsSeeder::class);
        $this->assertSame($count, Subject::count(), 'Running again adds nothing.');

        $this->get(route('subjects.index'))->assertOk()->assertSee('Junior High School')->assertSee('Career Technology')->assertSee('Robotics');
    }

    public function test_demo_questions_are_seeded_once_with_keys_kept_apart(): void
    {
        $this->seed(DemoQuestionSeeder::class);
        $count = Question::count();
        $this->assertGreaterThan(100, $count);
        $this->assertSame($count, QuestionKey::count());

        $question = Question::with('currentRevision')->first();
        $this->assertArrayNotHasKey('correct', $question->currentRevision->payload);
        $this->assertStringContainsString('written by Otisam', $question->source);

        $this->seed(DemoQuestionSeeder::class);
        $this->assertSame($count, Question::count());
    }

    public function test_practice_set_hides_answers_and_marks_on_the_server(): void
    {
        $this->seed(DemoQuestionSeeder::class);
        $subject = Subject::where('slug', 'core-mathematics')->first();

        $this->get(route('practice.index'))->assertOk()->assertSee('Core Mathematics')->assertSee('BECE');
        $this->get(route('practice.index', ['subject' => 'core-mathematics']))->assertOk()->assertSee('44 questions match');
        $this->get(route('practice.show', $subject))->assertRedirect(route('practice.start', ['subject' => 'core-mathematics']));

        $page = $this->get(route('practice.start', ['subject' => 'core-mathematics', 'count' => 10]))->assertOk();
        $ids = session('practice.set')['ids'];
        $this->assertCount(10, $ids);
        $page->assertDontSee('Explanation')->assertDontSee('"correct"', false);

        // Answer every question correctly except the first.
        $questions = Question::whereIn('id', $ids)->get();
        $answers = [];
        foreach ($questions as $i => $question) {
            $correct = QuestionKey::find($question->current_revision_id)->key['correct'];
            $answers[$question->ulid] = $i === 0 ? ($correct === 'A' ? 'B' : 'A') : $correct;
        }

        $this->post(route('practice.mark'), ['answers' => $answers])
            ->assertOk()->assertSee('9 / 10')->assertSee('Explanation');

        // The set is used up: marking it again needs a new set.
        $this->post(route('practice.mark'), ['answers' => $answers])->assertStatus(419);

        // The score shows on the practice home page.
        $this->get(route('practice.index'))->assertSee('Your recent scores')->assertSee('9/10');
    }

    public function test_filters_difficulty_count_and_timed_mode(): void
    {
        $this->seed(DemoQuestionSeeder::class);

        $this->get(route('practice.start', ['subject' => 'core-mathematics', 'difficulty' => 'easy', 'count' => 5]))->assertOk();
        $ids = session('practice.set')['ids'];
        $this->assertNotEmpty($ids);
        $this->assertLessThanOrEqual(5, count($ids));
        $this->assertTrue(Question::whereIn('id', $ids)->get()->every(fn ($q) => $q->difficulty <= 2));
        $this->assertNull(session('practice.set')['minutes']);

        $this->get(route('practice.start', ['subject' => 'core-mathematics', 'mode' => 'timed', 'count' => 5]))->assertOk()->assertSee('Time left');
        $this->assertSame(5, session('practice.set')['minutes']);

        $this->get(route('practice.start', ['subject' => 'core-mathematics', 'count' => 7]))->assertSessionHasErrors('count');
        $this->get(route('practice.start'))->assertRedirect();
    }

    public function test_video_filters_tell_same_named_subjects_apart(): void
    {
        $jhs = Subject::where('slug', 'jhs-social-studies')->first();
        $shs = Subject::where('slug', 'social-studies')->first();
        foreach ([$jhs, $shs] as $s) {
            $video = new VideoLesson(['title' => "Video for {$s->slug}", 'youtube_id' => substr(md5($s->slug), 0, 11), 'subject_id' => $s->id, 'access_level' => 'free']);
            $video->status = 'published';
            $video->published_at = now();
            $video->save();
        }

        $this->get(route('videos.index'))->assertOk()
            ->assertSee('<optgroup label="Junior High School">', false)
            ->assertSee('<optgroup label="Senior High School">', false);
        $this->get(route('videos.index', ['subject' => 'jhs-social-studies']))->assertSee('Video for jhs-social-studies')->assertDontSee('Video for social-studies');
        $this->get(route('videos.index', ['level' => 'shs']))->assertSee('Video for social-studies')->assertDontSee('Video for jhs-social-studies');
    }

    public function test_premium_questions_need_a_subscription_and_hidden_subjects_404(): void
    {
        $this->seed(DemoQuestionSeeder::class);
        $subject = Subject::where('slug', 'physics')->first();
        Question::where('subject_id', $subject->id)->update(['access_level' => 'premium']);

        $this->get(route('practice.start', ['subject' => 'physics']))->assertOk()->assertSee('No practice questions match these filters yet.');

        $subject->update(['is_active' => false]);
        $this->get(route('practice.start', ['subject' => 'physics']))->assertNotFound();
    }
}
