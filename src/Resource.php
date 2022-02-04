<?php

namespace Prodemmi\Lava;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

abstract class Resource extends Tools
{

    public static $group = 'Resources';

    public static $model;
    public static $primaryKey = 'id';
    public static $selectable = TRUE;
    public static $searches   = [ '*' ];
    public static $perPages   = [ 25, 50, 100 ];
    public static $sort       = 'id desc';
    public static $subtitle;

    protected $with = [];

    public abstract static function fields(): array;

    public abstract static function actions(): array;

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

        return static::getFieldsOfForDesign()->flatten( 1 )->filter( function ($field) {

            if ( !$field->select ) {

                return FALSE;

            }

            switch ( $field->relation ?? '' ) {
                case 'hasMany':
                    return FALSE;
                case 'morphToMany':
                    return FALSE;
                    break;
            }

            $show = ( $field->showOnIndex ?? FALSE ) || ( $field->showOnDetail ?? FALSE ) || ( $field->showOnForms ?? FALSE );

            return $show && !str_contains( $field->column, '.' );

        } )->pluck( 'column' )->toArray();
    }

    public function headers()
    {

        $fields = static::getFieldsOfForDesign();

        return $fields->filter( function ($field) {

            return ( $field->stack ?? FALSE ) || $field->showOnIndex === TRUE;

        } )->map( function ($field) {

            return [
                'name'      => $field->name ?? $field->title,
                'column'    => $field->column ?? NULL,
                'stack'     => $field->stack ?? FALSE,
                'sortable'  => $field->sortable ?? FALSE,
                'show'      => !$field->hideDefault,
                'headers'   => $field->fields ?? [],
                'direction' => $field->direction ?? NULL
            ];
        } )->toArray();
    }

    public function findField($column)
    {

        return static::getFieldsOfForDesign()->first( function ($field, $key) use ($column) {

            return isset( $field->column ) && $field->column == $column;
        } );

    }

    public function getWith()
    {

        return static::getFieldsOfForDesign()->filter( function ($field) {

            return str_contains( $field->column, '.' ) || ( $field->relation ?? FALSE );

        } )->map( function ($field) {

            switch ( $field->relation ?? '' ) {
                case 'hasOne':
                    return str_replace( '_id', '', $field->column );
                    break;
                case 'belongsTo':
                    return str_replace( '_id', '', $field->column );
                    break;
            }

            return Arr::first( explode( '.', $field->column ) );

        } )->values()->toArray();
    }

    public function getSearches()
    {

        if ( count( static::$searches ) === 1 && head( static::$searches ) === '*' ) {

            return static::getFieldsOfForDesign()->pluck( 'column' )->toArray();
        }

        return static::$searches;
    }

    protected static function getActions()
    {

        $actions = collect( static::actions() )->unique()->push( ShowAction::class );

        if ( static::editableWhen() ) {
            $actions->push( EditAction::class );
        }

        if ( static::deletableWhen() ) {
            $actions->push( DeleteAction::class );
        }

        return $actions->map( function ($action) {

            if ( is_string( $action ) ) {

                return resolve( $action )->toArray();
            }


            return $action->toArray();
        } )->toArray();

    }

    public static function getFieldsOfForDesign($fields = NULL)
    {

        $fields = $fields ?? static::fields();

        foreach ( $fields as $key => $field ) {

            $field = $fields[$key];

            if ( $field->forDesign ?? FALSE ) {

                $fields[$key] = static::getFieldsOfForDesign( $field->fields ?? [] );
            }

        }

        return collect( $fields )->flatten( 1 )->unique( 'column' );
    }

    public static function getRules()
    {

        $fields = static::getFieldsOfForDesign();
        $rules  = [];

        collect( $fields )->each( function ($field) use (&$rules) {

            if ( isset( $field->column ) )
                $rules[(string)$field->column] = implode( '|', $field->rules ?? [] );
        } );

        return $rules;
    }

    public static function getFilters()
    {

        return DB::table( 'lava_filters' )->where( 'resource', static::class )->get()->map( function ($filter) {

            $filter->filter = json_decode( $filter->filter, TRUE );

            return $filter;

        } );

    }

    public static function getSort()
    {

        $sort    = explode( ' ', static::$sort );
        $sort[1] = strtoupper( $sort[1] );

        return $sort;
    }

    public function toArray()
    {
        $parent         = parent::toArray();
        $parent['tool'] = FALSE;

        return array_merge( $parent, [
            'resource'   => static::class,
            'model'      => static::$model,
            'selectable' => static::$selectable,
            'subtitle'   => static::$subtitle,
            'searches'   => static::$searches,
            'modelKey'   => static::getModelInstance()->getKeyName(),
            'perPages'   => static::$perPages,
            'primaryKey' => static::getPrimaryKey(),
            'fields'     => static::fields(),
            'actions'    => static::getActions(),
            'filters'    => static::getFilters(),
            'sort'       => static::getSort(),
            'active'     => TRUE,
            'creatable'  => static::creatableWhen(),
            'editable'   => static::editableWhen(),
            'deletable'  => static::deletableWhen()
        ] );
    }

}
