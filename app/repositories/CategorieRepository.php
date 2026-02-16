<?php
class CategorieRepository
{
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function findAll()
    {
        $st = $this->pdo->query("SELECT * FROM bn_categorie ORDER BY libelle");
        return $st->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findById($id)
    {
        $st = $this->pdo->prepare("SELECT * FROM bn_categorie WHERE id=? LIMIT 1");
        $st->execute([(int) $id]);
        return $st->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function create($data)
    {
        $st = $this->pdo->prepare("INSERT INTO bn_categorie(libelle) VALUES(?)");
        $st->execute([(string) $data['libelle']]);
        return $this->pdo->lastInsertId();
    }

    public function update($id, $data)
    {
        $st = $this->pdo->prepare("UPDATE bn_categorie SET libelle=? WHERE id=?");
        $st->execute([(string) $data['libelle'], (int) $id]);
    }

    public function deleteById($id)
    {
        $st = $this->pdo->prepare("DELETE FROM bn_categorie WHERE id=?");
        $st->execute([(int) $id]);
    }
}
