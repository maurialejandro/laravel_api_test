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

    public function store(Request $request){
        $JwtAuth = new JwtAuth();
        
        if(isset($request->name) && isset($request->price) && isset($request->description)){
            if(isset($request->token)){
                $checkToken = $JwtAuth->checkToken($request->token);
                if($checkToken){
                    $json = json_decode($request->getContent());
                    $user = $JwtAuth->checkToken($hash, true);
                   
                    if(isset($json->name) && isset($json->price)){
                        $plato = new Plato();
                        $plato->user_id = $json->user_id;
                        $plato->name = $json->name;
                        $plato->price = $json->price;
                        if($plato->save()){
                            $data = array(
                                'status' => 'success',
                                'code' => 200,
                                'message' => 'Plato guardado satisfactoriamente'
                            );
                        }else{
                            $data = array(
                                'status' => 'error',
                                'code' => 400,
                                'message' => 'Ocurrio error al intentar guardar plato'
                            );
                        }
    
                    }else{
                        $data = array(
                            'status' => 'error',
                            'code' => 400,
                            'message' => 'Falto ingresar campo'
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
