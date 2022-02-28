<?php

namespace Prodemmi\Lava;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use JsonSerializable;

abstract class Tools implements JsonSerializable, Arrayable
{

    use AuthorizedToSee;

    public $group = 'Tools';
    public $route;
    public $icon;

    protected function label()
    {
        return Str::of( class_basename( get_called_class() ), ' ' )->replace( 'Resource', '' )->headline();
    }

    public function pluralLabel()
    {
        return Str::of($this->label() )->plural()->ucfirst();
    }

    public function singularLabel()
    {
        return Str::of($this->label() )->singular()->ucfirst();
    }

    public function authorized(Request $request)
    {

        return $this->authenticated( $request );

    }

    public function jsonSerialize()
    {
        return $this->toArray();
    }

    public function route()
    {
        return $this->route ?? Str::of($this->label() )->lower()->plural()->slug();
    }

    public function toArray()
    {
        $toArray = [
            'tool'          => TRUE,
            'group'         => $this->group,
            'icon'          => $this->icon,
            'route'         => $this->route(),
            'singularLabel' => $this->singularLabel(),
            'pluralLabel'   => $this->pluralLabel()
        ];

        if(method_exists(get_class($this), 'view')){
            $toArray['view'] = $this->view()->render();
        }

        return $toArray;
    }
}
