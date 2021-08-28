<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class UserController extends Controller
{
    public function register(Request $request){
        $json = json_decode($request->getContent());
        
        if (empty($json->name) && empty($json->email) && empty($json->password)){// valida que no venga vacio
            return "Nombre, email y contraseña son requeridos";
        } else if (empty($json->name) && empty($json->password)) {
            return "Nombre y contraseña son requerido";
        } else if (empty($json->email) && empty($json->password)){
            return "Correo y contraseña son requeridos";
        } else if (empty($json->name) && empty($json->email)){
            return "Nombre y correo son requeridos";
        } else if (empty($json->name)){
            return "Nombre es requerido";
        }else if (empty($json->email)){
            return "Correo es requerido";
        }else if (empty($json->password)){
            return "Password es requerida";
        }

        $name = (!is_null($json->name) && isset($json->name)) ? $json->name : null;
        $email = (!is_null($json->email) && isset($json->email)) ? $json->email : null;
        $pass = (!is_null($json->password) && isset($json->password)) ? $json->password : null;
        


        return 1;      
    }

}
