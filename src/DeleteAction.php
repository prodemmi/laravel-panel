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
        
        $activeTool = $this->activeTool();

        if($activeTool && isset($activeTool::$model)){

            $model = $this->activeTool()->getModelInstance();

            if (in_array('Illuminate\Database\Eloquent\SoftDeletes', class_uses($model))) {
    
                return [
                    Boolean::create('Force Delete')
                ];
            }

        }


        return [];
    }

    public function showOn($row, $resource): bool
    {

        return $resource->creatableWhen() ?? false;
    }

    public function handle($collection, $values, $resource): array
    {

        $forceDelete = $values['force_delete'] ?? false;

        $model      = $resource->getModelInstance();
        $primaryKey = $resource->getPrimaryKey();

        try {

            $collection->each(function ($row) use ($model, $primaryKey, $forceDelete) {

                $model->where($primaryKey, $row[$primaryKey])->first()->{$forceDelete ? 'forceDelete' : 'delete'}();

            });

            $counts = $collection->count();

            $end = $forceDelete ? 'deleted' : 'trashed';

            if ($counts > 1) {
             
                $message = "$counts Records successfully $end.";

            } else {

                $message = "Record " . $collection->first()[$primaryKey] . " successfully $end.";

            }

            return ActionStatus::success($message);

        } catch (\Exception $e) {

            return ActionStatus::error($e->getMessage());
            
        }
    }

    public function getIcon(){

        return 'delete-bin';

    }

}
