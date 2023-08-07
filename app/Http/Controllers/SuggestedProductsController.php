<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SuggestedProduct;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;

class SuggestedProductsController extends Controller
{
    public function index()
    {
        $user = Auth::guard('api')->user();
        if($user->hasRole('admin')){
            $suggested_products = SuggestedProduct::orderBy('created_at', 'desc')
                            ->get();
            return response()->json(["data"=>$suggested_products]);
        }elseif($user->hasRole('farmer')){
            $suggested_products = SuggestedProduct::where('user_id', $user->id)
                            ->orderBy('created_at', 'desc')
                            ->get();
            return response()->json(["data"=>$suggested_products]);
        }
        return response()->json(["error"=>"Tu usuario no cuenta con un rol indicado"], 400);
    }

    public function store(Request $request)
    {
        $data = $request->all();
        $userid = Auth::guard('api')->user()->id;
        $suggested_product = new SuggestedProduct($data);
        $suggested_product->user_id = $userid;
        if($suggested_product->save()){
            return response()->json(["data" => $suggested_product], 200);
        }else{
            return response()->json(["error" => "Ocurrrio un error"], 400);
        }
    }

    public function show(string $id)
    {
        $suggested_product = $this->set_suggested_product($id);
        return response()->json(["data" => $suggested_product], 200);
    }

    public function update(Request $request, string $id)
    {
        $suggested_product = $this->set_suggested_product($id);
        $suggested_product->update($request->all());
        return response()->json(["data" => $suggested_product], 200);
    }

    public function update_status(string $id){
        try{
            $product = SuggestedProduct::find($id);
            (!$product->finished) ? $product->finished = true : $product->finished = false;
            if($product->save()){
                return response()->json(["data" => $product], 200);
            }
        }catch(QueryException $e){
            return response()->json(["error"=> $e], 500);
        }
    }

    public function destroy(string $id)
    {
        $suggested_product =  $this->set_suggested_product($id);
        $suggested_product->delete();
        return response()->json(["data" => "Sugerencia eliminada"], 200);
    }

    private function set_suggested_product(string $id){
        $suggested_product = SuggestedProduct::findOrFail($id);
        return $suggested_product;
    }
}
