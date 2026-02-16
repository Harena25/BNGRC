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
}
