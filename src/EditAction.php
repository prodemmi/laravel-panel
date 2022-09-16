<?php

namespace Prodemmi\Lava;


class EditAction extends Action
{
    public $name = 'Edit';

    public $icon = 'edit';

    public function fields(): array
    {
        return [];
    }

    public function showOn($row, $resource): bool
    {

        return $resource->editableWhen() ?? false;
    }

    public function handle($collection, $values, $resource): array
    {

        $primaryKey = $collection->first()[$resource->getPrimaryKey()];

        return ActionStatus::route('edit', ['primaryKey' => $primaryKey, 'resource' => $resource->route()]);
    }

    public function getIcon()
    {

        return 'edit';
    }
}
