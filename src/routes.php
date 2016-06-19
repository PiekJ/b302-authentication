<?php


/*
|--------------------------------------------------------------------------
| Routes
|--------------------------------------------------------------------------
|
| Defines the needed routes for this package
|
*/
Route::group(['prefix' => 'users'], function() {
    Route::get('create', 'UsersController@create');
    Route::post('/', 'UsersController@store');
    Route::get('login', 'UsersController@login');
    Route::post('login', 'UsersController@doLogin');
    Route::get('confirm/{code}', 'UsersController@confirm');
    Route::get('forgot_password', 'UsersController@forgotPassword');
    Route::post('forgot_password', 'UsersController@doForgotPassword');
    Route::get('reset_password/{token}', 'UsersController@resetPassword');
    Route::post('reset_password', 'UsersController@doResetPassword');
    Route::get('logout', 'UsersController@logout');
});