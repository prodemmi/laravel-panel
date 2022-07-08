<?php

namespace Prodemmi\Lava\Fields;


class ID extends Number
{

    public $component = 'number';
    public $id        = true;

    public function __construct($name, $column)
    {
        parent::__construct( $name ?? 'ID', $column );

        $this->sortable()->exceptOnForms( );

    }

    public function toArray()
    {
        return array_merge( parent::toArray(), [
            'id' => true
        ] );
    }

}
