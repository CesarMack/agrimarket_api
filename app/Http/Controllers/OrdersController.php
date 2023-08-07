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
                            ->where('active', true)
                            ->orderBy('created_at', 'desc')
                            ->get();
            return response()->json(["data"=>$orders]);
        }
        return response()->json(["error"=>"Tu usuario no cuenta con un rol indicado"], 400);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = Auth::guard('api')->user();
        if($user->hasRole('farmer') || $user->hasRole('client')){
            $order = $this->set_order($id);
            return response()->json(["data" => $order], 200);;
        }
        return response()->json(["error"=>"Tu usuario no cuenta con un rol indicado"], 400);
    }

    public function store(Request $request){
        $data = $request->all();
        $user = Auth::guard('api')->user();
        $order = new Order($data);
        $product = Product::find($data["product_id"]);
        $order->client_id = $user->id;
        $order->farmer_id = $product->user_id;
        $order->unit_of_measurement_id = $product->unit_of_measurement->id;
        $order->status = "Pendiente";
        $order->total = ($data["quantity"] * $product->price_per_measure);
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

    public function update_status(Request $request, string $id){
        $user = Auth::guard('api')->user();
        if($user->hasRole('farmer') || $user->hasRole('client')){
            try{
                $data = $request->all();
                $order = Order::find($id);
                $product = $order->product;
                $order->status = $data["status"];
                if($order->save()){
                    if($order->status == "Completado"){
                        $product->stock = (($product->stock) - ($order->quantity));
                        $product->save();
                    }
                    $order = $this->reduce_data($order);
                    return response()->json(["data" => $order], 200);
                }
            }catch(QueryException $e){
                return response()->json(["error"=> $e], 500);
            }
        }
        return response()->json(["error"=>"Tu usuario no cuenta con un rol indicado"], 400);
    }

    public function destroy(string $id)
    {
        try{
            $order = Order::find($id);
            ($order->active) ? $order->active = false : $order->active = true;
            if($order->save()){
                return response()->json(["data" => $order], 200);
            }
        }catch(QueryException $e){
            return response()->json(["error"=> $e], 500);
        }
    }

    private function set_order(string $id){
        $order = Order::findOrFail($id);
        return $order;
    }

    private function reduce_data(object $order){
        $data = [
            "id" => $order->id,
            "product_id" => $order->product_id,
            "quantity" => $order->quantity." ".$order->unit_of_measurement->code,
            "total" => $order->total,
            "status" => $order->status,
            "created_at" => $order->created_at,
            "updated_at" => $order->updated_at
        ];
        return $data;
    }
}
