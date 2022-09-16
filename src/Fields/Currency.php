<?php

namespace Prodemmi\Lava\Fields;


use Brick\Money\Money;

class Currency extends Number
{

    public $component = 'currency';

    public $format;

    public $locale;

    public $currency;

    public $currencySymbol = NULL;

    public function __construct($name, $column)
    {
        parent::__construct( $name, $column );

        $this->locale   = config( 'app.locale', 'en' );
        $this->currency = config( 'nova.currency', 'USD' );

        $this->step( '0.01' )->min('0.01')->display( function ($value) {

            return $this->format( $value );

        } );

    }

    protected function format($value, $currency = NULL, $locale = NULL)
    {

        return Money::of( $value, $currency ?? $this->currency )->formatTo( $locale ?? $this->locale );

    }

    public function currency($currency)
    {
        $this->currency = $this->callableValue( $currency );

        return $this;
    }

    public function locale($locale)
    {
        $this->locale = $this->callableValue( $locale );

        return $this;
    }

    public function symbol($symbol)
    {
        $this->currencySymbol = $this->callableValue( $symbol );

        return $this;
    }

    public function toArray()
    {
        return array_merge( parent::toArray(), [
            'currencySymbol'  => $this->currencySymbol,
            'currency'        => $this->currency,
            'locale'          => $this->locale,
        ] );
    }

}