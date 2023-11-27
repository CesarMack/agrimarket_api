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
        return response()->json(["data"=>$estates->first()]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = Auth::guard('api')->user();
        $estates = Estate::where('user_id', $user->id)
                        ->where('active', true)
                        ->orderBy('created_at', 'desc')
                        ->get();
        if (count($estates) < 1){
            $data = $request->all();
            $estate = new Estate($data);
            $estate->user_id = $user->id;
            if ($request->file('photo')){
                $url = $this->upload_photo($request);
                $estate->photo = $url->original["url"];
            }
            if($estate->save()){
                return response()->json(["data" => $estate], 200);
            }else{
                return response()->json(["error" => "Ocurrrio un error"], 400);
            }
        }else{
            return response()->json(["error" => "El usuario ya cuenta con una granja registrada"], 400);
        }
    }

    public function show(string $id)
    {
        $estate = $this->set_estate($id);
        return response()->json(["data" => $estate], 200);
    }

    public function update(Request $request, string $id)
    {
        $estate = $this->set_estate($id);
        $url = $estate->photo;
        $estate->update($request->all());
        $estate->photo = $url;
        $estate->save();
        if ($request->file('photo')){
            $url = $this->upload_photo($request);
            $estate->photo = $url->original["url"];
            $estate->save();
        }
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

    private function upload_photo(Request $request){
        $response = cloudinary()->upload($request->file('photo')->getRealPath())->getSecurePath();
        return response()->json(['url' => $response]);
    }
}
