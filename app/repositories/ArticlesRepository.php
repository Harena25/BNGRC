<?php

class ArticlesRepository
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
                a.id,
                a.libelle,
                a.categorie_id,
                c.libelle AS categorie,
                a.prix_unitaire
            FROM bn_article a
            JOIN bn_categorie c ON c.id = a.categorie_id
            ORDER BY c.libelle ASC, a.libelle ASC
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
