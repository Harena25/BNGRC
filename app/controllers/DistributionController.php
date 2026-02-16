<?php
require_once __DIR__ . '/../services/AutoDistributor.php';
class DistributionController{
    public function autoDistribution(){
        $pdo = Flight::db();
        $distributor = new AutoDistributor($pdo);
        $log = $distributor->run();
        echo '<pre>';
        foreach ($log as $line) {
            echo htmlspecialchars($line) . "\n";
        }
        echo '</pre>';
    }
}