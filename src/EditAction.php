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

    public function handle($collection, $values, $resource): array
    {

        $primaryKey = $collection->first()[$resource->getPrimaryKey()];

        return ActionStatus::route( 'edit', [ 'id' => $primaryKey, 'resource' => $resource->route() ] );

    }
}