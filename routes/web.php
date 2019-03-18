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

#Route::get('/', function () {
#    return view('welcome');
#});

# Authentication routes
Auth::routes();

Route::post('/webhook', 'WebhookController@handle')->name('webhook');

Route::get('/application-changelog', function() { return view('app_changelog'); })->name('app_changelog');

Route::group([ 'middleware' => ['auth', 'active', 'userheader'] ], function() {
    # Home page
    Route::get('/', 'HomeController@index')->name('home');

    # Password Reset
    Route::get('/users/password', 'UserController@password')->name('users.password');
    Route::post('/users/password', 'UserController@updatePassword')->name('users.updatepassword');
    Route::resource('dc', 'DCController');
    Route::get('/ip/find', 'IPController@findRange')->name('ip.find');
    Route::resource('ip', 'IPController');
    Route::resource('hostgroup', 'HostGroupController');
    Route::get('/actions', 'ActionController@index')->name('action.index');
    Route::get('/actions/{action}', 'ActionController@show')->name('action.show');

    # Admin routes
    Route::group(['middleware' => 'admin'], function () {
        Route::put('/blackhole', 'HomeController@createBlackhole');
        Route::delete('/blackhole', 'HomeController@deleteBlackhole');

        Route::resource('users', 'UserController');
        Route::get('/users/{user}/toggle', 'UserController@toggle')->name('users.toggle');
        Route::get('/users/{user}/delete', 'UserController@delete')->name('users.delete');

        Route::get('/dc/{dc}/toggle/ban', 'DCController@toggleBan')->name('dc.toggleban');
        Route::get('/dc/{dc}/toggle/unban', 'DCController@toggleUnban')->name('dc.toggleunban');

        Route::put('/hostgroup/{hostgroup}/thresholds', 'HostGroupController@updateThresholds')->name('hostgroup.thresholds');
        Route::get('/hostgroup/{hostgroup}/delete', 'HostGroupController@destroy')->name('hostgroup.delete');
    });
});
