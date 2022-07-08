<?php

namespace Prodemmi\Lava\Fields;

use Prodemmi\Lava\Element;
use Prodemmi\Lava\Fieldable;

class Layout extends Element
{

    use  Fieldable;

    public $fields = [];

    public $forDesign = true;

    public $component = 'lava-layout';

    protected $direction = 'row';

    public static function create($fields)
    {

        return new static( $fields );

    }

    public function __construct($fields)
    {

        $this->fields = $this->callableValue( $fields );

    }

    public function column()
    {

        $this->direction = 'column';

        return $this;

    }

    public function toArray()
    {

        return array_merge( parent::toArray(), [
            'fields'       => $this->fields,
            'direction'    => $this->direction,
            'forDesign'    => true,
            'showOnForms'   => $this->showOnForms,
            'showOnIndex'  => $this->showOnIndex,
            'showOnDetail' => $this->showOnDetail
        ] );

    }

}
