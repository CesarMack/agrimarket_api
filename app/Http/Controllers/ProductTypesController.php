<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\ProductType;

class ProductTypesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $product_types = ProductType::all();
        return response()->json(["data" => $product_types], 200);
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
        $product_type =  $this->product_type($id);
        $product_type->delete();
        return response()->json(["data" => "Categoria eliminada"], 200);
    }

    private function set_product_type(string $id){
        $product_type = ProductType::findOrFail($id);
        return $product_type;
    }
}
