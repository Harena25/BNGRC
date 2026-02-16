<?php
require_once __DIR__ . '/controllers/ArticlesController.php';
require_once __DIR__ . '/controllers/DonsController.php';
require_once __DIR__ . '/repositories/ArticlesRepository.php';
require_once __DIR__ . '/repositories/DonsRepository.php';
require_once __DIR__ . '/repositories/CategorieRepository.php';
require_once __DIR__ . '/repositories/StockRepository.php';
require_once __DIR__ . '/controllers/DistributionController.php';
require_once __DIR__ . '/controllers/BesoinsController.php';
require_once __DIR__ . '/controllers/VillesController.php';
require_once __DIR__ . '/controllers/ArticlesController.php';
require_once __DIR__ . '/controllers/DashboardController.php';
require_once __DIR__ . '/controllers/StockController.php';

Flight::route('/', function () {
    Flight::redirect('/dashboard');
});

// Dashboard
Flight::route('GET /dashboard', ['DashboardController', 'index']);

Flight::route('GET /needs', function () {
    $ctrl = new BesoinsController(Flight::db());
    return $ctrl->index();
});

Flight::route('GET /needs/list', function () {
    $ctrl = new BesoinsController(Flight::db());
    return $ctrl->listPage();
});

Flight::route('POST /needs', function () {
    $ctrl = new BesoinsController(Flight::db());
    return $ctrl->store();
});

Flight::route('GET /cities', function () {
    $ctrl = new VillesController(Flight::db());
    return $ctrl->index();
});


// Articles routes
Flight::route('GET /articles', ['ArticlesController', 'list']);
Flight::route('GET /articles/create', ['ArticlesController', 'showForm']);
Flight::route('POST /articles', ['ArticlesController', 'create']);
Flight::route('GET /articles/@id/edit', ['ArticlesController', 'editForm']);
Flight::route('POST /articles/@id', ['ArticlesController', 'update']);
Flight::route('POST /articles/@id/delete', ['ArticlesController', 'delete']);

// Dons routes
Flight::route('GET /dons', ['DonsController', 'list']);
Flight::route('GET /dons/create', ['DonsController', 'showForm']);
Flight::route('POST /dons', ['DonsController', 'create']);
Flight::route('GET /dons/@id/edit', ['DonsController', 'editForm']);
Flight::route('POST /dons/@id', ['DonsController', 'update']);
Flight::route('POST /dons/@id/delete', ['DonsController', 'delete']);
// Login routes

Flight::route('GET /autoDistribution', function () {
    $ctrl = new DistributionController();
    return $ctrl->autoDistribution();
});

// Page to view distributions
Flight::route('GET /distribution', ['DistributionController', 'list']);

// Stock
Flight::route('GET /stock', ['StockController', 'list']);



