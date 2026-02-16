<?php
class BesoinsRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function getAll(): array
    {
        $sql = "
            SELECT b.id,b.ville_id,v.libelle AS ville,v.region_id,r.libelle AS region,b.article_id,a.libelle AS article,a.prix_unitaire,c.id AS categorie_id,c.libelle AS categorie,b.quantite,b.quantite_initiale,b.date_besoin,b.status_id,s.libelle AS status,b.created_at
            FROM bn_besoin b
            JOIN bn_ville v ON v.id = b.ville_id
            JOIN bn_region r ON r.id = v.region_id
            JOIN bn_article a ON a.id = b.article_id
            JOIN bn_categorie c ON c.id = a.categorie_id
            JOIN bn_status s ON s.id = b.status_id
            ORDER BY b.created_at DESC, b.id DESC
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getBesoinsRestants(): array
    {
        $sql = "
            SELECT b.id,b.ville_id,v.libelle AS ville,v.region_id,r.libelle AS region,b.article_id,a.libelle AS article,a.prix_unitaire,c.id AS categorie_id,c.libelle AS categorie,b.quantite,b.quantite_initiale,b.date_besoin,b.status_id,s.libelle AS status,b.created_at
            FROM bn_besoin b
            JOIN bn_ville v ON v.id = b.ville_id
            JOIN bn_region r ON r.id = v.region_id
            JOIN bn_article a ON a.id = b.article_id
            JOIN bn_categorie c ON c.id = a.categorie_id
            JOIN bn_status s ON s.id = b.status_id
            WHERE b.status_id != 3
            ORDER BY b.date_besoin ASC, b.created_at ASC
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getById(int $id): ?array
    {
        $sql = "SELECT * FROM bn_besoin WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row === false ? null : $row;
    }

    public function create(int $villeId, int $articleId, int $quantite, string $dateBesoin, int $statusId): int
    {
        $sql = "
            INSERT INTO bn_besoin (ville_id, article_id, quantite, quantite_initiale, date_besoin, status_id)
            VALUES (:ville_id, :article_id, :quantite, :quantite_initiale, :date_besoin, :status_id)
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':ville_id' => $villeId,
            ':article_id' => $articleId,
            ':quantite' => $quantite,
            ':quantite_initiale' => $quantite,
            ':date_besoin' => $dateBesoin,
            ':status_id' => $statusId,
        ]);

        return (int)$this->pdo->lastInsertId();
    }

    public function update(int $id, int $villeId, int $articleId, int $quantite, string $dateBesoin, int $statusId): bool
    {
        $sql = "
            UPDATE bn_besoin
            SET ville_id = :ville_id,
                article_id = :article_id,
                quantite = :quantite,
                quantite_initiale = :quantite_initiale,
                date_besoin = :date_besoin,
                status_id = :status_id
            WHERE id = :id
        ";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':id' => $id,
            ':ville_id' => $villeId,
            ':article_id' => $articleId,
            ':quantite' => $quantite,
            ':quantite_initiale' => $quantite,
            ':date_besoin' => $dateBesoin,
            ':status_id' => $statusId,
        ]);
    }

    public function delete(int $id): bool
    {
        $sql = "DELETE FROM bn_besoin WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }

    /**
     * Réduire la quantité restante des besoins pour une ville+article donnés après un achat.
     * Distribue la quantité achetée sur les besoins par ordre chronologique (date_besoin, created_at).
     * Met à jour le status_id : 2 = partiel, 3 = satisfait.
     *
     * @return int Quantité effectivement déduite des besoins
     */
    public function reduireParAchat(int $villeId, int $articleId, int $quantiteAchetee): int
    {
        // Récupérer les besoins non satisfaits pour cette ville+article, par ordre chronologique
        $sql = "
            SELECT id, quantite
            FROM bn_besoin
            WHERE ville_id = :ville_id
              AND article_id = :article_id
              AND status_id != 3
              AND quantite > 0
            ORDER BY date_besoin ASC, created_at ASC
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':ville_id' => $villeId, ':article_id' => $articleId]);
        $besoins = $stmt->fetchAll();

        $restant = $quantiteAchetee;
        $totalReduit = 0;

        foreach ($besoins as $besoin) {
            if ($restant <= 0) break;

            $reduction = min($restant, (int) $besoin['quantite']);
            $nouvelleQte = (int) $besoin['quantite'] - $reduction;
            $nouveauStatus = $nouvelleQte <= 0 ? 3 : 2; // 3=Satisfait, 2=Partiel

            $updateSql = "UPDATE bn_besoin SET quantite = :quantite, status_id = :status_id WHERE id = :id";
            $updateStmt = $this->pdo->prepare($updateSql);
            $updateStmt->execute([
                ':quantite' => $nouvelleQte,
                ':status_id' => $nouveauStatus,
                ':id' => $besoin['id'],
            ]);

            $restant -= $reduction;
            $totalReduit += $reduction;
        }

        return $totalReduit;
    }
}
