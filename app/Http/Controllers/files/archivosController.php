<?php

namespace App\Http\Controllers\files;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Collection;
use Storage;
use Validator;

class archivosController extends Controller
{
    private $urlapis ;
    private $headers;
    public function __construct(){
        $this->urlapis = 'https://apitest-bt.herokuapp.com/api/v1/imagenes';
        $this->headers = [
            'user' => 'User123',
            'password' => 'Password123'
        ];
    }
    public function getImages(){
        $response = Http::accept('application/json')
        ->withHeaders($this->headers)
        ->get($this->urlapis);
        $getImages = $response->collect();
        
        $imageFilter = $getImages->filter(function ($value) {
            if(strlen($value["base64"])>1000){
                return $value;
            }
        });

        return response()->json([
            "data" => array_values($imageFilter->all())
        ],200);
    }
    public function uploadImage(Request $request){
        if($request->imagen && !empty($request->nombre)){
            
            $allowedMimeTypes = ['image/jpg','image/jpeg','image/gif','image/png','image/bmp','image/svg+xml'];
            $contentType = $request->imagen->getMimeType();

            if(in_array($contentType,$allowedMimeTypes)){
                $imagenCodificada = 'data:'.$contentType.';base64,'.base64_encode(file_get_contents($request->imagen->getRealPath()));
            
                $response = Http::contentType("application/json")
                                    ->withHeaders($this->headers)
                                    ->withBody(json_encode(
                                        [
                                            "imagene"=>[
                                                "nombre"=>$request->nombre,
                                                "base64"=>$imagenCodificada
                                                ]
                                            ]
                                        )
                                        , 'application/json'
                                    )
                                    ->post($this->urlapis);

                 if($response->successful()){
                    return response()->json([
                        "mensaje"=>"Guardado correctamente",
                        "imagen" => $imagenCodificada
                    ],200);
                 }else if($response->failed()){
                    return response()->json([
                        "mensaje"=>"Error al intentar guardar",
                        "error" =>$response->throw()->json()
                    ],400);
                 }
                
            }else{
                return response()->json([
                    "mensaje"=>"El tipo de archivo no es una imagen"
                ],400);
            }
        }else{
            return response()->json([
                "mensaje"=>"Faltan datos"
            ],400);
        }
    }
    public function deleteImage($id){
        $response = Http::accept('application/json')
        ->withHeaders($this->headers)
        ->delete($this->urlapis.'/'.$id);
        // $getImages = $response->collect();
        // dd($response->failed());
        if($response->successful()){
            return response()->json([
                "mensaje"=>"Borrado correctamente"
            ],200);
         }else if($response->failed()){
            return response()->json([
                "mensaje"=>"Error al intentar borrar",
                "error" =>$response->throw()->json()
            ],400);
         }
        
        
        // // dd($imageFilter->all());
        return response()->json([
            "data" => array_values($imageFilter->all())
        ],200);
    }
}
