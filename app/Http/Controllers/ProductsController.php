<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;

class ProductsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Product::all();
        return response()->json(["data" => $products], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $product = $this->set_product($id);
        return response()->json(["data" => $product], 200);
    }

    public function store(Request $request){
        $data = $request->all();
        $user = Auth::guard('api')->user();
        $product = new Product($data);
        $product->user_id = $user->id;
        if($product->save()){
            return response()->json(["data" => $product], 200);
        }else{
            return response()->json(["error" => "Ocurrrio un error"], 400);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $user = $this->set_user($id);
        $user->update($request->all());
        return response()->json(["data" => $user], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = $this->set_user($id);
        $user->delete();
        return response()->json(["data" => "Usuario eliminado"], 200);
    }

    private function set_product(string $id){
        $product = Product::findOrFail($id);
        return $product;
    }
}
