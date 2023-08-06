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
            "products" => count($products),
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

    public function orders(){
        $user = Auth::guard('api')->user();
        $orders = Order::where('farmer_id', $user->id)
                        ->orderBy('created_at', 'desc')
                        ->get();
        return response()->json(["data"=>$orders]);
    }

    public function update_order_status(Request $request, string $id){
        try{
            $data = $request->all();
            $order = Order::find($id);
            if ($order){
                $order->status = $data["status"];
                if($order->save()){
                    return response()->json(["data" => $order], 200);
                }
            }else{
                return response()->json(["error"=>"Ninguna orden fue encontrada con ese ID"], 400);
            }
        }catch(QueryException $e){
            return response()->json(["error"=> $e], 500);
        }
    }
    public function me(){
        $user = Auth::guard('api')->user();
        $u_data = UserData::where("user_id", $user->user);
        if(!$u_data){
            $data = $this->set_data($user, $u_data);
            return response()->json(["data"=>$data]);
        }
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
        $u_data = UserData::where("user_id", $user->user);
        if(!$u_data){
            $u_data->update($data);
            $u_data = $this->set_data($user, $u_data);
            return response()->json(["data"=>$u_data]);
        }
        if($data["phone"] && $data["street"]){
            $u_data = new UserData($data);
            $u_data->user_id = $user->id;
            $u_data->save();
            $u_data = $this->set_data($user, $u_data);
            return response()->json(["data"=>$u_data]);
        }
        return response()->json(["data"=>[
            "id" => $user->id,
            "first_name" => $user->first_name,
            "last_name" => $user->last_name,
            "email" => $user->email
        ]]);
    }

    public function index()
    {
        $categories = User::all();
        return response()->json(["data" => $categories], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $category =  $this->set_category($id);
        return response()->json(["data" => $category], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {

    }

    private function set_farmer(string $id){
        $user = User::findOrFail($id);
        return $user;
    }
}
