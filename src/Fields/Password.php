<?php

namespace Prodemmi\Lava\Fields;

class Password extends Field
{

    public $component = 'password';

    public function __construct($name, $column)
    {
        parent::__construct( $name, $column );

        $this->exceptOnIndex();

    }

}