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

Route::get('/', function () {
    return view('welcome');
});

Route::any('admin/upload','AdminController@upload');
Route::any('admin/do_upload','AdminController@do_upload');
Route::any('admin/index','AdminController@Admin');
Route::any('admin/login','AdminController@AdminLogin');
Route::any('admin/captcha','CommonController@userCaptcha');
Route::any('admin/do_login','AdminController@do_login');
Route::any('search','AdminController@search');
Route::any('swiper','AdminController@swiper');
Route::any('click_swiper','AdminController@click_swiper');


//一个路由支持多个方法
Route::any('qiniu/show', 'AdminController@show');
Route::any('qiniu/delete', 'AdminController@delete');
Route::any('qiniu/is_use', 'AdminController@is_use');
Route::any('qiniu/update', 'AdminController@update');
Route::any('qiniu/do_update', 'AdminController@do_update');
