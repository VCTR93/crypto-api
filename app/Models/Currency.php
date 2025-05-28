<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\CryptoCurrency;
class Currency extends Model
{
    protected $fillable = ['code', 'name'];

    public function cryptoCurrencies(): HasMany
    {
        return $this->hasMany(CryptoCurrency::class);
    }
}
