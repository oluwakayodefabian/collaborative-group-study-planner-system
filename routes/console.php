<?php

use App\Console\Commands\SendUpcomingSessionReminders;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command(SendUpcomingSessionReminders::class)->everyTwoMinutes()->withoutOverlapping();

// Schedule::call(function () {
//     info('Running SendUpcomingSessionReminders');
//     // Artisan::call('send-upcoming-session-reminders');
// })->everyMinute();
