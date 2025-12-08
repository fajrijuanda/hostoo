<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Subscription;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Mail\SubscriptionExpiring;
use Illuminate\Support\Facades\Log;

class SendSubscriptionExpiryReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscription:send-expiry-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send email reminders to users whose subscriptions expire in 3 days.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $targetDate = Carbon::now()->addDays(3)->format('Y-m-d');
        
        $this->info("Checking subscriptions expiring on: {$targetDate}");

        $subscriptions = Subscription::where('status', 'active')
            ->whereDate('ends_at', $targetDate)
            ->with('user')
            ->get();

        $count = 0;

        foreach ($subscriptions as $subscription) {
            if ($subscription->user) {
                try {
                    Mail::to($subscription->user->email)->send(new SubscriptionExpiring($subscription));
                    $this->info("Reminder sent to: {$subscription->user->email}");
                    $count++;
                } catch (\Exception $e) {
                    Log::error("Failed to send expiry reminder to {$subscription->user->email}: " . $e->getMessage());
                    $this->error("Failed to send to: {$subscription->user->email}");
                }
            }
        }

        $this->info("Total reminders sent: {$count}");
    }
}
