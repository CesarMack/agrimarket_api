<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\UserData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;
use App\Models\User;
use Illuminate\Http\File;
use Symfony\Component\Console\Input\Input;

class ClientsController extends Controller
{
    public function orders(){
        $user = Auth::guard('api')->user();
        $orders = Order::where('client_id', $user->id)
                        ->orderBy('created_at', 'desc')
                        ->get();
        return response()->json(["data"=>$orders]);
    }

    public function update_order_status(Request $request, string $id){
        try{
            $data = $request->all();
            $order = Order::find($id);
            if ($order){
                $order->status = $data["status"];
                if($order->save()){
                    return response()->json(["data" => $order], 200);
                }
            }else{
                return response()->json(["error"=>"Ninguna orden fue encontrada con ese ID"], 400);
            }
        }catch(QueryException $e){
            return response()->json(["error"=> $e], 500);
        }
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
        $u_data = UserData::where("user_id", $user->user);
        if(!$u_data->isEmpty()){
            $u_data->update($data);
            if($data["photo"]){
                return response()->json(["data"=>$data["photo"]]);
                $url = $this->store_photo($data["photo"]);
                $u_data->photo = $url;
                $u_data->save();
            }
            $u_data = $this->set_data($user, $u_data);
            return response()->json(["data"=>$u_data]);
        }
        if($data["phone"] && $data["street"]){
            $u_data = new UserData($data);
            if($data["photo"]){
                //return response()->json(["data"=>$_FILES["photo"]]);
                $url = $this->store_photo($_FILES["photo"]);
                $u_data->photo = $url;
            }
            $u_data->user_id = $user->id;
            $u_data->save();
            $u_data = $this->set_data($user, $u_data);
            return response()->json(["data"=>$u_data]);
        }
        return response()->json(["data"=>[
            "id" => $user->id,
            "first_name" => $user->first_name,
            "last_name" => $user->last_name,
            "email" => $user->email
        ]]);
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    private function store_photo(File $photo){
        $response = cloudinary()->upload($photo->getRealPath())->getSecurePath();
        $url = dd($response);
        return $url;
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
