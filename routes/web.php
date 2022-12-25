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

$router->post('setup-wizard', 'SetupWizardController@store');

$router->post('sign-in', 'UserController@signin');
$router->get('sign-out', ['middleware' => 'auth', 'uses' => 'UserController@signout']);

$router->group(['middleware' => 'auth', 'prefix' => 'users'], function () use ($router) {
    $router->get('/', ['uses' => 'UserController@index']);
    $router->get('{id}', ['uses' =>  'UserController@show']);
    $router->post('/', ['middleware' => 'permission:Add User', 'uses' => 'UserController@store']);
    $router->put('{id}', ['middleware' => 'permission:Update User', 'uses' =>  'UserController@update']);
    $router->put('activate/{id}', ['middleware' => 'permission:Update User Status', 'uses' =>  'UserController@activate']);
    $router->put('deactivate/{id}', ['middleware' => 'permission:Update User Status', 'uses' =>  'UserController@deactivate']);
});
