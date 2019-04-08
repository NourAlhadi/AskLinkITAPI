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

// Login / Registration
Route::post('login', 'API\UserController@login');
Route::post('register', 'API\UserController@register');

// Users
Route::group(['middleware' => 'auth:api'], function() {
    Route::get('user', 'API\UserController@user');
    Route::patch('user/edit', 'API\UserController@userEdit');
    Route::get('user/avatar', 'API\UserController@getAvatar');
    Route::patch('user/edit/avatar', 'API\UserController@setAvatar');
});

// Admin
Route::group(['prefix' => 'admin', 'middleware' => ['auth:api']], function() {
    // User Manipulation
    Route::get('/check','API\Admin\AdminController@check');
    Route::post('/make/user','API\Admin\AdminController@makeUser');
    Route::post('/make/admin','API\Admin\AdminController@makeAdmin');
    Route::post('/make/super','API\Admin\AdminController@makeSuper');
});


// Posts
// Categories
Route::group(['middleware'=>['auth:api']], function(){
    Route::apiResource('categories', 'API\CategoryController');
});