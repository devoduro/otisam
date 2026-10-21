<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Fixed practice papers: numbered sets of objective questions per subject, laid out like a
 * WASSCE (SHS) or BECE (JHS) objective paper. Built from Otisam's own practice questions by
 * App\Services\Practice\PaperBuilder; they are not WAEC past papers.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exam_papers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subject_id')->constrained()->restrictOnDelete();
            $table->unsignedSmallInteger('number');
            $table->string('title', 160);
            // WASSCE | BECE | GES promotion: the exam the paper imitates.
            $table->string('exam_style', 30);
            $table->unsignedSmallInteger('duration_minutes');
            // draft (hidden) | published
            $table->string('status', 20)->default('published');
            $table->dateTime('published_at')->nullable();
            $table->timestamps();

            $table->unique(['subject_id', 'number']);
            $table->index(['status', 'subject_id']);
        });

        Schema::create('exam_paper_question', function (Blueprint $table) {
            $table->foreignId('exam_paper_id')->constrained()->cascadeOnDelete();
            $table->foreignId('question_id')->constrained()->cascadeOnDelete();
            $table->unsignedSmallInteger('position');

            $table->primary(['exam_paper_id', 'position']);
            $table->unique(['exam_paper_id', 'question_id']);
            $table->index('question_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exam_paper_question');
        Schema::dropIfExists('exam_papers');
    }
};
