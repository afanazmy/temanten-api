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
    $router->get('/', 'UserController@index');
    $router->get('{id}',  'UserController@show');
    $router->post('/', 'UserController@store');
    $router->put('{id}',  'UserController@update');
});
