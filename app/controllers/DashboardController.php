<?php
class DashboardController
{

    /**
     * Show the main dashboard with cities, needs, and donations
     * Uses v_ville_resume view for simplified data access
     */
    public static function index()
    {
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
    private static function getVilleResume(PDO $pdo): array
    {
        $sql = "SELECT * FROM v_ville_resume ORDER BY region_name, ville_name";
        $stmt = $pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get global statistics
    private static function getGlobalStats(PDO $pdo): array
    {
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
    private static function getRecentBesoins(PDO $pdo, int $limit = 5): array
    {
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
    private static function getRecentDons(PDO $pdo, int $limit = 5): array
    {
        $sql = "SELECT d.*, a.libelle AS article_name
                FROM bn_dons d
                LEFT JOIN bn_article a ON d.article_id = a.id
                ORDER BY d.date_don DESC
                LIMIT $limit";
        $stmt = $pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Reset data (for testing/demo purposes)
    public static function ResetData()
    {
        $pdo = Flight::db();
        try {
            // Chemin vers le fichier SQL de réinitialisation
            $sqlFile = __DIR__ . '/../../database/20260217-02-exam-data.sql';

            // Vérifier que le fichier existe
            if (!file_exists($sqlFile)) {
                throw new \Exception("Fichier SQL introuvable: $sqlFile");
            }

            // Lire le contenu du fichier SQL
            $sql = file_get_contents($sqlFile);

            if ($sql === false) {
                throw new \Exception("Impossible de lire le fichier SQL");
            }

            // Nettoyer le contenu SQL : supprimer les commentaires multi-lignes /* ... */
            $cleanSql = preg_replace('/\/\*.*?\*\//s', '', $sql);

            // Supprimer les commentaires de ligne (-- et #) et les lignes vides
            $lines = explode("\n", $cleanSql);
            $cleanedLines = [];

            foreach ($lines as $line) {
                $line = trim($line);
                // Ignorer les lignes vides et les commentaires SQL (-- ou #)
                if (!empty($line) && !preg_match('/^(--|#)/', $line)) {
                    $cleanedLines[] = $line;
                }
            }

            $cleanSql = implode("\n", $cleanedLines);

            // Désactiver temporairement les vérifications de clés étrangères
            $pdo->exec("SET FOREIGN_KEY_CHECKS = 0");

            // Exécuter le script SQL
            // Séparer les instructions par point-virgule
            $statements = array_filter(
                array_map('trim', explode(';', $cleanSql)),
                function ($stmt) {
                    return !empty($stmt);
                }
            );

            foreach ($statements as $statement) {
                if (!empty($statement)) {
                    $pdo->exec($statement);
                }
            }

            // Réactiver les vérifications de clés étrangères
            $pdo->exec("SET FOREIGN_KEY_CHECKS = 1");

            // Stocker le message de succès dans la session
            if (!isset($_SESSION)) {
                session_start();
            }
            $_SESSION['reset_success'] = true;
            $_SESSION['reset_message'] = 'Données réinitialisées avec succès depuis le fichier ' . basename($sqlFile);

            // Rediriger vers le dashboard
            Flight::redirect(BASE_PATH . '/dashboard');

        } catch (\Exception $e) {
            // Stocker le message d'erreur dans la session
            if (!isset($_SESSION)) {
                session_start();
            }
            $_SESSION['reset_success'] = false;
            $_SESSION['reset_message'] = 'Erreur lors de la réinitialisation: ' . $e->getMessage();

            // Rediriger vers le dashboard
            Flight::redirect(BASE_PATH . '/dashboard');
        }
    }
}
