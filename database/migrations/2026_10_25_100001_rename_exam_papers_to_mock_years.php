<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Practice papers were first called "Subject Paper N". They are Otisam's own papers, not exam
 * papers from a particular year, so they are now "Subject Mock YEAR": paper 1 is the current year
 * and each later paper the year before. Only titles change; numbers (and so URLs) stay the same.
 */
return new class extends Migration
{
    public function up(): void
    {
        $year = (int) now()->year;

        DB::table('exam_papers')->join('subjects', 'subjects.id', '=', 'exam_papers.subject_id')
            ->select('exam_papers.id', 'exam_papers.number', 'subjects.name')
            ->orderBy('exam_papers.id')->each(function ($row) use ($year) {
                DB::table('exam_papers')->where('id', $row->id)->update(['title' => $row->name.' Mock '.($year - $row->number + 1)]);
            });
    }

    public function down(): void
    {
        DB::table('exam_papers')->join('subjects', 'subjects.id', '=', 'exam_papers.subject_id')
            ->select('exam_papers.id', 'exam_papers.number', 'subjects.name')
            ->orderBy('exam_papers.id')->each(function ($row) {
                DB::table('exam_papers')->where('id', $row->id)->update(['title' => $row->name.' Paper '.$row->number]);
            });
    }
};
