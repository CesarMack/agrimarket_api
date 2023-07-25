<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User; // Asegúrate de importar el modelo User
use App\Models\UserData;
use Spatie\Permission\Models\Role;

class AuthenticationController extends Controller
{
    public function register(Request $request)
    {
        try {
            $request->validate([
                'type' => 'required',
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
            if($user->save()){
                if ($request->input('type') == "1"){
                    $user->assignRole('admin');
                }else{
                ($request->input('type') == "2") ? $user->assignRole('farmer') : $user->assignRole('client');
                $data_user = new UserData();
                $data_user->user_id = $user->id;
                $data_user->phone = $request->input('phone');
                $data_user->street = $request->input('street');
                $data_user->ext_num = $request->input('ext_num');
                $data_user->int_num = $request->input('int_num');
                $data_user->suburb = $request->input('suburb');
                $data_user->city = $request->input('city');
                $data_user->state = $request->input('state');
                $data_user->zip_code = $request->input('zip_code');
                //$data_user->photo = $request->input('photo');
                $data_user->save();
                }
            }

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

