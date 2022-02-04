<?php

use Illuminate\Support\Facades\Route;

foreach ( Lava::getPanels() as $panel ) {

    Route::group( [
        'namespace'  => 'Prodemmi\Lava\Http\Controllers',
        'prefix'     => $panel->route . "/api",
//        'middleware' => 'api'
    ], function () use ($panel) {

        Route::post( 'table', 'ResourceController@table' );
        Route::post( 'action', 'ResourceController@action' );
        Route::post( 'detail', 'ResourceController@detail' );
        Route::post( 'form', 'ResourceController@form' );
        Route::post( 'update', 'ResourceController@update' );
        Route::post( 'store', 'ResourceController@store' );
        Route::post( 'search-select', 'ResourceController@searchSelect' );
        Route::post( 'select', 'ResourceController@select' );

        Route::post( 'store-filter', 'ResourceController@storeFilter' );
        Route::post( 'delete-filter', 'ResourceController@deleteFilter' );

        Route::post( 'relation', 'ResourceController@relation' );
        Route::post( 'update-has-one', 'ResourceController@updateHasOne' );
        Route::post( 'update-has-many', 'ResourceController@updateHasMany' );
        Route::post( 'update-morph', 'ResourceController@updateMorph' );

        Route::post( 'media/upload', 'MediaController@upload' );
        Route::delete( 'media/delete', 'MediaController@delete' );
        Route::get( 'media/get-media', 'MediaController@getMedia' );

    } );

}