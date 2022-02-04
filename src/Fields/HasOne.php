<?php

namespace Prodemmi\Lava\Fields;

use Illuminate\Support\Str;

class HasOne extends RelationField
{

    public $component = 'has-one';

    public $relation = 'hasOne';

    public function __construct($name, $resource, $column = NULL)
    {

        if ( is_null( $column ) ) {

            $column = Str::of( $name )->lower()->singular() . '_id';

        }

        parent::__construct( $name, $column );

        $this->resource = $resource;

    }

}