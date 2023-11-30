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
        //Ordenes completadas
        $completed_orders = Order::where("farmer_id", $user->id)->where("status", 'Completado')->get();
        //Ordenes pendientes
        $pending_orders = Order::where("farmer_id", $user->id)->where("status", 'Pendiente')->get();
        //Ordenes canceladas
        $canceled_orders = Order::where("farmer_id", $user->id)->where("status", 'Cancelado')->get();
        //Productos activos
        $active_products = Product::where("user_id", $user->id)->where("active", true)->get();

        return response()->json([
            "data" => [
                "completed_orders" => $completed_orders->count(),
                "pending_orders" => $pending_orders->count(),
                "canceled_orders" => $canceled_orders->count(),
                "active_products" => $active_products->count(),
                "orders_last_week" => $this->orders_last_week(),
                "orders_last_month" => $this->orders_last_month(),
                "orders_last_six_months" => $this->orders_last_six_months()
            ]
        ]);
    }

    public function orders_last_week(){
        // Obtén la fecha de inicio para la semana (hoy es miércoles, retrocede hasta el martes)
        $startOfWeek = Carbon::now()->startOfWeek()->subDay();

        // Inicializa un array para almacenar los resultados por día
        $ordersByDay = [];

        // Días de la semana en el orden correcto a partir del día actual
        $weekdays = collect(['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado']);

        // Obtén el índice del día actual y reorganiza el array de días
        $currentIndex = $weekdays->search(Carbon::now()->translatedFormat('l'));
        $weekdays = $weekdays->merge($weekdays->splice(0, $currentIndex));

        // Realiza la consulta para cada día de la semana
        foreach ($weekdays as $day) {
            $ordersByDay[$day] = Order::whereDate('created_at', $startOfWeek->copy()->toDateString())->count();
            $startOfWeek->subDay();
        }

        return $ordersByDay;
    }

    public function orders_last_month(){
        // Obtén la fecha de inicio para la semana (hoy es miércoles, retrocede hasta el martes)
        $startOfWeek = Carbon::now()->startOfWeek()->subDay();
        // Inicializa un array para almacenar los resultados por semana
        $ordersByWeek = [];
        // Realiza la consulta para cada semana del mes
        for ($i = 0; $i < 4; $i++) {
            $startOfWeek->subWeek(); // Retrocede una semana
            $weekNumber = Carbon::now()->diffInWeeks($startOfWeek); // Número de semana
            $ordersByWeek["Semana $weekNumber"] = Order::whereBetween('created_at', [$startOfWeek->copy()->startOfWeek(), $startOfWeek->copy()->endOfWeek()])->count();
        }
        return $ordersByWeek;
    }

    public function orders_last_six_months(){
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
