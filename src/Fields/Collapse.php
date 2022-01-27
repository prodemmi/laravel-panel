<?php

namespace Prodemmi\Lava\Fields;

class Collapse extends DesignField
{

    public $component = 'lava-collapse';

    public function defaultOpened($defaultOpened = TRUE)
    {

        return $this->attributes( [
            'default-opened' => $this->callableValue( $defaultOpened )
        ] );

    }

}
