<?php

/** @var \Laravel\Lumen\Routing\Router $router */

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

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->group(['middleware' => 'dbconn'], function () use ($router) {
    $router->group(['prefix' => 'api/configuration'], function () use ($router) {
        $router->group(['prefix' => 'v2', 'namespace' => 'V2'], function () use ($router) {

            $router->get('/display-image/{folder}', 'ImageController@display');

            $router->group(['middleware' => 'validatetoken'], function () use ($router) {

                $router->group(['prefix' => 'unit', 'namespace' => 'Unit'], function () use ($router) {
                    $router->get('/', 'UnitController@getAll');
                    $router->get('/{unitId}', 'UnitController@getById');
                    $router->post('/', 'UnitController@add');
                    $router->put('/{unitId}', 'UnitController@edit');
                    $router->get('/sum-target/{regionalId}', 'UnitController@sumtarget');
                });

                $router->group(['prefix' => 'regional', 'namespace' => 'Regional'], function () use ($router) {
                    $router->get('/', 'RegionalController@getAll');
                    $router->get('/{regionalId}', 'RegionalController@getById');
                    $router->post('/', 'RegionalController@add');
                    $router->put('/{regionalId}', 'RegionalController@edit');
                });

                $router->group(['prefix' => 'asmen-area', 'namespace' => 'Asmen'], function () use ($router) {
                    $router->get('/', 'AsmenController@getAll');
                    $router->get('/{asmenAreaId}', 'AsmenController@getById');
                    $router->post('/', 'AsmenController@add');
                    $router->put('/{asmenAreaId}', 'AsmenController@edit');
                });

                $router->group(['prefix' => 'bank', 'namespace' => 'Bank'], function () use ($router) {
                    $router->get('/', 'BankController@getAll');
                    $router->get('/{bankId}', 'BankController@getById');
                    $router->post('/', 'BankController@add');
                    $router->put('/{bankId}', 'BankController@edit');
                });

                $router->group(['prefix' => 'rute', 'namespace' => 'Rute'], function () use ($router) {
                    $router->get('/', 'RuteController@getAll');
                    $router->get('/{ruteId}', 'RuteController@getById');
                    $router->post('/', 'RuteController@add');
                    $router->put('/{ruteId}', 'RuteController@edit');
                });

                $router->group(['prefix' => 'nominal', 'namespace' => 'Nominal'], function () use ($router) {
                    $router->get('/', 'NominalController@getAll');
                    $router->get('/{nominalId}', 'NominalController@getById');
                    $router->post('/', 'NominalController@add');
                    $router->put('/{nominalId}', 'NominalController@edit');
                });

                $router->group(['prefix' => 'date-input-limit', 'namespace' => 'DateInputLimit'], function () use ($router) {
                    $router->get('/', 'DateInputLimitController@index');
                });
            });
        });

        // OPEN API RUMATKITA
        $router->group(['middleware' => 'basictoken'], function () use ($router) {
            $router->group(['prefix' => 'open-api', 'namespace' => 'OpenAPI'], function () use ($router) {
                $router->group(['prefix' => 'data-unit'], function () use ($router) {
                    $router->get('/get-all', 'UnitController@getAll');
                });
            });
        });

        // AOL
        $router->group(['prefix' => 'aol', 'namespace' => 'V2\Aol'], function () use ($router) {
            $router->get('bulk-integrate', 'AolController@bulkIntegrate');
        });

        // internal comm
        $router->get('/internal-comm/unit/{unitId}', 'V2\Unit\UnitController@getById');
    });
});
