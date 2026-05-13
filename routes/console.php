<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

/*
|--------------------------------------------------------------------------
| Scheduled Tasks
|--------------------------------------------------------------------------
|
| Here you can register scheduled tasks for your application. These
| tasks will be run by the Laravel scheduler.
|
*/

// Generate monthly charges for all active athletes
Schedule::command('financial:generate-monthly-charges')->monthlyOn(1, '00:00');

// Send payment reminders via WhatsApp
Schedule::command('notifications:payment-reminders')->dailyAt('09:00');

// Clean up old AI generated content
Schedule::command('ai:cleanup-old-content')->weekly();

// Backup tenant databases
Schedule::command('tenancy:backup')->dailyAt('02:00');

// Send birthday notifications
Schedule::command('notifications:birthday-reminders')->dailyAt('08:00');

// Generate performance reports
Schedule::command('reports:generate-monthly')->monthlyOn(1, '06:00');

// Send AI athlete plan notifications (Workout/Nutrition) via WhatsApp
Schedule::command('athlete:send-ai-notifications')->everyMinute();

// Update athlete subcategories (Sub-11, Sub-13, etc) based on birth year
Schedule::command('athletes:update-subcategories')->daily();
