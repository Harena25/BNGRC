<?php
class StockController {
    
    /**
     * Show stock list
     */
    public static function list() {
        $pdo = Flight::db();
        $sql = "SELECT s.*, a.libelle AS article_name, c.libelle AS categorie_name
                FROM bn_stock s
                LEFT JOIN bn_article a ON s.article_id = a.id
                LEFT JOIN bn_categorie c ON a.categorie_id = c.id
                ORDER BY c.libelle, a.libelle";
        $stmt = $pdo->query($sql);
        $stocks = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Calculate total value
        $totalValue = 0;
        foreach ($stocks as $stock) {
            // We need to get price, let's do it in the query
        }
        
        // Better query with price
        $sql2 = "SELECT s.*, a.libelle AS article_name, a.prix_unitaire, c.libelle AS categorie_name,
                        (s.quantite_stock * a.prix_unitaire) AS valeur
                 FROM bn_stock s
                 LEFT JOIN bn_article a ON s.article_id = a.id
                 LEFT JOIN bn_categorie c ON a.categorie_id = c.id
                 ORDER BY c.libelle, a.libelle";
        $stmt2 = $pdo->query($sql2);
        $stocks = $stmt2->fetchAll(PDO::FETCH_ASSOC);
        
        $pagename = 'stock/list.php';
        Flight::render('modele', ['stocks' => $stocks, 'pagename' => $pagename]);
    }
}
