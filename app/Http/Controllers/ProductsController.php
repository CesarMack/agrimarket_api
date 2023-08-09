<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;
use App\Models\Photo;

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
        $user = Auth::guard('api')->user();
        if($user->hasRole('farmer') || $user->hasRole('client')){
            $product = $this->set_product($id);
            return response()->json(["data" => $this->set_complete_data($product)], 200);
        }
        return response()->json(["error"=>"Tu usuario no cuenta con un rol indicado"], 400);
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

    public function update(Request $request, string $id)
    {
        $product = $this->set_product($id);
        $product->update($request->all());
        return response()->json(["data" => $product], 200);
    }

    public function destroy(string $id)
    {
        try{
            $product = Product::find($id);
            ($product->active) ? $product->active = false : $product->active = true;
            if($product->save()){
                return response()->json(["data" => $product], 200);
            }
        }catch(QueryException $e){
            return response()->json(["error"=> $e], 500);
        }
    }

    private function set_product(string $id){
        $product = Product::findOrFail($id);
        return $product;
    }

    private function reduce_data(object $pt){
        $photos = Photo::where("product_id", $pt->id)->get();
        $photos = $photos->map(function ($pt) {
            return ["photo" => $pt->photo];
        });
        $data = [
            "id" => $pt->id,
            "user_id" => $pt->user->first_name." ".$pt->user->last_name,
            "product" => $pt->product_type->name,
            "price" => $pt->price_per_measure,
            "measure" => $pt->unit_of_measurement->name,
            "minimum_sale" => $pt->minimum_sale,
            "cutoff_date" => $pt->cutoff_date,
            "photos" => $photos,
            "created_at" => $pt->created_at,
            "updated_at" => $pt->updated_at
        ];
        return $data;
    }
    private function set_complete_data(object $pt){
        $photos = Photo::where("product_id", $pt->id)->get();
        $photos = $photos->map(function ($pt) {
            return ["photo" => $pt->photo];
        });
        $data = [
            "id" => $pt->id,
            "user_id" => $pt->user->first_name." ".$pt->user->last_name,
            "product" => $pt->product_type->name,
            "description" => $pt->description,
            "price" => $pt->price_per_measure,
            "measure" => $pt->unit_of_measurement->name,
            "stock" => $pt->stock,
            "minimum_sale" => $pt->minimum_sale,
            "cutoff_date" => $pt->cutoff_date,
            "photos" => $photos,
            "created_at" => $pt->created_at,
            "updated_at" => $pt->updated_at
        ];
        return $data;
    }
}
