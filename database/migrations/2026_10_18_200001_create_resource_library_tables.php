<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Resource library (BLUEPRINT §5.2): past questions, solutions, marking schemes, notes and other
 * documents. Files live on a private disk and are only ever served through the access policy.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('resource_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name', 80)->unique();
            $table->string('slug', 90)->unique();
            // student | teacher | both: who the category is meant for by default.
            $table->string('audience', 10)->default('student');
            // Shows the exam fields (exam type, year, paper) on the resource form.
            $table->boolean('is_exam_paper')->default(false);
            $table->unsignedSmallInteger('sort')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('exam_types', function (Blueprint $table) {
            $table->id();
            $table->string('name', 80)->unique();
            $table->string('code', 20)->unique();
            $table->unsignedSmallInteger('sort')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('resources', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('resource_categories')->restrictOnDelete();
            $table->string('title', 200);
            $table->string('slug', 220)->unique();
            $table->text('description')->nullable();
            $table->string('audience', 10)->default('student');
            $table->foreignId('education_level_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('class_level_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('programme_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('subject_id')->nullable()->constrained()->restrictOnDelete();
            $table->foreignId('exam_type_id')->nullable()->constrained()->nullOnDelete();
            $table->unsignedSmallInteger('exam_year')->nullable();
            $table->string('paper_number', 20)->nullable();
            $table->foreignId('curriculum_version_id')->nullable()->constrained()->nullOnDelete();
            // free | premium
            $table->string('access_level', 10)->default('premium');
            // Pages shown to people without full access; null = the library default setting.
            $table->unsignedTinyInteger('preview_pages')->nullable();
            // draft | in_review | published | archived
            $table->string('status', 20)->default('draft');
            $table->dateTime('publish_at')->nullable();
            $table->dateTime('published_at')->nullable();
            $table->foreignId('author_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('copyright_holder', 200)->nullable();
            $table->string('licence', 120)->nullable();
            $table->string('permission_reference', 255)->nullable();
            $table->unsignedInteger('view_count')->default(0);
            $table->unsignedInteger('download_count')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'subject_id', 'category_id', 'exam_year'], 'resources_library_index');
            $table->index(['status', 'publish_at']);
        });

        Schema::create('resource_files', function (Blueprint $table) {
            $table->id();
            $table->ulid('uuid')->unique();
            $table->foreignId('resource_id')->constrained()->cascadeOnDelete();
            // main = the full document; preview = the shortened copy shown without full access.
            $table->string('role', 10);
            $table->string('disk', 20);
            $table->string('path', 255);
            $table->string('original_name', 255);
            $table->string('mime', 120);
            $table->string('extension', 10);
            $table->unsignedBigInteger('size');
            $table->char('sha256', 64)->index();
            $table->unsignedSmallInteger('page_count')->nullable();
            $table->unsignedSmallInteger('version')->default(1);
            $table->boolean('is_current')->default(true);
            // For main files: pending | ready | failed | not_applicable (preview generation state).
            $table->string('preview_status', 20)->default('not_applicable');
            $table->string('preview_error', 255)->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['resource_id', 'role', 'is_current']);
        });

        // "linked_resource_id is the <relation> of resource_id", e.g. a solution of a paper.
        Schema::create('resource_links', function (Blueprint $table) {
            $table->id();
            $table->foreignId('resource_id')->constrained()->cascadeOnDelete();
            $table->foreignId('linked_resource_id')->constrained('resources')->cascadeOnDelete();
            $table->string('relation', 20);
            $table->timestamps();

            $table->unique(['resource_id', 'linked_resource_id', 'relation']);
            $table->index('linked_resource_id');
        });

        Schema::create('curriculum_node_resource', function (Blueprint $table) {
            $table->foreignId('resource_id')->constrained()->cascadeOnDelete();
            $table->foreignId('curriculum_node_id')->constrained()->cascadeOnDelete();

            $table->primary(['resource_id', 'curriculum_node_id']);
            $table->index('curriculum_node_id');
        });

        Schema::create('resource_access_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('resource_id')->constrained()->cascadeOnDelete();
            $table->foreignId('resource_file_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            // view_full | view_preview | download
            $table->string('action', 20);
            $table->string('ip_address', 45)->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['resource_id', 'created_at']);
            $table->index(['user_id', 'created_at']);
        });

        Schema::create('takedown_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('resource_id')->nullable()->constrained()->nullOnDelete();
            $table->string('resource_url', 500)->nullable();
            $table->string('name', 120);
            $table->string('email', 150);
            $table->string('phone', 20)->nullable();
            $table->string('rights_holder', 200);
            $table->text('details');
            // open | resolved | rejected
            $table->string('status', 20)->default('open')->index();
            $table->text('resolution_note')->nullable();
            $table->foreignId('handled_by')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('handled_at')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('takedown_requests');
        Schema::dropIfExists('resource_access_logs');
        Schema::dropIfExists('curriculum_node_resource');
        Schema::dropIfExists('resource_links');
        Schema::dropIfExists('resource_files');
        Schema::dropIfExists('resources');
        Schema::dropIfExists('exam_types');
        Schema::dropIfExists('resource_categories');
    }
};
