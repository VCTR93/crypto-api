<?php

namespace App\Jobs;

use App\Models\CryptoCurrency;
use App\Models\CryptoHistory;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ReplicateHistoricalData implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        $cryptos = CryptoCurrency::all();

        foreach($cryptos as $crypto){
            CryptoHistory::create([
                'crypto_currency_id' => $crypto -> id,
                'price' => $crypto ->cuurent_price,
                'date' => now()
            ]);
        }
        // limpiar datos antiguos despues de 30 días
        CryptoCurrency::where('created_at', '<' ,now()->subDays(30)->delete());
    }
}
