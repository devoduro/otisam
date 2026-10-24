<?php

namespace Tests\Feature;

use App\Models\Curriculum\ClassLevel;
use App\Models\Curriculum\Subject;
use Database\Seeders\CurriculumSubjectsSeeder;
use Database\Seeders\DemoQuestionSeeder;
use Tests\TestCase;

/** Checks every row in the question files before it can reach a student. */
class QuestionBankDataTest extends TestCase
{
    public function test_every_question_file_is_well_formed(): void
    {
        $this->seed(CurriculumSubjectsSeeder::class);
        $files = DemoQuestionSeeder::subjectFiles();
        $this->assertNotEmpty($files);

        // Stems already in the first demo sets, so the subject files do not repeat them.
        $seen = [];
        foreach (array_keys(DemoQuestionSeeder::SOURCES) as $set) {
            foreach (require database_path("seeders/data/demo-questions-{$set}.php") as $slug => $rows) {
                foreach ($rows as $row) {
                    $seen[$slug][DemoQuestionSeeder::stemHash($row[0])] = true;
                }
            }
        }

        foreach ($files as $slug => $file) {
            $this->assertTrue(Subject::where('slug', $slug)->exists(), "{$slug}: no subject with this slug.");
            $this->assertIsArray(require $file, "{$slug}: the file must return the questions grouped by topic.");
            $tagged = DemoQuestionSeeder::rows($file);
            $rows = array_column($tagged, 'row');
            // Every question needs a topic so learners can practise one topic at a time.
            $this->assertNotContains(null, array_column($tagged, 'topic'), "{$slug}: group the questions under topic names.");
            foreach (array_filter(array_unique(array_column($tagged, 'year'))) as $year) {
                $this->assertTrue(ClassLevel::where('name', $year)->exists(), "{$slug}: no class level called {$year}.");
            }

            foreach ($rows as $i => $row) {
                $where = "{$slug} #".($i + 1);
                $this->assertContains(count($row), [5, 6, 7], "{$where}: expected [stem, options, answer, explanation, difficulty, type?, marks?].");
                [$stem, $options, $answer, $explanation, $difficulty] = $row;
                $kind = $row[5] ?? 'mcq';
                $this->assertContains($kind, ['mcq', 'tf', 'fill', 'theory'], "{$where}: unknown question type.");

                $this->assertIsString($stem, $where);
                $this->assertGreaterThan(5, mb_strlen(trim($stem)), "{$where}: stem is empty.");
                $this->assertIsArray($options, $where);
                $this->assertSame(array_values($options), $options, "{$where}: options must be a plain list.");
                foreach ($options as $option) {
                    $this->assertIsString($option, $where);
                    $this->assertNotSame('', trim($option), "{$where}: empty option.");
                }
                $normalised = array_map(fn ($o) => mb_strtolower(trim($o)), $options);
                if (in_array($kind, ['mcq', 'tf'], true)) {
                    $this->assertSame(count($normalised), count(array_unique($normalised)), "{$where}: two options are the same.");
                }
                if ($kind === 'mcq') {
                    $this->assertContains(count($options), [4, 5], "{$where}: needs 4 or 5 options.");
                    $this->assertContains($answer, array_slice(['A', 'B', 'C', 'D', 'E'], 0, count($options)), "{$where}: answer must be one of the option letters.");
                } elseif ($kind === 'tf') {
                    $this->assertSame(['True', 'False'], $options, "{$where}: true/false options must be ['True', 'False'].");
                    $this->assertContains($answer, ['A', 'B'], $where);
                } elseif ($kind === 'fill') {
                    $this->assertStringContainsString('___', $stem, "{$where}: a fill-in stem needs ___ for the blank.");
                    $this->assertGreaterThanOrEqual(1, count($options), "{$where}: list the accepted answers.");
                } else {
                    $this->assertGreaterThan(20, mb_strlen(trim($answer)), "{$where}: a theory question needs a model answer.");
                    $this->assertGreaterThanOrEqual(1, count($options), "{$where}: list the marking points.");
                    $this->assertContains($row[6] ?? 5, [2, 3, 4, 5, 6, 8, 10], "{$where}: marks.");
                }
                $this->assertIsString($explanation, $where);
                $this->assertGreaterThan(5, mb_strlen(trim($explanation)), "{$where}: explanation is empty.");
                $this->assertIsInt($difficulty, $where);
                $this->assertGreaterThanOrEqual(1, $difficulty, $where);
                $this->assertLessThanOrEqual(5, $difficulty, $where);

                $hash = DemoQuestionSeeder::stemHash($stem);
                $this->assertArrayNotHasKey($hash, $seen[$slug] ?? [], "{$where}: this question is already in the bank.");
                $seen[$slug][$hash] = true;
            }

            // The right answer should not give itself away by usually sitting in the same place.
            $mcq = array_filter($rows, fn ($r) => ($r[5] ?? 'mcq') === 'mcq');
            if (count($mcq) >= 20) {
                $top = max(array_count_values(array_column($mcq, 2)));
                $this->assertLessThanOrEqual(0.4 * count($mcq), $top, "{$slug}: one answer letter is used for over 40% of questions.");
            }
        }
    }
}
