<?php
require_once __DIR__ . '/controllers/DistributionController.php';

Flight::route('/', function () {
    Flight::redirect('/login');
});

// Login routes

Flight::route('GET /autoDistribution', function () {
    $ctrl = new DistributionController();
    return $ctrl->autoDistribution();
});



// Flight::route('POST /register', ['AuthController', 'postRegister']);



