<?php

namespace App\Http\Controllers;

use App\Models\Photo;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PhotosController extends Controller
{

    public function store(Request $request, string $id)
    {
        if($this->store_validation($id)){
            $photo = new Photo();
            $url = $this->upload_photo($request);
            $photo->product_id = $id;
            $photo->photo = $url->original["url"];
            if($photo->save()){
                return response()->json(["data" => $photo], 200);
            }else{
                return response()->json(["error" => "Ocurrrio un error"], 400);
            }
        }
    }

    public function destroy(string $id)
    {
        $photo =  $this->set_photo($id);
        $photo->delete();
        return response()->json(["data" => "Foto eliminada"], 200);
    }

    private function set_photo(string $id){
        $photo = Photo::findOrFail($id);
        return $photo;
    }

    private function upload_photo(Request $request){
        $response = cloudinary()->upload($request->file('photo')->getRealPath())->getSecurePath();
        return response()->json(['url' => $response]);
    }

    private function store_validation(string $id){
        $product = Product::find($id);
        $stored_photos = count($product->photos);
        $stored_photos < 5 ? $value = true : $value = false;
        return $value;
    }
}
