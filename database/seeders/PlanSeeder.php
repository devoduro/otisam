<?php

namespace Database\Seeders;

use App\Models\Commerce\Plan;
use Illuminate\Database\Seeder;

/**
 * The three standard plans (BLUEPRINT A3): same premium library, different durations. Prices are
 * the owner's decision (D5), so the plans are created INACTIVE with no price. An administrator
 * sets the prices and activates them in Admin → Plans.
 */
class PlanSeeder extends Seeder
{
    public function run(): void
    {
        if (Plan::exists()) {
            return;
        }

        $benefits = ['All premium worked solutions and marking guides', 'Full premium notes and practice material', 'Download premium documents while your plan is active'];

        foreach ([3 => 'Term pass', 6 => 'Half-year pass', 12 => 'Full-year pass'] as $months => $name) {
            Plan::create([
                'name' => $name,
                'description' => "{$months} months of premium access.",
                'duration_months' => $months,
                'price_minor' => 0,
                'currency' => config('platform.currency'),
                'benefits' => $benefits,
                'is_active' => false,
                'is_featured' => $months === 12,
                'sort' => $months,
            ]);
        }
    }
}
