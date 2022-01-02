<?php

namespace Prodemmi\Lava\Fields;


use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class Badge extends Field
{

    public $component = 'badge';
    public $data      = [];
    public $types
                      = [
            'danger'  => 'bg-red-600',
            'success' => 'bg-green-600',
            'info'    => 'bg-blue-600',
            'warning' => 'bg-yellow-600',

        ];

    public function __construct($name, $column = NULL)
    {
        parent::__construct( $name, $column );

        $this->setOnIndex();
        $this->setOnDetail();

    }

    public function data(array $data)
    {

        $this->data = $data;

        return $this;

    }

    public function types(array $types)
    {

        $this->types = $types;

        return $this;

    }

    public function toArray()
    {
        return array_merge( parent::toArray(), [
            'data'  => $this->data,
            'types' => $this->types
        ] );
    }

}