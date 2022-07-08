<?php

namespace Prodemmi\Lava\Metrics;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

abstract class TrendMetric extends Metric
{

    public $component = 'trend-metric';
    
    public $disableLabels = false;

    public function __construct(){
        $this->width('33%')->styles('min-height: 120px', 'height: 100%');
    }

    public function count($modelClass, $period)
    {

        return $this->calculateAggregate($modelClass, __FUNCTION__, $period, NULL, NULL);
    }

    public function avg($modelClass, $period, $column, $dateColumn = NULL)
    {

        return $this->calculateAggregate($modelClass, __FUNCTION__, $period, $column, $dateColumn);
    }

    public function sum($modelClass, $period, $column, $dateColumn = NULL)
    {

        return $this->calculateAggregate($modelClass, __FUNCTION__, $period, $column, $dateColumn);
    }

    public function max($modelClass, $period, $column, $dateColumn = NULL)
    {

        return $this->calculateAggregate($modelClass, __FUNCTION__, $period, $column, $dateColumn);
    }

    public function min($modelClass, $period, $column, $dateColumn = NULL)
    {

        return $this->calculateAggregate($modelClass, __FUNCTION__, $period, $column, $dateColumn);
    }

    protected function calculateAggregate($modelClass, $function, $period, $column, $dateColumn)
    {

        $model = $modelClass instanceof Builder ? $modelClass->getModel() : resolve($modelClass);

        $dateColumn = $dateColumn ?? $model->getCreatedAtColumn();

        $column = $function === 'count' ? '*' : $column;
        
        // $data = with(new $model)->whereDate($dateColumn, '>=', now()->subDays($this->range))
        //             ->get($selects)
        //             ->groupBy(function($record) use ($period, $dateColumn) {

        //                 $date = data_get($record, $dateColumn);
        //                 $period = $this->getFormatFromPeriod($period);

        //                 return $period ? Carbon::parse($date)->format($period) : Carbon::parse($date)->diffInDays($period);
        //             })
        //             ->orderBy($dateColumn);

        $period = Str::singular($period);

        $data = $model
            ->select(DB::raw("$function($column) as value, HOUR($dateColumn) as label"))
            ->whereDate($dateColumn, '>=', Carbon::now()->subDays($this->range))
            ->groupBy("label")
            ->get()
            ->toArray();

        return [
            'data'   => $data,
            'name'   => $function,
            'period' => $period
        ];

    }

    protected function getFormatFromPeriod($period){

        switch (strtolower($period)) {
            case 'minutes':
                return 'i';
            case 'hours':
                return 'h';
            case 'days':
                return 's';
            case 'months':
                return 'm';
            case 'years':
                return 'Y';
            default:
                return false;
        }

    }

    public function toArray(){

        return array_merge(parent::toArray(), [
            'disableLabels' => $this->disableLabels
        ]);

    }

    
}
