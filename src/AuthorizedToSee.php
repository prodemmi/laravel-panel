<?php

namespace Prodemmi\Lava;

use Closure;
use Illuminate\Http\Request;

trait AuthorizedToSee
{

    /**
     * List of the registered metrics classes
     *
     * @var array
     */
    public $seeWhenCallback;


    public function seeWhen(Closure $callback)
    {

        $this->seeWhenCallback = $callback;

        return $this;

    }

    public function authenticated(Request $request)
    {
        return $this->seeWhenCallback ? call_user_func( $this->seeWhenCallback, $request ) : TRUE;
    }

}
