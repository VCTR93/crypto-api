<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\CryptoCurrency;
use App\Models\Currency;

class CryptoPrice extends Model
{
    protected $fillable = [
        'crypto_currency_id',
        'currency_id',
        'price_value', // Asegúrate de que 'price_value' esté en fillable
    ];

    // Define los atributos que deben ser anexados al array del modelo
    protected $appends = ['formatted_price'];

    // Accessor para formatted_price
    public function getFormattedPriceAttribute(): string
    {
        // Obtener el símbolo de la moneda fiduciaria (USD, EUR)
        $currencySymbol = $this->currency->symbol ?? '';

        // Formatear el valor del precio
        return sprintf('%s%s', $currencySymbol, number_format($this->price_value, 2, '.', ','));
    }

    // Relaciones
    public function cryptoCurrency()
    {
        return $this->belongsTo(CryptoCurrency::class);
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }
}
