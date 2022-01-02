<?php

use Illuminate\Support\Facades\Route;
use Prodemmi\Lava\Http\Controllers\Auth\AuthenticatedSessionController;
use Prodemmi\Lava\Http\Controllers\Auth\NewPasswordController;

foreach ( \Prodemmi\Lava\Facades\Lava::getPanels() as $dashboard ) {

    if ( empty( $dashboard->route ) ) {

        throw new \Exception( "Panel $dashboard->name route is not defined." );
    }

    Route::group( $dashboard->routeConfiguration(), function () {

        Route::get( '/{path?}', 'DashboardController@index' )->where( 'path', '.*' )->middleware( 'admin' );

        Route::get( '/login', [ AuthenticatedSessionController::class, 'create' ] )
             ->withoutMiddleware( 'admin' )
             ->name( 'login' );

        Route::post( '/login', [ AuthenticatedSessionController::class, 'store' ] )->withoutMiddleware( 'admin' );

        Route::get( '/reset-password/{token}', [ NewPasswordController::class, 'create' ] )
             ->withoutMiddleware( 'admin' )
             ->name( 'password.reset' );

        Route::post( '/reset-password', [ NewPasswordController::class, 'store' ] )
             ->withoutMiddleware( 'admin' )
             ->name( 'password.update' );

        Route::post( '/logout', [ AuthenticatedSessionController::class, 'destroy' ] )
             ->withoutMiddleware( 'admin' )
             ->name( 'logout' );

    } );

}

Route::get( 'test', function () {

    return \Illuminate\Support\Str::of('Laravel')
                 ->append(' Framework')
                 ->tap(function ($string) {
                     dump('String after append: ' . $string);
                 })
                 ->upper();

} );