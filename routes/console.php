<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/*
|--------------------------------------------------------------------------
| Jadwal Otomatis — Sistem Arsip ESDM
|--------------------------------------------------------------------------
| Jalankan scheduler via cron:
|   * * * * * php /path/to/artisan schedule:run >> /dev/null 2>&1
|
| Atau manual: php artisan arsip:resend-expired-links
|--------------------------------------------------------------------------
*/
Schedule::command('arsip:resend-expired-links')
    ->dailyAt('08:00')
    ->withoutOverlapping()
    ->appendOutputTo(storage_path('logs/expired-links.log'));
