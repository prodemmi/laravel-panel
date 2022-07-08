<?php

namespace Prodemmi\Lava\Fields;


use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class Badge extends Field
{

    public $component = 'badge';
    public $map      = [];
    public $options      = [];
    public $types
    = [
        'danger'  => 'danger',
        'success' => 'success',
        'info'    => 'info',
        'warning' => 'warning'
    ];

    public function __construct($name, $column = NULL)
    {
        parent::__construct($name, $column);

        $this->display(function ($value) {

            return $this->options[$value];
        });

        $this->exceptOnForms();
    }

    public function map(array $map)
    {

        $this->map = $map;

        return $this;
    }

    public function options(array $options)
    {

        $this->options = $this->callableValue($options);

        return $this;
    }

    public function types(array $types)
    {

        $this->types = $types;

        return $this;
    }

    public function toArray()
    {
        return array_merge(parent::toArray(), [
            'data'  => $this->map,
            'options'  => $this->options,
            'types' => $this->types
        ]);
    }
}
