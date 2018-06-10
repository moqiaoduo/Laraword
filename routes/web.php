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

Route::get('/', 'IndexController@index')->name('index');

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Route::get(getSetting('route.post','/archive/{id}'),'PostController@content')->name('content');

Route::group(['middleware'=>'admin','prefix'=>'admin','as'=>'admin::'],function () {
    Route::get('/', 'Admin\IndexController@index')->name('index');
    Route::resource('post','Admin\PostController');
});