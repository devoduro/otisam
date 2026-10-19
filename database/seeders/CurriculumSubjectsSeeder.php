<?php

namespace Database\Seeders;

use App\Models\Curriculum\EducationLevel;
use App\Models\Curriculum\Programme;
use App\Models\Curriculum\Subject;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Brings the subject list in line with the NaCCA curriculum microsite (curriculumresources.edu.gh):
 * the JHS Common Core Programme subjects and the subjects introduced by the 2023 SHS curriculum.
 *
 * The older WASSCE subject names (Core Mathematics, Integrated Science, Elective Mathematics…) are
 * kept, because WAEC past papers are filed under them; their 2023 curriculum name goes in the
 * description instead of creating a duplicate subject.
 *
 * Only adds what is missing and never overwrites an administrator's edits. Safe to run again.
 */
class CurriculumSubjectsSeeder extends Seeder
{
    private const JHS = [
        // name => [category, description]
        'English Language' => ['core', 'BECE subject. JHS Common Core Programme.'],
        'Mathematics' => ['core', 'BECE subject. JHS Common Core Programme.'],
        'Science' => ['core', 'BECE subject (Integrated Science). JHS Common Core Programme.'],
        'Social Studies' => ['core', 'BECE subject. JHS Common Core Programme.'],
        'Computing' => ['core', 'BECE subject (formerly ICT). JHS Common Core Programme.'],
        'Religious and Moral Education' => ['core', 'BECE subject. JHS Common Core Programme.'],
        'Career Technology' => ['core', 'BECE subject (Pre-Technical Skills and Home Economics). JHS Common Core Programme.'],
        'Creative Arts and Design' => ['core', 'BECE subject. JHS Common Core Programme.'],
        'Physical Education and Health' => ['core', 'JHS Common Core Programme.'],
        'Ghanaian Language' => ['elective', 'Asante Twi, Akuapem Twi, Mfantse, Ewe, Ga, Dangme, Nzema, Dagbanli, Dagaare, Gonja, Gurene, Kasem.'],
        'French' => ['elective', 'BECE subject. JHS Common Core Programme.'],
        'Arabic' => ['elective', 'BECE subject. JHS Common Core Programme.'],
    ];

    /** Subjects added by the 2023 SHS curriculum that have no WASSCE-era equivalent here yet. */
    private const SHS_NEW = [
        'Religious and Moral Education' => ['core', '2023 SHS curriculum core subject.'],
        'Physical Education and Health' => ['core', '2023 SHS curriculum core subject (Core PEH).'],
        'Information and Communication Technology' => ['core', '2023 SHS curriculum core subject (ICT).'],
        'Intervention English' => ['core', '2023 SHS curriculum support course for learners who need extra help in English.'],
        'Intervention Mathematics' => ['core', '2023 SHS curriculum support course for learners who need extra help in Mathematics.'],
        'Computing' => ['elective', '2023 SHS curriculum elective.'],
        'Elective Physical Education and Health' => ['elective', '2023 SHS curriculum elective.'],
        'Agricultural Science' => ['elective', '2023 SHS curriculum elective.'],
        'Applied Technology' => ['elective', '2023 SHS curriculum elective.'],
        'Art and Design Foundation' => ['elective', '2023 SHS curriculum elective.'],
        'Arts and Design Studio' => ['elective', '2023 SHS curriculum elective.'],
        'Aviation and Aerospace Engineering' => ['elective', '2023 SHS curriculum elective (STEM).'],
        'Biomedical Science' => ['elective', '2023 SHS curriculum elective (STEM).'],
        'Design and Communication Technology' => ['elective', '2023 SHS curriculum elective.'],
        'Engineering' => ['elective', '2023 SHS curriculum elective (STEM).'],
        'Manufacturing Engineering' => ['elective', '2023 SHS curriculum elective (STEM).'],
        'Robotics' => ['elective', '2023 SHS curriculum elective (STEM).'],
        'Islamic Religious Studies' => ['elective', 'Religious Studies (Islamic) in the 2023 SHS curriculum.'],
        'Ghanaian Language' => ['elective', 'Asante Twi, Akuapem Twi, Mfantse, Ewe, Ga, Dangme, Nzema, Dagbanli, Dagaare, Gonja, Gurene, Kasem.'],
        'French' => ['elective', '2023 SHS curriculum elective.'],
        'Spanish' => ['elective', '2023 SHS curriculum elective.'],
        'Arabic' => ['elective', '2023 SHS curriculum elective.'],
        'Music' => ['elective', '2023 SHS curriculum elective.'],
        'Performing Arts' => ['elective', '2023 SHS curriculum elective.'],
    ];

    /**
     * Programmes for the 2023 subjects above, so the programme filter shows them. "*" means every programme,
     * as a core subject. Only subjects with no programme yet are linked, so an administrator's choices stay.
     */
    private const PROGRAMME_LINKS = [
        'Religious and Moral Education' => '*',
        'Physical Education and Health' => '*',
        'Information and Communication Technology' => '*',
        'Intervention English' => '*',
        'Intervention Mathematics' => '*',
        'Computing' => ['General Science', 'General Arts', 'Business', 'Technical'],
        'Elective Physical Education and Health' => ['General Science', 'General Arts'],
        'Agricultural Science' => ['Agricultural Science'],
        'Applied Technology' => ['Technical'],
        'Art and Design Foundation' => ['Visual Arts'],
        'Arts and Design Studio' => ['Visual Arts'],
        'Aviation and Aerospace Engineering' => ['General Science', 'Technical'],
        'Biomedical Science' => ['General Science'],
        'Design and Communication Technology' => ['Technical', 'Visual Arts'],
        'Engineering' => ['General Science', 'Technical'],
        'Manufacturing Engineering' => ['General Science', 'Technical'],
        'Robotics' => ['General Science', 'Technical'],
        'Islamic Religious Studies' => ['General Arts'],
        'Ghanaian Language' => ['General Arts'],
        'French' => ['General Arts'],
        'Spanish' => ['General Arts'],
        'Arabic' => ['General Arts'],
        'Music' => ['General Arts', 'Visual Arts'],
        'Performing Arts' => ['General Arts', 'Visual Arts'],
    ];

    /** Existing WASSCE-era subjects => their name in the 2023 SHS curriculum. */
    private const SHS_ALSO_KNOWN_AS = [
        'Core Mathematics' => 'Mathematics',
        'Integrated Science' => 'General Science',
        'Elective Mathematics' => 'Additional Mathematics',
        'Christian Religious Studies' => 'Religious Studies (Christian)',
        'Financial Accounting' => 'Accounting',
        'General Agriculture' => 'Agriculture',
    ];

    public function run(): void
    {
        DB::transaction(function () {
            $jhs = EducationLevel::firstOrCreate(['code' => 'JHS'], ['name' => 'Junior High School', 'sort' => 1, 'is_active' => true]);
            foreach (['JHS 1', 'JHS 2', 'JHS 3'] as $i => $name) {
                $jhs->classLevels()->firstOrCreate(['name' => $name], ['sort' => $i + 1]);
            }
            $added = $this->addMissing($jhs, self::JHS);
            // JHS was created switched off while it had no subjects; with subjects it can show.
            if (! $jhs->is_active && $jhs->subjects()->exists()) {
                $jhs->forceFill(['is_active' => true])->save();
            }

            $shs = EducationLevel::where('code', 'SHS')->first();
            if ($shs) {
                $added += $this->addMissing($shs, self::SHS_NEW);
                $this->linkProgrammes($shs);
                foreach (self::SHS_ALSO_KNOWN_AS as $name => $newName) {
                    $subject = $shs->subjects()->where('name', $name)->first();
                    if ($subject && blank($subject->description)) {
                        $subject->update(['description' => "Called {$newName} in the 2023 SHS curriculum."]);
                    }
                }
            }

            $this->command?->info("Curriculum subjects: {$added} added.");
        });
    }

    private function linkProgrammes(EducationLevel $shs): void
    {
        $programmes = Programme::where('education_level_id', $shs->id)->get();
        foreach (self::PROGRAMME_LINKS as $name => $names) {
            $subject = $shs->subjects()->where('name', $name)->first();
            if (! $subject || $subject->programmes()->exists()) {
                continue;
            }
            $targets = $names === '*' ? $programmes : $programmes->filter(fn ($p) => in_array($p->name, $names, true));
            foreach ($targets as $programme) {
                $programme->subjects()->syncWithoutDetaching([$subject->id => ['is_core' => $names === '*']]);
            }
        }
    }

    private function addMissing(EducationLevel $level, array $subjects): int
    {
        $sort = (int) $level->subjects()->max('sort');
        $added = 0;
        foreach ($subjects as $name => [$category, $description]) {
            if ($level->subjects()->where('name', $name)->exists()) {
                continue;
            }
            $subject = new Subject(['education_level_id' => $level->id, 'name' => $name, 'category' => $category, 'description' => $description, 'sort' => ++$sort, 'is_active' => true]);
            // Slugs are unique across levels: "jhs-social-studies" rather than "social-studies-2".
            if ($level->code !== 'SHS') {
                $subject->slug = Str::lower($level->code).'-'.Str::slug($name);
            }
            $subject->save();
            $added++;
        }

        return $added;
    }
}
