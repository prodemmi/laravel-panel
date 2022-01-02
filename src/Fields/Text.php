<?php

namespace Prodemmi\Lava\Fields;


use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class Text extends Field
{

    public $component = 'text';

    public $suggestions = [];

    public $prepend;

    public $append;


    public function suggestions($suggestions)
    {
        $this->suggestions = $this->callableValue( $suggestions );

        return $this;
    }

    public function prepend($prepend)
    {

        if ( is_null( $this->prepend ) ) {

            $this->prepend = $this->callableValue( $prepend );

            array_push( $this->resolveCallbacks, function ($value) {

                return $this->prepend . "$value";

            } );

        }

        return $this;
    }

    public function append($append)
    {

        if ( is_null( $this->append ) ) {

            $this->append = $this->callableValue( $append );

            array_unshift( $this->resolveCallbacks, function ($value) {

                return "$value" . $this->append;

            } );

        }

        return $this;
    }

    public function link($regex, $blank = FALSE)
    {

        $regex = $this->callableValue( $regex );

        $this->displayValue( function ($value, $row) use ($regex, $blank) {

            preg_match_all( "/\{(.*?)\}/", $regex ?? $value, $matches );

            $variables = Arr::only( $row, $matches[1] );

            $link = Str::of( $regex )->replace( $matches[0], array_values( $variables ) )->rtrim( '/' );

            $target = $blank ? '_blank' : '_self';

            return "<a href='$link' target='$target'>$value</a>";

        } );

        return $this;

    }

}