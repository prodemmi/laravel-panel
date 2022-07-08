<?php

namespace Prodemmi\Lava\Fields;

use Prodemmi\Lava\Element;
use Prodemmi\Lava\Fieldable;

class Tab extends Element
{

    use Fieldable;

    public $component = 'lava-tab';

    public $tabs = [];

    public $fields = [];

    public $forDesign = true;

    protected $tabs_data = [];

    public static function create($tabs)
    {
        return new static($tabs);
    }

    public function __construct($tabs)
    {

        $tabs = $this->callableValue($tabs);
        $this->tabs_data = $tabs;

        $this->tabs = $this->getTabs($tabs);
        $this->fields = $this->getFields($tabs);

        $this->exceptOnIndex();
    }

    protected function getTabs($tabs){
        return collect($tabs)->map(fn($tab) => $tab['title'])->flatten();
    }

    protected function getFields($tabs){

        return collect($tabs)->map(fn($tab) => $tab['fields'])->flatten();
        
    }

    public function toArray()
    {

        return array_merge(parent::toArray(), [
            'fields'       => $this->fields,
            'tabs'         => $this->tabs,
            'tabs_data'    => $this->tabs_data,
            'forDesign'    => $this->forDesign,
            'showOnForms'   => $this->showOnForms,
            'showOnIndex'  => $this->showOnIndex,
            'showOnDetail' => $this->showOnDetail
        ]);

    }
}
