<?php

namespace Prodemmi\Lava\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Prodemmi\Lava\Facades\Lava;
use Prodemmi\Lava\Utils\ArrayHelper;

class PanelController extends Controller
{

    public function __construct()
    {
        $this->middleware( 'api' );
    }

    public function resources()
    {

        $resources = Lava::getActivePanel()->getResources();

        return response()->json( ArrayHelper::group_by( $resources, 'group' ) );
    }

    public function logout(Request $request)
    {

    }

}
