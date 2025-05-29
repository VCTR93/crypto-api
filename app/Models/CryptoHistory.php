<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CryptoHistory extends Model
{
    protected $fillable = [
        'crypto_currency_id',
        'currency_id', 
        'price',
        'date',
    ];

    protected $casts = [
        'price' => 'decimal:8', // Para precisión alta
        'date' => 'datetime',
    ];

    public $timestamps = false; 

    public function cryptoCurrency(): BelongsTo
    {
        return $this->belongsTo(CryptoCurrency::class);
    }
}
