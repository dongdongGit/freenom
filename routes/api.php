<?php

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['namespace' => 'Admin'], function () {
    // github webhoobs
    Route::post('/webhooks', 'UtilController@webhooks')->middleware('webhook');

    Route::group(['prefix' => 'admin'], function () {
        Route::post('login', 'AuthController@login');

        Route::group(['middleware' => 'jwt'], function () {
            Route::post('logout', 'AuthController@logout');
        });
    });
});
