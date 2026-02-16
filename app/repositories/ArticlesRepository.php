<?php
class ArticlesRepository
{
    private $pdo;
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function create($data)
    {
        $st = $this->pdo->prepare("
      INSERT INTO bn_article(libelle, categorie_id, prix_unitaire)
      VALUES(?, ?, ?)
    ");
        $st->execute([
            (string) $data['libelle'],
            (int) $data['categorie_id'],
            (float) $data['prix_unitaire']
        ]);
        return $this->pdo->lastInsertId();
    }

    public function findAll()
    {
        $st = $this->pdo->query("SELECT * FROM vw_articles");
        return $st->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findById($id)
    {
        $st = $this->pdo->prepare("SELECT * FROM vw_articles WHERE id=? LIMIT 1");
        $st->execute([(int) $id]);
        return $st->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function deleteById($id)
    {
        $st = $this->pdo->prepare("DELETE FROM bn_article WHERE id=?");
        $st->execute([(int) $id]);
    }

    public function update($id, $data)
    {
        $st = $this->pdo->prepare("
      UPDATE bn_article
      SET libelle=?, categorie_id=?, prix_unitaire=?
      WHERE id=?
    ");
        $st->execute([
            (string) $data['libelle'],
            (int) $data['categorie_id'],
            (float) $data['prix_unitaire'],
            (int) $id
        ]);
    }

}
