<?php

namespace Prodemmi\Lava\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Prodemmi\Lava\Panel;

class MetricsController extends Controller
{

    public function __construct()
    {
        $this->middleware('api');
    }

    public function getMetricData(Request $request)
    {

        $metric = resolve($request->input('name'));

        return response()->json($metric->calc($request->input('range')));
        
    }

}
