<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Helpers\JwtAuth;

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
        if($json->token){
            $infoUser = $JwtAuth->info($json->token);
            return $infoUser;
        }
       
    }
}
