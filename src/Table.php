<?php

namespace Prodemmi\Lava;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

trait Table
{

    public $total, $total_pages;

    public function search()
    {

        $searchIn = $this->resource()->getSearches();

        $this->records->where( function ($q) use ($searchIn) {

            foreach ( $searchIn as $search ) {

                $toSearch = is_numeric( $this->search ) ? $this->search : '%' . $this->search . '%';

                $q->orWhere( $search, 'like', $toSearch );

            }

        } );

    }

    public function sort()
    {

        if ( $this->sort ) {

            $this->records->orderBy( $this->sort['column'], $this->sort['direction'] );

        }
        else {

            $this->records->orderBy( ...$this->resource()::getSort() );

        }

        return $this;

    }

    public function filter()
    {

        $filters = $this->filter['filter'];

        $newFilters = [];

        foreach ( $filters as $filter ) {

            $value = $filter['value'] ?? '';
            $value = $filter['where']['where'] === 'like' ? "%$value%" : $value;

            $newFilters[] = [ $filter['column'], $filter['where']['where'], $value ];

        }

        $this->records = $this->records->where( $newFilters );

    }

    public function pagination()
    {

        $records = $this->records->offset( ( $this->page - 1 ) * $this->per_page )->limit( $this->per_page )->get();

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
            'max_shows_pages' => $max_shows_pages
        ];

    }

    public function resolveValue($records, $display = TRUE, $value = TRUE, $resource = NULL)
    {

        $fields = ( $resource ?? $this->resource() )::getFieldsOfForDesign();

        return $records->map( function ($row) use ($fields, $display, $value) {

            $rowToSent = $row->toArray();

            $row = array_map( function ($value) {

                return [
                    'value'   => $value,
                    'display' => $value
                ];

            }, $rowToSent );

            foreach ( $fields as $field ) {

                if ( filled( $field->resolveCallbacks ) && $value ) {

                    $field->value = data_get( $row, $field->column . ".value" );

                    foreach ( $field->resolveCallbacks as $resolveCallback ) {

                        if ( !is_null( $resolveCallback ) ) {

                            $field->value = call_user_func( $resolveCallback, $field->value, $rowToSent, $this->env );

                        }

                    }

                    data_set( $row, $field->column . ".value", $field->value );

                }

                if ( filled( $field->displayCallbacks ) && $display ) {

                    $field->value = data_get( $row, $field->column . ".value" );

                    foreach ( $field->displayCallbacks as $displayCallback ) {

                        if ( !is_null( $displayCallback ) ) {

                            $field->value = call_user_func( $displayCallback, $field->value, $rowToSent, $this->env );

                        }

                    }

                    data_set( $row, $field->column . ".display", $field->value );

                }

            }

            return $row;

        } );

    }

    public function resource()
    {

        return resolve( $this->resource );

    }

    public function model()
    {

        return resolve( $this->model );

    }

    public function removeDisplay($row)
    {

        return array_map( function ($rts) {
            return $rts['value'];
        }, $row );

    }

}