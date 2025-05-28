<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(Request $request){
        $request->validate([
           'email' => 'request|email',
           'password' => 'required',
        ]);
        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash:: check($request->password, $user->password)){ 
             throw ValidationException::withMessages([
                'email'=>['Las credenciales de usuario no son válidas.'],
            ]);
        }

        return response()->json([
            'token' => $user->createToken('auth_token')->plainTextToken
        ]);
    }
}
