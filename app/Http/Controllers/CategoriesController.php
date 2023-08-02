<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoriesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Category::all();
        return response()->json(["data" => $categories], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->all();
        $category = new Category($data);
        if($category->save()){
            return response()->json(["data" => $category], 200);
        }else{
            return response()->json(["error" => "Ocurrrio un error"], 400);
        }
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
        $category =  $this->set_category($id);
        $category->update($request->all());
        return response()->json(["data" => $category], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $category =  $this->set_category($id);
        $category->delete();
        return response()->json(["data" => "Categoria eliminada"], 200);
    }

    private function set_category(string $id){
        $category = Category::findOrFail($id);
        return $category;
    }
}
