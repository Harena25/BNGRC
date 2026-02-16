<?php
class DashboardController {
    
    /**
     * Show the main dashboard with cities, needs, and donations
     */
    public static function index() {
        $pdo = Flight::db();
        
        // Get all cities with region info
        $cities = self::getCitiesWithRegions($pdo);
        
        // Get besoins summary per city
        $besoinsByCity = self::getBesoinsByCity($pdo);
        
        // Get distributions (donations attributed) per city
        $distributionsByCity = self::getDistributionsByCity($pdo);
        
        // Get global stats
        $stats = self::getGlobalStats($pdo);
        
        // Get recent activities
        $recentBesoins = self::getRecentBesoins($pdo);
        $recentDons = self::getRecentDons($pdo);
        
        $pagename = 'dashboard/index.php';
        Flight::render('modele', [
            'cities' => $cities,
            'besoinsByCity' => $besoinsByCity,
            'distributionsByCity' => $distributionsByCity,
            'stats' => $stats,
            'recentBesoins' => $recentBesoins,
            'recentDons' => $recentDons,
            'pagename' => $pagename
        ]);
    }
    
    // Get all cities with their region
    private static function getCitiesWithRegions(PDO $pdo): array {
        $sql = "SELECT v.id, v.libelle AS ville_name, r.libelle AS region_name
                FROM bn_ville v
                LEFT JOIN bn_region r ON v.region_id = r.id
                ORDER BY r.libelle, v.libelle";
        $stmt = $pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Get besoins summary grouped by city
    private static function getBesoinsByCity(PDO $pdo): array {
        $sql = "SELECT 
                    b.ville_id,
                    COUNT(b.id) AS total_besoins,
                    SUM(CASE WHEN b.status_id = 1 THEN 1 ELSE 0 END) AS besoins_ouverts,
                    SUM(CASE WHEN b.status_id = 2 THEN 1 ELSE 0 END) AS besoins_partiels,
                    SUM(CASE WHEN b.status_id = 3 THEN 1 ELSE 0 END) AS besoins_satisfaits,
                    SUM(b.quantite * a.prix_unitaire) AS valeur_totale_besoins
                FROM bn_besoin b
                LEFT JOIN bn_article a ON b.article_id = a.id
                GROUP BY b.ville_id";
        $stmt = $pdo->query($sql);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $byCity = [];
        foreach ($results as $row) {
            $byCity[$row['ville_id']] = $row;
        }
        return $byCity;
    }
    
    // Get distributions (donations attributed) grouped by city
    private static function getDistributionsByCity(PDO $pdo): array {
        $sql = "SELECT 
                    b.ville_id,
                    COUNT(d.id) AS total_distributions,
                    SUM(d.quantite_distribuee) AS quantite_totale_distribuee,
                    SUM(d.quantite_distribuee * a.prix_unitaire) AS valeur_totale_distribuee
                FROM bn_distribution d
                LEFT JOIN bn_besoin b ON d.besoin_id = b.id
                LEFT JOIN bn_article a ON d.article_id = a.id
                GROUP BY b.ville_id";
        $stmt = $pdo->query($sql);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $byCity = [];
        foreach ($results as $row) {
            $byCity[$row['ville_id']] = $row;
        }
        return $byCity;
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
