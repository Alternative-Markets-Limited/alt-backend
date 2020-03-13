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

$router->group(['prefix' => 'api/v1'], function () use ($router) {
    //Home
    $router->get('/', 'HomeController@index');
    //Authentication
    $router->get('/auth/user', 'AuthController@user');
    $router->post('/auth/register', 'AuthController@register');
    $router->post('/auth/login', 'AuthController@login');
    $router->post('/auth/logout', 'AuthController@logout');
    //Email
    //handle frontend
    $router->post('/auth/email/verify/{verification_code}', ['as' => 'verify', 'uses' => 'AuthController@verifyUser']);
    $router->post('/auth/email/verify/request_verification/{user_id}', 'AuthController@requestVerification');
    //Forgot password
    //handle frontend
    $router->post('/password/email', 'PasswordController@postEmail');
    $router->post('/password/reset/{token}', ['as' => 'password.reset', 'uses' => 'PasswordController@postReset']);
});
