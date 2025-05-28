<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CryptoHistory extends Model
{
    //campos que se pueden asignar masivamente
    protected $fillable = [
        'crypto_currency_id',
        'price',
        'date', 
    ];

    // los campos 'created_at' y 'updated_at' se desactivan, ya que 'date' cumple la función  de timestand en la tabla
    public $timestamps = false; 

    public function cryptoCurrency(): BelongsTo
    {
        return $this->belongsTo(CryptoCurrency::class);
    }
}
