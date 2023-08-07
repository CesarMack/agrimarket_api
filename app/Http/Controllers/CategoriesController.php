<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;

class CategoriesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::guard('api')->user();
        if($user->hasRole('admin')){
            $orders = Category::orderBy('created_at', 'desc')
                            ->get();
            return response()->json(["data"=>$orders]);
        }elseif($user->hasRole('farmer')){
            $orders = Category::where('active', true)
                            ->orderBy('created_at', 'desc')
                            ->get();
            return response()->json(["data"=>$orders]);
        }
        return response()->json(["error"=>"Tu usuario no cuenta con un rol indicado"], 400);
    }

    public function find_category(Request $request)
    {
        if ($request->has('name')) {
            $name = $request->input('name');
            $categories = Category::whereRaw("LOWER(name) LIKE LOWER(?)", ["%{$name}%"])
                ->orderBy('name')
                ->get();
            // Retornar los resultados en formato JSON
            return response()->json(['data' => $categories]);
        }
        return response()->json(['error' => "No se encontró un nombre, apellido o e-mail"], 400);
    }

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
        try{
            $category = Category::find($id);
            ($category->active) ? $category->active = false : $category->active = true;
            if($category->save()){
                return response()->json(["data" => $category], 200);
            }
        }catch(QueryException $e){
            return response()->json(["error"=> $e], 500);
        }
    }

    private function set_category(string $id){
        $category = Category::findOrFail($id);
        return $category;
    }
}
