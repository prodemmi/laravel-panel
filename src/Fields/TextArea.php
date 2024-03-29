<?php

namespace Prodemmi\Lava\Fields;


use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class TextArea extends Field
{

    public $component = 'text-area';

    public $more = FALSE;

    public function __construct($name, $column = NULL)
    {
        
        parent::__construct( $name, $column );

        $this->exceptOnIndex();

    }

    public function more($more = TRUE)
    {

        $this->more = $this->callableValue( $more );

        return $this;

    }

    public function ckeditor(){

        $this->component = 'ckeditor';

        return $this;

    }

    public function toArray()
    {
        return array_merge( parent::toArray(), [ 'more' => $this->more ] );
    }

}