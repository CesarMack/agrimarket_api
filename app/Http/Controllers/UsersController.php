<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Models\UserData;
use Illuminate\Support\Facades\Storage;

class UsersController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::all();
        $users = $users->map(function ($user) {
            return $this->reduce_data($user);
        });
        return response()->json(["data" => $users], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = $this->set_user($id);
        $user_data = UserData::where('user_id', $user->id)->first();
        if($user_data){
            $data = $this->set_complete_data($user, $user_data);
        return response()->json(["data" => $data], 200);
        }
        $data = $this->set_data($user, $user_data);
        return response()->json(["data" => $data], 200);
    }

    public function me(){
        $user = Auth::guard('api')->user();
        $u_data = UserData::where("user_id", $user->user);
        if(!$u_data){
            $data = $this->set_data($user, $u_data);
            return response()->json(["data"=>$data]);
        }
        return response()->json(["data"=>[
            "id" => $user->id,
            "first_name" => $user->first_name,
            "last_name" => $user->last_name,
            "email" => $user->email
        ]]);
    }

    public function update_me(Request $request)
    {
        $data = $request->all();
        $user = Auth::guard('api')->user();
        $user = User::find($user->id);
        $user->update($data);
        $u_data = UserData::where("user_id", $user->user)->first();
        if($u_data){
            $u_data->update($data);
            $u_data = $this->set_data($user, $u_data);
            return response()->json(["data"=>$u_data]);
        }
        if($data["phone"] && $data["street"]){
            $u_data = new UserData($data);
            $u_data->user_id = $user->id;
            $u_data->save();
            $u_data = $this->set_complete_data($user, $u_data);
            return response()->json(["data"=>$u_data]);
        }
        return response()->json(["data"=>[
            "id" => $user->id,
            "first_name" => $user->first_name,
            "last_name" => $user->last_name,
            "email" => $user->email
        ]]);
    }

    public function find_user(Request $request)
    {
        if ($request->has('name')) {
            $name = $request->input('name');
            $users = User::whereRaw("LOWER(first_name) LIKE LOWER(?) OR LOWER(last_name) LIKE LOWER(?) OR email LIKE ?", ["%{$name}%", "%{$name}%", "%{$name}%"])
                ->orderBy('first_name')
                ->get();
            // Retornar los resultados en formato JSON
            return response()->json(['data' => $users]);
        }
        return response()->json(['error' => "No se encontró un nombre, apellido o e-mail"], 400);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $user = $this->set_user($id);
        $user_data = UserData::where('user_id', $user->id)->first();
        $user->update($request->all());
        $user_data->update($request->all());
        if ($request->file('photo')){
            $file = $request->file('photo');
            $file->storeAs('',$user->id.".".$file->extension(), 'public');
            //$user_data->photo = $url;
            //$user_data->save();
        }
        $data = $this->set_data($user, $user_data);
        return response()->json(["data" => $data], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = $this->set_user($id);
        $user->delete();
        return response()->json(["data" => "Usuario eliminado"], 200);
    }

    private function set_user(string $id){
        $user = User::findOrFail($id);
        return $user;
    }

    private function set_complete_data(object $user, object $user_data){
        $data = [
            'id' => $user->id,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'email' => $user->email,
            "phone" => $user_data->phone,
            "street" => $user_data->street,
            "ext_num"=> $user_data->ext_num,
            "int_num" => $user_data->int_num,
            "suburb" => $user_data->suburb,
            "city" => $user_data->city,
            "state" => $user_data->state,
            "zip_code" => $user_data->zip_code,
            "photo" => $user_data->photo,
            "created_at" => $user->created_at,
            "updated_at" => $user->updated_at
        ];
        return $data;
    }

    private function set_data(object $user){
        $data = [
            'id' => $user->id,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'email' => $user->email,
            "created_at" => $user->created_at,
            "updated_at" => $user->updated_at
        ];
        return $data;
    }

    private function reduce_data(object $user){
        $data = [
            'id' => $user->id,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'email' => $user->email,
            "role"=> $user->getRoleNames()->first()
        ];
        return $data;
    }
}
