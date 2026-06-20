<?php

use App\Controllers\HistoryController;
use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

$routes->setAutoRoute(false);

$routes->group('', ['filter' => 'auth'], function ($routes) {
    $routes->get('dashboard', 'DashboardController::dashboard');
    $routes->post('dashboard', 'DashboardController::dashboard');

    $routes->get('dashboard/history/(:num)', 'DashboardController::getHistory/$1');
    $routes->post('dashboard/history/(:num)', 'DashboardController::getHistory/$1');

    $routes->post('dashboard/check/(:num)', 'DashboardController::checkMutasi/$1');

    $routes->get('dashboard/userList', 'DashboardController::userList');
    $routes->post('dashboard/addUser', 'DashboardController::addUser');
    $routes->post('dashboard/deleteUser/(:num)', 'DashboardController::deleteUser/$1');
    $routes->post('dashboard/updateUser/(:num)', 'DashboardController::updateUser/$1');

    $routes->get('dashboard/adminList', 'DashboardController::adminList');
    $routes->post('dashboard/addAdmin', 'DashboardController::addAdmin');
    $routes->post('dashboard/deleteAdmin/(:num)', 'DashboardController::deleteAdmin/$1');

    $routes->get('dashboard/edit/(:num)', 'PerangkatController::editPerangkat/$1');
    $routes->post('dashboard/update', 'PerangkatController::updatePerangkat');
    $routes->post('dashboard/bulkUpdate', 'PerangkatController::bulkUpdatePerangkat');
    $routes->post('perangkat/bulkDelete', 'PerangkatController::bulkDelete');

    $routes->post('dashboard/simpan', 'PerangkatController::tambahPerangkat');
    $routes->get('perangkat/cek-noreg', 'PerangkatController::cekNoreg');

    $routes->get('perangkat/delete/(:num)', 'PerangkatController::delete/$1');

    $routes->get('perangkat/getSpec', 'PerangkatController::getSpec');
    $routes->get('perangkat/getSpecById', 'PerangkatController::getSpecById');

    $routes->post('perangkat/validateCsvNoreg', 'PerangkatController::validateCsvNoreg');
    $routes->post('perangkat/importCsv', 'PerangkatController::importCsv');

    // Return Requests Routes (Admin)
    $routes->get('dashboard/returns', 'DashboardController::getPendingReturns');
    $routes->post('dashboard/returns/approve', 'DashboardController::approveReturnGroup');
    $routes->post('dashboard/returns/mark-read', 'DashboardController::markReturnRead');

    // Installation Requests Routes (Admin)
    $routes->get('dashboard/installations', 'DashboardController::getPendingInstallations');
    $routes->post('dashboard/installations/approve', 'DashboardController::approveInstallationGroup');
    $routes->post('dashboard/installations/mark-read', 'DashboardController::markInstallationRead');

    // Nodes CRUD Routes (Admin)
    $routes->get('dashboard/nodeList', 'DashboardController::nodeList');
    $routes->post('dashboard/addNode', 'DashboardController::addNode');
    $routes->post('dashboard/deleteNode/(:num)', 'DashboardController::deleteNode/$1');

    $routes->get('dashboard/followUpItems', 'DashboardController::followUpItems');
    $routes->get('dashboard/checkUpdates', 'DashboardController::checkUpdates');
});

$routes->get('/', 'FormController::index');
$routes->get('formmutasi', 'FormController::index');
$routes->post('submit', 'FormController::submit');
$routes->get('submit/pdf', 'FormController::generatePdf');
$routes->get('submit/pdf/clear', 'FormController::clearPdfSession');

// Return Requests Routes (Public)
$routes->get('form/devices/(:num)', 'FormController::getDevicesDibawa/$1');
$routes->post('form/return', 'FormController::submitReturnRequest');
$routes->get('form/cek-noreg', 'FormController::cekNoreg');

// Installation Requests Routes (Public)
$routes->get('form/nodes', 'FormController::getNodes');
$routes->post('form/installation', 'FormController::submitInstallationRequest');

$routes->get('login', 'AdminController::index');
$routes->post('login', 'AdminController::login');

$routes->get('history', 'HistoryController::index');
$routes->get('history/log/(:num)', 'HistoryController::historylog/$1');
$routes->post('history/log/(:num)', 'HistoryController::historylog/$1');

$routes->get('logout', 'AdminController::logout');
$routes->get('setup-password', 'AdminController::setupPassword');
$routes->post('setup-password', 'AdminController::setupPassword');
$routes->get('export/excel', 'ExportController::exportExcel');
$routes->get('export/pdf', 'ExportController::exportPdf');
$routes->post('update-password', 'AdminController::updatePassword');