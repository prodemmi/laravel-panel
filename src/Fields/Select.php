<?php

namespace Prodemmi\Lava\Fields;


use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class Select extends Field
{

    public $component = 'select';

    public $options = [];

    public $searchable = FALSE;

    public $multiple = FALSE;

    public $maxSelect = 1;

    public function __construct($name, $column = NULL)
    {
        parent::__construct( $name, $column );

        $this->displayValue( function ($value) {

            return $this->options[$value] ?? NULL;

        } );

    }

    public function options($options)
    {
        $this->options = $this->callableValue( $options );

        return $this;
    }

    public function searchable($searchable)
    {
        $this->searchable = $this->callableValue( $searchable );

        return $this;
    }

    public function multiple($multiple, $length = 1)
    {
        $this->multiple  = $this->callableValue( $multiple );
        $this->maxSelect = $length;

        return $this;
    }

    public function toArray()
    {
        return array_merge( parent::toArray(), [
            'options'    => $this->options,
            'searchable' => $this->searchable,
            'multiple'   => $this->multiple,
            'maxSelect'  => $this->maxSelect
        ] );
    }

}