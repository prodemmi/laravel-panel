<?php

namespace Prodemmi\Lava\Fields;


use Carbon\Carbon;
use DateTimeInterface;

class Date extends Field
{

    public $component = 'date';

    public $local = 'en';

    public $jalali = FALSE;

    public function __construct($name, $column = NULL)
    {

        parent::__construct( $name, $column );

        $this->display( function ($value) {

            if(blank($value)){
                return null;
            }

            $value = Carbon::parse($value);

            if($this->jalali){

                return verta($value)->format('Y-m-d');

            }

            return $value->format( 'Y-m-d' );

        } )->sortable();

    }

    public function local($local)
    {

        $this->local = $this->callableValue( $local );
        $this->jalali( $this->local === 'fa' );

        return $this;

    }

    public function jalali($jalali = TRUE)
    {

        $this->jalali = $this->callableValue( $jalali );

        if ( $this->jalali ) {

            $this->local = 'fa';

        }

        return $this;

    }

    public function min($min)
    {

        return $this->attributes( 'min', $this->callableValue( $min ) );

    }

    public function max($max)
    {

        return $this->attributes('max', $this->callableValue( $max ));

    }

    public function toArray()
    {
        return array_merge( parent::toArray(), [
            'format' => 'YYYY-MM-DD',
            'local'  => $this->local,
            'jalali' => $this->jalali
        ] );
    }

}