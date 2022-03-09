<?php

namespace Prodemmi\Lava;

use Prodemmi\Lava\MediaTool;

class Panel
{

    use AuthorizedToSee, CallableValue;

    /**
     * Name of the Lava dashboard
     *
     * @var array
     */
    public $name = "Lava dashboard";

    /**
     * List of the registered resource classes
     *
     * @var array
     */
    public $resources = [];

    /**
     * List of the registered tools classes
     *
     * @var array
     */
    public $tools = [];

    /**
     * List of the registered metrics classes
     *
     * @var array
     */

    public $metrics = [];

    /**
     * List of the registered metrics classes
     *
     * @var array
     */
    public $styles = [];

    /**
     * List of the registered metrics classes
     *
     * @var array
     */
    public $scripts = [];

    /**
     * List of the registered metrics classes
     *
     * @var string
     */
    public $route;

    /**
     * List of the registered metrics classes
     *
     * @var string
     */
    public $rtl = FALSE;

    /**
     * List of the registered metrics classes
     *
     * @var string
     */
    public $timezone = 'UTC';

    /**
     * List of the registered metrics classes
     *
     * @var string
     */
    public $locale = 'en';

    /**
     * List of the registered metrics classes
     *
     * @var string
     */
    public $showDashboard = TRUE;


    public function __construct($name)
    {

        $name = is_callable( $name ) ? $name() : $name;

        $this->name = $name;

    }

    public function create($name)
    {
        return new static( $name );
    }

    /**
     * Register the given resources.
     *
     * @param  array $resources
     * @return static
     */
    public function resources(array $resources)
    {
        $this->resources = array_unique( array_merge( $this->resources, $resources ) );

        return $this;
    }

    /**
     * Register the given resources.
     *
     * @param  array $tools
     * @return static
     */
    public function tools(array $tools)
    {
        $this->tools = array_unique( array_merge( $this->tools, $tools ) );

        return $this;
    }

    /**
     * Register the given metrics.
     *
     * @param  array $metrics
     * @return static
     */
    public function metrics(array $metrics)
    {
        $this->metrics = array_unique( array_merge( $this->metrics, $metrics ) );

        return $this;
    }

    /**
     * Register the given metrics.
     *
     * @param array $themes
     * @return static
     */
    public function styles(...$styles)
    {
        $this->styles = array_unique( array_merge( $this->styles, $styles ) );

        return $this;
    }

    /**
     * Register the given metrics.
     *
     * @param array $themes
     * @return static
     */
    public function scripts(...$scripts)
    {
        $this->scripts = array_unique( array_merge( $this->scripts, $scripts ) );

        return $this;
    }

    /**
     * Register the given metrics.
     *
     * @param array $themes
     * @return static
     */
    public function options(...$options)
    {
        $this->options = array_unique( array_merge( $this->options, $options ) );

        return $this;
    }

    /**
     * Register the given metrics.
     *
     * @param $rtl
     * @return static
     */
    public function rtl($rtl = TRUE)
    {
        $this->rtl = $this->callableValue( $rtl ) ? 'rtl': 'ltr';

        return $this;
    }

    /**
     * Register the given metrics.
     *
     * @param string $timezone
     * @return static
     */
    public function timezone($timezone = 'UTC')
    {
        $this->timezone = $this->callableValue( $timezone );

        return $this;
    }

    /**
     * Register the given metrics.
     *
     * @param string $locale
     * @return static
     */
    public function locale($locale = 'en')
    {
        $this->locale = $this->callableValue( $locale );

        return $this;
    }

    public function route($route)
    {

        $this->route = trim( $this->callableValue( $route ), '/' );

        return $this;

    }

    /**
     * @return array
     */
    public function getStyles()
    {
        return $this->styles;
    }

    /**
     * @return array
     */
    public function getScripts()
    {
        return $this->scripts;
    }

    /**
     * @return string
     */
    public function getRTL()
    {
        return $this->rtl ? 'rtl': 'ltr';
    }

    /**
     * @return string
     */
    public function getTimezone()
    {
        return $this->timezone;
    }

    /**
     * @return string
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * @return array
     */
    public function getResources()
    {
       
        return collect($this->resources)->push(MediaTool::class)->unique()->map(function($resource){

            return resolve($resource);

        })->toArray();
        
    }

    public function disableDashboard()
    {
        $this->showDashboard = TRUE;

        return $this;
    }

    protected function sideBarItems()
    {

        return collect( $this->getResources() )->groupBy( 'group' )->sortKeys();

    }

    public function getBaseUrl()
    {

        return $this->route;
        
    }

    public function getDebug()
    {

        return config('app.debug');
        
    }

    public function getConfig()
    {

        return [
            'name'          => $this->name,
            'baseUrl'       => $this->route,
            'rtl'           => $this->getRTL() === 'rtl',
            'showDashboard' => $this->showDashboard,
            'resources'     => $this->getResources(),
            'sidebarItems'  => $this->sideBarItems(),
            'tools'         => [],
            'metrics'       => [],
            'config'        => config( 'lava' ),
            'debug'         => config('app.debug')
        ];
        
    }

    public function getLicense()
    {

        return [
            'key'  => env('LAVA_KEY'),
            'username'         => env('LAVA_USERNAME'),
            'password'         => env('LAVA_PASSWORD')
        ];

    }

    public function routeConfiguration()
    {

        return [
            'namespace'  => 'Prodemmi\Lava\Http\Controllers',
            'as'         => $this->route . ".",
            'prefix'     => $this->route,
            'middleware' => [ 'web', 'admin' ]
        ];

    }

}