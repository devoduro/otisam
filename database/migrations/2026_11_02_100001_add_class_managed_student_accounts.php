<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Student accounts a teacher creates from a class list. They have no phone number: the student
 * signs in with a username (users.username) and a 6-digit PIN, and users.managed_by_id records the
 * teacher who created the account (and may reset the PIN).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('username', 40)->nullable()->unique()->after('phone');
            $table->foreignId('managed_by_id')->nullable()->after('username')->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('managed_by_id');
            $table->dropColumn('username');
        });
    }
};
