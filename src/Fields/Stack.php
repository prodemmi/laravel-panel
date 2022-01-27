<?php

namespace Prodemmi\Lava\Fields;

class Stack extends DesignField
{

    public $component = 'lava-stack';

    public $direction = 'column';

    public $stack = true;

    public function row()
    {

        $this->direction = 'row';

        return $this;
    }

    public function toArray()
    {
        
        return array_merge(parent::toArray(), [
            'direction' => $this->direction,
            'stack'     => true
        ]);
        
    }
}
