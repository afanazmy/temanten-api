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

$router->get('/phpinfo', function () {
    return phpinfo();
});

$router->get('setup-wizard', 'SetupWizardController@index');
$router->post('setup-wizard', 'SetupWizardController@store');

$router->post('sign-in', 'UserController@signin');
$router->get('sign-out', ['middleware' => 'auth', 'uses' => 'UserController@signout']);

$router->group(['middleware' => 'auth', 'prefix' => 'users'], function () use ($router) {
    $router->get('auth', ['uses' => 'UserController@auth']);
    $router->get('permissions', ['uses' => 'UserController@permissions']);
    $router->put('activate', ['middleware' => 'permission:Update User Status', 'uses' =>  'UserController@activate']);
    $router->put('deactivate', ['middleware' => 'permission:Update User Status', 'uses' =>  'UserController@deactivate']);

    $router->get('/', ['uses' => 'UserController@index']);
    $router->get('{id}', ['uses' =>  'UserController@show']);
    $router->post('/', ['middleware' => 'permission:Add User', 'uses' => 'UserController@store']);
    $router->put('{id}', ['middleware' => 'permission:Update User', 'uses' =>  'UserController@update']);
});

$router->group(['middleware' => 'auth', 'prefix' => 'invitations'], function () use ($router) {
    $router->get('qr-code', ['uses' =>  'InvitationController@generateQr']);
    $router->post('qr-code/export', ['uses' =>  'InvitationController@exportQr']);
    $router->delete('clear', ['middleware' => 'permission:Delete All Invitation', 'uses' =>  'InvitationController@clear']);
    $router->put('restore-all', ['middleware' => 'permission:Restore All Invitation', 'uses' =>  'InvitationController@restoreAll']);
    $router->delete('delete', ['middleware' => 'permission:Delete Invitation', 'uses' =>  'InvitationController@delete']);
    $router->put('restore', ['middleware' => 'permission:Restore Invitation', 'uses' =>  'InvitationController@restore']);
    $router->get('download-template', ['middleware' => 'permission:Add Invitation', 'uses' =>  'InvitationController@downloadTemplate']);
    $router->post('import-template', ['middleware' => 'permission:Add Invitation', 'uses' =>  'InvitationController@importTemplate']);
    $router->put('sent', ['uses' =>  'InvitationController@sent']);

    $router->get('/', ['uses' => 'InvitationController@index']);
    $router->get('{id}', ['uses' =>  'InvitationController@show']);
    $router->post('/', ['middleware' => 'permission:Add Invitation', 'uses' => 'InvitationController@store']);
    $router->put('{id}', ['middleware' => 'permission:Update Invitation', 'uses' =>  'InvitationController@update']);
});

$router->group(['middleware' => 'auth', 'prefix' => 'wishes'], function () use ($router) {
    $router->get('/has-wishes', ['uses' => 'WishController@hasWish']);
    $router->delete('clear', ['middleware' => 'permission:Delete All Wish', 'uses' =>  'WishController@clear']);
    $router->put('restore-all', ['middleware' => 'permission:Restore All Wish', 'uses' =>  'WishController@restoreAll']);
    $router->delete('delete', ['middleware' => 'permission:Delete Wish', 'uses' =>  'WishController@delete']);
    $router->put('restore', ['middleware' => 'permission:Restore Wish', 'uses' =>  'WishController@restore']);

    $router->get('/', ['uses' => 'WishController@index']);
    $router->get('{id}', ['uses' =>  'WishController@show']);
    $router->post('/', ['uses' => 'WishController@store']);
    $router->put('{id}', ['middleware' => 'permission:Update Wish', 'uses' =>  'WishController@update']);
});

$router->get('galeries/img', ['uses' =>  'GaleryController@getImage']);
$router->group(['middleware' => 'auth', 'prefix' => 'galeries'], function () use ($router) {
    $router->delete('clear', ['middleware' => 'permission:Delete All Galery', 'uses' =>  'GaleryController@clear']);
    $router->put('restore-all', ['middleware' => 'permission:Restore All Galery', 'uses' =>  'GaleryController@restoreAll']);
    $router->delete('delete', ['middleware' => 'permission:Delete Galery', 'uses' =>  'GaleryController@delete']);
    $router->put('restore', ['middleware' => 'permission:Restore Galery', 'uses' =>  'GaleryController@restore']);

    $router->get('/', ['uses' => 'GaleryController@index']);
    $router->get('{id}', ['uses' =>  'GaleryController@show']);
    $router->post('/', ['uses' => 'GaleryController@store']);
    $router->put('{id}', ['middleware' => 'permission:Update Galery', 'uses' =>  'GaleryController@update']);
});

$router->get('settings/show', ['uses' => 'SettingController@show']);
$router->group(['middleware' => 'auth', 'prefix' => 'settings'], function () use ($router) {
    $router->get('/', ['uses' => 'SettingController@index']);
    $router->put('/', ['middleware' => 'permission:Update Setting', 'uses' =>  'SettingController@update']);
});
