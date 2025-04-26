<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

$routes->get('/logout', 'Auth::logout');

$routes->group('', ['filter' => 'guest'], function ($routes) {
    $routes->get('/login', 'Auth::index');
    $routes->post('/login', 'Auth::doLogin');
    $routes->get('/register', 'Auth::register');
    $routes->post('/register', 'Auth::doRegister');
});

$routes->group('', ['filter' => 'auth'], function ($routes) {
    $routes->get('/', 'Dashboard::index');
    $routes->get('/topup', 'Topup::index');
    $routes->post('/topup', 'Topup::doTopup');
    $routes->get('/transaction', 'Transaction::index');
    $routes->get('/payment/(:any)', 'Transaction::doPayment/$1');
    $routes->post('/transaction', 'Transaction::doTransaction');
    $routes->get('/transaction-history', 'Transaction::doHistory');
    $routes->get('/profile', 'Profile::index');
    $routes->get('/edit-profile', 'Profile::editProfile');
    $routes->post('/edit-profile', 'Profile::doProfileUpdate');
    $routes->post('/edit-profile-image', 'Profile::doProfileImage');
});
