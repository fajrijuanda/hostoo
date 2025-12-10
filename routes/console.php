<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule Subscription Reminder
use Illuminate\Support\Facades\Schedule;
Schedule::command('subscription:send-expiry-reminders')->daily();

// Check for Expired Subscriptions
Schedule::command('subscription:check-expired')->daily();
