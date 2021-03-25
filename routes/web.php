<?php

use App\Http\Controllers\Admin\HomeController;
use App\Http\Controllers\Admin\ImageController;
use App\Http\Controllers\Admin\UtilController;
use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/login', 'Auth\LoginController@showLoginForm')->name('login');
Route::post('/login', 'Auth\LoginController@login');

Route::post('/logout', 'Auth\LoginController@logout')->name('logout')->middleware('auth:web');

Route::group(['prefix' => 'admin', 'middleware' => ['auth:web'], 'namespace' => 'Admin'], function () {
    Route::get('token', 'UtilController@generateCsrfToken');
    Route::get('home', 'HomeController@index')->name('home');
    Route::get('index', 'UtilController@index');
    Route::post('image', 'UtilController@storeImage');
    Route::post('image/delete', 'UtilController@destroyImage');
    Route::post('freenom/batch', 'FreenomController@batch');
    Route::resource('freenom', 'FreenomController', ['except' => ['store', 'edit', 'show', 'create']]);
    Route::resource('image', 'ImageController', ['only' => ['index', 'destroy']]);
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
