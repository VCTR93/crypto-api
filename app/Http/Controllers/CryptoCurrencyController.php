<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CryptoCurrency;
use App\Models\Currency;
use App\Models\CryptoPrice;

class CryptoCurrencyController extends Controller
{
    public function index(Request $request)
    {
        $query = CryptoCurrency::with(['prices.currency']);
        
        // Filtro por moneda fiduciaria
        if ($request->has('moneda')) {
            $query->whereHas('prices.currency', function($q) use ($request) {
                $q->where('code', strtoupper($request->moneda));
            });
        }
        
        // Filtro opcional por símbolo de criptomoneda
        if ($request->has('symbol')) {
            $query->where('symbol', strtoupper($request->symbol));
        }

        $cryptos = $query->get()->map(function ($crypto) {
            return $this->formatCryptoData($crypto);
        });

        return response()->json($cryptos);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'symbol' => 'required|string|max:10|unique:crypto_currencies',
            'prices' => 'required|array|min:1',
            'prices.*.currency_id' => 'required|exists:currencies,id',
            'prices.*.price_value' => 'required|numeric|min:0'
        ]);

        // Crear la criptomoneda
        $crypto = CryptoCurrency::create([
            'name' => $validated['name'],
            'symbol' => strtoupper($validated['symbol'])
        ]);

        // Crear los precios asociados
        foreach ($validated['prices'] as $priceData) {
            $currency = Currency::find($priceData['currency_id']);
            
            CryptoPrice::create([
                'crypto_currency_id' => $crypto->id,
                'currency_id' => $priceData['currency_id'],
                'price_value' => $priceData['price_value'],
                'formatted_price' => $currency->symbol . number_format($priceData['price_value'], 2)
            ]);
        }

        return response()->json($this->formatCryptoData($crypto->fresh()), 201);
    }

    public function show($id)
    {
        $crypto = CryptoCurrency::with(['prices.currency'])->findOrFail($id);
        return response()->json($this->formatCryptoData($crypto));
    }

    protected function formatCryptoData(CryptoCurrency $crypto)
    {
        return [
            'id' => $crypto->id,
            'name' => $crypto->name,
            'symbol' => $crypto->symbol,
            'prices' => $crypto->prices->map(function ($price) {
                return [
                    'currency_code' => $price->currency->code,
                    'currency_symbol' => $price->currency->symbol,
                    'price_value' => $price->price_value,
                    'formatted_price' => $price->formatted_price,
                    'last_updated' => $price->updated_at
                ];
            }),
            'usd_price' => optional($crypto->usdPrice)->formatted_price,
            'eur_price' => optional($crypto->eurPrice)->formatted_price,
            'created_at' => $crypto->created_at,
            'updated_at' => $crypto->updated_at
        ];
    }
}