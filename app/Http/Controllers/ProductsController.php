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
        $user = Auth::guard('api')->user();
        if($user->hasRole('farmer')){
            $products = Product::where('user_id', $user->id)
                            ->orderBy('created_at', 'desc')
                            ->get();
            $products = $products->map(function ($pt) {
                return $this->reduce_data($pt);
            });
            return response()->json(["data"=>$products]);
        }elseif($user->hasRole('client')){
            $products = Product::where('active', true)
                            ->orderBy('created_at', 'desc')
                            ->get();
            $products = $products->map(function ($pt) {
                return $this->reduce_data($pt);
            });
            return response()->json(["data"=>$products]);
        }
        return response()->json(["error"=>"Tu usuario no cuenta con un rol indicado"], 400);;
    }

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
        $product->active = true;
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
        $product = $this->set_product($id);
        $product->update($request->all());
        return response()->json(["data" => $product], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $product = $this->set_product($id);
        $product->delete();
        return response()->json(["data" => "Producto eliminado"], 200);
    }

    private function set_product(string $id){
        $product = Product::findOrFail($id);
        return $product;
    }

    private function reduce_data(object $pt){
        $data = [
            "id" => $pt->id,
            "user" => $pt->user->first_name." ".$pt->user->last_name,
            "price" => $pt->price,
            "measure" => $pt->unit_of_measurement->name,
            "minimum_sale" => $pt->minimum_sale,
            "cutoff_date" => $pt->cutoff_date,
            "created_at" => $pt->created_at,
            "updated_at" => $pt->updated_at
        ];
        return $data;
    }
}
