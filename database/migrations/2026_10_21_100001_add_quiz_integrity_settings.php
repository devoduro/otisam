<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Quiz integrity options a teacher can tick:
 * - assignments.shuffle_options: each student sees the answer options in their own order.
 * - assignments.block_copy: students cannot select, copy or right-click the questions.
 * - submissions.tab_leaves: how many times the student left the quiz page (shown to the teacher).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('assignments', function (Blueprint $table) {
            $table->boolean('shuffle_options')->default(false)->after('show_answers');
            $table->boolean('block_copy')->default(false)->after('shuffle_options');
        });

        Schema::table('submissions', function (Blueprint $table) {
            $table->unsignedSmallInteger('tab_leaves')->default(0)->after('started_at');
        });
    }

    public function down(): void
    {
        Schema::table('submissions', fn (Blueprint $table) => $table->dropColumn('tab_leaves'));
        Schema::table('assignments', fn (Blueprint $table) => $table->dropColumn(['shuffle_options', 'block_copy']));
    }
};
