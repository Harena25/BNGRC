<?php

class RecapRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function getVilles(): array
    {
        $stmt = $this->pdo->query("SELECT id, libelle FROM bn_ville ORDER BY libelle");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getRecapRows(?int $ville_id = null): array
    {
        if ($ville_id) {
            $sql = "SELECT * FROM v_recap_ville WHERE ville_id = :vid ORDER BY region_name, ville_name";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':vid' => $ville_id]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        $sql = "SELECT * FROM v_recap_ville ORDER BY region_name, ville_name";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
