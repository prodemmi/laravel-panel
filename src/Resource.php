<?php

namespace Prodemmi\Lava;

use Illuminate\Support\Arr;

abstract class Resource extends Tools
{

    public static $group = 'Resources';

    public static $model;
    public static $primaryKey;
    public static $selectable = TRUE;
    public static $searches   = [ '*' ];
    public static $perPages   = [ 25, 50, 100 ];

    protected $with = [];

    public abstract static function fields(): array;

    public abstract static function actions(): array;

    public abstract static function filters(): array;

    public abstract static function creatableWhen(): bool;

    public abstract static function editableWhen(): bool;

    public abstract static function deletableWhen(): bool;

    public static function getPrimaryKey()
    {

        return static::$primaryKey ?? self::getModelInstance()->getKeyName();

    }

    public static function getModelInstance()
    {
        return new static::$model;
    }

    public function selects()
    {
        return collect( static::fields() )->filter( function ($field) {

            return $field->showOnIndex === TRUE && !str_contains( $field->column, '.' );

        } )->pluck( 'column' )->toArray();
    }

    public function headers()
    {
        return collect( static::fields() )->filter( function ($field) {

            return $field->showOnIndex === TRUE;

        } )->map( function ($field) {

            return [
                'name'     => $field->name,
                'column'   => $field->column,
                'sortable' => $field->sortable,
                'show'     => !$field->hideDefault
            ];

        } )->toArray();
    }

    public function findField($column)
    {

        return collect( static::fields() )->filter( function ($field) use ($column) {

            return $field->column == $column;

        } )->first();

    }

    public function getWith()
    {

        return collect( static::fields() )->filter( function ($field) {

            return str_contains( $field->column, '.' );

        } )->map( function ($field) {

            return Arr::first( explode( '.', $field->column ) );

        } )->toArray();

    }

    public function getSearches()
    {

        if ( count( static::$searches ) === 1 && head( static::$searches ) === '*' ) {

            return collect( static::fields() )->pluck( 'column' )->toArray();

        }

        return static::$searches;

    }

    protected static function getActions()
    {

        return collect( static::actions() )
            ->unique()
            ->prepend( DeleteAction::class )
            ->prepend( ShowAction::class )
            ->prepend( EditAction::class )
            ->map( function ($action) {

                if ( is_string( $action ) ) {

                    return ( new ( $action )() )->toArray();

                }

                return $action->toArray();

            } )
            ->toArray();

    }

    protected static function getFilters()
    {

        return array_map( function ($filter) {

            if ( is_string( $filter ) ) {

                return ( new ( $filter )() )->toArray();

            }

            return $filter->toArray();

        }, array_unique( static::filters() ) );

    }

    public function toArray()
    {
        $parent         = parent::toArray();
        $parent['tool'] = FALSE;

        return array_merge( $parent, [
            'resource'   => static::class,
            'model'      => static::$model,
            'selectable' => static::$selectable,
            'searches'   => static::$searches,
            'perPages'   => static::$perPages,
            'primaryKey' => static::getPrimaryKey(),
            'fields'     => collect( static::fields() )->unique( 'column' )->toArray(),
            'actions'    => static::getActions(),
            'filters'    => static::getFilters(),
            'active'     => TRUE,
            'creatable'  => static::creatableWhen(),
            'editable'   => static::editableWhen(),
            'deletable'  => static::deletableWhen()
        ] );
    }
}
