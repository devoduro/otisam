<?php

namespace Tests\Feature;

use App\Models\Curriculum\Subject;
use App\Models\QuestionBank\Question;
use App\Models\QuestionBank\QuestionKey;
use Database\Seeders\CurriculumStructureSeeder;
use Database\Seeders\CurriculumSubjectsSeeder;
use Database\Seeders\DemoQuestionSeeder;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/** Fill-in, true/false and theory questions in the practice section. */
class PracticeQuestionTypesTest extends TestCase
{
    private function seedComputing(): Subject
    {
        $this->seed(CurriculumStructureSeeder::class);
        $this->seed(CurriculumSubjectsSeeder::class);
        DemoQuestionSeeder::$only = ['jhs-computing'];
        try {
            $this->seed(DemoQuestionSeeder::class);
        } finally {
            DemoQuestionSeeder::$only = null;
        }

        return Subject::where('slug', 'jhs-computing')->firstOrFail();
    }

    /** @return array<string, array{string, string}> subject file slug => [slug, level code] */
    public static function fullBanks(): array
    {
        return [
            'JHS Computing' => ['jhs-computing', 'JHS'],
            'SHS Computing' => ['computing', 'SHS'],
            'SHS ICT' => ['information-and-communication-technology', 'SHS'],
            'JHS Mathematics' => ['jhs-mathematics', 'JHS'],
            'JHS English Language' => ['jhs-english-language', 'JHS'],
            'SHS English Language' => ['english-language', 'SHS'],
            'JHS Science' => ['jhs-science', 'JHS'],
            'SHS Core Mathematics' => ['core-mathematics', 'SHS'],
        ];
    }

    #[DataProvider('fullBanks')]
    public function test_full_banks_cover_all_three_years_with_every_question_type(string $slug, string $level): void
    {
        $tagged = DemoQuestionSeeder::rows(DemoQuestionSeeder::subjectFiles()[$slug]);
        $kinds = array_count_values(array_map(fn ($t) => $t['row'][5] ?? 'mcq', $tagged));

        $this->assertGreaterThanOrEqual(1000, count(array_filter($tagged, fn ($t) => $t['year'] !== null)), "{$slug}: year-tagged questions.");
        $this->assertGreaterThanOrEqual(150, $kinds['theory'] ?? 0, "{$slug}: theory questions.");
        $this->assertGreaterThan(100, $kinds['tf'] ?? 0, "{$slug}: true/false questions.");
        $this->assertGreaterThan(100, $kinds['fill'] ?? 0, "{$slug}: fill-in questions.");
        $this->assertEqualsCanonicalizing(["{$level} 1", "{$level} 2", "{$level} 3"], array_values(array_unique(array_filter(array_column($tagged, 'year')))));
    }

    public function test_jhs_computing_has_every_question_type(): void
    {
        $subject = $this->seedComputing();
        $types = Question::where('subject_id', $subject->id)->selectRaw('type, count(*) as n')->groupBy('type')->pluck('n', 'type');

        foreach (['mcq', 'true_false', 'fill_in', 'theory'] as $type) {
            $this->assertGreaterThan(0, $types[$type] ?? 0, "No {$type} questions seeded.");
        }
        $theory = Question::where('subject_id', $subject->id)->where('type', 'theory')->first();
        $key = QuestionKey::where('question_revision_id', $theory->current_revision_id)->first()->key;
        $this->assertNotEmpty($key['model']);
        $this->assertNotEmpty($key['points']);
        // Keys never reach the page with the question.
        $this->assertArrayNotHasKey('model', $theory->currentRevision->payload);
    }

    public function test_objective_sets_hold_no_theory_and_theory_sets_hold_only_theory(): void
    {
        $subject = $this->seedComputing();

        $this->get(route('practice.start', ['subject' => $subject->slug, 'count' => 40]))->assertOk();
        $objective = Question::whereIn('id', session('practice.set.ids'))->pluck('type')->unique()->all();
        $this->assertNotContains('theory', $objective);

        $this->get(route('practice.start', ['subject' => $subject->slug, 'count' => 10, 'type' => 'theory']))->assertOk()->assertSee('Theory question');
        $this->assertSame(['theory'], Question::whereIn('id', session('practice.set.ids'))->pluck('type')->unique()->all());
    }

    public function test_marking_handles_fill_in_true_false_and_theory(): void
    {
        $subject = $this->seedComputing();
        $pick = fn (string $type) => Question::where('subject_id', $subject->id)->where('type', $type)->with('currentRevision')->first();
        $fill = $pick('fill_in');
        $tf = $pick('true_false');
        $theory = $pick('theory');
        $accepted = QuestionKey::where('question_revision_id', $fill->current_revision_id)->first()->key['accepted'][0];
        $correctTf = QuestionKey::where('question_revision_id', $tf->current_revision_id)->first()->key['correct'];

        $this->withSession(['practice.set' => [
            'subject_id' => $subject->id,
            'ids' => [$fill->id, $tf->id, $theory->id],
            'started_at' => now()->timestamp,
            'minutes' => null,
            'filters' => ['subject' => $subject->slug],
        ]])->post(route('practice.mark'), ['answers' => [
            // Case and stray spaces and a full stop do not matter.
            $fill->ulid => '  '.strtoupper($accepted).'. ',
            $tf->ulid => $correctTf,
            $theory->ulid => 'My own answer to the theory question',
        ]])->assertOk()
            ->assertSee('2 / 2')
            ->assertSee('Model answer')
            ->assertSee('My own answer to the theory question');
    }

    public function test_a_wrong_fill_in_answer_shows_the_accepted_answer(): void
    {
        $subject = $this->seedComputing();
        $fill = Question::where('subject_id', $subject->id)->where('type', 'fill_in')->with('currentRevision')->first();
        $accepted = QuestionKey::where('question_revision_id', $fill->current_revision_id)->first()->key['accepted'][0];

        $this->withSession(['practice.set' => [
            'subject_id' => $subject->id, 'ids' => [$fill->id], 'started_at' => now()->timestamp, 'minutes' => null, 'filters' => ['subject' => $subject->slug],
        ]])->post(route('practice.mark'), ['answers' => [$fill->ulid => 'zzz not it']])
            ->assertOk()->assertSee('0 / 1')->assertSee('Accepted answer')->assertSee($accepted);
    }
}
