<?php

use Illuminate\Support\Facades\Route;

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

Route::prefix('/services/main/get')->group(function() {
    Route::get('/domain', 'PixelController@showDomain');
    Route::get('/providers', 'PixelController@showProviders');
});


Route::prefix('/services/main')->group(function() {
    Route::post('/domains/{domainId}/campaigns/current/reduce', 'PixelController@reduceCampaignBalance');
    Route::post('/domains/{domainId}/campaigns/current/increase', 'PixelController@increaseCampaignBalance');
});
