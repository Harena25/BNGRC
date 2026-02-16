<?php
class AutoDistributor {
    private $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    // Public entry: run the simple distribution
    public function run(): array {
        $this->pdo->beginTransaction();
        $log = [];
        try {
            $besoins = $this->getPendingBesoins();
            foreach ($besoins as $besoin) {
                $this->allocateForBesoin($besoin, $log);
            }
            $this->pdo->commit();
            $log[] = 'Distribution completed.';
            return $log;
        } catch (Exception $e) {
            $this->pdo->rollBack();
            $log[] = 'Error: ' . $e->getMessage();
            return $log;
        }
    }

    // Simulate distribution without modifying database
    public function simulate(): array {
        $log = [];
        try {
            $besoins = $this->getPendingBesoins();
            
            // Get all stock in memory
            $stocks = $this->getAllStocks();
            
            foreach ($besoins as $besoin) {
                $this->simulateAllocateForBesoin($besoin, $stocks, $log);
            }
            
            $log[] = 'Simulation terminee (aucune modification effectuee).';
            return $log;
        } catch (Exception $e) {
            $log[] = 'Error: ' . $e->getMessage();
            return $log;
        }
    }

    // Get all stocks in memory for simulation
    private function getAllStocks(): array {
        $sql = "SELECT article_id, quantite_stock FROM bn_stock";
        $stmt = $this->pdo->query($sql);
        $stocks = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $stocks[$row['article_id']] = (int)$row['quantite_stock'];
        }
        return $stocks;
    }

    // Simulate allocation without DB changes
    private function simulateAllocateForBesoin(array $besoin, array &$stocks, array &$log) {
        $needed = (int)$besoin['quantite'];
        $article_id = (int)$besoin['article_id'];
        $besoin_id = (int)$besoin['id'];

        $available = isset($stocks[$article_id]) ? $stocks[$article_id] : 0;

        if ($available <= 0) {
            $log[] = "Besoin #{$besoin_id} (article {$article_id}): pas de stock disponible.";
            return;
        }

        if ($available >= $needed) {
            // fully satisfy
            $stocks[$article_id] -= $needed;
            $log[] = "Besoin #{$besoin_id}: satisfait totalement ({$needed}).";
        } else {
            // partial
            $stocks[$article_id] = 0;
            $remaining = $needed - $available;
            $log[] = "Besoin #{$besoin_id}: partiellement satisfait ({$available}), reste {$remaining}.";
        }
    }

    // Small helper: fetch pending besoins ordered
    private function getPendingBesoins(): array {
        $sql = "SELECT * FROM bn_besoin WHERE status_id <> 3 ORDER BY date_besoin ASC, created_at ASC";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Small helper: lock and get stock row for an article
    private function getStockForArticle(int $article_id): ?array {
        $stmt = $this->pdo->prepare("SELECT * FROM bn_stock WHERE article_id = ? FOR UPDATE");
        $stmt->execute([$article_id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    // Allocate stock to a single besoin (mutates DB and appends messages to $log)
    private function allocateForBesoin(array $besoin, array &$log) {
        $needed = (int)$besoin['quantite'];
        $article_id = (int)$besoin['article_id'];
        $besoin_id = (int)$besoin['id'];

        $stock = $this->getStockForArticle($article_id);
        $available = $stock ? (int)$stock['quantite_stock'] : 0;

        if ($available <= 0) {
            $log[] = "Besoin #{$besoin_id} (article {$article_id}): pas de stock disponible.";
            return;
        }

        if ($available >= $needed) {
            // fully satisfy
            $this->createDistribution($besoin_id, $needed, $article_id);
            $this->updateStock($stock['id'], $available - $needed);
            $this->markBesoinSatisfied($besoin_id);
            $log[] = "Besoin #{$besoin_id}: satisfait totalement ({$needed}).";
        } else {
            // partial
            $this->createDistribution($besoin_id, $available, $article_id);
            $this->updateStock($stock['id'], 0);
            $remaining = $needed - $available;
            $this->reduceBesoin($besoin_id, $remaining);
            $log[] = "Besoin #{$besoin_id}: partiellement satisfait ({$available}), reste {$remaining}.";
        }
    }

    // DB small actions
    private function createDistribution(int $besoin_id, int $qty, int $article_id) {
        $stmt = $this->pdo->prepare("INSERT INTO bn_distribution (besoin_id, quantite_distribuee, date_distribution, article_id) VALUES (?,?,?,?)");
        $stmt->execute([$besoin_id, $qty, date('Y-m-d'), $article_id]);
    }

    private function updateStock(int $stock_id, int $newQty) {
        $stmt = $this->pdo->prepare("UPDATE bn_stock SET quantite_stock = ? WHERE id = ?");
        $stmt->execute([$newQty, $stock_id]);
    }

    private function markBesoinSatisfied(int $besoin_id) {
        $stmt = $this->pdo->prepare("UPDATE bn_besoin SET status_id = 3 WHERE id = ?");
        $stmt->execute([$besoin_id]);
    }

    private function reduceBesoin(int $besoin_id, int $remaining) {
        $stmt = $this->pdo->prepare("UPDATE bn_besoin SET quantite = ?, status_id = 2 WHERE id = ?");
        $stmt->execute([$remaining, $besoin_id]);
    }
}
