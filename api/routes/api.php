<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::prefix('v1')->group(function () {
    Route::middleware('auth:api', 'verified')->group(function() {
        Route::get('/cabinets', 'CabinetController@index');
        Route::post('/cabinets', 'CabinetController@store');

//        superadmin
        Route::get('/providers', 'ProvidersController@index');
        Route::post('/providers', 'ProvidersController@store');
        Route::put('/providers/{id}', 'ProvidersController@update');
        Route::delete('/providers/{id}', 'ProvidersController@destroy');

        Route::get('/geo/cities', 'CityController@index');

    //    Домены для суперпользователя и статистики
        Route::get('/domains', 'DomainController@rootIndex');


        Route::get('/users/me', 'AuthController@user');
    //    Route::get('/users', 'UserController@index');
    //    Route::post('/users', 'UserController@store');
    //    Route::delete('/users/{id}', 'UserController@destroy');
        Route::post('/users/me/change-password', 'UserController@changePassword');

        Route::get('/roles', 'RoleController@index');

        Route::get('/tariffs', 'TariffController@index');
        Route::post('/tariffs', 'TariffController@store');
        Route::get('/tariffs/{id}', 'TariffController@show');
        Route::put('/tariffs/{id}', 'TariffController@update');
        Route::delete('/tariffs/{id}', 'TariffController@destroy');

        Route::get('/campaigns', 'CampaignController@indexAdmin');
        Route::get('/campaigns/{campaignId}', 'CampaignController@show');
        Route::post('/campaigns', 'CampaignController@store');
        Route::put('/campaigns/{campaignId}', 'CampaignController@update');
        Route::delete('/{campaignId}', 'CampaignController@destroy');

        Route::get('/payments', 'PaymentController@indexAll');
        Route::get('/payments/{paymentId}', 'PaymentController@show');
        Route::post('/payments', 'PaymentController@store');

        Route::prefix('/cabinets/{cabinetId}')->middleware( 'auth.cabinet')->group(function() {
            Route::get('/', 'CabinetController@show');
            Route::put('/', 'CabinetController@update');
            Route::patch('/attach/provider/{id}', 'CabinetController@attachProvider');
            Route::patch('/detach/provider/{id}', 'CabinetController@detachProvider');
    //        Route::delete('/', 'CabinetController@destroy');

            Route::get('/members/me', 'MemberController@currentMember');
            Route::get('/members', 'MemberController@index');
            Route::get('/members/{id}', 'MemberController@show');
            Route::post('/members', 'MemberController@store');
            Route::put('/members/{id}', 'MemberController@update');
            Route::delete('/members/{id}', 'MemberController@destroy');

            Route::get('/domains', 'DomainController@index');
            Route::get('/domains/{id}', 'DomainController@show');
            Route::post('/domains', 'DomainController@store');
            Route::put('/domains/{id}', 'DomainController@update');
            Route::delete('/domains/{id}', 'DomainController@destroy');

            Route::get('/domains/{domainId}/campaigns', 'CampaignController@index');
            Route::get('/domains/{domainId}/campaigns/{campaignId}', 'CampaignController@show');
            Route::post('/domains/{domainId}/campaigns', 'CampaignController@store');
            Route::put('/domains/{domainId}/campaigns/{campaignId}', 'CampaignController@update');
            Route::delete('/domains/{domainId}/campaigns/{campaignId}', 'CampaignController@destroy');

            Route::get('/domains/{domainId}/campaigns/{campaignId}/payments', 'PaymentController@index');
            Route::get('/domains/{domainId}/campaigns/{campaignId}/payments/{paymentId}', 'PaymentController@show');
            Route::post('/domains/{domainId}/campaigns/{campaignId}/payments', 'PaymentController@store');
        });

    //    Auth
    //    Route::post('/login/xcrm/{id}', 'AuthController@xcrmLogin');
        Route::post('/logout', 'AuthController@logout');
    });

    //Первичная авторизация
    Route::post('/register', 'AuthController@register');
    Route::post('/login', 'AuthController@login');
    Route::post('/restore', 'AuthController@restore');
    Route::post('/confirm/auth/mail', 'AuthController@confirmEmail');
    Route::post('/confirm/auth/mail-again', 'AuthController@confirmEmailCodeAgain');
    Route::post('/confirm/auth/restore', 'AuthController@confirmRestore');
    Route::post('/confirm/auth/restore-again', 'AuthController@confirmRestoreCodeAgain');

});

Route::get('/documentation', 'DocumentationController@index');
Route::get('/documentation/swagger.json', 'DocumentationController@swaggerJson');
Route::get('/documentation/swagger.yaml', 'DocumentationController@swaggerYaml');
