<?php

namespace Prodemmi\Lava;


class DeleteAction extends Action
{
    public $name = 'Delete';

    public $icon = 'delete-bin';

    public $danger = TRUE;

    public $onlyOnTable = FALSE;

    public function fields(): array
    {
        return [];
    }

    public function handle($collection, $values, $resource): array
    {

        $model      = $resource::getModelInstance();
        $primaryKey = $resource->getPrimaryKey();

        try {

            $collection->each( function ($row) use ($model, $primaryKey) {

                $model->where( $primaryKey, $row[$primaryKey] )->first()->delete();

            } );

            $counts = $collection->count();


            if ( $counts > 1 ) {

                $message = "$counts Records successfully deleted.";

            }
            else {

                $message = "Record " . $collection->first()[$primaryKey] . " successfully deleted.";

            }

            return ActionStatus::success( $message );

        }
        catch ( \Exception $e ) {

            return ActionStatus::error( $e->getMessage() );

        }

    }
}