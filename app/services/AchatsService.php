<?php

require_once __DIR__ . '/../repositories/AchatsRepository.php';
require_once __DIR__ . '/../repositories/ArticlesRepository.php';

class AchatsService
{
    private AchatsRepository $achatsRepo;
    private ArticlesRepository $articlesRepo;
    private PDO $pdo;

    // Frais d'achat par défaut en pourcentage
    const DEFAULT_FRAIS_POURCENTAGE = 10;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
        $this->achatsRepo = new AchatsRepository($pdo);
        $this->articlesRepo = new ArticlesRepository($pdo);
    }

    /**
     * Valider et créer un achat
     * - Vérifie que l'article n'est pas de catégorie "Argent" (on ne peut pas acheter de l'argent)
     * - Calcule le prix total avec les frais
     * - Vérifie que le solde des dons en argent est suffisant
     *
     * @return array ['success' => bool, 'message' => string, 'id' => int|null]
     */
    public function creerAchat(int $villeId, int $articleId, int $quantite, string $dateAchat, ?float $fraisPourcentage = null): array
    {
        if ($fraisPourcentage === null) {
            $fraisPourcentage = self::DEFAULT_FRAIS_POURCENTAGE;
        }

        // Récupérer l'article
        $article = $this->articlesRepo->findById($articleId);
        if (!$article) {
            return ['success' => false, 'message' => 'Article introuvable.', 'id' => null];
        }

        // Vérifier que l'article n'est pas de catégorie "Argent" (categorie_id = 3)
        if ((int) $article['categorie_id'] === 3) {
            return ['success' => false, 'message' => 'Impossible d\'acheter un article de type "Argent".', 'id' => null];
        }

        // Calculer le prix total avec frais
        $prixUnitaire = (float) $article['prix_unitaire'];
        $prixBase = $prixUnitaire * $quantite;
        $prixTotal = $prixBase * (1 + $fraisPourcentage / 100);

        // Vérifier le solde des dons en argent
        $solde = $this->achatsRepo->getSoldeArgent();
        if ($prixTotal > $solde) {
            return [
                'success' => false,
                'message' => sprintf(
                    'Solde insuffisant. Coût total : %.2f MAD (dont %.0f%% de frais). Solde disponible : %.2f MAD.',
                    $prixTotal,
                    $fraisPourcentage,
                    $solde
                ),
                'id' => null
            ];
        }

        // Créer l'achat
        $id = $this->achatsRepo->create($villeId, $articleId, $quantite, $prixUnitaire, $fraisPourcentage, $prixTotal, $dateAchat);

        return [
            'success' => true,
            'message' => sprintf(
                'Achat enregistré avec succès. Montant : %.2f MAD (prix de base : %.2f + %.0f%% de frais).',
                $prixTotal,
                $prixBase,
                $fraisPourcentage
            ),
            'id' => $id
        ];
    }

    /**
     * Récupérer le solde disponible en argent
     */
    public function getSoldeArgent(): float
    {
        return $this->achatsRepo->getSoldeArgent();
    }

    /**
     * Récupérer tous les achats, avec filtre optionnel par ville
     */
    public function getAll(?int $villeId = null): array
    {
        return $this->achatsRepo->getAll($villeId);
    }
}
