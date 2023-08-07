<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Estate;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;

class EstatesController extends Controller
{
    public function index()
    {
        $user = Auth::guard('api')->user();
        $estates = Estate::where('user_id', $user->id)
                        ->where('active', true)
                        ->orderBy('created_at', 'desc')
                        ->get();
        return response()->json(["data"=>$estates]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->all();
        $userid = Auth::guard('api')->user()->id;
        $estate = new Estate($data);
        $estate->user_id = $userid;
        if($estate->save()){
            return response()->json(["data" => $estate], 200);
        }else{
            return response()->json(["error" => "Ocurrrio un error"], 400);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $estate = $this->set_estate($id);
        return response()->json(["data" => $estate], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $estate = $this->set_estate($id);
        $estate->update($request->all());
        return response()->json(["data" => $estate], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try{
            $estate = $this->set_estate($id);
            ($estate->active) ? $estate->active = false : $estate->active = true;
            if($estate->save()){
                return response()->json(["data" => $estate], 200);
            }
        }catch(QueryException $e){
            return response()->json(["error"=> $e], 500);
        }
    }

    private function set_estate(string $id){
        $estate = Estate::findOrFail($id);
        return $estate;
    }
}
