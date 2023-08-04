<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;

class OrdersController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $orders = Order::all();
        return response()->json(["data" => $orders], 200);
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
        $order->user_id = $user->id;
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

    /**
     * Remove the specified resource from storage.
     */
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
