<?php

namespace Prodemmi\Lava;

use Illuminate\Support\Arr;

trait ResolveData
{

    public function resolveValue($records, $createDisplay = TRUE, $createValue = TRUE, $resource = NULL, $export = FALSE, $selects = [], $createActions = TRUE)
    {

        $resource = $resource ?? $this->resource();

        $empty = config('lava.table.empty');

        return $records->map(function ($row) use ($createDisplay, $createValue, $export, $selects, $resource, $createActions, $empty) {

            $rowToSend = $mainRow = is_array($row) ? $row : $row->toArray();

            foreach (array_keys($mainRow) as $column) {

                $field = $resource->findField($column);

                if (!$field) {
                    continue;
                }

                if ($field->showIfCallback && !call_user_func($field->showIfCallback, $mainRow, $this->env)) {
                    continue;
                }

                if (filled($selects) && !in_array($field->column, $selects)) {
                    continue;
                }

                if ($export && !$field->inExport()) {
                    continue;
                }

                $column    = (string)$field->column;
                $value     = data_get($row, $column);
                
                if ($createValue) {

                    data_set($rowToSend, "$column.value", $value);
                    
                }
                
                if ($createDisplay) {
                    
                    data_set($rowToSend, "$column.display", $this->createDisplay($field, $value, $mainRow, $export, $empty));

                }

                if (($field->searchable ?? FALSE) && filled($field->searchCallback)) {

                    data_set($rowToSend, "$column.display", $this->createSearchableField($field, $value, $empty));

                }

            }

            if ($createActions) {

                $rowToSend = $this->addActionsForRow($rowToSend, $resource);
                
            }

            return $rowToSend;

        });
    }

    public function mapValue($array){
        return array_map(function($row){

            if(is_array($row) && !isset($row['value'], $row['display'])){
                return $row;
            }

            return isset($row['value']) ? $row['value'] : $row;
        }, $array);
    }

    protected function createSearchableField($field, $value, $empty)
    {

        if (filled($value)) {

            $options = call_user_func($field->searchCallback, $value);

            $value = optional($options->first(function ($option) use ($value) {
                return $option['value'] === $value;
            }));

            return filled($value->label) ? $value->label : $empty;

        }

    }

    protected function addActionsForRow($row, $res)
    {

        return ['rows' => $row, 'actions' => array_map(function ($action) use ($row, $res) {

            return ['name' => $action['class'], 'show' => resolve($action['class'])->showOn($this->mapValue($row), $res)];

        }, $res->getActions())];

    }

    protected function createDisplay($field, $value, $rowToSend, $export, $empty)
    {

        if (filled($field->displayCallbacks)) {

            foreach ($field->displayCallbacks as $displayCallback) {

                if ($field->custom ?? FALSE)
                    $value = call_user_func($displayCallback, $rowToSend, $this->env, $export);
                else
                    $value = call_user_func($displayCallback, $value, $rowToSend, $this->env, $export);

            }

            return filled($value) ? $value : $empty;

        }

        return $value;

    }

}
