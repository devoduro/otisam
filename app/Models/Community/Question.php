<?php

namespace App\Models\Community;

use App\Models\Curriculum\ClassLevel;
use App\Models\Curriculum\EducationLevel;
use App\Models\Curriculum\Subject;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

/** A community Q&A question (not the question bank: see App\Models\QuestionBank). */
class Question extends Model
{
    use Moderated;

    protected $table = 'qa_questions';

    protected $fillable = ['title', 'body', 'education_level_id', 'class_level_id', 'subject_id'];

    protected function casts(): array
    {
        return ['edited_at' => 'datetime'];
    }

    public function educationLevel(): BelongsTo
    {
        return $this->belongsTo(EducationLevel::class);
    }

    public function classLevel(): BelongsTo
    {
        return $this->belongsTo(ClassLevel::class);
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function answers(): HasMany
    {
        return $this->hasMany(Answer::class, 'qa_question_id');
    }

    public function acceptedAnswer(): BelongsTo
    {
        return $this->belongsTo(Answer::class, 'accepted_answer_id');
    }

    /** "SHS · SHS 2 · Core Mathematics" or whichever parts are set. */
    public function tagLabel(): string
    {
        return collect([$this->educationLevel?->code, $this->classLevel?->name, $this->subject?->name])->filter()->implode(' · ');
    }

    public function label(): string
    {
        return 'Question: '.Str::limit($this->title, 80);
    }
}
