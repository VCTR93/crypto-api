<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CryptoCurrencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('crypto_currencies')->insert([
            [
                'name' => 'Bitcoin',
                'symbol' => 'BTC',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Ethereum',
                'symbol' => 'ETH',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Solana',
                'symbol' => 'SOL',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Cardano',
                'symbol' => 'ADA',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}