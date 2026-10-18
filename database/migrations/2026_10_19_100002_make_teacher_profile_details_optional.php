<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Teacher sign-up now asks only for a phone number and password (region and district optional).
 * School and subjects are filled in later on the teacher's profile, before verification.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('teacher_profiles', function (Blueprint $table) {
            $table->string('school_name')->nullable()->change();
            $table->string('region', 40)->nullable()->change();
            $table->string('subjects_taught')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('teacher_profiles', function (Blueprint $table) {
            $table->string('school_name')->nullable(false)->default('')->change();
            $table->string('region', 40)->nullable(false)->default('')->change();
            $table->string('subjects_taught')->nullable(false)->default('')->change();
        });
    }
};
