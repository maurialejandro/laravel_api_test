<?php
namespace App\Helpers;

use Firebase\JWT\JWT;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class JwtAuth{
    
    public $key;

    public function __construct(){
        $this->key = 'key-secret-for-token-789654321';
    }

    public function signup($email, $password, $gettoken=null){

        $user = User::where(array('email' => $email, 'password' => $password))->first();
   
        $signup = false;
        if(is_object($user)){
            $signup = true;
        } 
        
        if($signup){
            // Entregar token
            $token = array( 
                'sub' => $user->id,
                'email' => $user->email,
                'name' => $user->name,
                'password' => $user->password,
                'iat' => time(), 
                'exp' => time() + (7 * 24 * 60 * 60)
            );
            
            $jwt = JWT::encode($token, $this->key, 'HS256');
            $decoded = JWT::decode($jwt, $this->key, array(('HS256')));
            if(is_null($gettoken)){
                return $jwt;
            } else {
                return $decoded;
            }
        } else {
            // Error
            return $signup;
        }
    }

    public function checkToken($jwt, $getIdentity = false){
        $auth = false;
       
        try {
            $decoded = JWT::decode($jwt, $this->key, array('HS256'));
        } catch (\UnexpectedValueException $e) {
            $auth = false;
        } catch (\DomainException $e){
            $auth = false; 
        }

        if(isset($decoded) && is_object($decoded) && isset($decoded->sub)){
            $auth = true;
        } else {
            $auth = false;
        }

        if($getIdentity){
            return $decoded;
        } 

        return $auth;
    }

    public function info($token){
        if($token){
            try{
                if($this->checkToken($token)){
                    $decoded = JWT::decode($token, $this->key, array('HS256'));
                    return $decoded;
                }
            }catch(\DomainException $e){
                $error = response()->json([
                    'error' => $e
                ]);
            }
        }else{
            $error = response()->json([
                'status' => 'error',
                'code' => 400,
                'message' => 'NO TOKEN'
            ]);
        }

        return $error;

    }
}

?>