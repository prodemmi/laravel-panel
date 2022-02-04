<?php

namespace Prodemmi\Lava\Fields;


use Facade\FlareClient\Http\Exceptions\MissingParameter;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class BelongsTo extends RelationField
{

    public $component = 'belongs-to';

    public $relation = 'belongsTo';

    public function __construct($name, $resource, $column = NULL)
    {

        if ( is_null( $column ) ) {

            $column = Str::of( $name )->lower()->singular() . '_id';

        }

        parent::__construct( $name, $column );

        $this->resource = $resource;

    }

}