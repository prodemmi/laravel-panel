<?php

namespace Prodemmi\Lava;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;

trait Table
{

    public $total, $total_pages;

    public function search()
    {

        $searchIn = $this->resource()->getSearches();

        $this->records = $this->records->where( function ($q) use ($searchIn) {

            foreach ( $searchIn as $search ) {

                $toSearch = is_numeric( $this->search ) ? $this->search : '%' . $this->search . '%';

                $q->orWhere( $search, 'like', $toSearch );

            }

        } );

    }

    public function sort()
    {

        if ( filled($this->sort) ) {

            $this->records = $this->records->orderBy( $this->sort['column'], $this->sort['direction'] );

        }
        else {

            $this->records = $this->records->orderBy( ...$this->resource()->getSort() );

        }

        return $this;

    }

    public function filter()
    {

        $filters = $this->filter['filter'] ?? [];

        $newFilters = [];

        foreach ( $filters as $filter ) {

            $value = $filter['value'] ?? '';
            $value = $filter['where']['where'] === 'like' ? "%$value%" : $value;

            $n = [ $filter['column'], $filter['where']['where'], $value];

            if($filter['relation'] ?? false){

                $n[] = $filter['relation'];

                $res = resolve($filter['resource']);
                $n[] = $this->resource()->getWith();
                $n[] = $res->getPrimaryKey();

            }

            $newFilters[] = $n;


        }

        $this->records = $this->records->where( array_filter($newFilters, fn($f) => !isset($f[3])) );

        $relFilters = array_filter($newFilters, fn($f) => isset($f[3]));

        if(filled($relFilters)){

            foreach($relFilters as $filter){

                $column = collect($filter[4])->first(function($fil, $key)use($filter){
                    return str_contains($filter[0], $fil);
                }) ?? $filter[0];

                $this->records = $this->records->whereHas($column, function($q)use($filter){
                    
                    if(is_array($filter[2])){

                        $op = $filter[1] == '<>' ? 'whereNotIn' : 'whereIn';

                        $q->{$op}($filter[5], $filter[2]);

                    }else{

                        $q->where($filter[5], $filter[1], $filter[2]);

                    }

                });

            }

        }

    }

    public function pagination($records)
    {

        $records = $records->offset( ( $this->page - 1 ) * $this->per_page )->limit( $this->per_page )->get();

        $this->total = $records->count();

        $this->total_pages = (int)ceil( $this->total / $this->per_page );

        $max_shows_pages = 8;

        $offset = $this->page - round( $max_shows_pages / 2 ) - $this->total_pages;

        if ( $this->total_pages - $this->page <= ( $max_shows_pages / 2 ) ) {

            $offset += $this->total_pages - $this->page - round( $max_shows_pages / 2 );

        }

        $range = range( 1, $this->total_pages - 1 );
        $range = collect( $range )->filter( function ($number) {
            return $number >= 2;
        } );

        $links = $range->slice( $offset, $max_shows_pages )->map( function ($link) {

            return [
                'link'   => $link,
                'label'  => $link,
                'active' => TRUE
            ];

        } );

        if ( ( $links->first()['link'] ?? PHP_INT_MIN ) - 1 >= 2 ) {

            $links->prepend( [
                'link'   => NULL,
                'label'  => '...',
                'active' => TRUE
            ] );

        }

        if ( $this->total_pages - ( $links->last()['link'] ?? PHP_INT_MAX ) >= 2 ) {

            $links->push( [
                'link'   => NULL,
                'label'  => '...',
                'active' => TRUE
            ] );

        }

        $links->prepend( [
            'link'   => 1,
            'label'  => 1,
            'active' => TRUE
        ] );

        $links->push( [
            'link'   => $this->total_pages,
            'label'  => $this->total_pages,
            'active' => TRUE
        ] );

        return [
            'rows'            => $this->resolveValue( $records ),
            'headers'         => $this->resource()->headers(),
            'current_page'    => $this->page,
            'per_page'        => $this->per_page,
            'total'           => $this->total,
            'all'             => $this->all,
            'total_pages'     => $this->total_pages,
            'links'           => $links,
            'max_shows_pages' => $max_shows_pages,
            'last'            => $this->getLast($this->resource())
        ];

    }

    public function resolveValue($records, $display = TRUE, $value = TRUE, $resource = NULL, $export = FALSE, $selects = [], $createActions = true)
    {

        $res = $resource ?? $this->resource();

        $fields = $res->getFieldsOfForDesign();

        return $records->map( function ($row) use ($fields, $display, $value, $export, $selects, $res, $createActions) {

            $rowToSend = is_array($row) ? $row : $row->toArray();
            $row = array_map( function ($value) {

                return [
                    'value'   => $value,
                    'display' => $value
                ];

            }, $rowToSend );;

            foreach ( $fields as $field ) {

                if ( filled( $selects ) && !in_array( $field->column, $selects) ) {

                    continue;

                }

                if ( $export && !$field->inExport() ) {

                    continue;

                }

                if ( filled( $field->resolveCallbacks ) && $value ) {
                    
                    $field->value = data_get( $row, $field->column . ".value" );

                    foreach ( $field->resolveCallbacks as $resolveCallback ) {

                        if ( isset( $resolveCallback ) ) {

                            $field->value = call_user_func( $resolveCallback, $field->value, $rowToSend, $this->env, $export );

                        }

                    }

                    if ( blank( $field->value ) ) {

                        $field->value = config( 'lava.table.empty' );

                    }

                    data_set( $row, $field->column . ".value", $field->value );

                }

                if ( filled( $field->displayCallbacks ) && $display ) {

                    $field->value = data_get( $row, $field->column . ".value" );

                    foreach ( $field->displayCallbacks as $displayCallback ) {

                        if ( !is_null( $displayCallback ) ) {

                            $field->value = call_user_func( $displayCallback, $field->value, $rowToSend, $this->env, $export );

                        }

                    }

                    if ( blank( $field->value ) ) {

                        $field->value = config( 'lava.table.empty' );

                    }

                    data_set( $row, $field->column . ".display", $field->value );

                }

            }

            if($createActions){

                return [
                    'rows' => $row,
                    'actions' => array_map(function($action)use($row, $res){
    
                        return [
                            'name' => $action['action'],
                            'show' => resolve($action['action'])->showOn($this->removeDisplay($row), $res)
                        ];
    
    
                    }, $res->getActions())
                ];
                
            }

            return $row;

        } );

    }

    protected function resource()
    {

        return resolve( $this->resource );

    }

    protected function model()
    {

        return resolve( $this->model );

    }

    protected function removeDisplay($row)
    {

        return array_map( function ($rts) {
            return $rts['value'] ?? $rts;
        }, $row );

    }

    protected function removeValue($rows)
    {
        foreach ( $rows as &$row ) {

            $row = array_map( function ($rts) {
                
                return $rts['display'];

            }, $row );

        }

        return $rows;

    }

    public function getLast($resource){

        $model = $resource->getModelInstance();
        $created_at = Schema::hasColumn($model->getTable(), 'created_at');
                    
        $last_key = $created_at ? 'created_at' : $model->getKeyName();

        return [
            'resource' => get_class($resource),
            'last'     => $model->select($last_key)->orderBy($last_key, 'desc')->first()[$last_key] ?? null,
            'last_key' => $last_key,
            'new_count'=> 0
        ];

    }

}