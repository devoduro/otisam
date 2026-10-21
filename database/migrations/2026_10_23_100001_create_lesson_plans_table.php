<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Weekly lesson plans written by teachers (and staff). Organised by subject, year and teaching
 * week, optionally tied to a curriculum node; the strand/sub-strand text is kept on the plan so a
 * plan still reads correctly if the tree is edited later. Teachers' plans are reviewed before
 * they appear publicly, like their video tutorials.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lesson_plans', function (Blueprint $table) {
            $table->id();
            $table->string('title', 200);
            $table->string('slug', 220)->unique();
            $table->foreignId('subject_id')->constrained()->restrictOnDelete();
            $table->foreignId('class_level_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('curriculum_node_id')->nullable()->constrained()->nullOnDelete();
            $table->unsignedTinyInteger('term')->nullable();
            $table->unsignedTinyInteger('week')->nullable();
            $table->string('strand', 200)->nullable();
            $table->string('sub_strand', 200)->nullable();
            $table->text('content_standard')->nullable();
            $table->text('indicators')->nullable();
            $table->text('core_competencies')->nullable();
            $table->string('key_vocabulary', 500)->nullable();
            $table->unsignedTinyInteger('lessons_per_week')->nullable();
            $table->unsignedSmallInteger('duration_minutes')->nullable();
            $table->unsignedSmallInteger('class_size')->nullable();
            $table->text('resources')->nullable();
            // The three lesson phases.
            $table->text('starter')->nullable();
            $table->text('main_activities');
            $table->text('reflection')->nullable();
            $table->text('assessment')->nullable();
            $table->text('differentiation')->nullable();
            $table->text('homework')->nullable();
            $table->text('teacher_notes')->nullable();
            // draft (private) | pending (awaiting review) | published | archived
            $table->string('status', 20)->default('draft');
            $table->string('review_note', 500)->nullable();
            $table->dateTime('published_at')->nullable();
            $table->unsignedInteger('view_count')->default(0);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'subject_id', 'class_level_id', 'week']);
            $table->index(['created_by', 'updated_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lesson_plans');
    }
};
