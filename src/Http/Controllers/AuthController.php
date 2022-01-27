<?php

namespace Prodemmi\Lava\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{

    public function index()
    {

        return view("lava::auth.login");
        
    }  

    public function login(Request $request)
    {

        $request->validate([
            'email' => 'required',
            'password' => 'required'
        ]);
   
        $credentials = $request->only('email', 'password');
        if (Auth::attempt($credentials, $request->input('remember', false))) {

            $route = session()->pull('auth_must_redirect');

            if($route){

                return redirect()->route($route);

            }

            return redirect('/');

        }

        return redirect()->route("auth.index");

    }

    public function logout() {

        session()->flush();
        Auth::logout();
  
        return redirect()->route("auth.index");

    }

}
