<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule)
    {
        // Setiap hari jam 20:00, cek siapa yang belum absen
        $schedule->command('attendance:check-alpha')->dailyAt('20:00');
    }
}