<?php

use App\Core\Router;
use App\Core\Auth;
use App\Controllers\DashboardController;
use App\Controllers\WooController;
// Assume DashboardController and WooController are created for dashboard and WooCommerce routes

$router = new Router;

// Public routes
$router->get('', 'PageController@home');
$router->get('login', 'AuthController@showLoginForm');
$router->post('login', 'AuthController@processLogin');
$router->get('register', 'AuthController@showRegistrationForm');
$router->post('register', 'AuthController@processRegistration');

$router->group('dashboard', function($router) {
    $router->get('dashboard', 'PageController@dashboard');
    
    $router->get('dashboard/credentials', 'CredentialController@showCredentials');
    $router->get('dashboard/credentials/delete/{id}', 'CredentialController@deleteCredentials');
    $router->post('dashboard/credentials', 'CredentialController@addCredentials');

    // WooCommerce routes within Dashboard
    $router->get('dashboard/woo', 'WooController@index');
    $router->get('dashboard/woo/add-product', 'WooController@showaddProduct');
    $router->post('dashboard/woo/add-product', 'WooController@storeProduct');

    $router->get('dashboard/settings', 'SettingController@showSettings');
    $router->post('dashboard/settings', 'SettingController@newSetting');

    $router->get('dashboard/add-content', 'ContentController@showStoreContent');
    $router->post('dashboard/add-content', 'ContentController@storeContent');
    
}, ['middleware' => function() { return Auth::isLoggedIn(); }]);

// Save $router for use in the front controller
return $router;
