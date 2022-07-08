<?php

namespace Prodemmi\Lava;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use JsonSerializable;

abstract class Tool implements JsonSerializable, Arrayable
{

    use AuthorizedToSee;

    public $group = 'Tools';
    public $route;
    public $icon;
    
    protected $tool = true;

    abstract public function showWhen(): bool;

    protected function label()
    {
        return (string)Str::of( class_basename( get_called_class() ), ' ' )->replace( 'Resource', '' )->headline();
    }

    public function pluralLabel()
    {
        return (string)Str::of($this->label() )->plural()->ucfirst();
    }

    public function singularLabel()
    {
        return (string)Str::of($this->label() )->singular()->ucfirst();
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
        return $this->route ?? (string)Str::of($this->label() )->lower()->plural()->slug();
    }

    public function getScripts()
    {
        return method_exists($this, 'scripts') ? $this->scripts() : null;
    }

    public function getStyles()
    {
        return method_exists($this, 'styles') ? $this->styles() : null;
    }

    public function getIsTool()
    {
        return $this->tool;
    }

    public function toArray()
    {
        $toArray = [
            'tool'           => $this->tool,
            'class'          => get_class($this),
            'group'          => $this->group,
            'show'           => $this->showWhen(),
            'icon'           => $this->icon,
            'route'          => $this->route(),
            'singularLabel'  => $this->singularLabel(),
            'pluralLabel'    => $this->pluralLabel(),
            'scripts'        => $this->getScripts(),
            'styles'         => $this->getStyles(),
        ];

        if($this->tool){
            $toArray['view'] = $this->view()->render();
        }

        return $toArray;
    }
}
