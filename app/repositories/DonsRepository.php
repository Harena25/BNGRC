<?php
class DonsRepository
{
  private $pdo;
  public function __construct(PDO $pdo)
  {
    $this->pdo = $pdo;
  }

  public function create($data)
  {
    $st = $this->pdo->prepare("
      INSERT INTO bn_dons(article_id, quantite_donnee, date_don)
      VALUES(?, ?, ?)
    ");
    $st->execute([(int) $data['article_id'], (int) $data['quantite_donnee'], (string) $data['date_don']]);
    return $this->pdo->lastInsertId();
  }

  public function findAll()
  {
    $st = $this->pdo->query("SELECT * FROM bn_dons");
    return $st->fetchAll(PDO::FETCH_ASSOC);
  }

  public function findById($id)
  {
    $st = $this->pdo->prepare("SELECT * FROM bn_dons WHERE id=? LIMIT 1");
    $st->execute([(int) $id]);
    return $st->fetch(PDO::FETCH_ASSOC) ?: null;
  }

  public function deleteById($id)
  {
    $st = $this->pdo->prepare("DELETE FROM bn_dons WHERE id=?");
    $st->execute([(int) $id]);
  }

  public function update($id, $data)
  {
    $st = $this->pdo->prepare("
      UPDATE bn_dons
      SET article_id=?, quantite_donnee=?, date_don=?
      WHERE id=?
    ");
    $st->execute([(int) $data['article_id'], (int) $data['quantite_donnee'], (string) $data['date_don'], (int) $id]);
  }

}
