<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class UserController extends Controller
{
    public function register(Request $request){
        $json = json_decode($request->getContent());
        if (!empty($json->name) && !empty($json->email) && !empty($json->password)){

            $name = (!is_null($json->name) && isset($json->name)) ? $json->name : null;
            $email = (!is_null($json->email) && isset($json->email)) ? $json->email : null;
            $pass = (!is_null($json->password) && isset($json->password)) ? $json->password : null;
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

}
