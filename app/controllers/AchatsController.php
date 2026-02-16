<?php

require_once __DIR__ . '/../services/AchatsService.php';
require_once __DIR__ . '/../repositories/VillesRepository.php';
require_once __DIR__ . '/../repositories/ArticlesRepository.php';

class AchatsController
{
    private AchatsService $achatsService;
    private VillesRepository $villesRepo;
    private ArticlesRepository $articlesRepo;

    public function __construct(PDO $pdo)
    {
        $this->achatsService = new AchatsService($pdo);
        $this->villesRepo = new VillesRepository($pdo);
        $this->articlesRepo = new ArticlesRepository($pdo);
    }

    /**
     * Afficher le formulaire de saisie d'achat
     */
    public function index()
    {
        $villes   = $this->achatsService->getVillesAvecBesoinsRestants();
        $solde    = $this->achatsService->getSoldeArgent();

        $success = isset($_SESSION['flash_success']) ? $_SESSION['flash_success'] : null;
        unset($_SESSION['flash_success']);

        Flight::render('modele', [
            'villes'   => $villes,
            'solde'    => $solde,
            'success'  => $success,
            'error'    => null,
            'old'      => [],
            'pagename' => 'achats/formulaire.php',
        ]);
    }

    /**
     * Afficher la liste des achats (avec filtre par ville)
     */
    public function listPage()
    {
        $villeId = Flight::request()->query->ville_id;
        $villeId = $villeId ? (int) $villeId : null;

        $achats  = $this->achatsService->getAll($villeId);
        $villes  = $this->villesRepo->getAll();
        $solde   = $this->achatsService->getSoldeArgent();

        Flight::render('modele', [
            'achats'        => $achats,
            'villes'        => $villes,
            'solde'         => $solde,
            'filtreVilleId' => $villeId,
            'pagename'      => 'achats/liste.php',
        ]);
    }

    /**
     * Enregistrer un nouvel achat
     */
    public function store()
    {
        $data = Flight::request()->data;

        $villeId    = (int) $data->ville_id;
        $articleId  = (int) $data->article_id;
        $quantite   = (int) $data->quantite;
        $dateAchat  = (string) $data->date_achat;
        $frais      = $data->frais_pourcentage !== null && $data->frais_pourcentage !== ''
            ? (float) $data->frais_pourcentage
            : null;

        // Validation basique
        if ($villeId <= 0 || $articleId <= 0 || $quantite <= 0 || $dateAchat === '') {
            $villes = $this->achatsService->getVillesAvecBesoinsRestants();
            $solde  = $this->achatsService->getSoldeArgent();

            Flight::render('modele', [
                'villes'   => $villes,
                'solde'    => $solde,
                'error'    => 'Tous les champs sont obligatoires et doivent être valides.',
                'success'  => null,
                'old'      => [
                    'ville_id'           => $villeId,
                    'article_id'         => $articleId,
                    'quantite'           => $quantite,
                    'date_achat'         => $dateAchat,
                    'frais_pourcentage'  => $frais,
                ],
                'pagename' => 'achats/formulaire.php',
            ]);
            return;
        }

        // Appel au service
        $result = $this->achatsService->creerAchat($villeId, $articleId, $quantite, $dateAchat, $frais);

        if ($result['success']) {
            $_SESSION['flash_success'] = $result['message'];
            Flight::redirect('/purchases');
        } else {
            // Erreur (solde insuffisant, article invalide, quantité trop élevée, etc.)
            $villes = $this->achatsService->getVillesAvecBesoinsRestants();
            $solde  = $this->achatsService->getSoldeArgent();

            Flight::render('modele', [
                'villes'   => $villes,
                'solde'    => $solde,
                'error'    => $result['message'],
                'success'  => null,
                'old'      => [
                    'ville_id'           => $villeId,
                    'article_id'         => $articleId,
                    'quantite'           => $quantite,
                    'date_achat'         => $dateAchat,
                    'frais_pourcentage'  => $frais,
                ],
                'pagename' => 'achats/formulaire.php',
            ]);
        }
    }

    /**
     * API JSON : retourne les articles achetables pour une ville donnée
     */
    public function getArticlesParVille()
    {
        $villeId = (int) Flight::request()->query->ville_id;
        if ($villeId <= 0) {
            Flight::json([]);
            return;
        }

        $articles = $this->achatsService->getArticlesAchetablesParVille($villeId);
        Flight::json($articles);
    }
}
