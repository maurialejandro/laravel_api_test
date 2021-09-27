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
        // Mejorar forma de retornar valores, crear variable y llenarla segun corresponda luego hacer solo un return o los menos posibles
        $hash = $request->header('Authorization', null);
        $JwtAuth = new JwtAuth();
        
        if(isset($hash) && !empty($hash)){
            $checkToken = $JwtAuth->checkToken($hash);
            
            if($checkToken){
                $json = json_decode($request->getContent());
                $user = $JwtAuth->checkToken($hash, true);
               
                if(isset($json->name) && isset($json->price)){
                    $plato = new Plato();
                    $plato->user_id = $json->user_id;
                    $plato->name = $json->name;
                    $plato->price = $json->price;
                    if($plato->save()){
                        return response()->json(array(
                            'status' => 'success',
                            'code' => 200,
                            'message' => 'Plato guardado satisfactoriamente'
                        ));
                    }else{
                        return response()->json(array(
                            'status' => 'error',
                            'code' => 400,
                            'message' => 'Ocurrio error al intentar guardar'
                        ));
                    }

                }else{
                    return response()->json(array(
                        'status' => 'error',
                        'code' => 400,
                        'message' => 'Falto ingresar campo requerido'
                    ));
                }
            } else{
                return response()->json(array(
                    'status' => 'error',
                    'message' => 'Token Invalido'
                ));
            }
        }else{
            return response()->json(array(
                'status' => 'error',
                'message' => 'No token'
            ));
        }
        
    }
}
