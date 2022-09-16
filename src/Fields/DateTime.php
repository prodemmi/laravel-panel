<?php

namespace Prodemmi\Lava\Fields;

use Carbon\Carbon;

class DateTime extends Field
{

    public $component = 'date-time';

    public $local = 'en';

    public $jalali = FALSE;

    protected $humanReadable = FALSE;

    public function __construct($name, $column = NULL)
    {

        parent::__construct($name, $column);

        $this->display(function ($value) {

            if (blank($value)) {
                return null;
            }

            $value = Carbon::parse($value);

            if ($this->jalali) {

                $value = verta($value);
                
                if ($this->humanReadable) {
                    return $value->formatDifference();
                }

                return $value->format('Y-m-d H:i:s');
            }

            if ($this->humanReadable) {
                return $value->diffForHumans();
            }

            return $value->format('Y-m-d H:i:s');

        })->sortable();
    }

    public function local($local)
    {

        $this->local = $this->callableValue($local);
        $this->jalali($this->local === 'fa');

        return $this;
    }

    public function jalali($jalali = TRUE)
    {

        $this->jalali = $this->callableValue($jalali);

        if ($this->jalali) {

            $this->local = 'fa';
        }

        return $this;
    }

    public function min($min)
    {

        return $this->attributes('min', $this->callableValue($min));
    }

    public function max($max)
    {

        return $this->attributes('max', $this->callableValue($max));
    }

    public function humanReadable($humanReadable = TRUE)
    {

        $this->humanReadable = $this->callableValue($humanReadable);

        return $this;
    }

    public function toArray()
    {
        return array_merge(parent::toArray(), [
            'format' => 'YYYY-MM-DD H:i:s',
            'local'  => $this->local,
            'jalali' => $this->jalali
        ]);
    }
}
