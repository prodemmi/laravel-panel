<?php

namespace Prodemmi\Lava\Fields;

class Avatar extends File
{

    public $maxFiles = 1;

    public $avatar   = true;

    public function __construct($name, $column = NULL)
    {

        parent::__construct($name, $column);
        
        $this->image()->classes('rounded-full');

    }

}