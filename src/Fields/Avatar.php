<?php

namespace Prodemmi\Lava\Fields;


use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class Avatar extends Field
{

    public $component = 'avatar';

    public $rounded = FALSE;

    public function __construct($name, $column = NULL)
    {
        parent::__construct( $name, $column );

        $this->sortable( FALSE );

    }

    public function rounded($rounded = TRUE)
    {

        $this->rounded = $this->callableValue( $rounded );

        return $this;

    }

    public function toArray()
    {
        return array_merge( parent::toArray(), [
            'rounded' => $this->rounded
        ] );
    }

}