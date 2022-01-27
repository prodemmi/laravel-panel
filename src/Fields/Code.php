<?php

namespace Prodemmi\Lava\Fields;


use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class Code extends Field
{

    public $component = 'code';

    public $mode;

    public $theme = 'darcula';

    public $options = [];

    public function __construct($name, $column = NULL)
    {
        parent::__construct($name, $column);

        $this->exceptOnIndex();
    }

    public function mode($mode)
    {

        $this->mode = $this->callableValue($mode);

        return $this;
    }

    public function theme($theme)
    {

        $this->theme = $this->callableValue($theme);

        return $this;
    }

    public function options($options)
    {

        $this->options = $this->callableValue($options);

        return $this;
    }

    public function toArray()
    {
        return array_merge(
            parent::toArray(),
            [
                'mode' => $this->mode,
                'theme' => $this->theme,
                'options' => $this->options
            ]
        );
    }
}
