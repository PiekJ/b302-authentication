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
    Route::get('create', 'B302AuthUsersController@create');
    Route::post('/', 'B302AuthUsersController@store');
    Route::get('login', 'B302AuthUsersController@login');
    Route::post('login', 'B302AuthUsersController@doLogin');
    Route::get('confirm/{code}', 'B302AuthUsersController@confirm');
    Route::get('forgot_password', 'B302AuthUsersController@forgotPassword');
    Route::post('forgot_password', 'B302AuthUsersController@doForgotPassword');
    Route::get('reset_password/{token}', 'B302AuthUsersController@resetPassword');
    Route::post('reset_password', 'B302AuthUsersController@doResetPassword');
    Route::get('logout', 'B302AuthUsersController@logout');
});