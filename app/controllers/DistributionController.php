<?php
require_once __DIR__ . '/../services/AutoDistributor.php';

class DistributionController{
    
    /**
     * Run automatic distribution and redirect to result page
     */
    public function autoDistribution(){
        $pdo = Flight::db();
        $distributor = new AutoDistributor($pdo);
        $log = $distributor->run();
        
        // Calculate summary
        $summary = ['satisfied' => 0, 'partial' => 0, 'skipped' => 0];
        foreach ($log as $line) {
            if (strpos($line, 'satisfait totalement') !== false) {
                $summary['satisfied']++;
            } elseif (strpos($line, 'partiellement') !== false) {
                $summary['partial']++;
            } elseif (strpos($line, 'pas de stock') !== false) {
                $summary['skipped']++;
            }
        }
        
        // Store in session for result page
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        $_SESSION['allocation_log'] = $log;
        $_SESSION['allocation_summary'] = $summary;
        $_SESSION['allocation_time'] = date('Y-m-d H:i:s');
        
        // Redirect to result page
        Flight::redirect('/distribution/result');
    }
    
    /**
     * Show the allocation result page (not in navbar)
     */
    public static function showResult(){
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        
        // Check if we have allocation data
        if (empty($_SESSION['allocation_log'])) {
            // No allocation data, redirect to dashboard
            Flight::redirect('/dashboard');
            return;
        }
        
        $log = $_SESSION['allocation_log'];
        $summary = $_SESSION['allocation_summary'] ?? ['satisfied' => 0, 'partial' => 0, 'skipped' => 0];
        
        // Clear session data after reading
        unset($_SESSION['allocation_log'], $_SESSION['allocation_summary'], $_SESSION['allocation_time']);
        
        $pagename = 'distribution/result.php';
        Flight::render('modele', [
            'log' => $log,
            'summary' => $summary,
            'pagename' => $pagename
        ]);
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