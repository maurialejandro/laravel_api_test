<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\JwtAuth;
use App\Models\Plato;
use Illuminate\Support\Facades\Storage;

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
    public function getFile($id, $img){
	    $file = Storage::disk('plato')->get($id.'/'.$img);
	    return response($file, 200);
    }

    public function getPlato(Request $request){
    $JwtAuth = new JwtAuth();
	if(isset($request->token)){
	    $user = $JwtAuth->checkToken($request->token, true);
	    if($user){
            $limit = $request->limit;
            $skip = $request->skip;
		    $totalPlatos = Plato::where('user_id', '=', $user->sub)->get();
		    $total = count($totalPlatos);
            if($total > 10){
                $limit = 10;
            }else{
                $limit = $total;
            }
		    $platos = Plato::where('user_id', '=', $user->sub)->orderBy('created_at', 'desc')->offset($skip)->limit($limit)->get();
		    $data = array(
		        'status' => 'success',
			    'code' => 200,
			    'message' => 'Obtencion de datos correctamente',
			    'totalPlatos' => $total,
			    'platos' => $platos,
			    'skip' => $skip,
			    'limit' => $limit
		    );
 	    }else{
	        $data = array(
	            'status' => 'error',
	            'code' => 402,
	            'message' => 'Token Invalido'
	        );
	    }
	}else{
	    $data = array(
            	'status' => 'error',
	    	'code' => 401,
		'message' => 'NO TOKEN'
            );
	}
	return response()->json($data);
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
                    $plato->latitudeDelta = $request->latitudeDelta;
                    $plato->longitudeDelta = $request->longitudeDelta;
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

    public function storePuntuation(Request $request){
        $jwtAuth = new JwtAuth();
        if(isset($request->token)){
            $user = $jwtAuth->checkToken($request->token, true);
            if($user){
                $plato = Plato::where('id', '=', $request->id)->first();
                $plato->rating = $request->rating;
                $plato->review = $request->review;
                if($plato->save()){
                    $data = array(
                        'status' => 'success',
                        'code' => 200,
                        'message' => 'Puntuacion almacenada satisfactoriamente'
                    );
                }else{
                    $data = array(
                        'status' => 'error',
                        'code' => 405,
                        'message' => 'NO se almaceno la puntuacion'
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
        return response()->json($data);
    }

    public function addFavorite(Request $request){
        $jwtAuth = new JwtAuth();
        if(isset($request->token)){
            $user = $jwtAuth->checkToken($request->token, true);
            if($user){
                $plato = Plato::where('id', '=', $request->id)->first();
                $plato->is_favorite = true;
                if($plato->save()){
                    $data = array(
                        'status' => 'success',
                        'code' => 200,
                        'message' => 'Estado Actualizado satisfactoriamente'
                    );
                }else{
                    $data = array(
                        'status' => 'error',
                        'code' => 405,
                        'message' => 'no se actualizo el estado'
                    );
                }
            }else{
                $data = array(
                    'status' => 'error',
                    'code' => 402,
                    'message' => 'Error de token'
                );
            }
        }else{
            $data = array(
                'status' => 'error',
                'code' => 400,
                'message' => 'NO TOKEN'
            );
        }

        return response()->json($data);
    }

    public function removeFavorite(Request $request){
        $jwtAuth = new JwtAuth();
        if(isset($request->token)){
            $user = $jwtAuth->checkToken($request->token, true);
            if($user){
                $plato = Plato::where('id', '=', $request->id)->first();
                $plato->is_favorite = false;
                if($plato->save()){
                    $data = array(
                        'status' => 'success',
                        'code' => 200,
                        'message' => 'Estado Actualizado satisfactoriamente'
                    );
                }else{
                    $data = array(
                        'status' => 'error',
                        'code' => 405,
                        'message' => 'no se actualizo el estado'
                    );
                }
            }else{
                $data = array(
                    'status' => 'error',
                    'code' => 402,
                    'message' => 'Error de token'
                );
            }
        }else{
            $data = array(
                'status' => 'error',
                'code' => 400,
                'message' => 'NO TOKEN'
            );
        }
        return response()->json($data);
    }

    public function getFavorites(Request $request){
        $jwtAuth = new Jwtauth();
        if(isset($request->token)){
            $user = $jwtAuth->checkToken($request->token, true);
            if($user){
                $favorite = Plato::where('user_id', '=', $user->sub)->where('is_favorite', '=', true)->get();
                if($favorite){
                    $data = array(
                        'status' => 'success',
                        'code' => 200,
                        'message' => 'Obtencion de platos favoritos correctamente',
                        'favorite' => $favorite
                    );
                }else{
                    $data = array(
                        'status' => 'success',
                        'code' => 200,
                        'message' => 'ningun plato'
                    );
                }
            }else{
                $data = array(
                    'status' => 'error',
                    'code' => 401,
                    'message' => 'Error de token'
                );
            }
        }else{
            $data = array(
                'status' => 'error',
                'code' => 400,
                'message' => 'NO TOKEN'
            );
        }
        return response()->json($data);
    }
}
