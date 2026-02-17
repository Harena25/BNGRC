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
        // Somme du stock de tous les articles de catégorie "Argent" (categorie_id = 3)
        $sql = "SELECT COALESCE(SUM(s.quantite_stock), 0) AS solde 
                FROM bn_stock s
                JOIN bn_article a ON a.id = s.article_id
                WHERE a.categorie_id = 3";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetch();
        
        return $result ? (float) $result['solde'] : 0.0;
    }

    /**
     * Récupérer les villes qui ont encore des besoins non satisfaits
     * Le calcul de quantité achetable se fait dans getArticlesAchetablesParVille()
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
            WHERE b.status_id != 3
              AND a.categorie_id != 3
              AND b.quantite > 0
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
                COALESCE(s.quantite_stock, 0) AS stock_disponible,
                COALESCE(ach.total_achete, 0) AS deja_achete,
                GREATEST(0, 
                    SUM(b.quantite) 
                    - LEAST(COALESCE(s.quantite_stock, 0), SUM(b.quantite)) 
                    - COALESCE(ach.total_achete, 0)
                ) AS quantite_achetable
            FROM bn_besoin b
            JOIN bn_article a ON a.id = b.article_id
            JOIN bn_categorie c ON c.id = a.categorie_id
            LEFT JOIN bn_stock s ON s.article_id = b.article_id
            LEFT JOIN (
                SELECT article_id, SUM(quantite) AS total_achete
                FROM bn_achats
                WHERE ville_id = :ville_id
                GROUP BY article_id
            ) ach ON ach.article_id = b.article_id
            WHERE b.ville_id = :ville_id
              AND b.status_id != 3
              AND a.categorie_id != 3
              AND b.quantite > 0
            GROUP BY a.id, a.libelle, c.libelle, a.prix_unitaire, s.quantite_stock, ach.total_achete
            HAVING besoin_restant > 0
            ORDER BY c.libelle, a.libelle
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':ville_id' => $villeId]);
        
        // Filtrer uniquement les articles avec quantite_achetable > 0
        $results = $stmt->fetchAll();
        return array_filter($results, function($article) {
            return (int)$article['quantite_achetable'] > 0;
        });
    }
}
