<?php

namespace Prodemmi\Lava\Fields;

use Illuminate\Support\Str;
use Prodemmi\Lava\Element;
use Prodemmi\Lava\Fieldable;
use Prodemmi\Lava\Resolvable;

class Field extends Element
{

    use Resolvable, Fieldable;

    public $column;

    public $name;

    public $value;

    public $sortable = FALSE;

    public $nullable = FALSE;

    public $link = FALSE;

    public $attributes = [];

    public $rules = [];

    public $displayCallbacks = [];

    public function __construct($name, $column = NULL)
    {

        $this->name   = $name;
        $this->column = $column ?? Str::of( $this->name )->title()->snake();

    }

    public static function create($name, $column = NULL)
    {

        return new static( $name, $column );

    }

    public function rules($rules)
    {

        $this->rules = array_merge( $this->rules, $rules );

        return $this;
    }

    public function sortable($sortable = TRUE)
    {

        $this->sortable = $this->callableValue( $sortable );

        return $this;

    }

    public function nullable($nullable)
    {

        $this->nullable = $this->callableValue( $nullable );

        return $this;
    }

    public function required($required = TRUE)
    {
        return $this->rules( [ 'required' => $this->callableValue( $required ) ] );
    }

    public function resolveValue($callback)
    {

        array_unshift( $this->resolveCallbacks, $callback );

        return $this;

    }

    public function displayValue($callback)
    {

        array_unshift( $this->displayCallbacks, $callback );

        return $this;

    }

    public function styles($styles)
    {

        $styles = $this->callableValue( $styles );

        $styles = is_array( $styles ) ? implode( ';', $styles ) : $styles;

        $this->attributes( [
            'style' => $styles
        ] );

        return $this;

    }

    public function classes($classes)
    {

        $classes = $this->callableValue( $classes );

        $classes = is_array( $classes ) ? trim( implode( ' ', $classes ), ' ' ) : $classes;

        $this->attributes( [
            'class' => $classes
        ] );

        return $this;

    }

    public function toArray()
    {
        return array_merge( parent::toArray(), [
            'column'       => $this->column,
            'name'         => $this->name,
            'sortable'     => $this->sortable,
            'nullable'     => $this->nullable,
            'rules'        => $this->rules,
            'showOnIndex'  => $this->showOnIndex,
            'showOnDetail' => $this->showOnDetail,
            'showOnForm'   => $this->showOnForm,
            'hideDefault'  => $this->hideDefault
        ] );
    }

}