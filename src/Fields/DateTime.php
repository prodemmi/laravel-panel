<?php

namespace Prodemmi\Lava\Fields;

use DateTimeInterface;

class DateTime extends Field
{

    public $component = 'date-time';

    public $local;

    public function __construct($name, $column = NULL)
    {

        parent::__construct( $name, $column );

        $this->displayValue( function ($value) {

            if ( !is_null( $value ) && $value instanceof DateTimeInterface ) {

                return $value->format( 'Y-m-d H:i:s' );

            }

            return $value;

        } )->sortable();

    }

    public function local($local)
    {

        $this->local = $this->callableValue( $local );

        return $this;

    }

}