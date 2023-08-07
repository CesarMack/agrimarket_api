<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;
use App\Models\UserData;

class FarmersController extends Controller
{
    public function dashboard(){
        $user = Auth::guard('api')->user();
        //Ganancias
        $completed_orders = Order::where("farmer_id", $user->id)->where("status", 'Completado')->get();
        $sales = 0;
        foreach ($completed_orders as $item) {
            $sales += $item['total'];
        }
        //Ordenes completadas
        $total_orders = Order::where("farmer_id", $user->id)->get();
        $products = Product::where("user_id", $user->id)->get();
        return response()->json(["data"=>[
            "completed_orders" => $sales,
            "total_orders" => count($total_orders),
            "products" => count($products)]
        ]);
    }

    public function top_sales(){
        $user = Auth::guard('api')->user();
        $products = Order::join('products', 'orders.product_id', '=', 'products.id')
                    ->where('products.user_id', $user->id)
                    ->select('orders.*')
                    ->groupBy('orders.id')
                    ->orderByRaw('COUNT(*) DESC')
                    ->take(3)
                    ->get();
        return response()->json(["data"=>$products]);
    }

    public function last_orders(){
        $user = Auth::guard('api')->user();
        $orders = Order::where('status', 'pendiente')
                       ->where("farmer_id", $user->id)
                       ->orderBy('created_at', 'desc')
                       ->get();
        return response()->json(["data"=>$orders]);
    }
}
