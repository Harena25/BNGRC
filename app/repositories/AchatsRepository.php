<?php

class AchatsRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function getAll(?int $villeId = null): array
    {
        if ($villeId) {
            $sql = "SELECT * FROM v_achats WHERE ville_id = :ville_id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':ville_id' => $villeId]);
        } else {
            $sql = "SELECT * FROM v_achats";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
        }

        return $stmt->fetchAll();
    }

    public function getById(int $id): ?array
    {
        $sql = "SELECT * FROM v_achats WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row === false ? null : $row;
    }

    public function create(int $villeId, int $articleId, int $quantite, float $prixUnitaire, float $fraisPourcentage, float $prixTotal, string $dateAchat): int
    {
        $sql = "
            INSERT INTO bn_achats (ville_id, article_id, quantite, prix_unitaire, frais_pourcentage, prix_total, date_achat)
            VALUES (:ville_id, :article_id, :quantite, :prix_unitaire, :frais_pourcentage, :prix_total, :date_achat)
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':ville_id' => $villeId,
            ':article_id' => $articleId,
            ':quantite' => $quantite,
            ':prix_unitaire' => $prixUnitaire,
            ':frais_pourcentage' => $fraisPourcentage,
            ':prix_total' => $prixTotal,
            ':date_achat' => $dateAchat,
        ]);

        return (int) $this->pdo->lastInsertId();
    }

    public function delete(int $id): bool
    {
        $sql = "DELETE FROM bn_achats WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }

    
    public function getSoldeArgent(): float
    {
        // Total des dons en argent (article_id = 8)
        $sqlDons = "SELECT COALESCE(SUM(quantite_donnee), 0) AS total_dons FROM bn_dons WHERE article_id = 8";
        $stmtDons = $this->pdo->prepare($sqlDons);
        $stmtDons->execute();
        $totalDons = (float) $stmtDons->fetch()['total_dons'];

        // Total des achats déjà effectués
        $sqlAchats = "SELECT COALESCE(SUM(prix_total), 0) AS total_achats FROM bn_achats";
        $stmtAchats = $this->pdo->prepare($sqlAchats);
        $stmtAchats->execute();
        $totalAchats = (float) $stmtAchats->fetch()['total_achats'];

        return $totalDons - $totalAchats;
    }

    /**
     * Récupérer les villes qui ont encore des besoins restants achetables
     * (besoin_restant - stock_disponible - deja_achete > 0)
     */
    public function getVillesAvecBesoinsRestants(): array
    {
        $sql = "
            SELECT DISTINCT
                v.id,
                v.libelle,
                v.region_id,
                r.libelle AS region
            FROM bn_besoin b
            JOIN bn_ville v ON v.id = b.ville_id
            JOIN bn_region r ON r.id = v.region_id
            JOIN bn_article a ON a.id = b.article_id
            LEFT JOIN bn_stock s ON s.article_id = b.article_id
            LEFT JOIN (
                SELECT ville_id, article_id, SUM(quantite) AS total_achete
                FROM bn_achats
                GROUP BY ville_id, article_id
            ) ach ON ach.ville_id = b.ville_id AND ach.article_id = b.article_id
            WHERE b.status_id != 3
              AND a.categorie_id != 3
            GROUP BY v.id, v.libelle, v.region_id, r.libelle, b.article_id
            HAVING SUM(b.quantite) - LEAST(COALESCE(MAX(s.quantite_stock), 0), SUM(b.quantite)) - COALESCE(MAX(ach.total_achete), 0) > 0
            ORDER BY r.libelle ASC, v.libelle ASC
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Récupérer les articles achetables pour une ville donnée
     * Retourne : article_id, libelle, categorie, prix_unitaire, besoin_restant, stock, deja_achete, quantite_achetable
     */
    public function getArticlesAchetablesParVille(int $villeId): array
    {
        $sql = "
            SELECT 
                a.id AS article_id,
                a.libelle AS article_name,
                c.libelle AS categorie_name,
                a.prix_unitaire,
                SUM(b.quantite) AS besoin_restant,
                COALESCE(MAX(s.quantite_stock), 0) AS stock_disponible,
                COALESCE(MAX(ach.total_achete), 0) AS deja_achete,
                GREATEST(0, 
                    SUM(b.quantite) 
                    - LEAST(COALESCE(MAX(s.quantite_stock), 0), SUM(b.quantite)) 
                    - COALESCE(MAX(ach.total_achete), 0)
                ) AS quantite_achetable
            FROM bn_besoin b
            JOIN bn_article a ON a.id = b.article_id
            JOIN bn_categorie c ON c.id = a.categorie_id
            LEFT JOIN bn_stock s ON s.article_id = b.article_id
            LEFT JOIN (
                SELECT ville_id, article_id, SUM(quantite) AS total_achete
                FROM bn_achats
                GROUP BY ville_id, article_id
            ) ach ON ach.ville_id = b.ville_id AND ach.article_id = b.article_id
            WHERE b.ville_id = :ville_id
              AND b.status_id != 3
              AND a.categorie_id != 3
            GROUP BY a.id, a.libelle, c.libelle, a.prix_unitaire
            HAVING quantite_achetable > 0
            ORDER BY c.libelle, a.libelle
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':ville_id' => $villeId]);
        return $stmt->fetchAll();
    }
}
