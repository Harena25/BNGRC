<?php
require_once __DIR__ . '/../services/AutoDistributor.php';

class DistributionController{
    
    /**
     * Run automatic distribution and redirect to dashboard
     */
    public function autoDistribution(){
        $pdo = Flight::db();
        $distributor = new AutoDistributor($pdo);
        $log = $distributor->run();
        
        // Store log in session for display
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        $_SESSION['allocation_log'] = $log;
        $_SESSION['allocation_success'] = true;
        
        // Redirect to dashboard
        Flight::redirect('/dashboard?allocation=done');
    }

    // New: list distributions and render a view
    public static function list(){
        $pdo = Flight::db();
        $sql = "SELECT d.*, a.libelle AS article_name, b.ville_id, v.libelle AS ville_name, b.date_besoin
                FROM bn_distribution d
                LEFT JOIN bn_article a ON d.article_id = a.id
                LEFT JOIN bn_besoin b ON d.besoin_id = b.id
                LEFT JOIN bn_ville v ON b.ville_id = v.id
                ORDER BY d.date_distribution DESC, d.id DESC";
        $stmt = $pdo->query($sql);
        $distributions = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $pagename = 'distribution/list.php';
        Flight::render('modele', ['distributions' => $distributions, 'pagename' => $pagename]);
    }
}