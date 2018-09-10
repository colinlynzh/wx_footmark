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

// Route::get('/', function () {
//     return view('welcome');
// });
Route::group(['middleware' => 'wxinfo'],function(){
    Route::get('/', 'IndexController@index');
    Route::get('/create', 'IndexController@index');
    Route::get('/vip', 'IndexController@index');
    Route::get('/ucenter', 'UserController@index');

    Route::get('/articles', 'ArticleController@list');


    Route::get('/footmark', 'FootmarkController@index');
    Route::get('/footmarks', 'FootmarkController@list');
    
    Route::resource('/articles', 'ArticleController');
    

    Route::get('/vip', 'UserController@vippower');
    Route::get('/vip/recommend/{recommend}', 'UserController@vippower');

});


// Route::get('/article/{article_id}', 'ArticleController@show');

// 前后台公共api
Route::group(['prefix' => '/api', 'middleware' => 'wxinfo'], function () {
    Route::get('/profiles', 'UserController@profiles');
    Route::put('/profiles', 'UserController@updateprofiles');
    Route::resource('/articles', 'ArticleController');
    Route::get('/footmark-count', 'FootmarkController@count');
    Route::post('/share', 'FootmarkController@share');
    Route::post('/wxpay-config', 'OrderController@getWxPayConfig');
    Route::post('/vip-pay', 'FootmarkController@vipPay');
    Route::get('/shares', 'FootmarkController@sharelist');
    Route::get('/footmarks', 'FootmarkController@footmarklist');
    Route::post('/figureurl', 'UserController@uploadfigureurl');
    Route::get('/interactdetail', 'FootmarkController@detail');
    Route::get('/vcoin', 'UserController@vcoindetail');
    Route::get('/customers', 'UserController@customerlist');
});


Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Auth::routes();


//api
Route::group(['namespace' => 'Common', 'prefix' => '/api'], function () {
    Route::get('/token', 'ApiController@token');
    // Route::get('/wx-notify', 'ApiController@wxpaynotify');
    Route::post('/wx-notify', 'ApiController@wxpaynotify');
});
