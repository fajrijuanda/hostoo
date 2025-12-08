<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\HostingPlan;

class AdditionalPlansSeeder extends Seeder
{
    public function run()
    {
        // Check if plans already exist to avoid duplicates
        if (!HostingPlan::where('name', 'Nano Plan')->exists()) {
            HostingPlan::create([
                'name' => 'Nano Plan',
                'description' => 'Super affordable for small tests.',
                'price' => 50000,
                'features' => [
                    '1 Week Duration',
                    'Test Website',
                    '500MB Storage',
                    'No SSL',
                    'Limited Bandwidth'
                ],
                'image' => 'plan-1.png' // Fallback to existing image
            ]);
        }

        if (!HostingPlan::where('name', 'Enterprise Plan')->exists()) {
            HostingPlan::create([
                'name' => 'Enterprise Plan',
                'description' => 'Ultimate power for big corps.',
                'price' => 500000,
                'features' => [
                    '1 Year Duration',
                    'Unlimited Everything',
                    'Dedicated Server',
                    'Free SSL Wildcard',
                    'Priority 24/7 Support',
                    'Dedicated IP'
                ],
                'image' => 'plan-2.png' // Fallback to existing image
            ]);
        }
    }
}
