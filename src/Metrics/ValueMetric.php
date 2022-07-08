<?php

namespace Prodemmi\Lava\Metrics;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

abstract class ValueMetric extends Metric
{

    public $component = 'value-metric';

    public function __construct(){
        $this->width('33%')->styles('min-height: 100px');
    }

    protected function count($modelClass){

        return $this->calculateAggregate($modelClass, __FUNCTION__, NULL, NULL);

    }

    protected function avg($modelClass, $column, $dateColumn = NULL){

        return $this->calculateAggregate($modelClass, __FUNCTION__, $column, $dateColumn);

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

            $previousValue = (float)$previousValue->count();
            $value = (float)$value->count();

        }else{
            $previousValue = (float)$previousValue->{$function}($column);
            $value = (float)$value->{$function}($column);
        }

        if (!$previousValue && $value > 0){

            return [
                'percent' => number_format($value * 100 , 2, '.', ''),
                'value'   => $value,
                'previous'=> $previousValue
            ];
            
        }elseif (!$previousValue){

            return [
                'percent' => number_format(0, 2, '.', ''),
                'value'   => $value,
                'previous'=> $previousValue
            ];

        }

        return [
            'percent' => number_format((($value - $previousValue) / $previousValue) * 100, 2, '.', ''),
            'value'   => $value,
            'previous'=> $previousValue
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
