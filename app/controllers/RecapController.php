<?php
class RecapController
{

    public static function index()
    {
        $pdo = Flight::db();

        // Liste des villes pour le filtre
        $stmt = $pdo->query("SELECT id, libelle FROM bn_ville ORDER BY libelle");
        $villes = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Données initiales (globales par ville)
        $stmt = $pdo->query("SELECT * FROM v_recap_ville ORDER BY region_name, ville_name");
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $pagename = 'dashboard/recap.php';
        Flight::render('modele', [
            'villes' => $villes,
            'rows' => $rows,
            'pagename' => $pagename
        ]);
    }

    // Retourne JSON pour AJAX, accepte optional ?ville_id=
    public static function data()
    {
        header('Content-Type: application/json');
        $pdo = Flight::db();

        $ville_id = isset($_GET['ville_id']) && is_numeric($_GET['ville_id']) ? (int) $_GET['ville_id'] : null;

        if ($ville_id) {
            $sql = "SELECT * FROM v_recap_ville WHERE ville_id = :vid";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':vid' => $ville_id]);
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } else {
            $sql = "SELECT * FROM v_recap_ville ORDER BY region_name, ville_name";
            $stmt = $pdo->query($sql);
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        // Calculer totaux globaux (incluant dons distribués et achats)
        $totals = [
            'montant_besoin_total' => 0,
            'montant_satisfait_distrib' => 0,
            'montant_achats' => 0,
            'montant_satisfait_total' => 0,
            'montant_restant' => 0
        ];

        foreach ($rows as $r) {
            $totals['montant_besoin_total'] += (float) $r['montant_besoin_total'];
            $totals['montant_satisfait_distrib'] += (float) $r['montant_satisfait_distrib'];
            $totals['montant_achats'] += (float) $r['montant_achats'];
            $totals['montant_satisfait_total'] += (float) $r['montant_satisfait_total'];
            $totals['montant_restant'] += (float) $r['montant_restant'];
        }

        echo json_encode(['success' => true, 'rows' => $rows, 'totals' => $totals]);
    }
}
