<?php

namespace Prodemmi\Lava\Fields;


use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class Slug extends Field
{

    public $component = 'text';

    public $createFrom;

    public function __construct($name, $column = NULL)
    {
        parent::__construct( $name, $column );

        $this->onlyOnDetail();

    }

    public function createFrom($column){

        $this->createFrom = $column;

        return $this;

    }

    public static function createSlug($text, $separator = '-'){

        if (is_array($text)){
            $text = implode($separator, $text);
        }

        return Str::slug($text, $separator);

    }

}