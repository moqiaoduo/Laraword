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
    Route::resource('post','Admin\PostController');
    Route::post('post/del','Admin\PostController@delete')->name('post.del');
    Route::resource('category','Admin\CategoryController');
    Route::post('category/del','Admin\CategoryController@delete')->name('category.del');
    Route::resource('page','Admin\PageController');
    Route::post('page/del','Admin\PageController@delete')->name('page.del');
    Route::post('upload','APIController@upload')->name('upload');
    Route::post('upload_update/{id}','APIController@upload_update')->name('upload_update');
    Route::post('delFile','APIController@delFile')->name('delFile');
    Route::resource('media','Admin\MediaController');
    Route::post('media/del','Admin\MediaController@delete')->name('media.del');
    Route::get('setting/{page}','Admin\SettingController@index')->name('setting');
    Route::post('setting/{page}','Admin\SettingController@update')->name('setting.save');
    Route::resource('theme','Admin\ThemeController');
    Route::get('comment','Admin\CommentController@index')->name('comment');
    Route::post('comment','Admin\CommentController@save')->name('comment.edit');
    Route::post('comment/del','Admin\CommentController@delete')->name('comment.del');
    Route::post('comment/{id}/{action}','Admin\CommentController@update')->name('comment.status');
});

Auth::routes();

Route::get('attachment/{id}','AttachmentController@show')->name('attachment');

Route::post('comment/add','CommentController@addComment')->name('comment.add');

if(empty(DB::select("SELECT table_name FROM information_schema.TABLES WHERE table_name ='options';"))){
    Route::get('/',function (){
        return '请先安装后使用。 Please install first.';
    });
}else{
    $routeTable=json_decode(getSetting('routeTable'),true);
    Route::get(getCustomRoutes(getCustomUri($routeTable,["post","page","category","articleList"])), 'IndexController@index')->name('main');
}
