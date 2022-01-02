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

            $this->records->orderBy( $this->resource()::getPrimaryKey(), 'DESC' );

        }

    }

    public function filter()
    {

        foreach ( $this->filter as $filter ) {

            $f = new( $filter['filter'] )();

            $this->records = $f->handle( $this->records, $filter['fields'] ?? [] );
        }

    }

    public function pagination()
    {

        $records = $this->records->offset( ( $this->page - 1 ) * $this->per_page )->limit( $this->per_page )->get();

        $this->total       = $this->records->count();
        $this->total_pages = round( $this->total / $this->per_page );

        $max_shows_pages = 8;

        $offset = $this->page - round( $max_shows_pages / 2 ) - $this->total_pages;

        if ( $this->total_pages - $this->page <= ( $max_shows_pages / 2 ) ) {

            $offset += $this->total_pages - $this->page - round( $max_shows_pages / 2 );

        }

        $links = collect( range( 2, $this->total_pages - 1 ) )
            ->slice( $offset, $max_shows_pages )
            ->map( function ($link) {

                return [
                    'link'   => $link,
                    'label'  => $link,
                    'active' => TRUE
                ];

            } );

        if ( $links->first()['link'] - 1 >= 2 ) {

            $links->prepend( [
                'link'   => NULL,
                'label'  => '...',
                'active' => TRUE
            ] );

        }

        if ( $this->total_pages - $links->last()['link'] >= 2 ) {

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
            'total_pages'     => $this->total_pages,
            'links'           => $links,
            'max_shows_pages' => $max_shows_pages
        ];

    }

    public function resolveValue($records, $display = TRUE, $value = TRUE)
    {

        $fields = collect( $this->resource()->fields() );

        return $records->map( function ($row) use ($fields, $display, $value) {

            $rowToSent = $row->toArray();

            $row = array_map( function ($value) {

                return [
                    'value'   => $value,
                    'display' => $value
                ];

            }, $rowToSent );

            foreach ( $fields as $field ) {

                if ( $field->resolveCallbacks && $value ) {

                    $field->value = data_get( $row, $field->column . ".value" );

                    foreach ( $field->resolveCallbacks as $resolveCallback ) {

                        if ( !is_null( $resolveCallback ) ) {

                            $field->value = call_user_func( $resolveCallback, $field->value, $rowToSent );

                        }

                    }

                    data_set( $row, $field->column . ".value", $field->value );

                }

                if ( $field->displayCallbacks && $display ) {

                    $field->value = data_get( $row, $field->column . ".value" );

                    foreach ( $field->displayCallbacks as $displayCallback ) {

                        if ( !is_null( $displayCallback ) ) {

                            $field->value = call_user_func( $displayCallback, $field->value, $rowToSent );

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

        return new ( $this->resource )();

    }

    public function model()
    {

        return new ( $this->model )();

    }

    public function removeDisplay($row)
    {

        return array_map( function ($rts) {
            return $rts['value'];
        }, $row );

    }

}