<?php

namespace Prodemmi\Lava\Fields;


use Carbon\Carbon;
use DateTimeInterface;

class Time extends Field
{

    public $component = 'time';

    public function min($min)
    {

        return $this->attributes( 'min', $this->callableValue( $min ) );

    }

    public function max($max)
    {

        return $this->attributes( 'max', $this->callableValue( $max ) );

    }

    public function toArray()
    {
        return array_merge( parent::toArray(), [
            'format' => 'HH:mm',
            'jalali' => $this->jalali
        ] );
    }

}