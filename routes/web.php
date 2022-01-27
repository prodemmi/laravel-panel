<?php

use App\Models\SmsTemplate;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get("auth/login", "Prodemmi\Lava\Http\Controllers\AuthController@index")->middleware('web')->name("auth.index");
Route::post("auth/login", "Prodemmi\Lava\Http\Controllers\AuthController@login")->middleware('web')->name("auth.login");
Route::get("auth/logout", "Prodemmi\Lava\Http\Controllers\AuthController@logout")->middleware('web')->name("auth.logout");

foreach (\Prodemmi\Lava\Facades\Lava::getPanels() as $dashboard) {

    if (empty($dashboard->route)) {

        throw new \Exception("Panel $dashboard->name routes are not defined.");
    }

    Route::get("$dashboard->route/{path?}", 'Prodemmi\Lava\Http\Controllers\DashboardController@index')
        ->where('path', '.*')
        ->middleware(['web', 'admin'])
        ->name("$dashboard->route.panel");
}

Route::get('test', function () {

    return SmsTemplate::first()->realText(User::find(1)->toArray(), [
        'order_at' => \Carbon\Carbon::now(),
        'reagent_id' => 12
    ]);
});
