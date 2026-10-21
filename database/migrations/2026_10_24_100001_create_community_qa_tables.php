<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Community Q&A: students and teachers ask questions, others answer. Plain text only. Posts are
 * hidden when enough people report them (platform.qa.hide_after_reports) or when a moderator
 * hides them; staff with tutorials.manage review the reports.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('qa_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('education_level_id')->constrained()->restrictOnDelete();
            $table->foreignId('class_level_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('subject_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title', 200);
            $table->text('body');
            // visible | hidden (by reports or a moderator; see hidden_reason)
            $table->string('status', 20)->default('visible');
            $table->string('hidden_reason', 20)->nullable();
            $table->unsignedBigInteger('accepted_answer_id')->nullable();
            $table->dateTime('edited_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'created_at']);
            $table->index(['education_level_id', 'subject_id', 'status']);
            $table->index(['user_id', 'created_at']);
        });

        Schema::create('qa_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('qa_question_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->text('body');
            $table->string('status', 20)->default('visible');
            $table->string('hidden_reason', 20)->nullable();
            $table->dateTime('edited_at')->nullable();
            $table->timestamps();

            $table->index(['qa_question_id', 'status', 'created_at']);
        });

        Schema::table('qa_questions', function (Blueprint $table) {
            $table->foreign('accepted_answer_id')->references('id')->on('qa_answers')->nullOnDelete();
        });

        // One report per person per post; resolved when a moderator acts on the post.
        Schema::create('qa_reports', function (Blueprint $table) {
            $table->id();
            $table->morphs('reportable');
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('reason', 20);
            $table->string('note', 300)->nullable();
            $table->dateTime('resolved_at')->nullable();
            $table->foreignId('resolved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['reportable_type', 'reportable_id', 'user_id']);
            $table->index(['resolved_at', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::table('qa_questions', fn (Blueprint $table) => $table->dropForeign(['accepted_answer_id']));
        Schema::dropIfExists('qa_reports');
        Schema::dropIfExists('qa_answers');
        Schema::dropIfExists('qa_questions');
    }
};
