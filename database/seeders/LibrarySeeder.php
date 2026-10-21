<?php

namespace Database\Seeders;

use App\Models\Library\ExamType;
use App\Models\Library\ResourceCategory;
use Illuminate\Database\Seeder;

/**
 * Starter resource categories and exam types (structure only, no documents). Adds missing rows by
 * name and never changes existing ones, so administrators' edits survive a reseed.
 */
class LibrarySeeder extends Seeder
{
    private const CATEGORIES = [
        // name => [audience, is_exam_paper]
        'WASSCE past questions' => ['student', true],
        'BECE past questions' => ['student', true],
        'Mock examination papers' => ['student', true],
        'School examination papers' => ['student', true],
        'Solutions and worked answers' => ['both', false],
        'Marking schemes' => ['both', false],
        // Reports by the exam body's chief examiners, one per subject and year (upload only what you are licensed to publish).
        'Chief Examiners reports' => ['student', true],
        'Lesson notes' => ['student', false],
        'Revision notes' => ['student', false],
        'Worksheets' => ['both', false],
        'Lesson plans' => ['teacher', false],
        'Schemes of learning' => ['teacher', false],
        'Teaching guides' => ['teacher', false],
    ];

    private const EXAM_TYPES = [
        'WASSCE-MJ' => 'WASSCE (May/June)',
        'WASSCE-ND' => 'WASSCE (Nov/Dec)',
        'BECE' => 'BECE',
        'MOCK' => 'Mock examination',
        'SCHOOL' => 'School examination',
    ];

    public function run(): void
    {
        $sort = 0;
        foreach (self::CATEGORIES as $name => [$audience, $isExam]) {
            ResourceCategory::firstOrCreate(['name' => $name], ['audience' => $audience, 'is_exam_paper' => $isExam, 'sort' => ++$sort]);
        }

        $sort = 0;
        foreach (self::EXAM_TYPES as $code => $name) {
            ExamType::firstOrCreate(['code' => $code], ['name' => $name, 'sort' => ++$sort]);
        }
    }
}
