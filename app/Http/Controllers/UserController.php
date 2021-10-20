<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Helpers\JwtAuth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function register(Request $request){
        $json = json_decode($request->getContent());
        
        if (!empty($json)){

            $name = (!is_null($json) && isset($json->name)) ? $json->name : null;
            $email = (!is_null($json) && isset($json->email)) ? $json->email : null;
            $pass = (!is_null($json) && isset($json->password)) ? $json->password : null;
            $user = new User();
            $user->name = $name;
            $user->email = $email;
            $user->password = hash('sha256', $pass);
            $isset_user = User::where('email' , '=', $email)->first();
            
            if(empty($isset_user)){
                $user->save();

                $data = array(
                    'status' => 'success',
                    'code' => 200,
                    'message' => 'User creado'
                );
            } else {
                $data = array(
                    'status' => 'error',
                    'code' => 400,
                    'message' => 'User ya existe'
                );
            }
        } else {
            $data = array(
                'status' => 'error',
                'code' => 400,
                'message' => 'User NO creado'
            );
        }
        
        return response()->json($data);      
    }

    public function login(Request $request){
        $jwtAuth = new JwtAuth();

        $json = json_decode($request->getContent());
        $email = (!is_null($json) && isset($json->email)) ? $json->email : null;
        $pass = (!is_null($json) && isset($json->password)) ? $json->password : null;
        $getToken = (!is_null($json) && isset($json->gettoken)) && $json->gettoken == true ? $json->gettoken  : null;
        $pwd = hash('sha256', $pass);
        
        if (!empty($json->email) && !empty($json->password) && ($getToken == null || $getToken == 'false')){
            $signup = array (
                'token' => $jwtAuth->signup($email, $pwd)
            );    
        } elseif ($getToken != null) {

            $signup = array(
                'token' => $jwtAuth->signup($email, $pwd, $getToken) 
            );       
        } else {
            $signup = array(
                'status' => 'error',
                'code' => 400,
                'message' => 'User NO ingresado'
            );
        }
        return $signup;
    }

    public function token(){
        return response()->json([
            'token' => csrf_token()  
        ]);
    }
    
    public function info(Request $request){
        $JwtAuth = new JwtAuth();
        $json = json_decode($request->getContent());
        // al obtener informacion de usuario validar token tiempo de login y authorization app
        if($json->token){
            $user = $JwtAuth->info($json->token);
            $isset_user = User::where('email', '=', $user->email)->first();
            return $isset_user;
        }
    }

    public function storeAvatar(Request $request){
        $JwtAuth = new JwtAuth();

        if(isset($request->token) && isset($request->image)){
            $user = $JwtAuth->checkToken($request->token, true);
            if($user){
                // guardar imagen 
                $isset_user = User::where('email' , '=', $user->email)->first();
                try {
                    //code...
                    if($isset_user->avatar){
                        Storage::delete('avatar/'.$isset_user->avatar);
                        $path = $request->file('image')->store('avatar'.'/'.$user->sub);
                    }else{
                        $path = $request->file('image')->store('avatar'.'/'.$user->sub);
                    }
                
                }catch (\Throwable $th){
                    //throw $th;
                    return response([
                        'error' => $th
                    ]);
                }
                $new_path = str_replace("avatar/", "", $path);
                $isset_user->avatar = $new_path;
                if($isset_user->save()){
                    $data = array(
                        "status" => "success",
                        "code" => 200,
                        "message" => "Imagen almacenada correctamente",
                        "avatar" => $new_path
                    );
                }else{
                    $data = array(
                        "status" => "success",
                        "code" => 400,
                        "message" => "Imagen no almacenada"
                    );
                }

            }else{
                $data = array(
                    "status" => "error",
                    "code" => 400,
                    "message" => "NO AUTHORIZATION"
                );
            }
        }else{
            $data = array(
                "status" => "error",
                "code" => 400,
                "message" => "NO AUTHORIZATION, NO IMG"
            );
        } 
        return $data;  
    }

    public function getAvatar($id, $img){
        $file = Storage::disk('avatar')->get($id.'/'.$img);
        return response($file, 200);
    }

    public function updateProfileName(Request $request){
        $JwtAuth = new JwtAuth();
        $user = $JwtAuth->checkToken($request->token, true);
        
        if(isset($request->token) && isset($request->name) && isset($user)){
            $isset_user = User::where('email', '=', $user->email)->first();
            $isset_user->name = $request->name;

            if($isset_user->save()){
                $data = array(
                    'status' => 'success',
                    'code' => 200,
                    'message' => 'Nombre actualizado satisfactoriamente'
                );
            }else{
                $data = array(
                    'status' => 'error',
                    'code' => 400,
                    'message' => 'Error al actualizar nombre'
                );
            }
        }else{
            $data = array(
                'status' => 'error',
                'code' => 400,
                'message' => 'Error al obtener token y nombre'
            );
        }
        
        return response($data);
    }

    public function updateProfileEmail(Request $request){
        $JwtAuth = new JwtAuth();

        if(isset($request->token) && isset($request->email)){
            $user = $JwtAuth->checkToken($request->token, true);
            $isset_user = User::where('email', '=', $user->email)->first();
            $repeat_email = User::where('email', '=', $request->email)->first();
            $password = hash('sha256', $request->pass);
    
            if(($isset_user) && ($password == $isset_user->password) ){
                if(!$repeat_email){
                    $isset_user->email = $request->email;
                    if($isset_user->save()){
                        $signup = array (
                            'token' => $JwtAuth->signup($isset_user->email, $password)
                        );
                        if($signup){
                            $data = $signup;
                        }else{
                            $data = array(
                                'status' => 'error',
                                'code' => 400,
                                'message' => 'Error de credenciales'
                            );
                        }

                    }else{
                        $data = array(
                            'status' => 'error',
                            'code' => 401,
                            'message' => 'Error al actualizar email'
                        );
                    }
                }else{
                    $data = array(
                        'status' => 'error',
                        'code' => 406,
                        'message' => 'Error email ya registrado'
                    );
                }
            }else{
                $data = array(
                    'status' => 'error',
                    'code' => 403,
                    'message' => 'Error al obtene usuario'
                );
            }
        }else{
            $data = array(
                'status' => 'error',
                'code' => 402,
                'message' => 'Error al obtener token'
            );
        }

        return response($data);
    }

    public function updateProfilePass(Request $request){
        $JwtAuth = new JwtAuth();
        if(isset($request->token) && isset($request->pass)){
            $user = $JwtAuth->checkToken($request->token, true);
            $isset_user = User::where('email', '=', $user->email)->first();
            if($isset_user){
                $passwordDb = hash('sha256', $request->passOld);
                
                if($user->password == $passwordDb){
                    $pwd = hash('sha256', $request->pass);
                    $isset_user->password = $pwd;
                    if($isset_user->save()){
                        $data = array(
                            'status' => 'success',
                            'code' => 200,
                            'message' => 'Contraseña actualizada satisfactoriamente'
                        );
                    }else{
                        $data = array(
                            'status' => 'error',
                            'code' => 405,
                            'message' => 'La contraseña no se almaceno'
                        );
                    }
                }else{
                    $data = array(
                        'status' => 'error',
                        'code' => 400,
                        'message' => 'Ingresar contraseña correcta'
                    );
                }
            }else{
                $data = array(
                    'status' => 'error',
                    'code' => 403,
                    'message' => 'Error al obtener usuario'
                );
            }
        }else{
            $data = array(
                'status' => 'error',
                'code' => 402,
                'message' => 'Error al obtener token'
            );
        }

        return response($data);
    }
}
