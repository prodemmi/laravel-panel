<?php

namespace Prodemmi\Lava\Fields;

class Group extends Field
{

    public $component = 'group';

    public $options = [];

    public $multiple = false;

    public function __construct($name, $column = NULL)
    {
        parent::__construct( $name, $column );

        $this->display( function ($value) {

            return $this->options[$value]['label'] ?? $value;

        } );

    }

    public function options($options)
    {

        $this->options = $this->optionResolve( $this->callableValue( $options ) );

        return $this;
    }

    public function multiple($multiple = true)
    {

        $this->multiple = $this->callableValue( $multiple );

        return $this;
    }

    protected function optionResolve($options)
    {

        return array_map( function ($label, $key) {

            return [
                'value' => $key,
                'label' => $label
            ];
        }, $options, array_keys( $options ) );

    }

    public function toArray()
    {
        return array_merge( parent::toArray(), [
            'options'    => $this->options,
            'multiple'   => $this->multiple
        ] );
    }
}
