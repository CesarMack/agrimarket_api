<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserLoginController extends Controller
{
    public function login(Request $request)
    {
        try {
            $credentials = $request->only('email', 'password');

            if (!Auth::attempt($credentials)) {
                return response()->json([
                    "message" => "Usuario y/o contraseña inválidos.",
                ], 401);
            }

            $user = Auth::user();

            if ($user instanceof \App\Models\User) {
                $accessToken = $user->createToken('token')->accessToken;
                return response()->json([
                    "id" => $user->id,
                    "name" => $user->first_name." ".$user->last_name,
                    "email" => $user->email,
                    "access_token" => $accessToken->token,
                ]);
            } else {
                // Si el usuario no es una instancia de \App\Models\User, manejar el error adecuadamente.
                return response()->json([
                    "message" => "Error de autenticación.",
                ], 401);
            }
        } catch (\Exception $e) {
            // Capturar cualquier excepción no controlada y devolver una respuesta 500
            return response()->json([
                "message" => $e,
            ], 500);
        }
    }
}

