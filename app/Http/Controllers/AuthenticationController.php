<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User; // Asegúrate de importar el modelo User
use Spatie\Permission\Models\Role;

class AuthenticationController extends Controller
{
    public function register(Request $request)
    {
        $data = $request->all();
        $user = new User($data);
        if ($user->save()){
            ($request->input('type') == "2") ? $user->assignRole('farmer') : $user->assignRole('client');
            //login
            $credentials = $request->only('email', 'password');
            Auth::attempt($credentials);
            $user = Auth::user();

            if ($user instanceof \App\Models\User) {
                $accessToken = $user->createToken('token')->accessToken;
                return response()->json([
                    "user" => $this->set_data($user, $accessToken)
                ]);
            }
        }
    }

    public function login(Request $request)
    {
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
                "user" => $this->set_data($user, $accessToken)
            ]);
        } else {
            return response()->json([
                "message" => "Error de autenticación.",
            ], 401);
        }
    }

    public function set_data(object $user, string $token){
        $data = [
            'id' => $user->id,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'email' => $user->email,
            "role"=> $user->getRoleNames()->first(),
            "access_token" => $token
        ];
        return $data;
    }
}

