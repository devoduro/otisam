<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * P1 stored a student's class and programme as text from config/platform.php. Replace them with
 * foreign keys to the curriculum tables, carrying existing values across by name.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('student_profiles', function (Blueprint $table) {
            $table->foreignId('class_level_id')->nullable()->after('user_id')->constrained()->nullOnDelete();
            $table->foreignId('programme_id')->nullable()->after('class_level_id')->constrained()->nullOnDelete();
        });

        $rows = DB::table('student_profiles')->select('user_id', 'class_level', 'programme')->get();

        if ($rows->isNotEmpty()) {
            $now = now();
            $levelId = DB::table('education_levels')->where('code', 'SHS')->value('id')
                ?? DB::table('education_levels')->insertGetId(['name' => 'Senior High School', 'code' => 'SHS', 'sort' => 2, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now]);

            $classIds = [];
            $programmeIds = [];

            foreach ($rows as $row) {
                $classId = null;
                if ($row->class_level) {
                    $classId = $classIds[$row->class_level] ??= DB::table('class_levels')->where('education_level_id', $levelId)->where('name', $row->class_level)->value('id')
                        ?? DB::table('class_levels')->insertGetId(['education_level_id' => $levelId, 'name' => $row->class_level, 'sort' => preg_match('/(\d+)\s*$/', $row->class_level, $n) ? (int) $n[1] : 0, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now]);
                }

                $programmeId = null;
                if ($row->programme) {
                    $programmeId = $programmeIds[$row->programme] ??= DB::table('programmes')->where('education_level_id', $levelId)->where('name', $row->programme)->value('id')
                        ?? DB::table('programmes')->insertGetId(['education_level_id' => $levelId, 'name' => $row->programme, 'sort' => 0, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now]);
                }

                DB::table('student_profiles')->where('user_id', $row->user_id)->update(['class_level_id' => $classId, 'programme_id' => $programmeId]);
            }
        }

        Schema::table('student_profiles', function (Blueprint $table) {
            $table->dropColumn(['class_level', 'programme']);
        });
    }

    public function down(): void
    {
        Schema::table('student_profiles', function (Blueprint $table) {
            $table->string('class_level', 30)->nullable()->after('user_id');
            $table->string('programme', 80)->nullable()->after('class_level');
        });

        DB::table('student_profiles')->orderBy('user_id')->each(function ($row) {
            DB::table('student_profiles')->where('user_id', $row->user_id)->update([
                'class_level' => DB::table('class_levels')->where('id', $row->class_level_id)->value('name'),
                'programme' => DB::table('programmes')->where('id', $row->programme_id)->value('name'),
            ]);
        }, 'user_id');

        Schema::table('student_profiles', function (Blueprint $table) {
            $table->dropConstrainedForeignId('class_level_id');
            $table->dropConstrainedForeignId('programme_id');
        });
    }
};
