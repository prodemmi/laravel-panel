<?php

namespace Prodemmi\Lava\Fields;

class Header extends DesignField
{

    public $component = 'lava-header';

    public $title;

    public static function create($title, $fields = [])
    {

        return new static($title, $fields);

    }

    public function __construct($title)
    {

        $this->title = $this->callableValue( $title );
        $this->exceptOnIndex();

    }

}