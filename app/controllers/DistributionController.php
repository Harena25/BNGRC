<?php
class DistributionController{
    // existing autoDistribution left intact if present
    public function autoDistribution(){
        $pdo=Flight::db(); 
        echo "Automatic distribution of messages executed.";
        $stockRepository = new StockRepository($pdo);
        $stock = $stockRepository->getAll();
        foreach($stock as $item){
            echo "Article: " . $item['article_name'] . " - Quantity: " . $item['quantite_stock'] . "<br>";
        }
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