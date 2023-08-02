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
        return response()->json(["data" => $users], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = $this->set_user($id);
        $user_data = UserData::where('user_id', $user->id)->first();
        $data = $this->set_data($user, $user_data);
        return response()->json(["data" => $data], 200);
    }

    public function profile(Request $request){
        $data = $request->all();
        $user= Auth::guard('api')->user();
        $profile = new UserData($data);
        $profile->user_id = $user->id;
        if($profile->save()){
            return response()->json(["data" => $profile], 200);
        }else{
            return response()->json(["error" => "Ocurrrio un error"], 400);
        }
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

    private function set_data(object $user, object $user_data){
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
}
