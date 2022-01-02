<?php

namespace Prodemmi\Lava;

use Illuminate\Contracts\Support\Arrayable;

abstract class Filter implements Arrayable
{

    public $title;

    abstract function fields();

    abstract function handle($query, array $fields);

    public function toArray()
    {

        return [
            'filter' => static::class,
            'title'  => $this->title,
            'fields' => collect( $this->fields() ?? [] )->unique( 'column' )->toArray()
        ];

    }

}
