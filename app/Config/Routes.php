<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

$routes->setAutoRoute(false);

$routes->group('', ['filter'=>'auth'], function($routes){
    $routes->get('dashboard', 'AdminController::dashboard');
    $routes->get('dashboard/edit/(:num)', 'AdminController::getPerangkat/$1');
    $routes->post('dashboard/update', 'AdminController::ajaxUpdate');
    $routes->post('dashboard/check/(:num)', 'AdminController::checkMutasi/$1');
});

$routes->get('/', 'AdminController::index');
$routes->post('login', 'AdminController::login');

$routes->post('logout', 'AdminController::logout');
$routes->get('perangkat/tambah', 'AdminController::tambah');
$routes->post('dashboard/save', 'AdminController::simpanPerangkat');
$routes->get('perangkat/delete/(:num)', 'AdminController::delete/$1');