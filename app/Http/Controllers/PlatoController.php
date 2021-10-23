<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\JwtAuth;
use App\Models\Plato;

class PlatoController extends Controller
{
    public function index(Request $request){
        $hash = $request->header('Authorization', null);
        $JwtAuth = new JwtAuth();
        if(isset($hash)){
            $checkToken = $JwtAuth->checkToken($hash);
            if($checkToken){
                echo "Ingresaste"; die();
            }else{
                echo "No Autenticado Index"; die();
            }
        }else{
            return response()->json(array(
                'status' => 'error',
                'code' => 400,
                'message' => 'No token'
            ));
        }
        
    }

    public function storeFilePlato(Request $request){
        $JwtAuth = new JwtAuth();

        if(isset($request->token)){
            $user = $JwtAuth->checkToken($request->token, true);
            if($user){
                if(isset($request->image)){
                    $path = $request->file('image')->store('plato'.'/'.$user->sub);
                    $new_path = str_replace("plato/", "", $path);
                    $data = array(
                        'status' => 'success',
                        'code' => 200,
                        'path' => $new_path
                    );
                }else{
                    $data = array(
                        'status' => 'error',
                        'code' => 402,
                        'message' => 'No Image'
                    );
                }

            }else{
                $data = array(
                    'status' => 'error',
                    'code' => 401,
                    'message' => 'Token invalido'
                );
            }

        }else{
            $data = array(
                'status' => 'error',
                'code' => 400,
                'message' => 'NO TOKEN'
            );
        }

        return response($data);
    }
    public function store(Request $request){
        $JwtAuth = new JwtAuth();
        if(isset($request->name) && isset($request->price) && isset($request->description) && isset($request->path) ){
            
            if(isset($request->token)){
                $user = $JwtAuth->checkToken($request->token, true);
                if($user){
                    $new_path = json_decode($request->path);
                    $plato = new Plato();
                    $plato->user_id = $user->sub;
                    $plato->name = $request->name;
                    $plato->price = $request->price;
                    $plato->description = $request->description;
                    $plato->latitude = $request->latitude;
                    $plato->longitude = $request->longitude;
                    $stringPath = implode(",", $new_path);
                    $plato->img = $stringPath;
                    try {
                        $plato->save();
                    } catch (\Throwable $th) {
                        return response([$th]);
                    }
                    if($plato->save()){
                        $data = array(
                            'status' => 'success',
                            'code' => 200,
                            'message' => 'Plato almacenado Satisfactoriamente'
                        );
                    }else{
                        $data = array(
                            'status' => 'error',
                            'code' => 405,
                            'message' => 'Plato NO guardado'
                        );
                    }
                } else{
                    $data = array(
                        'status' => 'error',
                        'code' => 405,
                        'message' => 'Token invalido'
                    );
                }

            }else{
                $data = array(
                    'status' => 'error',
                    'code' => 401,
                    'message' => 'No token'
                );
            }
        }else{
            $data = array(
                'status' => 'error',
                'code' => 400,
                'message' => 'No data'
            );
        }

        return response()->json($data);        
    }
}
