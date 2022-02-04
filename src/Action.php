<?php

namespace Prodemmi\Lava;

use Illuminate\Contracts\Support\Arrayable;

abstract class Action implements Arrayable
{

    public $name;

    public $help;

    public $icon;

    public $danger = FALSE;

    public $onlyOnTable = TRUE;

    public $color = 'gray-700';

    protected $fields = [];

    protected function iconTemplate()
    {

        if ( $this->danger ) {

            $this->color = 'red-700';

        }

        return "<i class='ri-$this->icon-line text-$this->color'></i>";
    }

    abstract public function fields(): array;

    abstract public function handle($collection, $values, $resource): array;

    public function toArray()
    {

        return [
            'action'      => static::class,
            'name'        => $this->name,
            'help'        => $this->help,
            'icon'        => $this->iconTemplate(),
            'danger'      => $this->danger,
            'onlyOnTable' => $this->onlyOnTable,
            'fields'      => $this->fields() ?? []
        ];

    }

}
