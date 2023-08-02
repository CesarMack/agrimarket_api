<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UnitOfMeasurement;

class UnitOfMeasurementsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $unit = UnitOfMeasurement::all();
        return response()->json(["data" => $unit], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->all();
        $unit = new UnitOfMeasurement($data);
        if($unit->save()){
            return response()->json(["data" => $unit], 200);
        }else{
            return response()->json(["error" => "Ocurrrio un error"], 400);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $unit =  $this->set_category($id);
        return response()->json(["data" => $unit], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $unit =  $this->set_category($id);
        $unit->update($request->all());
        return response()->json(["data" => $unit], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $unit =  $this->set_category($id);
        $unit->delete();
        return response()->json(["data" => "Unidad eliminada"], 200);
    }

    private function set_category(string $id){
        $unit = UnitOfMeasurement::findOrFail($id);
        return $unit;
    }
}
