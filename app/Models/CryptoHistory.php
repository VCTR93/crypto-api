<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CryptoHistory extends Model
{
    protected $fillable = [
        'crypto_currency_id',
        'price',
        'date',
    ];

    protected $casts = [
        'price' => 'decimal:8', // Para precisión alta
        'date' => 'datetime',
    ];


    // los campos 'created_at' y 'updated_at' se desactivaron, ya que 'date' cumple la función  de timestand en la tabla
    public $timestamps = false; 

    public function cryptoCurrency(): BelongsTo
    {
        return $this->belongsTo(CryptoCurrency::class);
    }
}
