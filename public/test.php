<?php
require_once __DIR__ . '/../vendor/autoload.php';

// Test de l'autoloading
echo "FlightPHP chargé avec succès!<br>";

// Test si Flight est disponible
if (class_exists('Flight')) {
    echo "La classe Flight existe.<br>";
    
    // Test de configuration de route
    Flight::route('/', function() {
        echo 'Hello World!';
    });
    
    echo "Route configurée.<br>";
} else {
    echo "ERREUR: La classe Flight n'existe pas.<br>";
}