<?php

namespace Prodemmi\Lava;


use Prodemmi\Lava\Fields\Boolean;

class DeleteAction extends Action
{

    use ActiveTool;

    public $name = 'Delete';

    public $help = 'Do you sure to delete ?';

    public $icon = 'delete-bin';

    public $danger = TRUE;

    public $onlyOnTable = FALSE;

    public function fields(): array
    {

//        $model          = $this->activeTool()::getModelInstance();
//        $useSoftDeletes = in_array( 'Illuminate\Database\Eloquent\SoftDeletes', class_uses( $model ) );
//
//        if ( $useSoftDeletes ) {
//
//            return [
//            ];
//
//        }
//

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