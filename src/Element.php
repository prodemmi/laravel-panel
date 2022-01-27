<?php

namespace Prodemmi\Lava;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use JsonSerializable;

class Element implements JsonSerializable, Arrayable
{

    use AuthorizedToSee, CallableValue;

    public $component;

    public $attributes = [];

    public function authorized(Request $request)
    {

        return $this->authenticated($request);
    }

    public function attributes($attributes)
    {

        $attributes = $this->callableValue($attributes);

        if (is_array($attributes)) {

            $this->attributes = array_merge($this->attributes, $attributes);
        }

        return $this;
    }

    public function jsonSerialize()
    {
        return $this->toArray();
    }

    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray()
    {
        
        return [
            'component'  => $this->component,
            'attributes' => $this->attributes
        ];
        
    }
}
