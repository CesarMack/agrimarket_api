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
                "completed_orders" => [
                    "week" => array_sum($this->orders_last_week($completed_orders)),
                    "month" => array_sum($this->orders_last_month($completed_orders)),
                    "six_months" => array_sum($this->orders_last_six_months($completed_orders))
                ],
                "pending_orders" => [
                    "week" => array_sum($this->orders_last_week($pending_orders)),
                    "month" => array_sum($this->orders_last_month($pending_orders)),
                    "six_months" => array_sum($this->orders_last_six_months($pending_orders))
                ],
                "canceled_orders" => [
                    "week" => array_sum($this->orders_last_week($canceled_orders)),
                    "month" => array_sum($this->orders_last_month($canceled_orders)),
                    "six_months" => array_sum($this->orders_last_six_months($canceled_orders))
                ],
                "active_products" => [
                    "week" => array_sum($this->orders_last_week($active_products)),
                    "month" => array_sum($this->orders_last_month($active_products)),
                    "six_months" => array_sum($this->orders_last_six_months($active_products))
                ],
                "orders_last_week" => $this->orders_last_week(Order::all()),
                "orders_last_month" => $this->orders_last_month(Order::all()),
                "orders_last_six_months" => $this->orders_last_six_months(Order::all())
            ]
        ]);
    }

    public function orders_last_week($orders){
        // Obtén la fecha de inicio para la semana (hoy es miércoles, retrocede hasta el martes)
        $startOfWeek = Carbon::now()->startOfWeek()->subDay();

        // Inicializa un array para almacenar los resultados por día
        $ordersByDay = [];

        // Días de la semana en el orden correcto a partir del día actual
        $weekdays = collect(['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado']);

        // Obtén el índice del día actual y reorganiza el array de días
        $currentIndex = $weekdays->search(Carbon::now()->translatedFormat('l'));
        $weekdays = $weekdays->merge($weekdays->splice(0, $currentIndex));

        $startOfWeek = Carbon::now()->startOfWeek()->subDay(); // Modifica según tus necesidades
        $endOfWeek = $startOfWeek->copy()->endOfWeek();

        // Realiza la consulta para cada día de la semana
        foreach ($weekdays as $day) {
            $ordersByDay[$day] = $orders->whereBetween('created_at', [$startOfWeek, $endOfWeek])->count();
            $startOfWeek->subDay();
        }

        return $ordersByDay;
    }

    public function orders_last_month($orders){
        // Obtén la fecha de inicio para la semana (hoy es miércoles, retrocede hasta el martes)
        $startOfWeek = Carbon::now()->startOfWeek()->subDay();
        // Inicializa un array para almacenar los resultados por semana
        $ordersByWeek = [];
        // Realiza la consulta para cada semana del mes
        for ($i = 0; $i < 4; $i++) {
            $startOfWeek->subWeek(); // Retrocede una semana
            $weekNumber = Carbon::now()->diffInWeeks($startOfWeek); // Número de semana
            $ordersByWeek["Semana $weekNumber"] = $orders->whereBetween('created_at', [$startOfWeek->copy()->startOfWeek(), $startOfWeek->copy()->endOfWeek()])->count();
        }
        return $ordersByWeek;
    }

    public function orders_last_six_months($orders) {
        $startOfMonth = Carbon::now()->subMonth()->startOfMonth();
        $ordersByMonth = [];

        for ($i = 0; $i < 6; $i++) {
            $monthName = $startOfMonth->copy()->format('F');
            $ordersByMonth[$monthName] = $orders->filter(function ($order) use ($startOfMonth) {
                return $order->created_at->year == $startOfMonth->year &&
                       $order->created_at->month == $startOfMonth->month;
            })->count();

            $startOfMonth->subMonth();
        }

        return $ordersByMonth;
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
