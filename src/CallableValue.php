<?php

namespace Prodemmi\Lava;

trait CallableValue
{

    protected function callableValue($value)
    {

        return is_callable( $value ) && !is_string($value) ? $value() : $value;

    }


}
