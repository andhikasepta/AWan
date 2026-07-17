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
    $routes->post('dashboard/importUsers', 'DashboardController::importUsers');
    $routes->post('dashboard/deleteUser/(:num)', 'DashboardController::deleteUser/$1');
    $routes->post('dashboard/updateUser/(:num)', 'DashboardController::updateUser/$1');
    $routes->get('dashboard/adminList', 'DashboardController::adminList');
    $routes->post('dashboard/addAdmin', 'DashboardController::addAdmin');
    $routes->post('dashboard/deleteAdmin/(:num)', 'DashboardController::deleteAdmin/$1');
    $routes->post('dashboard/updateAdmin/(:num)', 'DashboardController::updateAdmin/$1');
    $routes->post('dashboard/resetAdminPassword/(:num)', 'DashboardController::resetAdminPassword/$1');
    $routes->post('dashboard/uploadAdminTtd/(:num)', 'DashboardController::uploadAdminTtd/$1');
    $routes->post('dashboard/deleteAdminTtd/(:num)', 'DashboardController::deleteAdminTtd/$1');
    $routes->get('dashboard/getAdminTtd/(:num)', 'DashboardController::getAdminTtd/$1');
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

    // Users with Dibawa Devices
    $routes->get('dashboard/usersDibawa', 'DashboardController::getUsersWithDibawa');
    $routes->post('dashboard/usersDibawa/markRead', 'DashboardController::markDibawaAsRead');
    $routes->post('dashboard/peminjaman/approve', 'DashboardController::approvePeminjaman');

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
    $routes->post('dashboard/importNodes', 'DashboardController::importNodes');
    $routes->post('dashboard/deleteNode/(:num)', 'DashboardController::deleteNode/$1');
    $routes->post('dashboard/updateNode/(:num)', 'DashboardController::updateNode/$1');
    $routes->post('dashboard/bulkDeleteNodes', 'DashboardController::bulkDeleteNodes');
    $routes->post('dashboard/deleteAllNodes', 'DashboardController::deleteAllNodes');
    $routes->get('dashboard/followUpItems', 'DashboardController::followUpItems');
    $routes->get('dashboard/checkUpdates', 'DashboardController::checkUpdates');
    $routes->get('dashboard/runMigration', 'DashboardController::runMigration');

    // Regional Manage Routes
    $routes->get('dashboard/regionalList', 'DashboardController::regionalList');
    $routes->post('dashboard/addRegional', 'DashboardController::addRegional');
    $routes->post('dashboard/deleteRegional/(:num)', 'DashboardController::deleteRegional/$1');

    // BRP (Bukti Request Perangkat) Routes
    $routes->get('dashboard/brpMonths', 'DashboardController::brpAvailableMonths');
    $routes->get('dashboard/brpList', 'DashboardController::brpList');
    $routes->get('dashboard/brpDownload/(:num)', 'DashboardController::brpDownload/$1');
    $routes->post('dashboard/brpDelete/(:num)', 'DashboardController::brpDelete/$1');

    // Self-service Signature Routes
    $routes->post('dashboard/uploadMySignature', 'DashboardController::uploadMySignature');
    $routes->get('dashboard/getMySignature', 'DashboardController::getMySignature');
    $routes->post('dashboard/deleteMySignature', 'DashboardController::deleteMySignature');

    // Session heartbeat — keeps backend session alive while user is active
    $routes->get('session/heartbeat', 'AdminController::sessionHeartbeat');
    
    // Non-Registration Material Routes (Admin)
    $routes->get('dashboard/nonreg', 'DashboardController::nonRegDashboard');
    $routes->get('dashboard/nonreg/history/(:num)', 'DashboardController::getNonRegHistory/$1');
    $routes->get('dashboard/nonRegList', 'DashboardController::nonRegList');
    $routes->get('dashboard/getNonReg', 'DashboardController::getNonReg');
    $routes->post('dashboard/saveNonReg', 'DashboardController::saveNonReg');
    $routes->post('dashboard/deleteNonReg/(:num)', 'DashboardController::deleteNonReg/$1');
    $routes->post('dashboard/uploadNonRegExcel', 'DashboardController::uploadNonRegExcel');

    // [SECURITY FIX] Export routes dipindah ke dalam auth group
    // Sebelumnya dapat diakses tanpa login → potensi kebocoran seluruh data perangkat
    $routes->get('export/excel', 'ExportController::exportExcel');
    $routes->get('export/pdf', 'ExportController::exportPdf');
});

$routes->get('/', 'FormController::index');
$routes->get('formmutasi', 'FormController::index');
$routes->post('submit', 'FormController::submit');
$routes->get('submit/pdf', 'FormController::streamPdf');
$routes->get('submit/pdf/clear', 'FormController::clearPdfSession');

// Return Requests Routes (Public)
$routes->get('form/devices/(:num)', 'FormController::getDevicesDibawa/$1');
$routes->post('form/return', 'FormController::submitReturnRequest');
$routes->get('form/cek-noreg', 'FormController::cekNoreg');
$routes->get('form/nonRegList', 'FormController::nonRegList');

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
$routes->post('update-password', 'AdminController::updatePassword');