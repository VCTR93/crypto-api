<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Jobs\ReplicateHistoricalData;

class Kernel extends ConsoleKernel
{
   
    protected function schedule(Schedule $schedule): void
    {
        $schedule->command('app:actualizar-precios-cripto')->everyMinute()->skip(false);
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
