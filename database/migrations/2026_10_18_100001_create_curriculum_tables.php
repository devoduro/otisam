<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Curriculum structure (BLUEPRINT §5.2): everything an administrator edits instead of code.
 * Education level → class levels / programmes / subjects; curriculum versions hold a topic tree
 * per subject (strand → sub-strand → indicator, or topic → subtopic for older syllabuses).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('education_levels', function (Blueprint $table) {
            $table->id();
            $table->string('name', 60)->unique();
            $table->string('code', 20)->unique();
            $table->unsignedSmallInteger('sort')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('class_levels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('education_level_id')->constrained()->restrictOnDelete();
            $table->string('name', 40);
            $table->unsignedSmallInteger('sort')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['education_level_id', 'name']);
        });

        Schema::create('programmes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('education_level_id')->constrained()->restrictOnDelete();
            $table->string('name', 80);
            $table->string('description', 500)->nullable();
            $table->unsignedSmallInteger('sort')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['education_level_id', 'name']);
        });

        Schema::create('subjects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('education_level_id')->constrained()->restrictOnDelete();
            $table->string('name', 100);
            $table->string('slug', 120)->unique();
            $table->string('code', 20)->nullable()->unique();
            // core = taken by every programme at this level; elective = programme-specific.
            $table->string('category', 20)->default('elective');
            $table->string('description', 500)->nullable();
            $table->unsignedSmallInteger('sort')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['education_level_id', 'name']);
        });

        Schema::create('programme_subject', function (Blueprint $table) {
            $table->foreignId('programme_id')->constrained()->cascadeOnDelete();
            $table->foreignId('subject_id')->constrained()->cascadeOnDelete();
            $table->boolean('is_core')->default(false);

            $table->primary(['programme_id', 'subject_id']);
            $table->index('subject_id');
        });

        Schema::create('curriculum_versions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('education_level_id')->constrained()->restrictOnDelete();
            $table->string('name', 120);
            // draft = being entered, hidden from the public; active = in use; retired = kept for history.
            $table->string('status', 20)->default('draft')->index();
            $table->date('effective_from')->nullable();
            $table->date('effective_to')->nullable();
            // Citation of the official document this version was entered from.
            $table->string('source_document', 500)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['education_level_id', 'name']);
        });

        Schema::create('curriculum_nodes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('curriculum_version_id')->constrained()->cascadeOnDelete();
            $table->foreignId('subject_id')->constrained()->restrictOnDelete();
            $table->foreignId('class_level_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('parent_id')->nullable()->constrained('curriculum_nodes')->cascadeOnDelete();
            $table->string('node_type', 30);
            $table->string('code', 40)->nullable();
            $table->string('title', 255);
            $table->text('statement')->nullable();
            // Official = taken word for word from the GES/NaCCA document; otherwise supplementary.
            $table->boolean('is_official')->default(false);
            $table->string('source_ref', 255)->nullable();
            $table->unsignedInteger('sort')->default(0);
            $table->unsignedTinyInteger('depth')->default(0);
            // Materialised ancestry, e.g. "/12/45/" for a node whose parent is 45 under 12. Used for subtree queries.
            $table->string('path', 255)->default('/')->index();
            $table->timestamps();

            $table->index(['curriculum_version_id', 'subject_id', 'parent_id', 'sort'], 'curriculum_nodes_tree_index');
            $table->unique(['curriculum_version_id', 'subject_id', 'code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('curriculum_nodes');
        Schema::dropIfExists('curriculum_versions');
        Schema::dropIfExists('programme_subject');
        Schema::dropIfExists('subjects');
        Schema::dropIfExists('programmes');
        Schema::dropIfExists('class_levels');
        Schema::dropIfExists('education_levels');
    }
};
