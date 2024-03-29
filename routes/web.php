<?php

use App\Models\SmsTemplate;
use App\Models\User;
use Illuminate\Support\Facades\Route;

Route::get("auth/login", "Prodemmi\Lava\Http\Controllers\AuthController@index")->middleware('web')->name("auth.index");
Route::post("auth/login", "Prodemmi\Lava\Http\Controllers\AuthController@login")->middleware('web')->name("auth.login");
Route::get("auth/logout", "Prodemmi\Lava\Http\Controllers\AuthController@logout")->middleware('web')->name("auth.logout");

foreach (\Prodemmi\Lava\Facades\Lava::getPanels() as $dashboard) {

    throw_if( blank($dashboard->route), \Exception::class, "Panel $dashboard->name routes are not defined.");

    Route::get("$dashboard->route/{path?}", 'Prodemmi\Lava\Http\Controllers\DashboardController@index')
        ->where('path', '.*')
        ->middleware(['web', 'admin'])
        ->name("$dashboard->route.panel");

}

Route::get('test', function () {

});
