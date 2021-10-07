<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Helpers\JwtAuth;
use Illuminate\Support\Facades\Storage;

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
            $infoUser = $JwtAuth->info($json->token);
            return $infoUser;
        }
    }

    // crear funcion para subir avatar 

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
                        Storage::delete($isset_user->avatar);
                        $path = $request->file('image')->store('avatar'.'/'.$user->sub);
                    }else{
                        $path = $request->file('image')->store('avatar'.'/'.$user->sub);
                    }
                
                } catch (\Throwable $th) {
                    //throw $th;
                    return response([
                        'error' => $th
                    ]);
                }
                
                $isset_user->avatar = $path;
                if($isset_user->save()){
                    $data = array(
                        "status" => "success",
                        "code" => 200,
                        "message" => "Imagen almacenada correctamente"
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

    public function getAvatar(Request $request){
        $JwtAuth = new JwtAuth();
       
        if(isset($request->token)){
            
            $user = $JwtAuth->checkToken($request->token, true);
            
            if($user){
                //devolver imagen avatar
                $isset_user = User::where('email', '=', $user->email)->first();
                try {
                    $file = Storage::disk('avatar')->get('2/8IaAarSgVolBykPdECdjIj12XA3XSL0JhG3SWpdF.jpg');
                    if($file){
                        $data = array(
                            "status" => "success",
                            "code" => 200,
                            "message" => "Aqui va la imagen wey",
                            "image" => $file
                        );
                    }else{
                        $data = array(
                            "status" => "error",
                            "code" => 400,
                            "message" => "Imagen no encontrada"
                        );
                    }
                } catch (\Throwable $th) {
                    $data = array(
                        "status" => "error catch",
                        "message" => $th
                    );
                }
                return $data;
            }
        }
    }

}
