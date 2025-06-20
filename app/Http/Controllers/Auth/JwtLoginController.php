<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class JwtLoginController extends Controller
{
    public function login(Request $request)
    {
      
        // Validar campos
        /* $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);  */
       
        $credentials = $request->only('email', 'password');
        

        $token = JWTAuth::attempt($credentials);

     
        // Intentar generar token
        if (!$token) {
            return response()->json(['error' => 'Credenciales inválidas'], 401);
        }
       
        // Retornar token y usuario autenticado
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60,
            'user' => auth()->user(),
        ]);
    }
}

?>