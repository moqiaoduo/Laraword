<?php

use Illuminate\Http\Request;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('category','Admin\CategoryController@getCategories');

Route::post('basename',function (Request $request){
    return basename($request->post('url'));
})->name('basename');

Route::get('getPostAttachment/{id}','APIController@getPostAttachment')->name('getPostAttachment');
Route::get('getPageAttachment/{id}','APIController@getPageAttachment')->name('getPageAttachment');
Route::post('getAttachmentUrl','APIController@getAttachmentUrl')->name('getAttachmentUrl');