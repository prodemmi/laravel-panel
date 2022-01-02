<?php

namespace Prodemmi\Lava\Fields;


use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class Number extends Field
{

    public $component = 'number';

    public $currency;

    public function __construct($name, $column = NULL)
    {
        parent::__construct( $name, $column );

        $this->sortable();

    }

    public function min($min = 0)
    {

        $this->attributes( [ 'min' => $this->callableValue( $min ) ] );

        return $this;
    }

    public function max($max = NULL)
    {
        $this->attributes( [ 'max' => $this->callableValue( $max ) ] );

        return $this;
    }

    public function step($step = 1)
    {
        $this->attributes( [ 'step' => $this->callableValue( $step ) ] );

        return $this;
    }

}