<?php

namespace Prodemmi\Lava\Metrics;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Arr;
use JsonSerializable;
use Prodemmi\Lava\Element;
use Prodemmi\Lava\Resolvable;
use Illuminate\Support\Str;
use Prodemmi\Lava\Facades\Lava;

class Metric extends Element implements Arrayable, JsonSerializable
{

    use Resolvable;

    public $title;
    public $defaultRange;

    protected $range;

    protected $helpText;
    protected $prefix;
    protected $suffix;

    protected $timezone;

    public function __construct(){

        $this->timezone = Lava::getActivePanel()?->getTimezone() ?: config('app.timezone');

        $this->width('100%');

    }

    public function help($helpText)
    {

        $this->helpText = $helpText;

        return $this;
    }

    public function prefix($prefix)
    {

        $this->prefix = $prefix;

        return $this;
    }

    public function suffix($suffix)
    {

        $this->suffix = $suffix;

        return $this;
    }

    public function width($with)
    {
        return $this->styles("width: $with;");
    }

    public function resolveValue($callback)
    {

        array_unshift($this->resolveCallbacks, $callback);

        return $this;
    }

    public function jsonSerialize()
    {
        return $this->toArray();
    }

    protected function getTitle()
    {
        return $this->title ?: Str::of( class_basename( get_called_class() ), ' ' )->remove( 'Metrics')->remove( 'Metric')->headline();
    }

    public function calc($range){

        $this->range = $range;

        return $this->calculate();

    }

    protected function getRange(){

        return $this->range ?: ($this->defaultRange ?: Arr::first(array_keys($this->ranges())));

    }

    public function toArray()
    {

        return array_merge(parent::toArray(), [
            'name'     => get_class($this),
            'title'     => $this->getTitle(),
            'helpText'  => $this->helpText,
            'prefix'    => $this->prefix,
            'suffix'    => $this->suffix,
            'range'     => $this->getRange(),
            'ranges'    => $this->ranges()
        ]);
        
    }
}
