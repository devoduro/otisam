<?php

namespace Database\Seeders;

use App\Models\Curriculum\ClassLevel;
use App\Models\Curriculum\CurriculumNode;
use App\Models\Curriculum\CurriculumVersion;
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
 * (one file per subject, the larger bank, grouped by topic). Each topic becomes a supplementary node in
 * a "Practice topics" curriculum version, so learners can practise one topic at a time; a topic named
 * like "SHS 2: Rigid motion" also files its questions under that year. Questions already in the bank
 * get a topic and year only if they have none, so an administrator's tagging stays. Not part of
 * DatabaseSeeder; run it on purpose:
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

    public const TOPIC_VERSION = 'Practice topics (Otisam)';

    /** @var array<string, CurriculumNode> */
    private array $nodes = [];

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
            foreach (self::rows($file) as ['row' => [$stem, $options, $answer, $explanation, $difficulty], 'topic' => $topic, 'year' => $year]) {
                $created += (int) $this->add($subject, $stem, $options, $answer, $explanation, $difficulty, $source);
                if ($topic !== null) {
                    $this->tag($subject, $stem, $topic, $year);
                }
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
        if (! Subject::where('slug', 'ges-promotion-aptitude')->exists()) {
            $subject = new Subject(['education_level_id' => $level->id, 'name' => 'GES Promotion (Aptitude)', 'category' => 'core', 'description' => 'Numerical, verbal and logical reasoning for all ranks: number work, word skills, analogies, series, coding and direction puzzles.', 'sort' => 2, 'is_active' => true]);
            $subject->slug = 'ges-promotion-aptitude';
            $subject->save();
        }
        if (! Subject::where('slug', 'ges-promotion-leadership')->exists()) {
            $subject = new Subject(['education_level_id' => $level->id, 'name' => 'GES Promotion (Leadership)', 'category' => 'core', 'description' => 'Leadership, administration, human resources, finance, policy, data and supervision for senior ranks.', 'sort' => 3, 'is_active' => true]);
            $subject->slug = 'ges-promotion-leadership';
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

    /**
     * The rows of a question file with their topic. A file returns either a plain list of rows or
     * ['Topic' => [rows…], …]; "SHS 2: Topic" gives the year as well.
     *
     * @return list<array{row: array, topic: ?string, year: ?string}>
     */
    public static function rows(string $file): array
    {
        $rows = [];
        foreach (require $file as $key => $value) {
            if (! is_string($key)) {
                $rows[] = ['row' => $value, 'topic' => null, 'year' => null];

                continue;
            }
            [$topic, $year] = self::splitTopic($key);
            foreach ($value as $row) {
                $rows[] = ['row' => $row, 'topic' => $topic, 'year' => $year];
            }
        }

        return $rows;
    }

    /** @return array{0: string, 1: ?string} topic title and class level name ("SHS 2"), if the key names one */
    public static function splitTopic(string $key): array
    {
        if (preg_match('/^((?:JHS|SHS) \d):\s*(.+)$/', trim($key), $m)) {
            return [trim($m[2]), $m[1]];
        }

        return [trim($key), null];
    }

    /** Files the question under its topic (and year), unless it is already tagged. */
    private function tag(Subject $subject, string $stem, string $topic, ?string $year): void
    {
        $question = Question::where('subject_id', $subject->id)->where('stem_hash', self::stemHash($stem))->first();
        if (! $question) {
            return;
        }
        $classLevel = $year ? ClassLevel::where('education_level_id', $subject->education_level_id)->where('name', $year)->first() : null;
        if (! $question->curriculumNodes()->exists()) {
            $question->curriculumNodes()->attach($this->topicNode($subject, $topic, $classLevel)->id);
        }
        if ($classLevel && ! $question->class_level_id) {
            $question->forceFill(['class_level_id' => $classLevel->id])->save();
        }
    }

    private function topicNode(Subject $subject, string $topic, ?ClassLevel $classLevel): CurriculumNode
    {
        $cacheKey = $subject->id.'|'.mb_strtolower($topic);
        if (isset($this->nodes[$cacheKey])) {
            return $this->nodes[$cacheKey];
        }

        $version = CurriculumVersion::firstOrCreate(
            ['education_level_id' => $subject->education_level_id, 'name' => self::TOPIC_VERSION],
            ['status' => 'active', 'notes' => 'Topic groups for the practice question bank, written by Otisam. Not the official curriculum document.'],
        );
        $node = CurriculumNode::where('curriculum_version_id', $version->id)->where('subject_id', $subject->id)
            ->whereNull('parent_id')->where('title', $topic)->first();
        if (! $node) {
            $node = new CurriculumNode(['class_level_id' => $classLevel?->id, 'node_type' => 'topic', 'title' => $topic, 'is_official' => false]);
            $node->forceFill([
                'curriculum_version_id' => $version->id,
                'subject_id' => $subject->id,
                'sort' => (int) CurriculumNode::where('curriculum_version_id', $version->id)->where('subject_id', $subject->id)->max('sort') + 1,
                'depth' => 0,
                'path' => '/',
            ])->save();
        }

        return $this->nodes[$cacheKey] = $node;
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
