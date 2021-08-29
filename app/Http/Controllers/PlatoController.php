<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\JwtAuth;

class PlatoController extends Controller
{
    public function index(Request $request){
        $hash = $request->header('Authorization', null);
        $JwtAuth = new JwtAuth();
        $checkToken = $JwtAuth->checkToken($hash);

        if($checkToken){
            echo "Ingresaste"; die();
        }else{
            echo "No Autenticado Index"; die();
        }
    }
}
