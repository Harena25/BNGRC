<?php
require_once __DIR__ . '/controllers/AuthController.php';
require_once __DIR__ . '/controllers/MessageController.php';
require_once __DIR__ . '/services/Validator.php';
require_once __DIR__ . '/services/UserService.php';
require_once __DIR__ . '/repositories/UserRepository.php';

Flight::route('/', function () {
    Flight::redirect('/login');
});

// Login routes
Flight::route('GET /login', ['AuthController', 'showLogin']);
Flight::route('POST /login', ['AuthController', 'postLogin']);

//message routes
Flight::route('GET /messages', ['MessageController', 'list']);

// Flight::route('POST /register', ['AuthController', 'postRegister']);



