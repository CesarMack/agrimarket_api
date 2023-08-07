<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\ProductType;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;

class ProductTypesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::guard('api')->user();
        if($user->hasRole('admin')){
            $product_types = ProductType::orderBy('created_at', 'desc')
                            ->get();
            $product_types = $product_types->map(function ($pt) {
                return $this->reduce_data($pt);
            });
            return response()->json(["data"=>$product_types]);
        }elseif($user->hasRole('farmer')){
            $product_types = ProductType::where('active', true)
                            ->orderBy('created_at', 'desc')
                            ->get();
            $product_types = $product_types->map(function ($pt) {
                return $this->reduce_data($pt);
            });
            return response()->json(["data"=>$product_types]);
        }
        return response()->json(["error"=>"Tu usuario no cuenta con un rol indicado"], 400);
    }

    public function find_product_type(Request $request)
    {
        if ($request->has('name')) {
            $name = $request->input('name');
            $product_types = ProductType::whereRaw("LOWER(name) LIKE LOWER(?)", ["%{$name}%"])
                ->orderBy('name')
                ->get();
            $product_types = $product_types->map(function ($pt) {
                return $this->reduce_data($pt);
            });
            return response()->json(['data' => $product_types]);
        }
        return response()->json(['error' => "No se encontró ningún registro"], 400);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->all();
        $product_type = new ProductType($data);
        if($product_type->save()){
            return response()->json(["data" => $product_type], 200);
        }else{
            return response()->json(["error" => "Ocurrrio un error"], 400);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $product_type =  $this->set_product_type($id);
        return response()->json(["data" => $product_type], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $product_type =  $this->set_product_type($id);
        $product_type->update($request->all());
        return response()->json(["data" => $product_type], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try{
            $product_type = ProductType::find($id);
            ($product_type->active) ? $product_type->active = false : $product_type->active = true;
            if($product_type->save()){
                return response()->json(["data" => $product_type], 200);
            }
        }catch(QueryException $e){
            return response()->json(["error"=> $e], 500);
        }
    }

    private function set_product_type(string $id){
        $product_type = ProductType::findOrFail($id);
        return $product_type;
    }

    private function reduce_data(object $pt){
        $data = [
            "id" => $pt->id,
            "name" => $pt->name,
            "category" => $pt->category->name,
            "active" => $pt->active,
            "created_at" => $pt->created_at,
            "updated_at" => $pt->updated_at
        ];
        return $data;
    }
}
