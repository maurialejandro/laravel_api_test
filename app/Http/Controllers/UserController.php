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
                    'message' => 'User ya creado'
                );
            }
        } else {
            $data = array(
                'status' => 'error',
                'code' => 400,
                'message' => 'User NO creado'
            );
        }
        
        return $data;      
    }

    public function login(Request $request){
        $jwtAuth = new JwtAuth();

        $json = json_decode($request->getContent());
        
        if (!empty($json->email) && !empty($json->password)){

            $email = (!is_null($json) && isset($json->email)) ? $json->email : null;
            $pass = (!is_null($json) && isset($json->password)) ? $json->password : null;
            $getToken = (!is_null($json) && isset($json->gettoken)) && $json->gettoken == true ? $json->gettoken  : null;
            
            $pwd = hash('sha256', $pass);
            
            $signup = $jwtAuth->signup($email, $pwd);
           
            $data = array(
                'status' => 'success',
                'code' => 200,
                'token' => $signup
            );
            
        } else {
            $data = array(
                'status' => 'error',
                'code' => 400,
                'message' => 'User NO creado'
            );
        }
        return $data;
    }
}
