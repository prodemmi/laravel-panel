<?php

namespace Prodemmi\Lava\Facades;

use Illuminate\Support\Facades\Facade;

class Lava extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'lava';
    }
}