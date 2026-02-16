<?php
class DashboardController {
    
    /**
     * Show the main dashboard with cities, needs, and donations
     * Uses v_ville_resume view for simplified data access
     */
    public static function index() {
        $pdo = Flight::db();
        
        // Get city summary from view (all data in one query)
        $villeResume = self::getVilleResume($pdo);
        
        // Get global stats
        $stats = self::getGlobalStats($pdo);
        
        // Get recent activities
        $recentBesoins = self::getRecentBesoins($pdo);
        $recentDons = self::getRecentDons($pdo);
        
        $pagename = 'dashboard/index.php';
        Flight::render('modele', [
            'villeResume' => $villeResume,
            'stats' => $stats,
            'recentBesoins' => $recentBesoins,
            'recentDons' => $recentDons,
            'pagename' => $pagename
        ]);
    }
    
    // Get all city data from the view v_ville_resume
    private static function getVilleResume(PDO $pdo): array {
        $sql = "SELECT * FROM v_ville_resume ORDER BY region_name, ville_name";
        $stmt = $pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Get global statistics
    private static function getGlobalStats(PDO $pdo): array {
        $stats = [];
        
        // Total cities
        $stmt = $pdo->query("SELECT COUNT(*) FROM bn_ville");
        $stats['total_villes'] = $stmt->fetchColumn();
        
        // Total besoins
        $stmt = $pdo->query("SELECT COUNT(*) FROM bn_besoin");
        $stats['total_besoins'] = $stmt->fetchColumn();
        
        // Besoins ouverts
        $stmt = $pdo->query("SELECT COUNT(*) FROM bn_besoin WHERE status_id = 1");
        $stats['besoins_ouverts'] = $stmt->fetchColumn();
        
        // Total dons
        $stmt = $pdo->query("SELECT COUNT(*) FROM bn_dons");
        $stats['total_dons'] = $stmt->fetchColumn();
        
        // Total stock value
        $stmt = $pdo->query("SELECT SUM(s.quantite_stock * a.prix_unitaire) FROM bn_stock s JOIN bn_article a ON s.article_id = a.id");
        $stats['valeur_stock'] = $stmt->fetchColumn() ?: 0;
        
        // Total distributions
        $stmt = $pdo->query("SELECT COUNT(*) FROM bn_distribution");
        $stats['total_distributions'] = $stmt->fetchColumn();
        
        return $stats;
    }
    
    // Get recent besoins
    private static function getRecentBesoins(PDO $pdo, int $limit = 5): array {
        $sql = "SELECT b.*, v.libelle AS ville_name, a.libelle AS article_name, s.libelle AS status_name
                FROM bn_besoin b
                LEFT JOIN bn_ville v ON b.ville_id = v.id
                LEFT JOIN bn_article a ON b.article_id = a.id
                LEFT JOIN bn_status s ON b.status_id = s.id
                ORDER BY b.created_at DESC
                LIMIT $limit";
        $stmt = $pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Get recent dons
    private static function getRecentDons(PDO $pdo, int $limit = 5): array {
        $sql = "SELECT d.*, a.libelle AS article_name
                FROM bn_dons d
                LEFT JOIN bn_article a ON d.article_id = a.id
                ORDER BY d.date_don DESC
                LIMIT $limit";
        $stmt = $pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
