<?php

class VillesRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function getAll(): array
    {
        $sql = "
            SELECT
                v.id,
                v.libelle,
                v.region_id,
                r.libelle AS region
            FROM bn_ville v
            JOIN bn_region r ON r.id = v.region_id
            ORDER BY r.libelle ASC, v.libelle ASC
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
