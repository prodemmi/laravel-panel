<?php

namespace Prodemmi\Lava\Metrics;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

abstract class ValueMetric extends Metric
{

    public $component = 'value-metric';

    public abstract function calculate();
    public abstract function ranges();

    protected function count($modelClass, $column = NULL, $dateColumn = null){

        return $this->calculateAggregate($modelClass, __FUNCTION__, $column, $dateColumn);

    }

    protected function average($modelClass, $column, $dateColumn = NULL){

        return $this->calculateAggregate($modelClass, 'avg', $column, $dateColumn);

    }

    protected function sum($modelClass, $column, $dateColumn = NULL){

        return $this->calculateAggregate($modelClass, __FUNCTION__, $column, $dateColumn);

    }

    protected function max($modelClass, $column, $dateColumn = NULL){

        return $this->calculateAggregate($modelClass, __FUNCTION__, $column, $dateColumn);

    }

    protected function min($modelClass, $column, $dateColumn = NULL){

        return $this->calculateAggregate($modelClass, __FUNCTION__, $column, $dateColumn);

    }

    protected function calculateAggregate($modelClass, $function, $column, $dateColumn){

        $model = $modelClass instanceof Builder ? $modelClass->getModel() : resolve($modelClass);
        
        $dateColumn = $dateColumn ?? $model->getCreatedAtColumn();
        
        $previousValue = with(clone $model)->whereBetween(
            $dateColumn,
            $this->previousRange()
        );

        $value = with(clone $model)->whereBetween(
            $dateColumn,
            $this->currentRange()
        );

        if($function === 'count') {

            $previousValue = $previousValue->count();
            $value = $value->count();

        }else{
            $previousValue = $previousValue->{$function}($column);
            $value = $value->{$function}($column);
        }

        if (empty($previousValue))
            return [
                'percent' => (float)number_format(0, 2, '.', ''),
                'value'   => (float)$previousValue,
                'previous'=> (float)$previousValue
            ];

        return [
            'percent' => (float)number_format((($value - $previousValue) / $previousValue) * 100, 2, '.', ''),
            'value'   => (float)$value,
            'previous'=> (float)$previousValue
        ];

    }

    protected function previousRange()
    {
        
        if ($this->range == 'M') {
            return [
                now($this->timezone)->subMinutes(2),
                now($this->timezone)->subMinutes(1)
            ];
        }

        if ($this->range == 'H') {
            return [
                now($this->timezone)->subHours(2),
                now($this->timezone)->subHours(1)
            ];
        }

        if ($this->range == 'TODAY') {
            return [
                now($this->timezone)->modify('yesterday')->setTime(0, 0),
                now($this->timezone)->subDays(1)
            ];
        }

        if ($this->range == 'Y') {
            return [
                now($this->timezone)->subYears(1)->firstOfYear()->setTime(0, 0),
                now($this->timezone)->subYearsNoOverflow(1)
            ];
        }

        return [
            now($this->timezone)->subDays($this->range * 2),
            now($this->timezone)->subDays($this->range)
        ];
    }

    protected function currentRange()
    {

        if ($this->range == 'M') {
            return [
                now($this->timezone)->subMinutes(1),
                now($this->timezone),
            ];
        }

        if ($this->range == 'H') {
            return [
                now($this->timezone)->subHours(1),
                now($this->timezone),
            ];
        }

        if ($this->range == 'TODAY') {
            return [
                now($this->timezone)->today(),
                now($this->timezone)
            ];
        }

        if ($this->range == 'Y') {
            return [
                now($this->timezone)->firstOfYear(),
                now($this->timezone)
            ];
        }

        return [
            now($this->timezone)->subDays($this->range),
            now($this->timezone)
        ];
    }

}
