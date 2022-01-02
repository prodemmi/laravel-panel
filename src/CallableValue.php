<?php

namespace Prodemmi\Lava;

trait CallableValue
{

    protected function callableValue($value)
    {

        return is_callable( $value ) ? $value() : $value;

    }


}
