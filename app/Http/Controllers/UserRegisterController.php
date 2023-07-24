<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User; // Asegúrate de importar el modelo User

class UserRegisterController extends Controller
{
    // ...

    public function register(Request $request)
    {
        try {
            // Valida los datos de entrada del formulario de registro
            $request->validate([
                'first_name' => 'required',
                'last_name' => 'required',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|string|min:6',
                'phone' => 'required',
                'street' => 'required',
                'ext_num' => 'required',
                'int_num',
                'suburb' => 'required',
                'city' => 'required',
                'state' => 'required',
                'zip_code' => 'required',
                'photo'
            ]);

            // Crea un nuevo usuario utilizando los datos del formulario de registro
            $user = new User();
            $user->first_name = $request->input('first_name');
            $user->last_name = $request->input('last_name');
            $user->email = $request->input('email');
            $user->password = bcrypt($request->input('password'));
            $user->phone = $request->input('phone');
            $user->street = $request->input('street');
            $user->ext_num = $request->input('ext_num');
            $user->int_num = $request->input('int_num');
            $user->suburb = $request->input('suburb');
            $user->city = $request->input('city');
            $user->state = $request->input('state');
            $user->zip_code = $request->input('zip_code');
            //$user->photo = $request->input('photo');
            $user->save();

            $credentials = $request->only('email', 'password');
            Auth::attempt($credentials);
            $user = Auth::user();

            if ($user instanceof \App\Models\User) {
                $accessToken = $user->createToken('token')->accessToken;
                return response()->json([
                    "id" => $user->id,
                    "name" => $user->first_name." ".$user->last_name,
                    "email" => $user->email,
                    "access_token" => $accessToken->token,
                ]);
            }

        } catch (\Exception $e) {
            // Captura cualquier excepción no controlada y devuelve una respuesta 500
            return response()->json([
                "message" => $e,
            ], 500);
        }
    }
}

