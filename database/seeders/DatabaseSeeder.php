<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RolesAndPermissionsSeeder::class,
            SuperAdminSeeder::class,
            CurriculumStructureSeeder::class,
            LibrarySeeder::class,
            PlanSeeder::class,
        ]);
    }
}
