<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Public teacher profiles (/teachers): a teacher chooses to be listed, with a photo, a headline
 * and a short bio. Teacher materials can now also be public (shown on /downloads).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('teacher_profiles', function (Blueprint $table) {
            $table->boolean('is_public')->default(false)->after('subjects_taught');
            $table->string('slug', 140)->nullable()->unique()->after('is_public');
            $table->string('headline', 120)->nullable()->after('slug');
            $table->text('bio')->nullable()->after('headline');
            $table->string('photo_path', 255)->nullable()->after('bio');
            $table->unsignedSmallInteger('years_teaching')->nullable()->after('photo_path');
            $table->index(['is_public', 'region']);
        });
    }

    public function down(): void
    {
        Schema::table('teacher_profiles', function (Blueprint $table) {
            $table->dropIndex(['is_public', 'region']);
            $table->dropUnique(['slug']);
            $table->dropColumn(['is_public', 'slug', 'headline', 'bio', 'photo_path', 'years_teaching']);
        });
    }
};
