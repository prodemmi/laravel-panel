<?php

namespace Prodemmi\Lava\Fields;

use Prodemmi\Lava\Element;
use Prodemmi\Lava\Fieldable;

class DesignField extends Element
{

    use Fieldable;

    public $title;

    public $fields = [];

    public $forDesign = true;

    public static function create($title, $fields)
    {

        return new static($title, $fields);
    }

    public function __construct($title, $fields)
    {

        $this->title = $this->callableValue($title);

        $this->fields = $this->callableValue($fields);

        $this->showOnAll();
    }

    public function toArray()
    {
        
        return array_merge(parent::toArray(), [
            'title'        => $this->title,
            'fields'       => $this->fields,
            'forDesign'    => $this->forDesign,
            'showOnForm'   => $this->showOnForm,
            'showOnIndex'  => $this->showOnIndex,
            'showOnDetail' => $this->showOnDetail
        ]);
        
    }

}
