<?php

use App\Controllers\HistoryController;
use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

$routes->setAutoRoute(false);

$routes->group('', ['filter'=>'auth'], function($routes){
    $routes->get('dashboard', 'DashboardController::dashboard');
    $routes->post('dashboard', 'DashboardController::dashboard');

    $routes->get('dashboard/history/(:num)', 'DashboardController::getHistory/$1');
    $routes->post('dashboard/history/(:num)', 'DashboardController::getHistory/$1');

    $routes->post('dashboard/check/(:num)', 'DashboardController::checkMutasi/$1');

    $routes->get('dashboard/userList', 'DashboardController::userList');
    $routes->post('dashboard/addUser', 'DashboardController::addUser');
    $routes->post('dashboard/deleteUser/(:num)', 'DashboardController::deleteUser/$1');

    $routes->get('dashboard/edit/(:num)', 'PerangkatController::editPerangkat/$1');
    $routes->post('dashboard/update', 'PerangkatController::updatePerangkat');

    $routes->post('dashboard/simpan', 'PerangkatController::tambahPerangkat');
    $routes->get('perangkat/cek-noreg', 'PerangkatController::cekNoreg');

    $routes->get('perangkat/delete/(:num)', 'PerangkatController::delete/$1');

    $routes->get('perangkat/getSpec', 'PerangkatController::getSpec');
    $routes->get('perangkat/getSpecById', 'PerangkatController::getSpecById');
});

$routes->get('/', 'FormController::index');
$routes->post('submit', 'FormController::submit');
$routes->get('submit/pdf', 'FormController::generatePdf');
$routes->get('submit/pdf/clear', 'FormController::clearPdfSession');

$routes->get('login', 'AdminController::index');
$routes->post('login', 'AdminController::login');

$routes->get('history', 'HistoryController::index');
$routes->get('history/log/(:num)', 'HistoryController::historylog/$1');
$routes->post('history/log/(:num)', 'HistoryController::historylog/$1');

$routes->get('logout', 'AdminController::logout');
$routes->get('export/excel', 'ExportController::exportExcel');
$routes->get('export/pdf', 'ExportController::exportPdf');
$routes->post('update-password', 'AdminController::updatePassword');