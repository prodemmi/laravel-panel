<?php

namespace Prodemmi\Lava\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Prodemmi\Lava\Facades\Lava;

class DashboardController extends Controller
{

    public function __constructor(){

        dd(session('auth_must_redirect'));

    }

    public function index()
    {

        return view( 'lava::dashboard' );
        
    }

}
