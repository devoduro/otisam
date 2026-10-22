<?php

namespace Tests\Feature;

use App\Enums\TeacherVerificationStatus;
use App\Models\Curriculum\EducationLevel;
use App\Models\Curriculum\Subject;
use App\Models\QuestionBank\Question;
use App\Models\QuestionBank\QuestionKey;
use App\Models\QuestionBank\QuestionRevision;
use App\Models\User;
use Tests\TestCase;

class PromotionAptitudeTest extends TestCase
{
    private function aptitudeSubject(int $questions): Subject
    {
        $level = EducationLevel::firstOrCreate(['code' => EducationLevel::PROFESSIONAL], ['name' => 'Teacher professional', 'sort' => 9, 'is_active' => false]);
        $subject = new Subject(['education_level_id' => $level->id, 'name' => 'GES Promotion (Aptitude)', 'category' => 'core', 'sort' => 2, 'is_active' => true]);
        $subject->slug = 'ges-promotion-aptitude';
        $subject->save();

        for ($i = 0; $i < $questions; $i++) {
            $question = new Question;
            $question->forceFill(['type' => 'mcq', 'education_level_id' => $level->id, 'subject_id' => $subject->id, 'difficulty' => 2, 'status' => 'published', 'access_level' => 'free', 'confidentiality' => 'promotion'])->save();
            $revision = new QuestionRevision;
            $revision->forceFill(['question_id' => $question->id, 'revision_no' => 1, 'stem' => "What is {$i} + 1?", 'payload' => ['options' => [['id' => 'A', 'text' => 'x'], ['id' => 'B', 'text' => 'y']]], 'explanation' => 'Add one.'])->save();
            (new QuestionKey)->forceFill(['question_revision_id' => $revision->id, 'key' => ['correct' => 'B']])->save();
            $question->forceFill(['current_revision_id' => $revision->id])->save();
        }

        return $subject;
    }

    public function test_promotion_page_offers_the_aptitude_papers_and_counts_both_banks(): void
    {
        $this->aptitudeSubject(45);

        // Guests see the papers and the total; the practice links are for approved teachers.
        $this->get(route('promotion'))->assertOk()
            ->assertSee('Aptitude paper: numerical, verbal and logical reasoning')->assertSee('Quick aptitude set: 20 questions')
            ->assertSee('45</strong> practice questions', false);

        $teacher = User::factory()->teacher(TeacherVerificationStatus::Approved)->create();
        $this->actingAs($teacher)->get(route('promotion'))->assertOk()
            ->assertSee(e(route('practice.start', ['subject' => 'ges-promotion-aptitude', 'count' => 40, 'mode' => 'timed'])), false);
    }

    public function test_only_approved_teachers_can_sit_the_aptitude_paper(): void
    {
        $this->aptitudeSubject(45);

        $this->get(route('practice.start', ['subject' => 'ges-promotion-aptitude', 'count' => 20, 'mode' => 'timed']))->assertRedirect();
        $this->actingAs(User::factory()->student()->create())->get(route('practice.start', ['subject' => 'ges-promotion-aptitude']))->assertRedirect(route('promotion'));

        $teacher = User::factory()->teacher(TeacherVerificationStatus::Approved)->create();
        $this->actingAs($teacher)->get(route('practice.start', ['subject' => 'ges-promotion-aptitude', 'count' => 20, 'mode' => 'timed']))
            ->assertOk()->assertSee('What is ')->assertSee('Mark my answers');
    }

    private function subjectWithQuestions(string $slug, string $name, int $count, int $difficulty): void
    {
        $level = EducationLevel::firstOrCreate(['code' => EducationLevel::PROFESSIONAL], ['name' => 'Teacher professional', 'sort' => 9, 'is_active' => false]);
        $subject = new Subject(['education_level_id' => $level->id, 'name' => $name, 'category' => 'core', 'sort' => 1, 'is_active' => true]);
        $subject->slug = $slug;
        $subject->save();
        for ($i = 0; $i < $count; $i++) {
            $question = new Question;
            $question->forceFill(['type' => 'mcq', 'education_level_id' => $level->id, 'subject_id' => $subject->id, 'difficulty' => $difficulty, 'status' => 'published', 'access_level' => 'free', 'confidentiality' => 'promotion'])->save();
            $revision = new QuestionRevision;
            $revision->forceFill(['question_id' => $question->id, 'revision_no' => 1, 'stem' => "{$name} item {$i}?", 'payload' => ['options' => [['id' => 'A', 'text' => 'x'], ['id' => 'B', 'text' => 'y']]], 'explanation' => 'Because.'])->save();
            (new QuestionKey)->forceFill(['question_revision_id' => $revision->id, 'key' => ['correct' => 'B']])->save();
            $question->forceFill(['current_revision_id' => $revision->id])->save();
        }
    }

    public function test_rank_papers_mix_the_banks_and_cap_leadership_difficulty_for_lower_ranks(): void
    {
        $this->subjectWithQuestions('ges-promotion-general', 'General', 60, 2);
        $this->subjectWithQuestions('ges-promotion-aptitude', 'Aptitude', 60, 2);
        $this->subjectWithQuestions('ges-promotion-leadership', 'Leadership', 10, 5);

        $this->get(route('promotion.paper', 'senior-to-principal'))->assertRedirect();
        $this->actingAs(User::factory()->student()->create())->get(route('promotion.paper', 'senior-to-principal'))->assertRedirect(route('promotion'));

        $teacher = User::factory()->teacher(TeacherVerificationStatus::Approved)->create();
        $this->actingAs($teacher)->get(route('promotion.paper', 'nope'))->assertNotFound();

        // A lower rank never sees the hardest leadership questions; the paper is topped up from the general set.
        $this->actingAs($teacher)->get(route('promotion.paper', 'senior-to-principal'))->assertOk();
        $lower = Question::whereIn('questions.id', session('practice.set.ids'))->join('subjects', 'subjects.id', '=', 'questions.subject_id')->pluck('subjects.slug')->countBy();
        $this->assertCount(40, session('practice.set.ids'));
        $this->assertSame(0, $lower['ges-promotion-leadership'] ?? 0);

        $this->actingAs($teacher)->get(route('promotion.paper', 'ad-i-to-deputy'))->assertOk()->assertSee('Mark my answers');
        $ids = session('practice.set.ids');
        $this->assertCount(40, $ids);
        $subjects = Question::whereIn('questions.id', $ids)->join('subjects', 'subjects.id', '=', 'questions.subject_id')->pluck('subjects.slug')->countBy();
        $this->assertGreaterThan(0, $subjects['ges-promotion-leadership'] ?? 0);
        $this->assertGreaterThan(0, $subjects['ges-promotion-aptitude'] ?? 0);
    }

    public function test_promotion_page_marks_rank_papers_as_practice_sets(): void
    {
        $this->subjectWithQuestions('ges-promotion-general', 'General', 45, 2);
        $this->get(route('promotion'))->assertOk()->assertSee('not official GES rank syllabi')->assertDontSee('Being prepared');
    }

    public function test_the_promotion_question_file_is_well_formed(): void
    {
        $data = require base_path('database/seeders/data/demo-questions-promotion.php');
        $this->assertArrayHasKey('ges-promotion-general', $data);
        $this->assertArrayHasKey('ges-promotion-aptitude', $data);
        $this->assertArrayHasKey('ges-promotion-leadership', $data);
        $this->assertGreaterThanOrEqual(150, count($data['ges-promotion-leadership']));
        $this->assertGreaterThanOrEqual(500, count($data['ges-promotion-aptitude']));
        $this->assertGreaterThanOrEqual(200, count($data['ges-promotion-general']));

        foreach ($data as $slug => $rows) {
            $seen = [];
            $letters = [];
            foreach ($rows as $i => [$stem, $options, $answer, $explanation, $difficulty]) {
                $where = "{$slug} #{$i}";
                $this->assertGreaterThan(5, mb_strlen(trim($stem)), $where);
                $this->assertCount(4, $options, $where);
                $this->assertSame(4, count(array_unique(array_map('mb_strtolower', $options))), "{$where}: duplicate options");
                $this->assertContains($answer, ['A', 'B', 'C', 'D'], $where);
                $this->assertGreaterThan(5, mb_strlen(trim($explanation)), $where);
                $this->assertGreaterThanOrEqual(1, $difficulty, $where);
                $this->assertLessThanOrEqual(5, $difficulty, $where);
                $key = mb_strtolower(trim($stem));
                $this->assertArrayNotHasKey($key, $seen, "{$where}: repeated question");
                $seen[$key] = true;
                $letters[$answer] = ($letters[$answer] ?? 0) + 1;
            }
            $this->assertLessThanOrEqual(0.4 * count($rows), max($letters), "{$slug}: one answer letter is used for over 40% of questions");
        }
    }
}
