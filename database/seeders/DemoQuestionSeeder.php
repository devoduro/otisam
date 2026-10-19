<?php

namespace Database\Seeders;

use App\Models\Curriculum\EducationLevel;
use App\Models\Curriculum\Subject;
use App\Models\QuestionBank\Question;
use App\Models\QuestionBank\QuestionKey;
use App\Models\QuestionBank\QuestionRevision;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Demo practice questions (WASSCE-style for SHS, BECE-style for JHS), written for Otisam, so the
 * practice pages have something to show. Published as free practice questions and labelled as
 * demo content in `source`. Safe to run again: a question whose stem is already in the bank for that
 * subject is skipped.
 *
 * Questions live in data/demo-questions-{set}.php (the first small sets) and data/questions/{slug}.php
 * (one file per subject, the larger bank). Both use the same row format. Not part of DatabaseSeeder;
 * run it on purpose:
 *
 *     php artisan db:seed --class=DemoQuestionSeeder
 */
class DemoQuestionSeeder extends Seeder
{
    public const SOURCES = [
        'shs' => 'Demo question, WASSCE-style (written by Otisam)',
        'jhs' => 'Demo question, BECE-style (written by Otisam)',
        'promotion' => 'Demo question, GES promotion exam style (written by Otisam)',
    ];

    public function run(): void
    {
        $created = 0;
        $missing = [];
        $this->ensurePromotionSubject();

        foreach (self::SOURCES as $set => $source) {
            foreach (require __DIR__."/data/demo-questions-{$set}.php" as $slug => $questions) {
                $subject = Subject::where('slug', $slug)->first();
                if (! $subject) {
                    $missing[] = $slug;

                    continue;
                }
                foreach ($questions as [$stem, $options, $answer, $explanation, $difficulty]) {
                    $created += (int) $this->add($subject, $stem, $options, $answer, $explanation, $difficulty, $source);
                }
            }
        }

        foreach (self::subjectFiles() as $slug => $file) {
            $subject = Subject::with('educationLevel')->where('slug', $slug)->first();
            if (! $subject) {
                $missing[] = $slug;

                continue;
            }
            $source = self::SOURCES[match ($subject->educationLevel?->code) {
                'JHS' => 'jhs',
                EducationLevel::PROFESSIONAL => 'promotion',
                default => 'shs',
            }];
            foreach (require $file as [$stem, $options, $answer, $explanation, $difficulty]) {
                $created += (int) $this->add($subject, $stem, $options, $answer, $explanation, $difficulty, $source);
            }
        }

        $this->command?->info("Demo questions: {$created} added.");
        if ($missing) {
            $this->command?->warn('Subjects not found (run CurriculumSubjectsSeeder first): '.implode(', ', $missing));
        }
    }

    /**
     * Promotion questions need a subject. It lives under a level that stays switched off, so it never
     * appears in the public subject lists; teachers reach it from the Promotion page.
     */
    private function ensurePromotionSubject(): void
    {
        $level = EducationLevel::firstOrCreate(['code' => EducationLevel::PROFESSIONAL], ['name' => 'Teacher professional', 'sort' => 9, 'is_active' => false]);
        if (! Subject::where('slug', 'ges-promotion-general')->exists()) {
            $subject = new Subject(['education_level_id' => $level->id, 'name' => 'GES Promotion (General)', 'category' => 'core', 'description' => 'General paper for all ranks: education acts and agencies, teacher standards, curriculum, educational psychology, assessment and school management.', 'sort' => 1, 'is_active' => true]);
            $subject->slug = 'ges-promotion-general';
            $subject->save();
        }
    }

    /** @return array<string, string> subject slug => path of its question file */
    public static function subjectFiles(): array
    {
        $files = [];
        foreach (glob(__DIR__.'/data/questions/*.php') ?: [] as $path) {
            $files[basename($path, '.php')] = $path;
        }

        return $files;
    }

    public static function stemHash(string $stem): string
    {
        return hash('sha256', Str::of($stem)->lower()->squish()->toString());
    }

    private function add(Subject $subject, string $stem, array $options, string $answer, string $explanation, int $difficulty, string $source): bool
    {
        $hash = self::stemHash($stem);
        if (Question::withTrashed()->where('subject_id', $subject->id)->where('stem_hash', $hash)->exists()) {
            return false;
        }

        $letters = ['A', 'B', 'C', 'D', 'E'];
        $choices = [];
        foreach (array_values($options) as $i => $text) {
            $choices[] = ['id' => $letters[$i], 'text' => $text];
        }

        DB::transaction(function () use ($subject, $stem, $choices, $answer, $explanation, $difficulty, $source, $hash) {
            $question = new Question;
            $question->forceFill([
                'type' => 'mcq',
                'education_level_id' => $subject->education_level_id,
                'subject_id' => $subject->id,
                'difficulty' => max(1, min(5, $difficulty)),
                'default_marks' => 1,
                'status' => 'published',
                'access_level' => 'free',
                'confidentiality' => $subject->isProfessional() ? 'promotion' : 'practice',
                'owner_scope' => 'platform',
                'source' => $source,
                'copyright_holder' => 'Otisam',
                'stem_hash' => $hash,
            ])->save();

            $revision = new QuestionRevision;
            $revision->forceFill([
                'question_id' => $question->id,
                'revision_no' => 1,
                'stem' => $stem,
                'payload' => ['options' => $choices],
                'explanation' => $explanation,
            ])->save();

            $key = new QuestionKey;
            $key->forceFill(['question_revision_id' => $revision->id, 'key' => ['correct' => $answer]])->save();

            $question->forceFill(['current_revision_id' => $revision->id])->save();
        });

        return true;
    }
}
