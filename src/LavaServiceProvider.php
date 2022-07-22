<?php

namespace Prodemmi\Lava;

use Illuminate\Routing\Router;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Prodemmi\Lava\Console\InstallCommand;
use Prodemmi\Lava\Http\Middleware\Authenticated;
use Prodemmi\Lava\Http\View\Components\AppLayout;
use Prodemmi\Lava\Http\View\Components\GuestLayout;

class LavaServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {

        $this->app->singleton( 'lava', function ($app) {

            return new Lava();

        } );

        if ( $this->publishedProviderExist() ) {

            $this->publishedProvider();

        }

    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {

        if ( $this->app->runningInConsole() ) {
            $this->registerPublishing();
            $this->registerCommands();
        }

        $this->registerRoutes();
        $this->registerViews();
        $this->registerConfig();
        $this->registerMigrations();

    }

    protected function registerPublishing()
    {
        
        $this->publishes( [
            __DIR__ . '/Console/stubs/LavaServiceProvider.stub' => app_path( 'Providers/LavaServiceProvider.php' ),
        ], 'lava-provider' );

        $this->publishes( [
            __DIR__ . '/config/lava.php' => config_path( 'lava.php' ),
        ], 'lava-config' );

        $this->publishes( [
            __DIR__ . '/public' => public_path( 'vendor/lava' ),
        ], 'lava-assets' );

        $this->publishes( [
            __DIR__ . '/resources/lang' => resource_path( 'lang/vendor/lava' ),
        ], 'lava-lang' );

        $this->publishes( [
            __DIR__ . '/resources/views' => resource_path( 'views/vendor/lava' ),
        ], 'lava-views' );

        $this->publishes( [
            __DIR__ . '/database/migrations' => database_path( 'migrations' ),
        ], 'lava-migrations' );

    }

    /**
     * Register the package routes.
     *
     * @return void
     */
    protected function registerViews()
    {

        $this->loadViewsFrom( __DIR__ . '/../resources/views', 'lava' );

    }

    /**
     * Register the package migrations.
     *
     * @return void
     */
    protected function registerMigrations()
    {

        $this->loadMigrationsFrom( __DIR__ . '/../database/migrations', 'lava' );

    }

    /**
     * Register the package routes.
     *
     * @return void
     */
    protected function registerRoutes()
    {

        $this->loadRoutesFrom( __DIR__ . '/../routes/web.php' );
        $this->loadRoutesFrom( __DIR__ . '/../routes/api.php' );

        $this->addMiddleware();

    }

    /**
     * Register the package routes.
     *
     * @return void
     */
    protected function registerConfig()
    {

        $this->mergeConfigFrom( __DIR__ . '/../config/lava.php', 'lava');

    }

    protected function addMiddleware()
    {

        $router = $this->app->make( Router::class );
        $router->aliasMiddleware( 'admin', Authenticated::class );

    }

    protected function registerCommands()
    {

        $this->commands( [
            InstallCommand::class
        ] );

    }

    protected function publishedProviderExist()
    {

        return File::exists( app_path( 'Providers/LavaServiceProvider.php' ) );

    }

    protected function publishedProvider()
    {

        $appNameSpace = $this->app->getNamespace();
        $provider     = "{$appNameSpace}Providers\LavaServiceProvider";

        $this->app->register( $provider );
    }


    /**
     * Get the tools that should be listed in the Nova sidebar.
     *
     * @return array
     */
    public function resources()
    {
        return [];
    }

    /**
     * Get the tools that should be listed in the Nova sidebar.
     *
     * @return array
     */
    public function tools()
    {
        return [];
    }

    /**
     * Get the cards that should be displayed on the default Nova dashboard.
     *
     * @return array
     */
    protected function cards()
    {
        return [];
    }

    /**
     * Get the tools that should be listed in the Nova sidebar.
     *
     * @return array
     */
    public function metrics()
    {
        return [];
    }

}
