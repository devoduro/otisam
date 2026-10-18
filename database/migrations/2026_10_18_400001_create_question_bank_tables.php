<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Question bank (BLUEPRINT §5.2, §8.1).
 *
 * - questions: identity and metadata only.
 * - question_revisions: the content. Never edited in place: a content change adds a revision, so
 *   a paper someone has already sat (P6 pins revision ids) never changes underneath them.
 * - question_keys: the answers, one row per revision, in their own table so that no query that
 *   builds what a candidate sees can join them by accident.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->ulid('ulid')->unique();
            $table->string('type', 30);
            $table->foreignId('education_level_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('subject_id')->constrained()->restrictOnDelete();
            $table->foreignId('class_level_id')->nullable()->constrained()->nullOnDelete();
            $table->unsignedTinyInteger('difficulty')->default(3); // 1 (easy) … 5 (hard)
            $table->decimal('default_marks', 6, 2)->default(1);
            // draft | in_review | changes_requested | published | archived
            $table->string('status', 20)->default('draft');
            $table->string('access_level', 10)->default('free');
            // practice (self-test) | exam (formal assessments only) | promotion (teacher professional exams)
            $table->string('confidentiality', 12)->default('practice');
            $table->foreignId('author_id')->nullable()->constrained('users')->nullOnDelete();
            // platform | teacher: who owns the question (teacher authoring arrives with the teacher portal)
            $table->string('owner_scope', 10)->default('platform');
            $table->string('source', 200)->nullable();
            $table->string('copyright_holder', 200)->nullable();
            $table->unsignedBigInteger('current_revision_id')->nullable();
            // sha256 of the normalised current stem; spots duplicates on import
            $table->char('stem_hash', 64)->nullable()->index();
            $table->text('review_note')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('reviewed_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'subject_id', 'type', 'difficulty'], 'questions_bank_index');
            $table->index(['confidentiality', 'status']);
        });

        Schema::create('question_revisions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('question_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('revision_no');
            $table->text('stem');
            // What a candidate may see: options, blank count, unit... Never correctness.
            $table->json('payload');
            $table->text('explanation')->nullable();
            // ulids of question_media attached to this revision
            $table->json('media')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('created_at')->useCurrent();

            $table->unique(['question_id', 'revision_no']);
        });

        Schema::table('questions', function (Blueprint $table) {
            $table->foreign('current_revision_id')->references('id')->on('question_revisions')->nullOnDelete();
        });

        Schema::create('question_keys', function (Blueprint $table) {
            $table->foreignId('question_revision_id')->primary()->constrained()->cascadeOnDelete();
            $table->json('key');
            $table->timestamp('created_at')->useCurrent();
        });

        Schema::create('question_media', function (Blueprint $table) {
            $table->id();
            $table->ulid('ulid')->unique();
            $table->foreignId('question_id')->constrained()->cascadeOnDelete();
            $table->string('disk', 20);
            $table->string('path', 255);
            $table->string('mime', 60);
            $table->unsignedInteger('size');
            $table->string('alt_text', 255);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('curriculum_node_question', function (Blueprint $table) {
            $table->foreignId('question_id')->constrained()->cascadeOnDelete();
            $table->foreignId('curriculum_node_id')->constrained()->cascadeOnDelete();

            $table->primary(['question_id', 'curriculum_node_id']);
            $table->index('curriculum_node_id');
        });

        Schema::create('question_imports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('original_name', 255);
            // validated | committed | rejected
            $table->string('status', 20);
            $table->unsignedInteger('total_rows')->default(0);
            $table->unsignedInteger('error_rows')->default(0);
            $table->unsignedInteger('committed_count')->default(0);
            $table->json('report')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('question_imports');
        Schema::dropIfExists('curriculum_node_question');
        Schema::dropIfExists('question_media');
        Schema::table('questions', fn (Blueprint $table) => $table->dropForeign(['current_revision_id']));
        Schema::dropIfExists('question_keys');
        Schema::dropIfExists('question_revisions');
        Schema::dropIfExists('questions');
    }
};
