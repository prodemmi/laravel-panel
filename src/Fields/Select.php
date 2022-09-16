<?php

namespace Prodemmi\Lava\Fields;

class Select extends Field
{

    public $component = 'select';

    public $options = [];

    public $multiple = FALSE;

    public $limit = 1;

    public $searchable;

    public $searchCallback;

    public function __construct($name, $column = NULL)
    {
        parent::__construct( $name, $column );

        $this->display( function ($value) {

            return $this->options[$value]['label'] ?? $value;

        } );
    }

    public function options($options, $searchable = FALSE)
    {

        if ( $searchable ) {

            $this->searchable     = $searchable;
            $this->searchCallback = $options;

        }
        else {

            $this->options = $this->optionResolve( $this->callableValue( $options ) );
        }

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

    public function multiple($limit = 1, $multiple = TRUE)
    {

        $this->attributes( 'multiple', $this->callableValue( $multiple ) );

        $this->limit = $limit;

        return $this;
    }

    public function toArray()
    {
        return array_merge( parent::toArray(), [
            'options'    => $this->options,
            'searchable' => $this->searchable,
            'limit'      => $this->limit
        ] );
    }
}
