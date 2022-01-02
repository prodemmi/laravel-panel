<?php

namespace Prodemmi\Lava;

trait Resolvable
{

    public $resolveCallbacks = [];

    abstract public function resolveValue($callback);

}
