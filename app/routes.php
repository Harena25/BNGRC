<?php
require_once __DIR__ . '/controllers/DistributionController.php';
require_once __DIR__ . '/controllers/BesoinsController.php';
require_once __DIR__ . '/controllers/VillesController.php';
require_once __DIR__ . '/controllers/ArticlesController.php';

Flight::route('/', function () {
    Flight::redirect('/needs');
});

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


Flight::route('GET /autoDistribution', function () {
    $ctrl = new DistributionController();
    return $ctrl->autoDistribution();
});



// Flight::route('POST /register', ['AuthController', 'postRegister']);



