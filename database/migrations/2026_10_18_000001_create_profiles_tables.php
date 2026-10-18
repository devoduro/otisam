<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_profiles', function (Blueprint $table) {
            $table->foreignId('user_id')->primary()->constrained()->cascadeOnDelete();
            // Free text / config values until the curriculum module (P2) adds the level,
            // class and programme tables; P2 migrates these into foreign keys.
            $table->string('class_level', 30)->nullable();
            $table->string('programme', 80)->nullable();
            $table->string('school_name')->nullable();
            $table->string('region', 40)->nullable();
            $table->boolean('is_minor')->default(true);
            $table->string('guardian_name')->nullable();
            $table->string('guardian_phone', 15)->nullable();
            $table->timestamp('guardian_consent_at')->nullable();
            $table->timestamps();
        });

        Schema::create('teacher_profiles', function (Blueprint $table) {
            $table->foreignId('user_id')->primary()->constrained()->cascadeOnDelete();
            $table->string('staff_number', 30)->nullable()->unique();
            $table->string('current_rank', 80)->nullable();
            $table->string('school_name')->nullable();
            $table->string('region', 40)->nullable();
            $table->string('district', 80)->nullable();
            $table->string('subjects_taught')->nullable();
            $table->string('verification_status', 20)->default('pending')->index();
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('verified_at')->nullable();
            $table->text('decision_note')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('teacher_profiles');
        Schema::dropIfExists('student_profiles');
    }
};
