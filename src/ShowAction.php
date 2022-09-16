<?php

namespace Prodemmi\Lava;

class ShowAction extends Action
{
    public $name = 'Show';

    public $icon = 'eye';

    public function fields(): array
    {
        return [];
    }

    public function showOn($row, $resource): bool
    {

        return true;

    }

    public function handle($collection, $values, $resource): array
    {

        $primaryKey = $collection->first()[$resource->getPrimaryKey()];

        return ActionStatus::route( 'detail', [
            'primaryKey' => urlencode( $primaryKey ),
            'resource'   => $resource->route()
        ] );

    }

    public function getIcon(){

        return 'eye';

    }

}