<?php

namespace Tests\Feature;

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
            $rows = require $file;
            $this->assertIsArray($rows, "{$slug}: the file must return a list of questions.");

            foreach ($rows as $i => $row) {
                $where = "{$slug} #".($i + 1);
                $this->assertCount(5, $row, "{$where}: expected [stem, options, answer, explanation, difficulty].");
                [$stem, $options, $answer, $explanation, $difficulty] = $row;

                $this->assertIsString($stem, $where);
                $this->assertGreaterThan(5, mb_strlen(trim($stem)), "{$where}: stem is empty.");
                $this->assertIsArray($options, $where);
                $this->assertContains(count($options), [4, 5], "{$where}: needs 4 or 5 options.");
                $this->assertSame(array_values($options), $options, "{$where}: options must be a plain list.");
                foreach ($options as $option) {
                    $this->assertIsString($option, $where);
                    $this->assertNotSame('', trim($option), "{$where}: empty option.");
                }
                $normalised = array_map(fn ($o) => mb_strtolower(trim($o)), $options);
                $this->assertSame(count($normalised), count(array_unique($normalised)), "{$where}: two options are the same.");
                $this->assertContains($answer, array_slice(['A', 'B', 'C', 'D', 'E'], 0, count($options)), "{$where}: answer must be one of the option letters.");
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
            if (count($rows) >= 20) {
                $top = max(array_count_values(array_column($rows, 2)));
                $this->assertLessThanOrEqual(0.4 * count($rows), $top, "{$slug}: one answer letter is used for over 40% of questions.");
            }
        }
    }
}
