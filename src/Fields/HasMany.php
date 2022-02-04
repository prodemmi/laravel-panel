<?php

namespace Prodemmi\Lava\Fields;

use Illuminate\Support\Str;

class HasMany extends RelationField
{

    public $component = 'has-many';

    public $relation = 'hasMany';

    public function __construct($name, $resource, $column)
    {

        if ( is_null( $column ) ) {

            $column = Str::of( $name )->lower()->plural();

        }

        parent::__construct( $name, $column );

        $this->resource = $resource;

    }

}