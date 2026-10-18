<?php

namespace Database\Seeders;

use App\Models\Curriculum\EducationLevel;
use App\Models\Curriculum\Programme;
use App\Models\Curriculum\Subject;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Starter SHS structure so the platform is usable on day one: the education level, SHS 1–3, the
 * common programmes and a list of widely taught subjects. Administrators edit all of it in
 * Admin → Curriculum.
 *
 * Deliberately NOT seeded: curriculum versions, strands, topics or learning indicators. Those are
 * entered from the official GES/NaCCA documents (BLUEPRINT §4: never invent official content).
 *
 * Runs only while no subject exists, and only adds missing rows, so it never overwrites an
 * administrator's edits.
 */
class CurriculumStructureSeeder extends Seeder
{
    private const PROGRAMMES = [
        'General Science' => ['Physics', 'Chemistry', 'Biology', 'Elective Mathematics'],
        'General Arts' => ['Economics', 'Government', 'Geography', 'History', 'Literature-in-English', 'Christian Religious Studies', 'Elective Mathematics'],
        'Business' => ['Financial Accounting', 'Business Management', 'Cost Accounting', 'Economics', 'Elective Mathematics'],
        'Visual Arts' => ['General Knowledge in Art', 'Graphic Design', 'Picture Making'],
        'Home Economics' => ['Management in Living', 'Food and Nutrition', 'Clothing and Textiles', 'Biology'],
        'Agricultural Science' => ['General Agriculture', 'Animal Husbandry', 'Chemistry', 'Physics'],
        'Technical' => ['Technical Drawing', 'Applied Electricity', 'Building Construction', 'Physics', 'Elective Mathematics'],
    ];

    private const CORE = ['English Language', 'Core Mathematics', 'Integrated Science', 'Social Studies'];

    public function run(): void
    {
        if (Subject::exists()) {
            $this->command?->info('Curriculum structure already exists; starter seeder skipped.');

            return;
        }

        DB::transaction(function () {
            $jhs = EducationLevel::firstOrCreate(['code' => 'JHS'], ['name' => 'Junior High School', 'sort' => 1, 'is_active' => false]);
            foreach (['JHS 1', 'JHS 2', 'JHS 3'] as $i => $name) {
                $jhs->classLevels()->firstOrCreate(['name' => $name], ['sort' => $i + 1]);
            }

            $shs = EducationLevel::firstOrCreate(['code' => 'SHS'], ['name' => 'Senior High School', 'sort' => 2, 'is_active' => true]);
            foreach (['SHS 1', 'SHS 2', 'SHS 3'] as $i => $name) {
                $shs->classLevels()->firstOrCreate(['name' => $name], ['sort' => $i + 1]);
            }

            $subjects = [];
            $sort = 0;
            foreach (self::CORE as $name) {
                $subjects[$name] = Subject::create(['education_level_id' => $shs->id, 'name' => $name, 'category' => 'core', 'sort' => ++$sort]);
            }
            foreach (array_unique(array_merge(...array_values(self::PROGRAMMES))) as $name) {
                $subjects[$name] = Subject::create(['education_level_id' => $shs->id, 'name' => $name, 'category' => 'elective', 'sort' => ++$sort]);
            }

            $p = 0;
            foreach (self::PROGRAMMES as $programmeName => $electives) {
                $programme = Programme::firstOrCreate(['education_level_id' => $shs->id, 'name' => $programmeName], ['sort' => ++$p]);
                $attach = [];
                foreach (self::CORE as $core) {
                    $attach[$subjects[$core]->id] = ['is_core' => true];
                }
                foreach ($electives as $elective) {
                    $attach[$subjects[$elective]->id] = ['is_core' => false];
                }
                $programme->subjects()->syncWithoutDetaching($attach);
            }
        });

        $this->command?->info('Starter SHS structure created. Review it in Admin → Curriculum.');
    }
}
