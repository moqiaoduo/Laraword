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

Route::post('getAttachmentInfo','APIController@getAttachmentInfo')->name('getAttachmentInfo');

Route::get('getPAttachment/{id}','APIController@getPAttachment')->name('getPAttachment');
Route::post('getAttachmentUrl','APIController@getAttachmentUrl')->name('getAttachmentUrl');