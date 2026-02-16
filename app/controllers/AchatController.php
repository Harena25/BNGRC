<?php

require_once __DIR__ . '/../repositories/BesoinsRepository.php';
require_once __DIR__ . '/../repositories/StockRepository.php';
require_once __DIR__ . '/../repositories/ArticlesRepository.php';
require_once __DIR__ . '/../repositories/AchatsRepository.php';

class AchatController
{
    private BesoinsRepository $besoinsRepo;
    private StockRepository $stockRepo;
    private ArticlesRepository $articlesRepo;
    private AchatsRepository $achatsRepo;
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
        $this->besoinsRepo = new BesoinsRepository($pdo);
        $this->stockRepo = new StockRepository($pdo);
        $this->articlesRepo = new ArticlesRepository($pdo);
        $this->achatsRepo = new AchatsRepository($pdo);
    }

    /**
     * Afficher le formulaire d'achat pour un besoin specifique
     */
    public function form($besoin_id)
    {
        // Recuperer le besoin
        $besoin = $this->besoinsRepo->getById($besoin_id);
        
        if (!$besoin) {
            Flight::redirect('/needs/restants');
            return;
        }

        // Recuperer infos complementaires via SQL
        $sql = "
            SELECT b.id, b.ville_id, v.libelle AS ville, r.libelle AS region,
                   b.article_id, a.libelle AS article, a.prix_unitaire,
                   c.libelle AS categorie,
                   b.quantite, b.quantite_initiale, b.date_besoin,
                   b.status_id, s.libelle AS status
            FROM bn_besoin b
            JOIN bn_ville v ON v.id = b.ville_id
            JOIN bn_region r ON r.id = v.region_id
            JOIN bn_article a ON a.id = b.article_id
            JOIN bn_categorie c ON c.id = a.categorie_id
            JOIN bn_status s ON s.id = b.status_id
            WHERE b.id = :id
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $besoin_id]);
        $besoinDetail = $stmt->fetch(PDO::FETCH_ASSOC);

        // Recuperer le solde argent
        $soldeArgent = $this->stockRepo->getSoldeArgent();

        // Recuperer le frais d'achat depuis la config
        $fraisAchat = defined('FRAIS_ACHAT_POURCENT') ? FRAIS_ACHAT_POURCENT : 10;

        $pagename = 'achats/form.php';
        Flight::render('modele', [
            'besoin' => $besoinDetail,
            'solde_argent' => $soldeArgent,
            'frais_achat' => $fraisAchat,
            'pagename' => $pagename
        ]);
    }

    /**
     * Simuler l'achat (retour JSON)
     */
    public function simuler()
    {
        header('Content-Type: application/json');
        
        $besoin_id = (int) ($_POST['besoin_id'] ?? 0);
        $quantite = (int) ($_POST['quantite'] ?? 0);
        $fraisAchat = (float) ($_POST['frais_pourcent'] ?? 10);

        // Validation
        if ($besoin_id <= 0 || $quantite <= 0) {
            echo json_encode(['success' => false, 'message' => 'Parametres invalides']);
            return;
        }

        if ($fraisAchat < 0 || $fraisAchat > 100) {
            echo json_encode(['success' => false, 'message' => 'Taux de frais invalide (0-100%)']);
            return;
        }

        // Recuperer le besoin
        $sql = "SELECT b.*, a.prix_unitaire, a.categorie_id 
                FROM bn_besoin b 
                JOIN bn_article a ON b.article_id = a.id 
                WHERE b.id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $besoin_id]);
        $besoin = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$besoin) {
            echo json_encode(['success' => false, 'message' => 'Besoin introuvable']);
            return;
        }

        // Verifier quantite
        if ($quantite > $besoin['quantite']) {
            echo json_encode(['success' => false, 'message' => 'Quantite superieure au besoin restant']);
            return;
        }

        // Verifier si l'article existe en stock
        if ($this->stockRepo->hasStock($besoin['article_id'])) {
            echo json_encode(['success' => false, 'message' => 'Cet article existe deja en stock. Utilisez d\'abord les dons existants.']);
            return;
        }

        // Calculer le montant (fraisAchat deja recupere depuis POST)
        $prixUnitaire = (float) $besoin['prix_unitaire'];
        $sousTotal = $quantite * $prixUnitaire;
        $frais = $sousTotal * ($fraisAchat / 100);
        $montantTotal = $sousTotal + $frais;

        // Verifier solde argent
        $soldeActuel = $this->stockRepo->getSoldeArgent();
        if ($soldeActuel < $montantTotal) {
            echo json_encode([
                'success' => false, 
                'message' => sprintf('Solde insuffisant. Disponible: %.2f MAD, Requis: %.2f MAD', $soldeActuel, $montantTotal)
            ]);
            return;
        }

        // Simulation OK
        echo json_encode([
            'success' => true,
            'data' => [
                'quantite' => $quantite,
                'prix_unitaire' => $prixUnitaire,
                'frais_pourcent' => $fraisAchat,
                'sous_total' => $sousTotal,
                'frais' => $frais,
                'montant_total' => $montantTotal,
                'solde_actuel' => $soldeActuel,
                'solde_apres' => $soldeActuel - $montantTotal
            ]
        ]);
    }

    /**
     * Valider l'achat (execution reelle)
     */
    public function valider()
    {
        header('Content-Type: application/json');
        
        $besoin_id = (int) ($_POST['besoin_id'] ?? 0);
        $quantite = (int) ($_POST['quantite'] ?? 0);
        $fraisAchat = (float) ($_POST['frais_pourcent'] ?? 10);

        // Re-verification (securite)
        if ($besoin_id <= 0 || $quantite <= 0) {
            echo json_encode(['success' => false, 'message' => 'Parametres invalides']);
            return;
        }

        if ($fraisAchat < 0 || $fraisAchat > 100) {
            echo json_encode(['success' => false, 'message' => 'Taux de frais invalide']);
            return;
        }

        try {
            $this->pdo->beginTransaction();

            // Recuperer le besoin
            $sql = "SELECT b.*, a.prix_unitaire, a.categorie_id 
                    FROM bn_besoin b 
                    JOIN bn_article a ON b.article_id = a.id 
                    WHERE b.id = :id FOR UPDATE";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':id' => $besoin_id]);
            $besoin = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$besoin) {
                throw new Exception('Besoin introuvable');
            }

            if ($quantite > $besoin['quantite']) {
                throw new Exception('Quantite superieure au besoin restant');
            }

            if ($this->stockRepo->hasStock($besoin['article_id'])) {
                throw new Exception('Cet article existe deja en stock');
            }

            // Calculer montant (fraisAchat deja recupere depuis POST)
            $prixUnitaire = (float) $besoin['prix_unitaire'];
            $sousTotal = $quantite * $prixUnitaire;
            $frais = $sousTotal * ($fraisAchat / 100);
            $montantTotal = $sousTotal + $frais;

            $soldeActuel = $this->stockRepo->getSoldeArgent();
            if ($soldeActuel < $montantTotal) {
                throw new Exception(sprintf('Solde insuffisant. Disponible: %.2f MAD', $soldeActuel));
            }

            // Executer l'achat
            // 1. Debiter l'argent
            $this->stockRepo->debitArgent($montantTotal);

            // 2. Ajouter au stock
            $this->stockRepo->addStock($besoin['article_id'], $quantite);

            // 3. Enregistrer dans bn_achats
            $this->achatsRepo->create(
                (int) $besoin['ville_id'],
                (int) $besoin['article_id'],
                $quantite,
                $prixUnitaire,
                $fraisAchat,
                $montantTotal,
                date('Y-m-d')
            );

            // 4. Mettre à jour le besoin (quantite restante et status)
            $this->besoinsRepo->reduireParAchat(
                (int) $besoin['ville_id'],
                (int) $besoin['article_id'],
                $quantite
            );

            $this->pdo->commit();

            echo json_encode([
                'success' => true,
                'message' => 'Achat effectue avec succes',
                'data' => [
                    'montant_total' => $montantTotal,
                    'quantite' => $quantite
                ]
            ]);

        } catch (Exception $e) {
            $this->pdo->rollBack();
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * Afficher la simulation globale des achats possibles
     */
    public function simulerGlobal()
    {
        $pagename = 'achats/simuler_global.php';
        
        // Recuperer le taux de frais depuis la query string ou utiliser la valeur par defaut
        $fraisAchat = isset($_GET['frais']) ? (float) $_GET['frais'] : (defined('FRAIS_ACHAT_POURCENT') ? FRAIS_ACHAT_POURCENT : 10);
        
        // Validation du taux
        if ($fraisAchat < 0 || $fraisAchat > 100) {
            $fraisAchat = 10; // Valeur par defaut si invalide
        }
        
        // Recuperer tous les besoins restants
        $besoins = $this->besoinsRepo->getBesoinsRestants();
        
        // Recuperer le solde argent
        $soldeArgent = $this->stockRepo->getSoldeArgent();
        
        // Calculer les achats possibles
        $achats = [];
        $articlesEnStock = [];
        $totalCout = 0;
        
        foreach ($besoins as $besoin) {
            $article_id = (int) $besoin['article_id'];
            
            // Verifier si l'article existe en stock
            if ($this->stockRepo->hasStock($article_id)) {
                // Article en stock - proposer distribution ou achat
                $stock = $this->stockRepo->findByArticleId($article_id);
                $articlesEnStock[] = [
                    'besoin_id' => $besoin['id'],
                    'ville' => $besoin['ville'],
                    'article' => $besoin['article'],
                    'categorie' => $besoin['categorie'],
                    'quantite_besoin' => (int) $besoin['quantite'],
                    'quantite_stock' => (int) ($stock['quantite_stock'] ?? 0),
                    'prix_unitaire' => (float) $besoin['prix_unitaire']
                ];
                continue;
            }
            
            // Article non en stock - calculer cout d'achat
            $quantite = (int) $besoin['quantite'];
            $prixUnitaire = (float) $besoin['prix_unitaire'];
            $sousTotal = $quantite * $prixUnitaire;
            $frais = $sousTotal * ($fraisAchat / 100);
            $montantTotal = $sousTotal + $frais;
            
            $achats[] = [
                'besoin_id' => $besoin['id'],
                'ville' => $besoin['ville'],
                'article' => $besoin['article'],
                'categorie' => $besoin['categorie'],
                'quantite' => $quantite,
                'prix_unitaire' => $prixUnitaire,
                'sous_total' => $sousTotal,
                'frais' => $frais,
                'montant_total' => $montantTotal
            ];
            
            $totalCout += $montantTotal;
        }
        
        Flight::render('modele', [
            'achats' => $achats,
            'articles_stock' => $articlesEnStock,
            'solde_argent' => $soldeArgent,
            'total_cout' => $totalCout,
            'frais_achat' => $fraisAchat,
            'pagename' => $pagename
        ]);
    }

    /**
     * Valider tous les achats en une seule fois
     */
    public function validerGlobal()
    {
        header('Content-Type: application/json');
        
        $fraisAchat = (float) ($_POST['frais_pourcent'] ?? 10);

        if ($fraisAchat < 0 || $fraisAchat > 100) {
            echo json_encode(['success' => false, 'message' => 'Taux de frais invalide']);
            return;
        }

        try {
            $this->pdo->beginTransaction();

            // Recuperer tous les besoins restants
            $besoins = $this->besoinsRepo->getBesoinsRestants();
            
            if (empty($besoins)) {
                throw new Exception('Aucun besoin restant a traiter');
            }

            // Calculer le cout total et preparer les achats
            $achatsAPreparer = [];
            $totalCout = 0;

            foreach ($besoins as $besoin) {
                $article_id = (int) $besoin['article_id'];
                
                // Ignorer les articles deja en stock
                if ($this->stockRepo->hasStock($article_id)) {
                    continue;
                }
                
                $quantite = (int) $besoin['quantite'];
                $prixUnitaire = (float) $besoin['prix_unitaire'];
                $sousTotal = $quantite * $prixUnitaire;
                $frais = $sousTotal * ($fraisAchat / 100);
                $montantTotal = $sousTotal + $frais;
                
                $achatsAPreparer[] = [
                    'besoin_id' => $besoin['id'],
                    'ville_id' => (int) $besoin['ville_id'],
                    'article_id' => $article_id,
                    'article_nom' => $besoin['article'],
                    'quantite' => $quantite,
                    'prix_unitaire' => $prixUnitaire,
                    'montant_total' => $montantTotal
                ];
                
                $totalCout += $montantTotal;
            }

            if (empty($achatsAPreparer)) {
                throw new Exception('Aucun achat a effectuer (tous les articles sont deja en stock)');
            }

            // Verifier le solde
            $soldeActuel = $this->stockRepo->getSoldeArgent();
            if ($soldeActuel < $totalCout) {
                throw new Exception(sprintf(
                    'Solde insuffisant. Disponible: %.2f MAD, Requis: %.2f MAD',
                    $soldeActuel,
                    $totalCout
                ));
            }

            // Executer tous les achats
            $nbAchats = 0;
            foreach ($achatsAPreparer as $achat) {
                // Debiter l'argent
                $this->stockRepo->debitArgent($achat['montant_total']);
                
                // Ajouter au stock
                $this->stockRepo->addStock($achat['article_id'], $achat['quantite']);

                // Enregistrer dans bn_achats
                $this->achatsRepo->create(
                    (int) $achat['ville_id'],
                    (int) $achat['article_id'],
                    $achat['quantite'],
                    $achat['prix_unitaire'],
                    $fraisAchat,
                    $achat['montant_total'],
                    date('Y-m-d')
                );

                // Mettre à jour les besoins (quantite restante et status)
                $this->besoinsRepo->reduireParAchat(
                    (int) $achat['ville_id'],
                    (int) $achat['article_id'],
                    $achat['quantite']
                );
                
                $nbAchats++;
            }

            $this->pdo->commit();

            echo json_encode([
                'success' => true,
                'message' => sprintf(
                    '%d achat(s) effectue(s) avec succes pour un montant total de %.2f MAD',
                    $nbAchats,
                    $totalCout
                ),
                'data' => [
                    'nb_achats' => $nbAchats,
                    'montant_total' => $totalCout,
                    'solde_apres' => $soldeActuel - $totalCout
                ]
            ]);

        } catch (Exception $e) {
            $this->pdo->rollBack();
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }
}
