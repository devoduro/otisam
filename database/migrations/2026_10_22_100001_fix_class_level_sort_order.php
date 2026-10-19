<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * An earlier migration created class levels such as "SHS 2" with sort 0, so they were listed out of order
 * and labelled "Year 0". Class levels named like "SHS 2" or "JHS 3" take their year from the name.
 */
return new class extends Migration
{
    public function up(): void
    {
        foreach (DB::table('class_levels')->get(['id', 'name', 'sort']) as $class) {
            if (preg_match('/^(JHS|SHS)\s*(\d+)$/i', trim($class->name), $m) && (int) $class->sort !== (int) $m[2]) {
                DB::table('class_levels')->where('id', $class->id)->update(['sort' => (int) $m[2]]);
            }
        }
    }

    public function down(): void
    {
        // Nothing to undo: the old sort values were wrong.
    }
};
