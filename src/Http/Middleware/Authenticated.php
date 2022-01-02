<?php

namespace Prodemmi\Lava\Http\Middleware;

use Prodemmi\Lava\Facades\Lava;

class Authenticated
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return \Illuminate\Http\Response
     */
    public function handle($request, $next)
    {

        $seeWhen = Lava::getActivePanel()->authenticated($request);
        
        if ( !$seeWhen ) {
            
            return redirect( Lava::getActivePanel()->route . "/login" );

        }

        return $next( $request );

    }
}
