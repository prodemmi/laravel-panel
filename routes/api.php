<?php

use Illuminate\Support\Facades\Route;

foreach ( Lava::getPanels() as $panel ) {

    Route::group( [
        'namespace'  => 'Prodemmi\Lava\Http\Controllers',
        'prefix'     => $panel->route . "/api",
        'middleware' => 'api'
    ], function () {

        Route::post( 'table', 'ResourceController@table' );
        Route::post( 'relation-table', 'ResourceController@relationTable' );

        Route::post( 'export', 'ResourceController@export' );
        Route::post( 'action', 'ResourceController@action' );
        Route::post( 'detail', 'ResourceController@detail' );
        Route::post( 'form', 'ResourceController@form' );
        Route::post( 'update', 'ResourceController@update' );
        Route::post( 'store', 'ResourceController@store' );
        Route::post( 'select-search', 'ResourceController@selectSearch' );
        Route::post( 'searchable-select', 'ResourceController@searchableSelect' );

        Route::post( 'store-filter', 'ResourceController@storeFilter' );
        Route::post( 'delete-filter', 'ResourceController@deleteFilter' );

        Route::post( 'get-config', 'ResourceController@getConfig' );
        Route::post( 'get-active-actions', 'ResourceController@getActiveActions' );

        Route::post( 'metric/get-metric-data', 'MetricsController@getMetricData' );

        Route::post( 'global-search', 'DashboardController@globalSearch' );
        
        Route::post( 'check-license', 'ResourceController@checkLicense' );

    } );

}