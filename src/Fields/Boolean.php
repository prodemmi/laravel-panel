<?php

namespace Prodemmi\Lava\Fields;


use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class Boolean extends Field
{

    public $component = 'boolean';

    public $trueValue = TRUE;

    public $falseValue = FALSE;

    public function __construct($name, $column = NULL)
    {
        parent::__construct( $name, $column );

        $this->displayValue( function ($value) {

            if ( $value === $this->trueValue ) {

                return TRUE;

            }

            if ( $value === $this->falseValue ) {

                return FALSE;

            }

            return NULL;

        } );

    }

    public function trueValue($value)
    {
        $this->trueValue = $this->callableValue( $value );

        return $this;
    }

    public function falseValue($value)
    {
        $this->falseValue = $this->callableValue( $value );

        return $this;
    }

}