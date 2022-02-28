<?php

namespace Prodemmi\Lava\Fields;


use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class Image extends File
{

    public $component = 'image';

    public $thumbnail;

    public $rounded = FALSE;

    public $fullRounded = FALSE;

    public function __construct($name, $column = NULL)
    {
        parent::__construct( $name, $column );

        $this->acceptTypes( 'image/*' )->hideFromExport();

    }

    public function thumbnail($thumbnail)
    {

        $this->thumbnail = $this->callableValue( $thumbnail );

        return $this;

    }

    public function rounded($fullRounded = FALSE)
    {

        $this->rounded     = TRUE;
        $this->fullRounded = $this->callableValue( $fullRounded );

        return $this;

    }


    public function toArray()
    {
        return array_merge( parent::toArray(), [
            'rounded'     => $this->rounded,
            'fullRounded' => $this->fullRounded
        ] );
    }

}