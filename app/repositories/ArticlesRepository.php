<?php
<<<<<<< HEAD

class ArticlesRepository
{
    private PDO $pdo;

=======
class ArticlesRepository
{
    private $pdo;
>>>>>>> refs/remotes/origin/main
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

<<<<<<< HEAD
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
=======
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
        $st = $this->pdo->query("
      SELECT a.*, c.libelle as categorie_libelle
      FROM bn_article a
      LEFT JOIN bn_categorie c ON a.categorie_id = c.id
    ");
        return $st->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findById($id)
    {
        $st = $this->pdo->prepare("
      SELECT a.*, c.libelle as categorie_libelle
      FROM bn_article a
      LEFT JOIN bn_categorie c ON a.categorie_id = c.id
      WHERE a.id=? LIMIT 1
    ");
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

>>>>>>> refs/remotes/origin/main
}
