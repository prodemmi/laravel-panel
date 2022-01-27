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

    public static $group = 'Tools';
    public static $route;
    public static $icon;

    protected static function label()
    {
        return Str::of( class_basename( get_called_class() ), ' ' )->replace( 'Resource', '' )->headline();
    }

    public static function iconTemplate()
    {
        return "<i class='ri-" . static::$icon . "-line'></i>";
    }

    public static function route()
    {
        return static::$route ?? Str::of( static::label() )->lower()->plural()->slug();
    }

    public static function pluralLabel()
    {
        return Str::of( static::label() )->plural()->ucfirst();
    }

    public static function singularLabel()
    {
        return Str::of( static::label() )->singular()->ucfirst();
    }

    public function authorized(Request $request)
    {

        return $this->authenticated( $request );

    }

    public function jsonSerialize()
    {
        return $this->toArray();
    }

    public function toArray()
    {
        return [
            'tool'          => TRUE,
            'group'         => static::$group,
            'icon'          => static::$icon,
            'iconTemplate'  => static::iconTemplate(),
            'route'         => static::route(),
            'singularLabel' => static::singularLabel(),
            'pluralLabel'   => static::pluralLabel()
        ];
    }
}
