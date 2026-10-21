<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Scores of signed-in students: one row per marked practice set or exam-mode paper. Feeds
 * "My progress", best scores on paper lists and the leaderboard.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quiz_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('subject_id')->constrained()->cascadeOnDelete();
            // paper (exam mode of a practice paper) | practice (a practice set)
            $table->string('kind', 20);
            $table->foreignId('exam_paper_id')->nullable()->constrained()->nullOnDelete();
            $table->unsignedSmallInteger('score');
            $table->unsignedSmallInteger('total');
            $table->unsignedInteger('seconds')->nullable();
            $table->boolean('late')->default(false);
            $table->timestamp('created_at')->useCurrent();

            $table->index(['user_id', 'created_at']);
            $table->index(['created_at', 'subject_id']);
            $table->index(['exam_paper_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quiz_attempts');
    }
};
