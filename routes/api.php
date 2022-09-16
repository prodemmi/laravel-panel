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
        Route::post( 'options', 'DashboardController@getOptions' );
        
        Route::post( 'check-license', 'ResourceController@checkLicense' );

        Route::post( 'media/upload', 'UploadController@upload' );
        
        // Filemanager Tool
        Route::post( 'media/delete-media', 'FileManagerToolController@deleteMedia' );
        Route::post( 'media/search-media', 'FileManagerToolController@searchMedia' );
        Route::post( 'media/paste-media', 'FileManagerToolController@pasteMedia' );
        Route::post( 'media/rename-media', 'FileManagerToolController@renameMedia' );
        Route::post( 'media/compress-media', 'FileManagerToolController@compressMedia' );
        Route::post( 'media/extract-media', 'FileManagerToolController@extractMedia' );
        Route::post( 'media/new-file', 'FileManagerToolController@newFile' );
        Route::post( 'media/new-folder', 'FileManagerToolController@newFolder' );
        Route::post( 'media/get-media', 'FileManagerToolController@getMedia' );
        Route::post( 'media/get-content', 'FileManagerToolController@getContent' );
        Route::post( 'media/edit-content', 'FileManagerToolController@editContent' );
        Route::post( 'media/get-statics', 'FileManagerToolController@getStatics' );

    } );

}