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

    protected $fields = [];

    protected function iconTemplate()
    {
        return "<i class='ri-" . $this->icon . "-line'></i>";
    }

    abstract public function fields(): array;

    abstract public function handle($collection, $values, $resource): array;

    public function toArray()
    {

        return [
            'action' => static::class,
            'name'   => $this->name,
            'help'   => $this->help,
            'icon'   => $this->iconTemplate(),
            'danger' => $this->danger,
            'onlyOnTable' => $this->onlyOnTable,
            'fields' => $this->fields() ?? []
        ];

    }

}
