<?php

namespace App\Observers;

use App\Models\HostingPlan;
use App\Models\Subscriber;
use App\Mail\NewPlanNotification;
use Illuminate\Support\Facades\Mail;

class PlanObserver
{
    /**
     * Handle the HostingPlan "created" event.
     */
    public function created(HostingPlan $hostingPlan): void
    {
        $subscribers = Subscriber::all();
        
        foreach ($subscribers as $subscriber) {
             try {
                Mail::to($subscriber->email)->send(new NewPlanNotification($hostingPlan, $subscriber->email));
            } catch (\Exception $e) {
                \Log::error('Failed to send new plan notification to ' . $subscriber->email . ': ' . $e->getMessage());
            }
        }
    }

    /**
     * Handle the HostingPlan "updated" event.
     */
    public function updated(HostingPlan $hostingPlan): void
    {
        //
    }

    /**
     * Handle the HostingPlan "deleted" event.
     */
    public function deleted(HostingPlan $hostingPlan): void
    {
        //
    }

    /**
     * Handle the HostingPlan "restored" event.
     */
    public function restored(HostingPlan $hostingPlan): void
    {
        //
    }

    /**
     * Handle the HostingPlan "force deleted" event.
     */
    public function forceDeleted(HostingPlan $hostingPlan): void
    {
        //
    }
}
