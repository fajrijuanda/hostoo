<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\HostingPlan;
use Carbon\Carbon;

class HostingPlanSeeder extends Seeder
{
    public function run()
    {
        // Clear existing plans
        HostingPlan::truncate();

        HostingPlan::create([
            'name' => 'Starter Plan',
            'description' => 'Perfect for beginners.',
            'price' => 140000,
            'features' => [
                '1 Month Duration',
                'Single Website',
                '10GB SSD Storage',
                'Free SSL Certificate',
                'Unlimited Bandwidth',
                '5 Email Accounts',
                '24/7 Support'
            ],
            'discount_price' => null,
            'discount_start_date' => null,
            'discount_end_date' => null,
            'image' => 'plan-1.png'
        ]);

        HostingPlan::create([
            'name' => 'Pro Plan',
            'description' => 'For growing sites.',
            'price' => 200000,
            'features' => [
                '2 Months Duration',
                'Unlimited Websites',
                '10GB NVMe Storage',
                'Free SSL Certificate',
                'Unlimited Bandwidth',
                'Unlimited Emails',
                'Priority Support'
            ],
            // 'discount_price' => 150000, // Discount removed as per request
            // 'discount_start_date' => Carbon::now(),
            // 'discount_end_date' => Carbon::now()->addDays(2),
            'discount_price' => null,
            'discount_start_date' => null,
            'discount_end_date' => null,
            'image' => 'plan-2.png'
        ]);
        
        HostingPlan::create([
            'name' => 'Business Plan',
            'description' => 'For established businesses.',
            'price' => 0, // Placeholder price
            'features' => [
                'Coming Soon',
                'Stay Tuned'
            ],
            'discount_price' => null,
            'discount_start_date' => null,
            'discount_end_date' => null,
            'image' => 'coming-soon.png'
        ]);
    }
}
