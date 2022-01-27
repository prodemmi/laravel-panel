<?php

namespace Prodemmi\Lava\Http\Middleware;

use Illuminate\Support\Facades\Session;
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

        $panel = Lava::getActivePanel($request->url());

        $seeWhen = $panel->authenticated($request) ?? false;

        if (!$seeWhen) {

            session()->put('auth_must_redirect', $panel->route . ".panel");

            return redirect()->route("auth.index");
            
        }

        return $next($request);

    }
}
