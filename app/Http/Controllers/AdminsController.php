<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Models\SuggestedProduct;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Product;

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
        // Total de usuarios
        $total_users = User::all();

        // Usuarios nuevos
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();
        $new_users = DB::table('users')
            ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->get();

        // Ordenes totales
        $total_orders = Order::all();

        //Productos activos
        $active_products = Product::where('active', true)->get();

        /*
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
        */

        return response()->json([
            "data"=>[
                "total_users"=> count($total_users),
                "new_users"=>count($new_users),
                "total_orders"=>count($total_orders),
                "active_products"=>count($active_products)
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
