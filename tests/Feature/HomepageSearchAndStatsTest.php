<?php

namespace Tests\Feature;

use App\Models\Curriculum\Subject;
use App\Models\QuestionBank\Question;
use App\Models\QuestionBank\QuestionRevision;
use App\Services\Practice\PaperBuilder;
use Database\Seeders\CurriculumSubjectsSeeder;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class HomepageSearchAndStatsTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(CurriculumSubjectsSeeder::class);
        Cache::forget('home.v3');
    }

    /** $count free practice MCQs for the subject. */
    private function questions(Subject $subject, int $count, array $overrides = []): void
    {
        for ($i = 0; $i < $count; $i++) {
            $question = new Question;
            $question->forceFill($overrides + [
                'type' => 'mcq', 'education_level_id' => $subject->education_level_id, 'subject_id' => $subject->id,
                'difficulty' => 3, 'status' => 'published', 'access_level' => 'free', 'confidentiality' => 'practice',
            ])->save();
            $revision = new QuestionRevision;
            $revision->forceFill(['question_id' => $question->id, 'revision_no' => 1, 'stem' => "Question {$question->id}", 'payload' => ['options' => [['id' => 'A', 'text' => 'a'], ['id' => 'B', 'text' => 'b']]]])->save();
            $question->forceFill(['current_revision_id' => $revision->id])->save();
        }
    }

    public function test_the_hero_shows_the_total_practice_questions_and_papers(): void
    {
        $maths = Subject::where('slug', 'core-mathematics')->firstOrFail();
        $this->questions($maths, 50);
        $this->questions($maths, 3, ['status' => 'draft']); // not counted
        $this->questions($maths, 2, ['confidentiality' => 'exam']); // not counted
        app(PaperBuilder::class)->build($maths);
        Cache::forget('home.v3');

        $response = $this->get('/')->assertOk()
            ->assertSee('Practice questions')->assertSee('Practice papers')
            ->assertSee('data-count="50"', false)->assertSee('data-count="1"', false);
        $this->assertStringNotContainsString('data-count="55"', $response->getContent());
    }

    public function test_the_hero_search_offers_exam_subject_and_year_filters_and_a_practice_mode(): void
    {
        $this->get('/')->assertOk()
            ->assertSee('aria-label="Search past questions and practice"', false)
            ->assertSee('BECE (JHS)')->assertSee('WASSCE (SHS)')->assertSee('Any year')
            ->assertSee('<option value="'.now()->year.'">', false)->assertSee('<option value="1990">', false)
            // Subject choices travel to the page for the filter, with each subject's exam level.
            ->assertSee('core-mathematics')->assertSee('jhs-science')
            ->assertSee('Past papers')->assertSee('Practice questions')
            ->assertSee(route('library.index'), false)->assertSee(route('practice.index'), false);
    }
}
