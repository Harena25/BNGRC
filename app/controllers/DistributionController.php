<?php
require_once __DIR__ . '/../services/AutoDistributor.php';

class DistributionController{
    
    /**
     * Run automatic distribution (simulate or execute)
     */
    public function autoDistribution(){
        $pdo = Flight::db();
        
        // Get sort mode from query parameter (default: date)
        // Options: 'date' (chronological) or 'quantite' (smallest first)
        $sortMode = $_GET['sortMode'] ?? 'date';
        
        $distributor = new AutoDistributor($pdo, $sortMode);
        
        // Get execution mode from query parameter (default: simulate)
        $mode = $_GET['mode'] ?? 'simulate';
        
        // Run simulation or execution
        if ($mode === 'execute') {
            // Actually execute the distribution
            $log = $distributor->run();
        } else {
            // Simulate only (don't modify database)
            $log = $distributor->simulate();
        }
        
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
        $_SESSION['allocation_mode'] = $mode;
        $_SESSION['allocation_sort_mode'] = $sortMode;
        
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
        $mode = $_SESSION['allocation_mode'] ?? 'simulate';
        $sortMode = $_SESSION['allocation_sort_mode'] ?? 'date';
        
        // Clear session data only if execution completed
        if ($mode === 'execute') {
            unset($_SESSION['allocation_log'], $_SESSION['allocation_summary'], $_SESSION['allocation_time'], $_SESSION['allocation_mode'], $_SESSION['allocation_sort_mode']);
        }
        
        $pagename = 'distribution/result.php';
        Flight::render('modele', [
            'log' => $log,
            'summary' => $summary,
            'mode' => $mode,
            'sortMode' => $sortMode,
            'pagename' => $pagename
        ]);
    }

    // New: list distributions and render a view
    public static function list(){
        $pdo = Flight::db();
        $sql = "SELECT d.*, 
                       a.libelle AS article_name, 
                       a.prix_unitaire,
                       c.libelle AS categorie_name,
                       b.ville_id, 
                       b.date_besoin,
                       b.quantite AS besoin_reste,
                       b.status_id,
                       s.libelle AS status_name,
                       v.libelle AS ville_name,
                       r.libelle AS region_name
                FROM bn_distribution d
                LEFT JOIN bn_article a ON d.article_id = a.id
                LEFT JOIN bn_categorie c ON a.categorie_id = c.id
                LEFT JOIN bn_besoin b ON d.besoin_id = b.id
                LEFT JOIN bn_status s ON b.status_id = s.id
                LEFT JOIN bn_ville v ON b.ville_id = v.id
                LEFT JOIN bn_region r ON v.region_id = r.id
                ORDER BY d.date_distribution DESC, d.id DESC";
        $stmt = $pdo->query($sql);
        $distributions = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Calculate stats
        $totalDistributions = count($distributions);
        $totalQuantite = array_sum(array_column($distributions, 'quantite_distribuee'));
        $totalValeur = 0;
        foreach($distributions as $d) {
            $totalValeur += ($d['quantite_distribuee'] ?? 0) * ($d['prix_unitaire'] ?? 0);
        }
        
        $pagename = 'distribution/list.php';
        Flight::render('modele', [
            'distributions' => $distributions, 
            'pagename' => $pagename,
            'stats' => [
                'total' => $totalDistributions,
                'quantite' => $totalQuantite,
                'valeur' => $totalValeur
            ]
        ]);
    }
}