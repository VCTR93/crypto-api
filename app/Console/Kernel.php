<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Jobs\ReplicateHistoricalData;

class Kernel extends ConsoleKernel
{
   
    protected function schedule(Schedule $schedule): void
    {
        $schedule->job(new ReplicateHistoricalData)->dailyAt('00:00');
    }

    /**
     * Registra los comandos para aplicación.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
