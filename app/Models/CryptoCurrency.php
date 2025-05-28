<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CryptoCurrency extends Model
{
    protected $fillable = ['name', 'symbol', 'current_price', 'currency_id'];

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }

    public function histories(): HasMany
    {
        return $this->hasMany(CryptoHistory::class);
    }
}
