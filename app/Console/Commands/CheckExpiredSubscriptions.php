<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Subscription;
use App\Services\CyberPanelService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class CheckExpiredSubscriptions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscription:check-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for expired subscriptions and remove associated CyberPanel resources';

    /**
     * Execute the console command.
     */
    public function handle(CyberPanelService $cyberPanel)
    {
        $this->info('Checking for expired subscriptions...');
        
        // Find active subscriptions that have passed their end date
        $expiredSubscriptions = Subscription::where('status', 'active')
                                            ->where('ends_at', '<', Carbon::now())
                                            ->get();

        if ($expiredSubscriptions->isEmpty()) {
            $this->info('No expired subscriptions found.');
            return;
        }

        foreach ($expiredSubscriptions as $subscription) {
            $user = $subscription->user;
            
            if (!$user) {
                // Orphaned subscription? Just mark expired.
                $subscription->update(['status' => 'expired']);
                continue;
            }

            $this->info("Processing expired subscription for user: {$user->email}");

            // 1. Delete CyberPanel Websites
            foreach ($user->domains as $domain) {
                if ($domain->domain_name) {
                    $this->info("Deleting website: {$domain->domain_name}");
                    try {
                        $cyberPanel->deleteWebsite($domain->domain_name);
                        // Delete local domain record
                        $domain->delete();
                    } catch (\Exception $e) {
                        Log::error("Failed to delete website {$domain->domain_name}: " . $e->getMessage());
                        $this->error("Failed to delete website {$domain->domain_name}");
                    }
                }
            }

            // 2. Delete CyberPanel User
            // Reconstruct username logic from AdminController
            $username = strtolower(explode('@', $user->email)[0]);
            $username = preg_replace('/[^a-z0-9]/', '', $username);
            // This is a "best effort" guess since we don't store the CP username in User model (based on current inspection)
            // If the username construction logic had random numbers appended, this might fail.
            //Ideally, we should store cyberpanel_username in users table.
            
            $this->info("Attempting to delete CyberPanel user: {$username}");
            try {
                $cyberPanel->deleteCyberPanelUser($username);
            } catch (\Exception $e) {
                Log::error("Failed to delete CyberPanel user {$username}: " . $e->getMessage());
                // We don't stop here, we proceed to mark local sub as expired
            }

            // 3. Update Local Subscription Status
            $subscription->update(['status' => 'expired']);
            $this->info("Subscription marked as expired.");
        }

        $this->info('Expired subscription check completed.');
    }
}
