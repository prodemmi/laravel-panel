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

    abstract public function fields(): array;

    abstract public function handle($collection, $values, $resource): array;

    abstract public function showOn($row, $resource): bool;

    private function getColor() {

        if ( $this->danger ) {

            return 'danger';

        }

        return $this->color;
        
    }

    public function getIcon(){

        return null;

    }

    public function toArray()
    {

        return [
            'action'      => static::class,
            'name'        => $this->name,
            'help'        => $this->help,
            'icon'        => $this->getIcon() ?? $this->icon,
            'color'       => $this->getColor(),
            'danger'      => $this->danger,
            'onlyOnTable' => $this->onlyOnTable,
            'fields'      => $this->fields() ?? []
        ];

    }

}
