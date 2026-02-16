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
require_once __DIR__ . '/controllers/AchatsController.php';
require_once __DIR__ . '/controllers/AchatController.php';
require_once __DIR__ . '/controllers/RecapController.php';

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

Flight::route('GET /needs/restants', function () {
    $ctrl = new BesoinsController(Flight::db());
    return $ctrl->besoinsRestants();
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

// Result page after allocation (hidden from navigation)
Flight::route('GET /distribution/result', ['DistributionController', 'showResult']);

// Stock
Flight::route('GET /stock', ['StockController', 'list']);

// Achats (purchases)
Flight::route('GET /purchases', function () {
    $ctrl = new AchatsController(Flight::db());
    return $ctrl->index();
});

Flight::route('GET /purchases/list', function () {
    $ctrl = new AchatsController(Flight::db());
    return $ctrl->listPage();
});

Flight::route('POST /purchases', function () {
    $ctrl = new AchatsController(Flight::db());
    return $ctrl->store();
});

// Achats - Nouveau systeme
Flight::route('GET /achats/form/@besoin_id', function ($besoin_id) {
    $ctrl = new AchatController(Flight::db());
    return $ctrl->form($besoin_id);
});

Flight::route('POST /achats/simuler', function () {
    $ctrl = new AchatController(Flight::db());
    return $ctrl->simuler();
});

Flight::route('POST /achats/valider', function () {
    $ctrl = new AchatController(Flight::db());
    return $ctrl->valider();
});

Flight::route('GET /achats/simuler-global', function () {
    $ctrl = new AchatController(Flight::db());
    return $ctrl->simulerGlobal();
});

Flight::route('POST /achats/valider-global', function () {
    $ctrl = new AchatController(Flight::db());
    return $ctrl->validerGlobal();
});

// Récapitulatif (montants besoins/satisfaits/restants)
Flight::route('GET /recap', ['RecapController', 'index']);
Flight::route('GET /recap/data', ['RecapController', 'data']);



