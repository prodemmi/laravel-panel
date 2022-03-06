<?php

namespace Prodemmi\Lava;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

abstract class Resource extends Tools
{

    public $group = 'Resources';

    public $model;
    public $primaryKey = 'id';
    public $selectable = TRUE;
    public $searches   = [ '*' ];
    public $perPages   = [ 25, 50, 100 ];
    public $sort       = 'id desc';
    public $subtitle;
    public $route;

    protected $with = [];

    public abstract function fields(): array;

    public abstract function actions(): array;

    public abstract function creatableWhen(): bool;

    public abstract function editableWhen(): bool;

    public abstract function deletableWhen(): bool;

    public function getPrimaryKey()
    {

        return $this->primaryKey ?? $this->getModelInstance()->getKeyName();
    }

    public function getModelInstance()
    {
        return new $this->model;
    }

    public function selects()
    {

        return $this->getFieldsOfForDesign()->flatten( 1 )->filter( function ($field) {

            if ( !$field->select ) {

                return FALSE;

            }

            switch ( $field->relation ?? '' ) {
                case 'hasMany':
                    return FALSE;
                case 'morphToMany':
                    return FALSE;
                    break;
                case 'morphedByMany':
                    return FALSE;
                    break;
            }

            $show = ( $field->showOnIndex ?? FALSE ) || ( $field->showOnDetail ?? FALSE ) || ( $field->showOnForms ?? FALSE );

            return $show && !str_contains( $field->column, '.' );

        } )->pluck( 'column' )->toArray();
    }

    public function headers()
    {

        $fields = $this->getFieldsOfForDesign();

        return $fields->filter( function ($field) {

            return ( $field->stack ?? FALSE ) || $field->showOnIndex === TRUE;

        } )->map( function ($field, $key) {

            return [
                'name'      => $field->name ?? $field->title,
                'column'    => $field->column ?? NULL,
                'stack'     => $field->stack ?? FALSE,
                'sortable'  => $field->sortable ?? FALSE,
                'show'      => $key <=2 ? true : !$field->hideDefault,
                'headers'   => $field->fields ?? [],
                'direction' => $field->direction ?? NULL
            ];
        } )->toArray();
    }

    public function findField($column)
    {

        return $this->getFieldsOfForDesign()->first( function ($field, $key) use ($column) {

            return isset( $field->column ) && $field->column == $column;
        } );

    }

    public function getWith()
    {

        return $this->getFieldsOfForDesign()->filter( function ($field) {

            return str_contains( $field->column, '.' ) || ( $field->relationType ?? FALSE ) || ( $field->file ?? FALSE );

        } )->map( function ($field) {

            return Arr::first( explode( '.', $field->column ) );

        } )->values()->toArray();
    }

    public function getSearches()
    {

        if ( count( $this->searches ) === 1 && head( $this->searches ) === '*' ) {

            return $this->getFieldsOfForDesign()->pluck( 'column' )->toArray();
        }

        return $this->searches;
    }

    public function getActions()
    {

        $actions = collect( $this->actions() )->unique()->push( ShowAction::class );

        if ( $this->editableWhen() ) {
            $actions->push( EditAction::class );
        }

        if ( $this->deletableWhen() ) {
            $actions->push( DeleteAction::class );
        }

        return $actions->map( function ($action) {

            if ( is_string( $action ) ) {

                return resolve( $action )->toArray();
            }


            return $action->toArray();
            
        } )->toArray();

    }

    public function getFieldsOfForDesign($fields = NULL)
    {

        $fields = $fields ?? $this->fields();

        foreach ( $fields as $key => $field ) {

            $field = $fields[$key];

            if ( $field->forDesign ?? FALSE ) {

                $fields[$key] = $this->getFieldsOfForDesign( $field->fields ?? [] );
            }

        }

        return collect( $fields )->flatten( 1 )->unique( 'column' );
    }

    public function getRules()
    {

        $fields = $this->getFieldsOfForDesign();
        $rules  = [];

        collect( $fields )->each( function ($field) use (&$rules) {

            if ( isset( $field->column ) )
                $rules[(string)$field->column] = implode( '|', $field->rules ?? [] );
        } );

        return $rules;
    }

    public function getFilters()
    {

        return DB::table( 'lava_filters' )->where( 'resource', get_class($this) )->get()->map( function ($filter) {

            $filter->filters = json_decode( $filter->filters, TRUE );

            return $filter;

        } );

    }

    public function getSort()
    {

        $sort    = explode( ' ', $this->sort );
        $sort[1] = strtoupper( $sort[1] );

        return $sort;
    }

    public function toArray()
    {
        $parent         = parent::toArray();
        $parent['tool'] = FALSE;

        return array_merge( $parent, [
            'resource'   => get_class($this),
            'model'      => $this->model,
            'selectable' => $this->selectable,
            'subtitle'   => $this->subtitle,
            'searches'   => $this->searches,
            'modelKey'   => $this->getModelInstance()->getKeyName(),
            'perPages'   => $this->perPages,
            'primaryKey' => $this->getPrimaryKey(),
            'fields'     => $this->fields(),
            'actions'    => $this->getActions(),
            'filters'    => $this->getFilters(),
            'sort'       => $this->getSort(),
            'active'     => TRUE,
            'creatable'  => $this->creatableWhen(),
            'editable'   => $this->editableWhen(),
            'deletable'  => $this->deletableWhen()
        ] );
    }

}
