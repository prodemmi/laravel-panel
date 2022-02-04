<?php

namespace Prodemmi\Lava\Fields;


use Facade\FlareClient\Http\Exceptions\MissingParameter;
use Illuminate\Support\Str;

class Morph extends Field
{

    public $relation;

    public $resource;

    public static function create($name, $resource = NULL, $column = NULL)
    {

        if ( blank( $resource ) ) {

            throw MissingParameter::create( 'resource' );

        }

        $static = new static( $name, $resource );

        $static->resource = $resource;
        $static->column   = $column ?? Str::of( $name )->lower()->plural();

        return $static;

    }

    public function __get($name)
    {

        $this->exceptOnIndex()->noSqlSelect();

    }

    public function toArray()
    {
        return array_merge( parent::toArray(), [
            'resource' => $this->resource,
            'relation' => $this->relation
        ] );
    }

}