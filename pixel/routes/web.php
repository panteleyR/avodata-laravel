<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->group(['prefix' => 'px'], function () use ($router) {
    $router->get('/', function () {
        return response()->json(['messasge'=>'asf']);
    });

    $router->get('/pixel.js', 'ScriptController@index');
    $router->get('/pixel2.js', 'ScriptController@providers');
    $router->get('/provider/run', 'ScriptController@providerRun');
    $router->post('/webhook', 'ScriptController@webhook');
    $router->get('/aggregate', 'ScriptController@aggregate');
    $router->post('/aggregate', 'ScriptController@aggregate');


//   API PIXEL SERVICE V1
    $router->group(['middleware' => 'auth', 'prefix' => 'v1'], function () use ($router) {
        $router->group(['prefix' => 'public'], function () use ($router) {
            $router->get('{cabinet_id}/users', 'DataController@users');
            $router->post('{cabinet_id}/users/{user_id}/sessions', 'DataController@sessions');
            $router->get('{cabinet_id}/users/{user_id}/sessions', 'DataController@sessions');
            $router->get('{cabinet_id}/users/csv', 'DataController@userCsv');
            $router->get('{cabinet_id}/statistics', 'DataController@statistics');
            $router->post('{cabinet_id}/adjustment', 'DataController@adjustment');
        });

        $router->group(['prefix' => 'admin'], function () use ($router) {
            $router->get('/statistics', 'DataController@adminStatistics');
            $router->get('/users', 'DataController@adminUsers');
            $router->get('/users/{user_id}/sessions', 'DataController@adminSessions');
            $router->get('/users/csv', 'DataController@adminUserCsv');
            $router->get('/statistics/providers', 'DataController@adminProviderStatistic');
        });
    });
});
