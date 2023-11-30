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
use Carbon\Carbon;

class FarmersController extends Controller
{
    public function dashboard(){
        $user = Auth::guard('api')->user();
        //Ganancias
        /* $completed_orders = Order::where("farmer_id", $user->id)->where("status", 'Completado')->get();
        $sales = 0;
        foreach ($completed_orders as $item) {
            $sales += $item['total'];
        }*/

        //Ordenes completadas
        $completed_orders = Order::where("farmer_id", $user->id)->where("status", 'Completado')->get();
        //Ordenes pendientes
        $pending_orders = Order::where("farmer_id", $user->id)->where("status", 'Pendiente')->get();
        //Ordenes canceladas
        $canceled_orders = Order::where("farmer_id", $user->id)->where("status", 'Cancelado')->get();
        //Productos activos
        $active_products = Product::where("user_id", $user->id)->where("active", true)->get();

       // Obtén la fecha de inicio para la semana (hoy es miércoles, retrocede hasta el martes)
        $startOfWeek = Carbon::now()->startOfWeek()->subDay();

        // Inicializa un array para almacenar los resultados por día
        $ordersByDay = [];

        // Realiza la consulta para cada día de la semana
        for ($i = 0; $i < 6; $i++) {
            $day = $startOfWeek->copy()->subDays($i)->format('l'); // Obtén el nombre del día
            $ordersByDay[$day] = Order::whereDate('created_at', $startOfWeek->subDays($i)->toDateString())->count();
        }

        // Obtén la fecha de inicio para la semana (hoy es miércoles, retrocede hasta el martes)
        $startOfWeek = Carbon::now()->startOfWeek()->subDay();

        // Inicializa un array para almacenar los resultados por semana
        $ordersByWeek = [];

        // Realiza la consulta para cada semana del mes
        for ($i = 0; $i < 4; $i++) {
            $startOfWeek->subWeek(); // Retrocede una semana
            $weekNumber = Carbon::now()->diffInWeeks($startOfWeek) + 1; // Número de semana
            $ordersByWeek["Semana $weekNumber"] = Order::whereBetween('created_at', [$startOfWeek->copy()->startOfWeek(), $startOfWeek->copy()->endOfWeek()])->count();
        }

        return response()->json([
            "data" => [
                "completed_orders" => $completed_orders->count(),
                "pending_orders" => $pending_orders->count(),
                "canceled_orders" => $canceled_orders->count(),
                "active_products" => $active_products->count(),
                "orders_last_week" => $ordersByDay,
                "orders_last_month" => $ordersByWeek
            ]
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
