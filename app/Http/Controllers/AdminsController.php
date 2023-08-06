<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Models\SuggestedProduct;

class AdminsController extends Controller
{
    public function register(Request $request)
    {
        try{
            $user = Auth::guard('api')->user();
            if (!$user->hasRole('admin')){
                return response()->json(["message"=>"Acceso inválido"]);
            }

            $data = $request->all();
            $user = new User($data);
            if ($user->save()){
                $user->assignRole('admin');
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
        } catch (QueryException $e) {
            return response()->json(["error"=> $e]);
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

        if (!$user->hasRole('admin')){
            return response()->json(["message"=>"Acceso inválido"]);
        }

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

    public function get_users()
    {
        $users = User::all();
        return response()->json(["data" => $users], 200);
    }

    public function find_user(Request $request)
    {
        if ($request->has('name')) {
            $name = $request->input('name');
            $users = User::whereRaw("LOWER(first_name) LIKE LOWER(?) OR LOWER(last_name) LIKE LOWER(?) OR email LIKE ?", ["%{$name}%", "%{$name}%", "%{$name}%"])
                ->orderBy('first_name')
                ->get();
            // Retornar los resultados en formato JSON
            return response()->json(['data' => $users]);
        }
        return response()->json(['error' => "No se encontró un nombre, apellido o e-mail"], 400);
    }

    public function suggested_products(){
        $suggestions = SuggestedProduct::all();
        return response()->json(["data" => $suggestions], 200);
    }

    public function update_suggested_products(Request $request, string $id){
        try{
            $product = SuggestedProduct::find($id);
            (!$product->finished) ? $product->finished = true : $product->finished = false;
            if($product->save()){
                return response()->json(["data" => $product], 200);
            }
        }catch(QueryException $e){
            return response()->json(["error"=> $e], 500);
        }
    }

    public function dashboard(){
        //Total de usuarios, Total de granjeros, Total de Clientes
        $admin = 'admin';
        $admins = User::whereHas('roles', function ($query) use ($admin) {
            $query->where('name', $admin);
        })->get();
        $client = 'client';
        $clients = User::whereHas('roles', function ($query) use ($client) {
            $query->where('name', $client);
        })->get();
        $farmer = 'farmer';
        $farmers = User::whereHas('roles', function ($query) use ($farmer) {
            $query->where('name', $farmer);
        })->get();
        return response()->json([
            "data"=>[
                "admins"=> count($admins),
                "clients"=>count($clients),
                "farmers"=>count($farmers)
            ]
        ]);
    }

    public function me(){
        $user = Auth::guard('api')->user();
        return response()->json(["data"=>[
            "id" => $user->id,
            "first_name" => $user->first_name,
            "last_name" => $user->last_name,
            "email" => $user->email
        ]]);
    }
    public function update_me(Request $request)
    {
        $data = $request->all();
        $user = Auth::guard('api')->user();
        $user = User::find($user->id);
        $user->update($data);
        return response()->json(["data"=>[
            "id" => $user->id,
            "first_name" => $user->first_name,
            "last_name" => $user->last_name,
            "email" => $user->email
        ]]);
    }

    private function set_data(object $user, string $token){
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
