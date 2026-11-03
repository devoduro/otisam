<?php

namespace Tests\Concerns;

use App\Models\Curriculum\CurriculumNode;
use App\Models\Curriculum\CurriculumVersion;
use App\Models\Curriculum\Subject;
use App\Models\QuestionBank\Question;
use App\Models\QuestionBank\QuestionKey;
use App\Models\QuestionBank\QuestionRevision;
use Illuminate\Support\Str;

/** Small, fast question-bank fixtures: a topic with multiple-choice questions whose answer is B. */
trait CreatesTopicQuestions
{
    protected function topicWithQuestions(Subject $subject, string $title, int $count, ?CurriculumNode $parent = null, array $question = []): CurriculumNode
    {
        $version = CurriculumVersion::firstOrCreate(['education_level_id' => $subject->education_level_id, 'name' => 'Test topics'], ['status' => 'active']);
        $node = new CurriculumNode(['node_type' => 'topic', 'title' => $title, 'is_official' => false]);
        $node->forceFill([
            'curriculum_version_id' => $version->id, 'subject_id' => $subject->id, 'parent_id' => $parent?->id, 'sort' => 1,
            'depth' => $parent ? $parent->depth + 1 : 0, 'path' => $parent ? $parent->childPath() : '/',
        ])->save();

        for ($i = 1; $i <= $count; $i++) {
            $this->bankQuestion($subject, "{$title} question {$i}", $question)->curriculumNodes()->attach($node->id);
        }

        return $node;
    }

    protected function bankQuestion(Subject $subject, string $stem, array $attributes = []): Question
    {
        $question = new Question;
        $question->forceFill($attributes + [
            'type' => 'mcq', 'education_level_id' => $subject->education_level_id, 'subject_id' => $subject->id, 'difficulty' => 2,
            'default_marks' => 1, 'status' => 'published', 'access_level' => 'free', 'confidentiality' => 'practice',
            'owner_scope' => 'platform', 'source' => 'test', 'stem_hash' => hash('sha256', Str::lower($stem)),
        ])->save();
        $revision = new QuestionRevision;
        $revision->forceFill(['question_id' => $question->id, 'revision_no' => 1, 'stem' => $stem, 'payload' => ['options' => [
            ['id' => 'A', 'text' => 'wrong one'], ['id' => 'B', 'text' => 'right one'], ['id' => 'C', 'text' => 'wrong two'], ['id' => 'D', 'text' => 'wrong three'],
        ]]])->save();
        (new QuestionKey)->forceFill(['question_revision_id' => $revision->id, 'key' => ['correct' => 'B']])->save();
        $question->forceFill(['current_revision_id' => $revision->id])->save();

        return $question;
    }
}
