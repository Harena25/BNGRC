<?php
class AutoDistributor
{
    private $pdo;
    private $sortMode; // 'date' ou 'quantite' ou 'proportionnelle'
    private $tour; // 1 ou 2 (pour le mode proportionnel)

    public function __construct(PDO $pdo, string $sortMode = 'date', int $tour = 1)
    {
        $this->pdo = $pdo;
        $this->sortMode = $sortMode;
        $this->tour = $tour;
    }

    // Public entry: run the simple distribution
    public function run(): array
    {
        // Mode proportionnel : logique différente
        if ($this->sortMode === 'proportionnelle') {
            return $this->runProportional();
        }

        // Mode classique (date ou quantité)
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
    public function simulate(): array
    {
        // Mode proportionnel : logique différente
        if ($this->sortMode === 'proportionnelle') {
            return $this->simulateProportional();
        }

        // Mode classique (date ou quantité)
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
    private function getAllStocks(): array
    {
        $sql = "SELECT article_id, quantite_stock FROM bn_stock";
        $stmt = $this->pdo->query($sql);
        $stocks = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $stocks[$row['article_id']] = (int) $row['quantite_stock'];
        }
        return $stocks;
    }

    // Simulate allocation without DB changes
    private function simulateAllocateForBesoin(array $besoin, array &$stocks, array &$log)
    {
        $needed = (int) $besoin['quantite'];
        $article_id = (int) $besoin['article_id'];
        $besoin_id = (int) $besoin['id'];

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
    private function getPendingBesoins(): array
    {
        // Choisir l'ordre selon le mode de distribution
        if ($this->sortMode === 'quantite') {
            // Mode quantité : du plus petit besoin au plus grand
            $sql = "SELECT * FROM bn_besoin WHERE status_id <> 3 ORDER BY quantite ASC, created_at ASC";
        } else {
            // Mode date : ordre chronologique (par défaut)
            $sql = "SELECT * FROM bn_besoin WHERE status_id <> 3 ORDER BY date_besoin ASC, created_at ASC";
        }
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Small helper: lock and get stock row for an article
    private function getStockForArticle(int $article_id): ?array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM bn_stock WHERE article_id = ? FOR UPDATE");
        $stmt->execute([$article_id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    // Allocate stock to a single besoin (mutates DB and appends messages to $log)
    private function allocateForBesoin(array $besoin, array &$log)
    {
        $needed = (int) $besoin['quantite'];
        $article_id = (int) $besoin['article_id'];
        $besoin_id = (int) $besoin['id'];

        $stock = $this->getStockForArticle($article_id);
        $available = $stock ? (int) $stock['quantite_stock'] : 0;

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
    private function createDistribution(int $besoin_id, int $qty, int $article_id)
    {
        $stmt = $this->pdo->prepare("INSERT INTO bn_distribution (besoin_id, quantite_distribuee, date_distribution, article_id) VALUES (?,?,?,?)");
        $stmt->execute([$besoin_id, $qty, date('Y-m-d'), $article_id]);
    }

    private function updateStock(int $stock_id, int $newQty)
    {
        $stmt = $this->pdo->prepare("UPDATE bn_stock SET quantite_stock = ? WHERE id = ?");
        $stmt->execute([$newQty, $stock_id]);
    }

    private function markBesoinSatisfied(int $besoin_id)
    {
        $stmt = $this->pdo->prepare("UPDATE bn_besoin SET status_id = 3 WHERE id = ?");
        $stmt->execute([$besoin_id]);
    }

    private function reduceBesoin(int $besoin_id, int $remaining)
    {
        $stmt = $this->pdo->prepare("UPDATE bn_besoin SET quantite = ?, status_id = 2 WHERE id = ?");
        $stmt->execute([$remaining, $besoin_id]);
    }

    // ========================================================================
    // DISTRIBUTION PROPORTIONNELLE
    // ========================================================================

    /**
     * Distribution proportionnelle : chaque bénéficiaire reçoit une part
     * proportionnelle à sa demande (avec arrondi inférieur)
     * Tour 1: distribution avec floor()
     * Tour 2: distribution du reste selon les décimales les plus élevées
     */
    public function runProportional(): array
    {
        $this->pdo->beginTransaction();
        $log = [];
        $log[] = "=== DISTRIBUTION PROPORTIONNELLE (TOUR {$this->tour}) ===";

        try {
            // Récupérer tous les stocks disponibles
            $stocks = $this->getAllStocksWithDetails();

            foreach ($stocks as $stock) {
                $article_id = (int) $stock['article_id'];
                $stock_disponible = (int) $stock['quantite_stock'];
                $stock_id = (int) $stock['id'];

                if ($stock_disponible <= 0) {
                    continue; // Pas de stock, on passe
                }

                // Récupérer tous les besoins pour cet article
                $besoins = $this->getBesoinsForArticle($article_id);

                if (empty($besoins)) {
                    continue; // Pas de besoin, on passe
                }

                // Calculer total des demandes
                $total_demandes = 0;
                foreach ($besoins as $besoin) {
                    $total_demandes += (int) $besoin['quantite'];
                }

                $log[] = "";
                $log[] = "Article #{$article_id} - Stock disponible: {$stock_disponible}, Total demandes: {$total_demandes}";

                // TOUR 1: Distribuer proportionnellement avec floor
                $distributions = [];
                $total_distribue = 0;

                foreach ($besoins as $besoin) {
                    $besoin_id = (int) $besoin['id'];
                    $qte_demandee = (int) $besoin['quantite'];
                    $ville_id = (int) $besoin['ville_id'];

                    // Calcul proportionnel
                    $ratio = $qte_demandee / $total_demandes;
                    $valeur_exacte = $ratio * $stock_disponible;
                    $qte_distribuee = floor($valeur_exacte);
                    $decimale = $valeur_exacte - $qte_distribuee;

                    $distributions[] = [
                        'besoin_id' => $besoin_id,
                        'ville_id' => $ville_id,
                        'qte_demandee' => $qte_demandee,
                        'ratio' => $ratio,
                        'valeur_exacte' => $valeur_exacte,
                        'qte_distribuee' => $qte_distribuee,
                        'decimale' => $decimale
                    ];

                    $total_distribue += $qte_distribuee;
                }

                $stock_restant = $stock_disponible - $total_distribue;

                // TOUR 2: Distribuer le reste selon les décimales
                if ($this->tour == 2 && $stock_restant > 0) {
                    $log[] = "  [Tour 1] Total distribué: {$total_distribue}, Reste: {$stock_restant}";
                    $log[] = "  [Tour 2] Distribution du reste par décimales décroissantes...";

                    // Trier par décimale décroissante
                    usort($distributions, function ($a, $b) {
                        return $b['decimale'] <=> $a['decimale'];
                    });

                    // Distribuer 1 unité à chaque besoin selon l'ordre (tant qu'il reste du stock)
                    foreach ($distributions as &$distrib) {
                        if ($stock_restant > 0) {
                            $distrib['qte_distribuee']++;
                            $stock_restant--;
                            $total_distribue++;
                            $log[] = "    + Ville #{$distrib['ville_id']}, Besoin #{$distrib['besoin_id']} (décimale: " . round($distrib['decimale'], 3) . "): +1";
                        }
                    }
                    unset($distrib);
                }

                // Enregistrer les distributions
                foreach ($distributions as $distrib) {
                    $besoin_id = $distrib['besoin_id'];
                    $ville_id = $distrib['ville_id'];
                    $qte_demandee = $distrib['qte_demandee'];
                    $qte_distribuee = $distrib['qte_distribuee'];

                    if ($qte_distribuee > 0) {
                        // Créer la distribution
                        $this->createDistribution($besoin_id, $qte_distribuee, $article_id);

                        // Mettre à jour le besoin
                        $qte_restante = $qte_demandee - $qte_distribuee;
                        if ($qte_restante <= 0) {
                            $this->markBesoinSatisfied($besoin_id);
                            $log[] = "  Ville #{$ville_id}, Besoin #{$besoin_id} (demande: {$qte_demandee}): {$qte_distribuee} distribué (satisfait totalement)";
                        } else {
                            $this->reduceBesoin($besoin_id, $qte_restante);
                            $log[] = "  Ville #{$ville_id}, Besoin #{$besoin_id} (demande: {$qte_demandee}): {$qte_distribuee} distribué, reste {$qte_restante}";
                        }
                    } else {
                        $log[] = "  Ville #{$ville_id}, Besoin #{$besoin_id} (demande: {$qte_demandee}): 0 distribué";
                    }
                }

                // Mettre à jour le stock
                $this->updateStock($stock_id, $stock_restant);
                $log[] = "  Total distribué: {$total_distribue}, Stock restant: {$stock_restant}";
            }

            $this->pdo->commit();
            $log[] = "";
            $log[] = "Distribution proportionnelle (tour {$this->tour}) terminée.";
            return $log;
        } catch (Exception $e) {
            $this->pdo->rollBack();
            $log[] = 'Error: ' . $e->getMessage();
            return $log;
        }
    }

    /**
     * Simulation de distribution proportionnelle (sans modifier la DB)
     */
    public function simulateProportional(): array
    {
        $log = [];
        $log[] = "=== SIMULATION DISTRIBUTION PROPORTIONNELLE (TOUR {$this->tour}) ===";

        try {
            // Récupérer tous les stocks disponibles
            $stocks = $this->getAllStocksWithDetails();

            foreach ($stocks as $stock) {
                $article_id = (int) $stock['article_id'];
                $stock_disponible = (int) $stock['quantite_stock'];

                if ($stock_disponible <= 0) {
                    continue;
                }

                // Récupérer tous les besoins pour cet article
                $besoins = $this->getBesoinsForArticle($article_id);

                if (empty($besoins)) {
                    continue;
                }

                // Calculer total des demandes
                $total_demandes = 0;
                foreach ($besoins as $besoin) {
                    $total_demandes += (int) $besoin['quantite'];
                }

                $log[] = "";
                $log[] = "Article #{$article_id} - Stock disponible: {$stock_disponible}, Total demandes: {$total_demandes}";

                // TOUR 1: Distribuer proportionnellement avec floor
                $distributions = [];
                $total_distribue = 0;

                foreach ($besoins as $besoin) {
                    $besoin_id = (int) $besoin['id'];
                    $qte_demandee = (int) $besoin['quantite'];
                    $ville_id = (int) $besoin['ville_id'];

                    // Calcul proportionnel
                    $ratio = $qte_demandee / $total_demandes;
                    $valeur_exacte = $ratio * $stock_disponible;
                    $qte_distribuee = floor($valeur_exacte);
                    $decimale = $valeur_exacte - $qte_distribuee;

                    $distributions[] = [
                        'besoin_id' => $besoin_id,
                        'ville_id' => $ville_id,
                        'qte_demandee' => $qte_demandee,
                        'ratio' => $ratio,
                        'valeur_exacte' => $valeur_exacte,
                        'qte_distribuee' => $qte_distribuee,
                        'decimale' => $decimale
                    ];

                    $total_distribue += $qte_distribuee;
                }

                $stock_restant = $stock_disponible - $total_distribue;

                // TOUR 2: Distribuer le reste selon les décimales
                if ($this->tour == 2 && $stock_restant > 0) {
                    $log[] = "  [Tour 1] Total distribué: {$total_distribue}, Reste: {$stock_restant}";
                    $log[] = "  [Tour 2] Distribution du reste par décimales décroissantes...";

                    // Trier par décimale décroissante
                    usort($distributions, function ($a, $b) {
                        return $b['decimale'] <=> $a['decimale'];
                    });

                    // Distribuer 1 unité à chaque besoin selon l'ordre (tant qu'il reste du stock)
                    foreach ($distributions as &$distrib) {
                        if ($stock_restant > 0) {
                            $distrib['qte_distribuee']++;
                            $stock_restant--;
                            $total_distribue++;
                            $log[] = "    + Ville #{$distrib['ville_id']}, Besoin #{$distrib['besoin_id']} (décimale: " . round($distrib['decimale'], 3) . "): +1";
                        }
                    }
                    unset($distrib);
                }

                // Afficher les résultats
                foreach ($distributions as $distrib) {
                    $besoin_id = $distrib['besoin_id'];
                    $ville_id = $distrib['ville_id'];
                    $qte_demandee = $distrib['qte_demandee'];
                    $qte_distribuee = $distrib['qte_distribuee'];
                    $ratio = $distrib['ratio'];

                    if ($qte_distribuee > 0) {
                        $qte_restante = $qte_demandee - $qte_distribuee;

                        if ($qte_restante <= 0) {
                            $log[] = "  Ville #{$ville_id}, Besoin #{$besoin_id} (demande: {$qte_demandee}): {$qte_distribuee} distribué (satisfait totalement, ratio " . round($ratio * 100, 1) . "%)";
                        } else {
                            $log[] = "  Ville #{$ville_id}, Besoin #{$besoin_id} (demande: {$qte_demandee}): {$qte_distribuee} distribué, reste {$qte_restante} (ratio " . round($ratio * 100, 1) . "%)";
                        }
                    } else {
                        $log[] = "  Ville #{$ville_id}, Besoin #{$besoin_id} (demande: {$qte_demandee}): 0 distribué (ratio " . round($ratio * 100, 1) . "% trop faible)";
                    }
                }

                $log[] = "  Total distribué: {$total_distribue}, Stock restant: {$stock_restant}";
            }

            $log[] = "";
            $log[] = "Simulation terminée (aucune modification effectuée).";
            return $log;
        } catch (Exception $e) {
            $log[] = 'Error: ' . $e->getMessage();
            return $log;
        }
    }

    /**
     * Récupérer tous les stocks avec détails (pour proportionnel)
     */
    private function getAllStocksWithDetails(): array
    {
        $sql = "SELECT id, article_id, quantite_stock FROM bn_stock WHERE quantite_stock > 0";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Récupérer tous les besoins non satisfaits pour un article
     */
    private function getBesoinsForArticle(int $article_id): array
    {
        $sql = "SELECT id, ville_id, quantite FROM bn_besoin WHERE article_id = ? AND status_id <> 3 ORDER BY id ASC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$article_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
