<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Runs when Hostinger cron calls `schedule:run` once daily at midnight.
Schedule::command('savings:notify-maturing-fds')->daily();
