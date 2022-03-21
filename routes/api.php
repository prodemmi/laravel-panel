<?php

use Illuminate\Support\Facades\Route;

foreach ( Lava::getPanels() as $panel ) {

    Route::group( [
        'namespace'  => 'Prodemmi\Lava\Http\Controllers',
        'prefix'     => $panel->route . "/api",
        'middleware' => 'api'
    ], function () use ($panel) {

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

        Route::post( 'media/upload', 'MediaController@upload' );
        Route::post( 'media/delete-media', 'MediaController@deleteMedia' );
        Route::post( 'media/search-media', 'MediaController@searchMedia' );
        Route::post( 'media/paste-media', 'MediaController@pasteMedia' );
        Route::post( 'media/rename-media', 'MediaController@renameMedia' );
        Route::post( 'media/compress-media', 'MediaController@compressMedia' );
        Route::post( 'media/extract-media', 'MediaController@extractMedia' );
        Route::post( 'media/new-file', 'MediaController@newFile' );
        Route::post( 'media/new-folder', 'MediaController@newFolder' );
        Route::post( 'media/get-media', 'MediaController@getMedia' );
        Route::post( 'media/get-content', 'MediaController@getContent' );
        Route::post( 'media/edit-content', 'MediaController@editContent' );
        Route::post( 'media/get-statics', 'MediaController@getStatics' );

        Route::post( 'metric/get-metric-data', 'MetricsController@getMetricData' );
        
        Route::post( 'check-license', 'ResourceController@checkLicense' );

    } );

}