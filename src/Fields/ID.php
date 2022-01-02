<?php

namespace Prodemmi\Lava\Fields;


use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class ID extends Number
{

    public $component = 'number';

    public function __construct($name, $column)
    {
        parent::__construct( $name ?? 'ID', $column );

        $this->sortable()->setOnForm( FALSE );

    }

}