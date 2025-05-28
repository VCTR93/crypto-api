<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CryptoCurrency;

class CryptoCurrencyController extends Controller
{
    public function index(Request $request){
        $query = CryptoCurrency::with('currency');
        
        if($request->has('moneda')){
            $query->wherehas('currency', function($q) use ($request){
                $q->where('code', $request->moneda);
            });
        }

        return response()->json($query->get());
    }

    public function store(Request $request){
        $validated = $request->validate([
            'name' => 'required|string',
            'symbol' => 'required|string|max:10|unique:crypto_currencies',
            'current_price' => 'required|numeric',
            'currency_id' => 'required|exists:currencies,id'
        ]);

        $crypto = CryptoCurrency::create($validated);
        return response()->json($crypto, 201);
    }
}
