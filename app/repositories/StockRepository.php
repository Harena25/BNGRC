<?php
class StockRepository
{
    private $pdo;
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }


    public function getAll()
    {
        $st = $this->pdo->query("SELECT s.*, a.libelle AS article_name FROM bn_stock s JOIN bn_article a ON s.article_id = a.id");
        return $st->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findByArticleId($articleId)
    {
        $st = $this->pdo->prepare("SELECT * FROM bn_stock WHERE article_id=? LIMIT 1");
        $st->execute([(int) $articleId]);
        return $st->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function upsertQuantity($articleId, $delta)
    {
        $current = $this->findByArticleId($articleId);
        if ($current) {
            $st = $this->pdo->prepare("UPDATE bn_stock SET quantite_stock = quantite_stock + ? WHERE article_id=?");
            $st->execute([(int) $delta, (int) $articleId]);
            return;
        }

        $st = $this->pdo->prepare("INSERT INTO bn_stock(article_id, quantite_stock) VALUES(?, ?)");
        $st->execute([(int) $articleId, (int) $delta]);
    }

    /**
     * Verifier si un article existe en stock (quantite > 0)
     */
    public function hasStock($articleId)
    {
        $stock = $this->findByArticleId($articleId);
        return $stock && ($stock['quantite_stock'] ?? 0) > 0;
    }

    /**
     * Recuperer le solde argent total (tous les articles de categorie_id = 3)
     */
    public function getSoldeArgent()
    {
        $sql = "SELECT COALESCE(SUM(s.quantite_stock), 0) AS total_argent
                FROM bn_stock s
                JOIN bn_article a ON a.id = s.article_id
                WHERE a.categorie_id = 3";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? (float) $result['total_argent'] : 0.0;
    }

    /**
     * Debiter l'argent (article_id = 8)
     */
    public function debitArgent($montant)
    {
        $st = $this->pdo->prepare("UPDATE bn_stock SET quantite_stock = quantite_stock - ? WHERE article_id = 8");
        return $st->execute([(float) $montant]);
    }

    /**
     * Ajouter du stock pour un article (INSERT ou UPDATE)
     */
    public function addStock($articleId, $quantite)
    {
        $this->upsertQuantity($articleId, $quantite);
        return true;
    }
}