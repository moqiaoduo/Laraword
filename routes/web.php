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

Route::group(['middleware'=>'admin','prefix'=>'admin','as'=>'admin::'],function () {
    Route::get('/', 'Admin\IndexController@index')->name('index');
    Route::resource('post','Admin\PostController', ['except'=>[
        'show'
    ]]);
    Route::post('post/del','Admin\PostController@delete')->name('post.del');
    Route::resource('category','Admin\CategoryController', ['except'=>[
        'show'
    ]]);
    Route::post('category/del','Admin\CategoryController@delete')->name('category.del');
    Route::resource('page','Admin\PageController', ['except'=>[
        'show'
    ]]);
    Route::post('page/del','Admin\PageController@delete')->name('page.del');
    Route::post('upload','APIController@upload')->name('upload');
    Route::post('delFile','APIController@delFile')->name('delFile');
});

if(empty(DB::select("SELECT table_name FROM information_schema.TABLES WHERE table_name ='settings';"))) dd('未安装，请先安装后使用。 Please install first.');

Auth::routes();

Route::get(getCustomRoutes(array(getSetting('route.post','/archive/{id}'),getSetting('route.page','/page/{slug}'),getSetting('route.category','/category/{category}'))), 'IndexController@index')->name('index');

