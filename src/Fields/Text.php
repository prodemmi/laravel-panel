<?php

namespace Prodemmi\Lava\Fields;


use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class Text extends Field
{

    public $component = 'text';

    public $suggestions = [];

    public function suggestions($suggestions)
    {
        $this->suggestions = $this->callableValue( $suggestions );

        return $this;
    }
    public function link($regex, $blank = FALSE)
    {

        $regex = $this->callableValue( $regex );

        $this->displayValue( function ($value, $row, $env) use ($regex, $blank) {
            
            if($env === 'index'){

                preg_match_all( "/\{(.*?)\}/", $regex ?? $value, $matches );

                $variables = Arr::only( $row, $matches[1] );

                $link = Str::of( $regex )->replace( $matches[0], array_values( $variables ) )->rtrim( '/' );

                $target = $blank ? '_blank' : '_self';

                return "<a href='$link' target='$target'>$value</a>";
                
            }

            return $value;

        } );

        return $this;

    }

}