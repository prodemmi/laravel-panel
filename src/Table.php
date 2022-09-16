<?php

namespace Prodemmi\Lava;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

trait Table {

    use ResolveData;

    public $total, $total_pages;

    public function search ()
    {

        $searchIn = $this->resource()->getSearches();

        $this->records = $this->records->where(function ($q) use ($searchIn) {

            foreach ( $searchIn as $search ) {

                $toSearch = is_numeric($this->search) ? $this->search : '%' . $this->search . '%';

                $q->orWhere($search, 'like', $toSearch);

            }

        });

    }

    public function sort ()
    {

        if ( filled($this->sort) ) {

            $this->records = $this->records->orderBy($this->sort['column'], $this->sort['direction']);

        } else {

            $this->records = $this->records->orderBy(...$this->resource()->getSort());

        }

        return $this;

    }

    public function filter ()
    {

        $filters = $this->filter['filter'] ?? [];

        foreach ( $filters as $filter ) {

            $value = $filter['value'] ?? '';
            $value = $filter['where']['where'] === 'LIKE' ||
                     $filter['where']['where'] === 'NOT LIKE' ? "%$value%" : $value;

            $col = explode('__', $filter['column'])[0];

            $n = [ $col, $filter['where']['where'], $value ];

            if ( $filter['relation'] ?? FALSE ) {

                $n[] = $filter['relation'];

                $res = resolve($filter['resource']);
                $n[] = $this->resource()->getWith();
                $n[] = $res->getPrimaryKey();

            }

            if ( isset($filter['relation']) && $filter['relation'] ) {

                $column = collect($n[4])->first(function ($fil, $key) use ($n) {
                        return str_contains($n[0], $fil);
                    }) ?? $n[0];

                $this->records = $this->records->whereHas($column, function ($q) use ($filter, $n) {

                    if ( is_array($n[2]) ) {

                        $op = $n[1] === '<>' ? 'whereNotIn' : 'whereIn';

                        $op = ( $filter['con']['con'] ?? 'and' ) === 'or' ? ( 'or' . ucfirst($op) ) : $op;

                        $q->{$op}($n[5], $n[2]);

                    } else {

                        $con = ( $filter['con']['con'] ?? 'and' ) === 'and' ? 'where' : 'orWhere';

                        $q->{$con}($n[5], $n[1], $n[2]);

                    }

                });

            } else {

                $con = ( $filter['con']['con'] ?? 'and' ) === 'and' ? 'where' : 'orWhere';

                if ( str_starts_with($filter['component'], 'date') ) {

                    $con .= 'Date';

                }

                $this->records = $this->records->{$con}(...$n);

            }

        }

        $relFilters = array_filter($filters, fn ($f) => isset($f['relation']) && $f['relation']);

    }

    public function pagination ($records)
    {

        $records = $records->offset(( $this->page - 1 ) * $this->per_page)->limit($this->per_page);

        if($this->limit){

            $records = $records->take($this->limit);

        }
        
        $this->total = $records->count();

        $this->total_pages = (int)ceil($this->total / $this->per_page);

        $max_shows_pages = 8;

        $offset = $this->page - round($max_shows_pages / 2) - $this->total_pages;

        if ( $this->total_pages - $this->page <= ( $max_shows_pages / 2 ) ) {

            $offset += $this->total_pages - $this->page - round($max_shows_pages / 2);

        }

        $range = range(1, $this->total_pages - 1);
        $range = collect($range)->filter(function ($number) {
            return $number >= 2;
        });

        $links = $range->slice($offset, $max_shows_pages)->map(function ($link) {

            return [ 'link' => $link, 'label' => $link, 'active' => TRUE ];

        });

        if ( ( $links->first()['link'] ?? PHP_INT_MIN ) - 1 >= 2 ) {

            $links->prepend([ 'link' => NULL, 'label' => '...', 'active' => TRUE ]);

        }

        if ( $this->total_pages - ( $links->last()['link'] ?? PHP_INT_MAX ) >= 2 ) {

            $links->push([ 'link' => NULL, 'label' => '...', 'active' => TRUE ]);

        }

        $links->prepend([ 'link' => 1, 'label' => 1, 'active' => TRUE ]);

        $links->push([ 'link' => $this->total_pages, 'label' => $this->total_pages, 'active' => TRUE ]);

        return [ 'rows' => $this->resolveValue($records->get()), 'headers' => $this->resource()
                                                                            ->headers(), 'current_page' => $this->page, 'per_page' => $this->per_page, 'total' => $this->total, 'all' => $this->all, 'total_pages' => $this->total_pages, 'links' => $links, 'max_shows_pages' => $max_shows_pages, 'last' => $this->getLast($this->resource()) ];

    }

    protected function resource ()
    {

        return resolve($this->resource);

    }

    protected function model ()
    {

        return resolve($this->model);

    }

    // protected function removeDisplay($row)
    // {

    //     return array_map(function ($rts) {
    //         return $rts['value'] ?? $rts;
    //     }, $row);

    // }

    // protected function removeValue($rows)
    // {
    //     foreach ( $rows as &$row ) {

    //         $row = array_map(function ($rts) {

    //             return $rts['display'];

    //         }, $row);

    //     }

    //     return $rows;

    // }

    public function getLast($resource)
    {

        $model = $resource->getModelInstance();
        $created_at = Schema::hasColumn($model->getTable(), 'created_at');

        $last_key = $created_at ? 'created_at' : $model->getKeyName();

        return [ 'resource' => get_class($resource), 'last' => $model->select($last_key)
                                                                     ->orderBy($last_key, 'desc')
                                                                     ->first()[$last_key] ??
                                                               NULL, 'last_key' => $last_key, 'new_count' => 0 ];

    }

}
