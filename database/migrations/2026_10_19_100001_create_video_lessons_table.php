<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Video lessons: YouTube videos embedded (never downloaded or re-hosted), organised by subject and
 * topic. Staff publish them directly; teacher submissions and moderation come with P9.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('video_lessons', function (Blueprint $table) {
            $table->id();
            $table->string('title', 200);
            $table->string('slug', 220)->unique();
            $table->text('description')->nullable();
            $table->char('youtube_id', 11);
            $table->foreignId('subject_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('class_level_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('curriculum_node_id')->nullable()->constrained()->nullOnDelete();
            $table->unsignedSmallInteger('duration_minutes')->nullable();
            $table->string('instructor', 120)->nullable();
            $table->string('access_level', 10)->default('free');
            // draft | published | archived
            $table->string('status', 20)->default('draft');
            $table->dateTime('published_at')->nullable();
            $table->unsignedInteger('view_count')->default(0);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'subject_id', 'published_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('video_lessons');
    }
};
