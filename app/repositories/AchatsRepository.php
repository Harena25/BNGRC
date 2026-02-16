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
}
