<?php

namespace Prodemmi\Lava\Fields;

class Boolean extends Field
{

    public $component = 'boolean';

    public $trueValue = TRUE;

    public $falseValue = FALSE;

    public function __construct($name, $column = NULL)
    {
        parent::__construct( $name, $column );

        $this->display( function ($value, $env, $export) {

            if ( $value === $this->trueValue ) {

                $value = TRUE;

            }

            if ( $value === $this->falseValue ) {

                $value = FALSE;

            }

            if($export){
                return $value ? 'True' : 'False' ;
            }

            return $value;

        } );

    }

    public function display($callback)
    {

        parent::display($callback);

        $this->asHtml();

        return $this;
    }

    public function trueValue($value)
    {
        $this->trueValue = $this->callableValue( $value );

        return $this;
    }

    public function falseValue($value)
    {
        $this->falseValue = $this->callableValue( $value );

        return $this;
    }

    public function switchDisplay($switch = true)
    {

        if($this->callableValue( $switch )){
            $this->component = 'switch';
        }

        return $this;
    }

}