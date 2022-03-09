<?php

namespace Prodemmi\Lava\Fields;

class Avatar extends Field
{

    public $component = 'avatar';
    
    public function rounded()
    {

        return $this->classes('rounded-full');

    }

}