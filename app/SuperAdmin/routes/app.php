<?php

use Illuminate\Http\Request;
use Examyou\RestAPI\Facades\ApiRoute;
use Illuminate\Support\Facades\Route;
Route::get('/process-reset-link', function(Request $request){
    $email = $request->query('email');
    $token = $request->query('token');

    return redirect("/admin/reset-password?token=$token&email=$email");
})->name('password.reset');
Route::post('api/v1/auth/reset-password', [\App\SuperAdmin\Http\Controllers\Api\SuperAdminPasswordReset::class, 'reset']);

Route::post('api/v1/auth/forgot-password', [\App\SuperAdmin\Http\Controllers\Api\SuperAdminPasswordReset::class, 'forgotPassword']);
ApiRoute::group(['namespace' => 'App\SuperAdmin\Http\Controllers\Api'], function () {
    ApiRoute::get('global-setting', ['as' => 'api.extra.global-setting', 'uses' => 'SuperAdminAuthController@globalSetting']);
    ApiRoute::get('app', ['as' => 'api.extra.app', 'uses' => 'SuperAdminAuthController@appDetails']);


    // Authentication routes
    ApiRoute::group(['prefix' => 'auth'], function () {
        // ApiRoute::get('forgot-password', function(){
        //     dd('mav');
        // });
        ApiRoute::post('login', ['as' => 'api.extra.login', 'uses' => 'SuperAdminAuthController@superAdminLogin']);
        ApiRoute::post('refresh-token', ['as' => 'api.extra.refresh-token', 'uses' => 'SuperAdminAuthController@refreshToken']);
        ApiRoute::post('logout', ['as' => 'api.extra.logout', 'uses' => 'SuperAdminAuthController@logout']);
    });
});
