<?php
class StockRepository{

    private $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function getAll(){
        $sql = "SELECT s.*, a.libelle AS article_name FROM bn_stock s JOIN bn_article a ON s.article_id = a.id";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

}