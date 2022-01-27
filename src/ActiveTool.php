<?php

namespace Prodemmi\Lava;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Str;

trait ActiveTool
{


    public function activeTool()
    {

        $current = url()->current();

        $url = ltrim( str_replace( Request::root(), "", $current ), "/" );

        $route = explode( '/', $url )[str_contains( $current, '/tool' ) ? 2 : 1] ?? NULL;

        if ( $route ) {

            $panel = Lava::getActivePanel();
            $res   = collect( $panel->resources )->first( function ($resource, $key) use ($route) {
                $resource = new $resource();
                return $resource::route() == $route;

            } );

            return new $res() ?? FALSE;

        }

        return FALSE;

    }

}