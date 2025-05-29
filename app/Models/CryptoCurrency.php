<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CryptoCurrency extends Model
{
    protected $fillable = [
        'name',
        'symbol',
        'current_price',
        'currency_id', // Para la relación con la moneda fiduciaria
    ];

    protected $casts = [
        'current_price' => 'decimal:8', // Para precisión alta
    ];

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }

    public function histories(): HasMany
    {
        return $this->hasMany(CryptoHistory::class);
    }

    public function prices()
    {
        return $this->hasMany(CryptoPrice::class);
    }
    
    // Accesor para precio en USD
    public function getUsdPriceAttribute()
    {
        return $this->prices->firstWhere('currency.code', 'USD');
    }
    
    // Accesor para precio en EUR
    public function getEurPriceAttribute()
    {
        return $this->prices->firstWhere('currency.code', 'EUR');
    }
}
