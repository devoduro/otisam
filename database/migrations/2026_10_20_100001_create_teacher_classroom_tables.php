<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Teacher tools:
 * - classrooms: a teacher's class; students join with a short code.
 * - assignments: work set for a class, either a written assignment (text and/or file answer) or a
 *   quiz (objective questions from the bank or written by the teacher, marked automatically).
 * - submissions: one per student per assignment.
 * - teacher_materials: lesson plans, notes, schemes of work… a teacher keeps private or shares
 *   with other teachers.
 * - users.referral_code / referred_by: teachers invite colleagues with a link.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('classrooms', function (Blueprint $table) {
            $table->id();
            $table->ulid('ulid')->unique();
            $table->foreignId('teacher_id')->constrained('users')->cascadeOnDelete();
            $table->string('name', 120);
            $table->foreignId('subject_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('class_level_id')->nullable()->constrained()->nullOnDelete();
            $table->string('description', 500)->nullable();
            $table->string('join_code', 8)->unique();
            $table->boolean('joining_open')->default(true);
            $table->dateTime('archived_at')->nullable();
            $table->timestamps();
            $table->index(['teacher_id', 'archived_at']);
        });

        Schema::create('classroom_student', function (Blueprint $table) {
            $table->foreignId('classroom_id')->constrained()->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('users')->cascadeOnDelete();
            $table->dateTime('joined_at');
            $table->primary(['classroom_id', 'student_id']);
            $table->index('student_id');
        });

        Schema::create('assignments', function (Blueprint $table) {
            $table->id();
            $table->ulid('ulid')->unique();
            $table->foreignId('classroom_id')->constrained()->cascadeOnDelete();
            $table->foreignId('teacher_id')->constrained('users')->cascadeOnDelete();
            // assignment (written work) | quiz (auto-marked objective questions)
            $table->string('type', 12);
            $table->string('title', 150);
            $table->text('instructions')->nullable();
            $table->dateTime('due_at')->nullable();
            $table->unsignedSmallInteger('time_limit_minutes')->nullable();
            $table->decimal('max_marks', 6, 2)->nullable();
            // draft | published | closed
            $table->string('status', 12)->default('draft');
            $table->dateTime('published_at')->nullable();
            // Quiz: let students see the correct answers after they submit.
            $table->boolean('show_answers')->default(true);
            // Optional handout: private library disk.
            $table->string('file_path', 255)->nullable();
            $table->string('file_name', 200)->nullable();
            $table->string('file_mime', 120)->nullable();
            $table->unsignedInteger('file_size')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['classroom_id', 'status', 'due_at']);
        });

        Schema::create('assignment_question', function (Blueprint $table) {
            $table->foreignId('assignment_id')->constrained()->cascadeOnDelete();
            $table->foreignId('question_id')->constrained()->restrictOnDelete();
            // The revision the quiz was built with, so later edits never change a set quiz.
            $table->foreignId('question_revision_id')->constrained()->restrictOnDelete();
            $table->unsignedSmallInteger('sort')->default(0);
            $table->decimal('marks', 6, 2)->default(1);
            $table->primary(['assignment_id', 'question_id']);
        });

        Schema::create('submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assignment_id')->constrained()->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('users')->cascadeOnDelete();
            $table->dateTime('started_at')->nullable();
            $table->dateTime('submitted_at')->nullable();
            $table->boolean('is_late')->default(false);
            // Quiz answers: {question ulid: option id}
            $table->json('answers')->nullable();
            $table->text('body')->nullable();
            $table->string('file_path', 255)->nullable();
            $table->string('file_name', 200)->nullable();
            $table->string('file_mime', 120)->nullable();
            $table->unsignedInteger('file_size')->nullable();
            $table->decimal('score', 6, 2)->nullable();
            $table->decimal('max_score', 6, 2)->nullable();
            $table->text('feedback')->nullable();
            $table->dateTime('graded_at')->nullable();
            $table->timestamps();
            $table->unique(['assignment_id', 'student_id']);
            $table->index('student_id');
        });

        Schema::create('teacher_materials', function (Blueprint $table) {
            $table->id();
            $table->ulid('ulid')->unique();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            // lesson_plan | lesson_note | scheme_of_work | worksheet | slides | assessment | other
            $table->string('kind', 20);
            $table->string('title', 150);
            $table->text('description')->nullable();
            $table->foreignId('subject_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('class_level_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('curriculum_node_id')->nullable()->constrained()->nullOnDelete();
            // private (only me) | teachers (all approved teachers)
            $table->string('visibility', 10)->default('private');
            $table->string('file_path', 255);
            $table->string('file_name', 200);
            $table->string('file_mime', 120);
            $table->unsignedInteger('file_size');
            $table->unsignedInteger('download_count')->default(0);
            $table->timestamps();
            $table->softDeletes();
            $table->index(['visibility', 'subject_id', 'kind']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('referral_code', 12)->nullable()->unique()->after('account_type');
            $table->foreignId('referred_by')->nullable()->after('referral_code')->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('referred_by');
            $table->dropColumn('referral_code');
        });
        Schema::dropIfExists('teacher_materials');
        Schema::dropIfExists('submissions');
        Schema::dropIfExists('assignment_question');
        Schema::dropIfExists('assignments');
        Schema::dropIfExists('classroom_student');
        Schema::dropIfExists('classrooms');
    }
};
