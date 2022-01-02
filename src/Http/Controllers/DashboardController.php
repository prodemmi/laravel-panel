<?php

namespace Prodemmi\Lava\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{

    public function index()
    {
        return view( 'lava::dashboard' );
    }

    public function logout(Request $request)
    {

    }

}
