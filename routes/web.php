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
    //User Profiles
    $router->post('/user/profile', 'ProfileController@createProfile');
    $router->get('/user/profile', 'ProfileController@getProfile');
    $router->put('/user/profile', 'ProfileController@updateProfile');
    $router->delete('user/profile', 'ProfileController@deleteUser');
    //Properties
    $router->get('property', 'PropertiesController@allProperties');
    $router->get('property/{id}', 'PropertiesController@showProperty');
    //Orders
    $router->get('user/orders', 'UsersController@allUserOrder');
    $router->get('user/orders/{id}', 'UsersController@oneUserOrder');
    $router->post('orders', 'OrdersController@store');
    $router->post('orders/verify', 'OrdersController@verifyPayment');
    //Admin Routes
    $router->group(['prefix' => 'admin', 'middleware' => 'admin'], function () use ($router) {
        //Properties
        $router->post('/property', 'PropertiesController@createProperty');
        $router->put('/property/{id}', 'PropertiesController@updateProperty');
        $router->delete('property/{id}', 'PropertiesController@deleteProperty');
        //Categories
        $router->get('/category', 'CategoriesController@index');
        $router->post('/category', 'CategoriesController@store');
        $router->get('/category/{id}', 'CategoriesController@show');
        $router->put('/category/{id}', 'CategoriesController@update');
        $router->delete('/category/{id}', 'CategoriesController@destroy');
        //Orders
        $router->get('/orders', 'OrdersController@index');
        $router->get('orders/{id}', 'OrdersController@show');
        $router->delete('orders/{id}', 'OrdersController@destroy');
    });
});
