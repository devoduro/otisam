<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * One row per question answered in a saved attempt, so staff can see which questions a student
 * got wrong and how often they have tried them. Attempts saved before this table existed have
 * a score only.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quiz_attempt_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quiz_attempt_id')->constrained()->cascadeOnDelete();
            $table->foreignId('question_id')->constrained()->cascadeOnDelete();
            // What the learner answered: an option letter, typed words, or a theory answer.
            $table->text('given')->nullable();
            // Null for theory, which the computer does not mark.
            $table->boolean('is_correct')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index('question_id');
            $table->index(['quiz_attempt_id', 'is_correct']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quiz_attempt_answers');
    }
};
