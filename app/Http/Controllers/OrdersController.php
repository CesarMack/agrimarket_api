<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;

class OrdersController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::guard('api')->user();
        if($user->hasRole('farmer')){
            $orders = Order::where('farmer_id', $user->id)
                            ->orderBy('created_at', 'desc')
                            ->get();
            return response()->json(["data"=>$orders]);
        }elseif($user->hasRole('client')){
            $orders = Order::where('client_id', $user->id)
                            ->orderBy('created_at', 'desc')
                            ->get();
            return response()->json(["data"=>$orders]);
        }
    }

    public function orders(){
        $user = Auth::guard('api')->user();
        $orders = Order::where('farmer_id', $user->id)
                        ->orderBy('created_at', 'desc')
                        ->get();
        return response()->json(["data"=>$orders]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $order = $this->set_order($id);
        return response()->json(["data" => $order], 200);
    }

    public function store(Request $request){
        $data = $request->all();
        $user = Auth::guard('api')->user();
        $order = new Order($data);
        $product = Product::find($data["product_id"]);
        $order->client_id = $user->id;
        $order->farmer_id = $product->user_id;
        if($order->save()){
            return response()->json(["data" => $order], 200);
        }else{
            return response()->json(["error" => "Ocurrrio un error"], 400);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $order = $this->set_order($id);
        $order->update($request->all());
        return response()->json(["data" => $order], 200);
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

    public function destroy(string $id)
    {
        $order = $this->set_order($id);
        $order->delete();
        return response()->json(["data" => "Orden eliminada"], 200);
    }

    private function set_order(string $id){
        $order = Order::findOrFail($id);
        return $order;
    }
}
