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
                "farmers"=>count($farmers),
                "total"=>count(User::all())
            ]
        ]);
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
