<?php

namespace Prodemmi\Lava\Fields;

use Prodemmi\Lava\Element;
use Prodemmi\Lava\Fieldable;

class Stack extends Element
{

    use  Fieldable;

    public $fields = [];

    public $forDesign = TRUE;

    public $component = 'lava-stack';

    public $direction = 'column';

    public $stack = TRUE;

    public static function create($fields)
    {

        return new static( $fields );

    }

    public function __construct($fields)
    {

        $this->fields = $this->callableValue( $fields );

        $this->showOnAll();

    }

    public function row()
    {

        $this->direction = 'row';

        return $this;

    }

    public function toArray()
    {

        return array_merge( parent::toArray(), [
            'fields'       => $this->fields,
            'stack'        => TRUE,
            'direction'    => $this->direction,
            'forDesign'    => $this->forDesign,
            'showOnForm'   => $this->showOnForm,
            'showOnIndex'  => $this->showOnIndex,
            'showOnDetail' => $this->showOnDetail
        ] );

    }

}
