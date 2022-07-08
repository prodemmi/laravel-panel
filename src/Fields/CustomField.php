<?php

namespace Prodemmi\Lava\Fields;

use Illuminate\Support\Str;

class CustomField extends Field {

    public $component = 'div';
    public $custom    = TRUE;

    public function __construct ($name)
    {

        $column = Str::of($name)->snake()->lower();

        parent::__construct($name, $column);

        $this->noSqlSelect();

    }

    public function toArray()
    {
        return array_merge( parent::toArray(), [
            'custom'       => $this->custom
        ] );
    }

}
