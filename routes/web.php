<?php

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

if (config('app.env') != 'production'){
    Route::get('/', function () {
        return view('welcome');
    });
    Route::get('/botman/tinker', 'BotManController@tinker');
}

Route::match(['get', 'post'], '/botman', 'BotManController@handle');
