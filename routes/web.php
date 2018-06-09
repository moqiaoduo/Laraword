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

use Illuminate\Support\Facades\App;

Route::get('/', 'IndexController@index');

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

//dd(getSetting('route.post','/archive/{id}'));

Route::get(getSetting('route.post','/archive/{id}'),'PostController@content')->name('content');

Route::prefix('admin')->group(function () {
    Route::get('/', 'Admin\IndexController@index');
    Route::resource('post','Admin\PostController');
});