<?php

namespace Prodemmi\Lava;


class Lava
{

    /**
     * Version of the Lava
     *
     * @var array
     */
    public static $version = "1.0.0";

    /**
     * Version of the Lava
     *
     * @var array
     */
    public static $dashboards = [];

    /**
     * Define Lava dashboard.
     *
     * @param  string $name
     * @return Panel
     */
    public static function create($name)
    {
        $dashboard = new Panel($name);

        static::$dashboards[] = $dashboard;

        return $dashboard;
    }

    public static function getPanels()
    {

        return static::$dashboards;
    }

    public static function getActivePanel($route = null)
    {

        foreach (static::$dashboards as $dashboard) {

            if (str_contains($route ?? url()->current(), $dashboard->route)) {

                return $dashboard;
            }
        }

        return NULL;
    }
}
