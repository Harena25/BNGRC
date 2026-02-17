================================================================================
                    TODO - BNGRC - Projet S3 Final [J]
                Gestion des secours et distributions d'aide
================================================================================

Date: 16 fevrier 2026
Responsable: J
Duree projet: 26h (16-17 fevrier 2026)

================================================================================
[OK] 1. Dispatch Automatique par Ordre Chronologique
================================================================================

Description:
    Distribution automatique des dons disponibles en stock vers les besoins
    par ordre chronologique (date_besoin ASC, puis created_at ASC).

[OK]Fonctionnalite:
    [OK] Recuperer tous les articles en stock (quantite > 0)
    [OK] Pour chaque article, trouver les besoins correspondants
    [OK] Trier par date_besoin ASC, created_at ASC
    [OK] Distribuer jusqu'a epuisement du stock ou satisfaction des besoins
    [OK] Mettre a jour bn_besoin.quantite et bn_besoin.status_id
    [OK] Enregistrer dans bn_distribution

[OK]Backend :
    - [OK]Service: app/services/AutoDistributor.php
    - [OK]Controller: app/controllers/DistributionController.php => autoDistribution()
    - [OK]Route: GET /distributions/auto

[OK]Frontend (FAIT):
    - [OK]Bouton "Lancer la distribution automatique" dans dashboard
    - [OK]Page resultat avec recapitulatif des distributions effectuees


================================================================================
[OK] 2. Systeme d'Achats avec Page de Simulation
================================================================================

Description:
    Permet d'acheter des articles en nature/materiaux en utilisant les dons 
    en argent. Avec frais d'achat configurable et page de simulation avant 
    validation.
    
    Formule: Cout Total = Quantite x Prix Unitaire x (1 + Frais%)
    Exemple: Acheter 100 avec 10% de frais => 100 x prix x 1.10

Regles metier:
    1. Verifier si l'article existe deja en stock -> Proposition offrant le choix
       Message: Dans simulation globale, choix entre "Distribuer" ou "Acheter"
    
    2. Verifier solde argent >= cout total
       Message: "Solde insuffisant. Disponible: X MAD, Requis: Y MAD"
    
    3. Validation quantite: 0 < qte <= quantite restante du besoin
       Message: "Quantite invalide"
    
    4. Articles achetables: categories 1,2,4,5 (Nourriture, Materiaux, Hygiene, Vetements)
       Exception: categorie 3 (Argent) NON achetable

Workflow:
    Page Besoins Restants -> [Bouton Acheter] -> Page Formulaire Achat
    -> [Simuler] calcule et affiche -> [Valider] execute l'achat
    -> Debite argent + Ajoute au stock
    
    OU
    
    Page Besoins Restants -> [Simuler achats] -> Simulation Globale
    -> Affiche tous les achats possibles avec choix stock/achat
    -> [Valider tous les achats] execute tous les achats en une fois

--------------------------------------------------------------------------------
2.1. BASE DE DONNEES
--------------------------------------------------------------------------------

Fichiers:
    [A FAIRE] database/migration_achat.sql (table preparee mais pas encore executee)

SQL:
    CREATE TABLE bn_achat (
        id INT AUTO_INCREMENT PRIMARY KEY,
        besoin_id INT NOT NULL COMMENT 'Besoin concerne',
        article_id INT NOT NULL COMMENT 'Article achete',
        quantite_achetee INT NOT NULL COMMENT 'Quantite achetee',
        prix_unitaire DECIMAL(10,2) NOT NULL COMMENT 'Prix au moment achat',
        frais_pourcent DECIMAL(5,2) NOT NULL COMMENT '% de frais applique',
        montant_total DECIMAL(12,2) NOT NULL COMMENT 'Cout total TTC',
        date_achat DATE NOT NULL COMMENT 'Date de l achat',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (besoin_id) REFERENCES bn_besoin(id),
        FOREIGN KEY (article_id) REFERENCES bn_article(id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--------------------------------------------------------------------------------
2.2. CONFIGURATION
--------------------------------------------------------------------------------

Fichiers:
    [OK] app/config.php (modifier)

Code:
    define('FRAIS_ACHAT_POURCENT', 10); // 10% de frais par defaut
    Note: Le taux est aussi configurable par l'utilisateur lors de la simulation

--------------------------------------------------------------------------------
2.3. BACKEND - REPOSITORIES
--------------------------------------------------------------------------------

Fichiers:
    [A FAIRE] app/repositories/AchatRepository.php (nouveau - pour historique)
    [OK] app/repositories/StockRepository.php (modifier)
    [OK] app/repositories/BesoinsRepository.php (ajoute getBesoinsRestants)

--- AchatRepository.php ---
    Methodes:
        [ ] create($data)
            INSERT INTO bn_achat
            Return: achat_id
        
        [ ] getAll($filters = [])
            SELECT avec JOIN bn_besoin, bn_article, bn_ville
            Filtres: ville_id, date_debut, date_fin
            Return: array of achats
        
        [ ] getByVille($ville_id)
            Return: achats d'une ville
        
        [ ] getTotalAchats()
            SUM(montant_total)
            Return: float

--- StockRepository.php (MODIFIER) ---
    Methodes a ajouter:
        [OK] hasStock($article_id)
            SELECT quantite_stock WHERE article_id = ?
            Return: bool (quantite > 0)
        
        [OK] getSoldeArgent()
            SELECT quantite_stock WHERE article_id = 8
            Return: int (quantite d'argent en MAD)
        
        [OK] debitArgent($montant)
            UPDATE bn_stock SET quantite_stock = quantite_stock - ?
            WHERE article_id = 8
            Return: bool
        
        [OK] addStock($article_id, $quantite)
            INSERT ou UPDATE bn_stock
            Return: bool

--------------------------------------------------------------------------------
2.4. BACKEND - CONTROLLER
--------------------------------------------------------------------------------

Fichiers:
    [OK] app/controllers/AchatController.php (nouveau)

Methodes:
    [OK] form($besoin_id)
        GET /achats/form/:besoin_id
        - Recuperer infos besoin (ville, article, quantite restante, prix)
        - Recuperer solde argent
        - Recuperer frais d'achat config
        - Render: app/views/achats/form.php
    
    [OK] simuler()
        POST /achats/simuler
        Input: besoin_id, quantite
        Process:
            1. Verifier quantite valide
            2. Verifier article pas en stock -> hasStock()
            3. Calculer montant = qte x prix x (1 + frais%)
            4. Verifier solde argent >= montant
            5. Return JSON:
                {
                    "success": true,
                    "data": {
                        "quantite": 30,
                        "prix_unitaire": 35.00,
                        "frais_pourcent": 10,
                        "sous_total": 1050.00,
                        "frais": 105.00,
                        "montant_total": 1155.00,
                        "solde_actuel": 10000.00,
                        "solde_apres": 8845.00
                    }
                }
    
    [OK] valider()
        POST /achats/valider
        Input: besoin_id, quantite, frais_pourcent
        Process:
            1. Re-verifier toutes les conditions (securite)
            2. Debiter argent -> debitArgent(montant_total)
            3. Ajouter au stock -> addStock(article_id, quantite)
            4. Enregistrer achat -> AchatRepository::create() [A FAIRE]
            5. Return JSON success/error
    
    [OK] simulerGlobal()
        GET /achats/simuler-global
        - Affiche tous les besoins restants
        - Separe articles en stock vs non en stock
        - Calcule cout total avec frais configurables
        - Render: app/views/achats/simuler_global.php
    
    [OK] validerGlobal()
        POST /achats/valider-global
        - Valide tous les achats d'articles non en stock
        - Transaction unique pour securite
        - Return JSON success/error
    
    [A FAIRE] liste()
        GET /achats/liste
        Query params: ville_id (optionnel)
        - Recuperer achats filtres
        - Recuperer liste villes pour filtre
        - Calculer total general
        - Render: app/views/achats/liste.php

--------------------------------------------------------------------------------
2.5. BACKEND - ROUTES
--------------------------------------------------------------------------------

Fichiers:
    [OK] app/routes.php (modifier)

Code:
    [OK] // Achats
    Flight::route('GET /achats/form/@id', [AchatController::class, 'form']);
    Flight::route('POST /achats/simuler', [AchatController::class, 'simuler']);
    Flight::route('POST /achats/valider', [AchatController::class, 'valider']);
    Flight::route('GET /achats/simuler-global', [AchatController::class, 'simulerGlobal']);
    Flight::route('POST /achats/valider-global', [AchatController::class, 'validerGlobal']);
    
    [A FAIRE] Flight::route('GET /achats/liste', [AchatController::class, 'liste']);

--------------------------------------------------------------------------------
2.6. FRONTEND - VUES
--------------------------------------------------------------------------------

--- Page 1: Formulaire Achat ---
Fichiers:
    [OK] app/views/achats/form.php

Contenu:
    Header:
        Titre: "Achat d'article"
        Sous-titre: Besoin pour [Ville] - [Article]
    
    Section Informations:
        - Ville: [nom_ville]
        - Article: [nom_article]
        - Quantite restante: [quantite] unites
        - Prix unitaire: [prix] MAD
        - Frais d'achat: [frais]%
        - Solde argent disponible: [solde] MAD
    
    Formulaire:
        Input: Quantite a acheter (type=number, min=1, max=quantite_restante)
        
        Bouton [Simuler] (btn-warning)
            -> Appel Ajax simuler()
            -> Affiche zone simulation
        
        Bouton [Valider l'achat] (btn-success, disabled jusqu'a simulation)
            -> Appel Ajax valider()
            -> Redirect vers liste achats
    
    Zone Simulation (masquee par defaut):
        Tableau recapitulatif:
            Sous-total:     [qte x prix] MAD
            Frais [%]:      [montant frais] MAD
            ------------------------------
            TOTAL:          [total] MAD
            
            Solde actuel:   [solde] MAD
            Solde apres:    [solde - total] MAD

--- Page 2: Liste des Besoins Restants ---
Fichiers:
    [OK] app/views/besoins/besoin_restant.php
    - Affiche tous les besoins avec statut != satisfait
    - Bouton "Acheter" par ligne
    - Bouton "Simuler achats" global

--- Page 3: Simulation Globale ---
Fichiers:
    [OK] app/views/achats/simuler_global.php
    - Tableau jaune pour articles en stock (choix Distribuer/Acheter)
    - Tableau blanc pour articles sans stock (achat obligatoire)
    - Frais configurables avec recalcul
    - Bouton "Valider tous les achats"
    - Bouton "Distribuer tout le stock"

--- Page 4: Liste des Achats (Historique) ---
Fichiers:
    [A FAIRE] app/views/achats/liste.php

Contenu:
    Header:
        Titre: "Liste des achats effectues"
        
    Filtres:
        Select Ville: [Toutes] + dropdown villes
        -> onChange: recharger page avec ?ville_id=X
    
    Tableau:
        Colonnes:
            Date | Ville | Article | Categorie | Qte | Prix Unit. | Frais% | Total
        
        Exemple ligne:
            16/02/26 | Ain Tala | Riz 50kg | Nourriture | 30 | 35.00 | 10% | 1155.00
    
    Footer tableau:
        TOTAL GENERAL: [SUM(montant_total)] MAD

--------------------------------------------------------------------------------
2.7. FRONTEND - JAVASCRIPT
--------------------------------------------------------------------------------

Fichiers:
    [OK] JavaScript inline dans les vues (form.php et simuler_global.php)

Fonctions:
    function simulerAchat(besoin_id, quantite) {
        // POST /achats/simuler
        // Afficher resultat dans #zoneSimulation
        // Activer bouton [Valider]
    }
    
    function validerAchat(besoin_id, quantite) {
        // Confirm dialog
        // POST /achats/valider
        // Success: redirect /achats/liste
        // Error: alert message
    }
    
    function filtrerListe(ville_id) {
        // Reload page: /achats/liste?ville_id=X
    }

--------------------------------------------------------------------------------
2.8. INTEGRATION AVEC NAVIGATION
--------------------------------------------------------------------------------

Fichiers:
    [OK] app/views/modele.php (modifier)

Modification:
    [OK] Ajouter lien "Besoins restants" dans sidebar
    -> Lien: /needs/restants
    
Fichiers:
    [OK] app/views/besoins/besoin_restant.php (nouveau)
    [OK] Boutons [Acheter] par ligne -> /achats/form/:id
    [OK] Bouton [Simuler achats] global -> /achats/simuler-global

================================================================================
CHECKLIST COMPLETE
================================================================================

Base de donnees:
    [A FAIRE] Creer migration_achat.sql
    [A FAIRE] Executer migration (table bn_achat pour historique)

Backend:
    [OK] Modifier config.php (FRAIS_ACHAT_POURCENT)
    [A FAIRE] Creer AchatRepository.php (pour historique achats)
    [OK] Modifier StockRepository.php (4 methodes)
    [OK] Creer BesoinsRepository.php (getBesoinsRestants)
    [OK] Creer AchatController.php (5 methodes: form, simuler, valider, simulerGlobal, validerGlobal)
    [OK] Modifier routes.php (5 routes achats)

Frontend:
    [OK] Creer besoins/besoin_restant.php (page besoins restants)
    [OK] Creer achats/form.php (formulaire achat individuel)
    [OK] Creer achats/simuler_global.php (simulation globale avec choix stock/achat)
    [A FAIRE] Creer achats/liste.php (historique des achats)
    [OK] JavaScript inline pour Ajax (simulation et validation)
    [OK] Modifier modele.php (lien sidebar)

Fonctionnalites implementees:
    [OK] Page besoins restants filtree (status != satisfait)
    [OK] Achat individuel avec simulation Ajax
    [OK] Frais d'achat configurables par utilisateur (input dynamique)
    [OK] Simulation globale de tous les achats possibles
    [OK] Separation articles en stock / non en stock
    [OK] Choix distribuer ou acheter pour articles en stock
    [OK] Validation globale de tous les achats en une transaction
    [OK] Bouton "Distribuer tout le stock" dans simulation globale
    [OK] Verification solde suffisant avec alertes
    [OK] Calcul automatique frais et totaux

Tests:
    [A TESTER] Test simulation avec stock existant -> Proposition choix
    [A TESTER] Test simulation avec solde insuffisant -> ERREUR
    [A TESTER] Test simulation valide -> OK
    [A TESTER] Test validation achat individuel -> OK
    [A TESTER] Test validation globale -> OK
    [A TESTER] Test integration complete workflow -> OK

================================================================================
[OK] 3. Fonctionnalites Avancees Implementees
================================================================================

Description:
    Fonctionnalites supplementaires ajoutees pour ameliorer l'experience
    utilisateur et l'efficacite du systeme d'achats.

--------------------------------------------------------------------------------
3.1. PAGE BESOINS RESTANTS
--------------------------------------------------------------------------------

Fichiers:
    [OK] app/views/besoins/besoin_restant.php
    [OK] app/repositories/BesoinsRepository.php::getBesoinsRestants()
    [OK] app/routes.php (GET /needs/restants)

Fonctionnalite:
    - Affiche uniquement les besoins non satisfaits (status_id != 3)
    - Tri par ordre chronologique (date_besoin ASC, created_at ASC)
    - Cartes statistiques: Total besoins, Total valeur, Nombre villes
    - Bouton "Acheter" par besoin -> formulaire achat individuel
    - Bouton global "Simuler achats" -> simulation globale
    - Integration sidebar navigation

--------------------------------------------------------------------------------
3.2. FRAIS D'ACHAT CONFIGURABLES PAR UTILISATEUR
--------------------------------------------------------------------------------

Description:
    Au lieu d'un taux fixe, l'utilisateur peut specifier le pourcentage
    de frais d'achat lors de la simulation.

Implementation:
    [OK] Input frais_pourcent dans form.php (validation 0-100%)
    [OK] Input frais dans simuler_global.php avec bouton "Recalculer"
    [OK] Validation backend du taux dans simuler() et valider()
    [OK] Valeur par defaut depuis FRAIS_ACHAT_POURCENT (10%)
    [OK] Recalcul dynamique Ajax dans simulation globale

Avantages:
    - Flexibilite selon le contexte (urgence, fournisseur, etc.)
    - Transparence sur les couts reels
    - Adaptation aux situations exceptionnelles

--------------------------------------------------------------------------------
3.3. CHOIX DISTRIBUTION VS ACHAT (Articles en Stock)
--------------------------------------------------------------------------------

Description:
    Quand un article existe deja en stock, le systeme propose un choix
    entre distribuer le stock existant (gratuit) ou acheter de nouveaux
    articles (avec frais).

Implementation:
    [OK] Separation dans simulerGlobal(): $articlesEnStock vs $achats
    [OK] Tableau jaune d'alerte pour articles en stock
    [OK] Deux boutons par article en stock:
        - [Distribuer] -> /autoDistribution?mode=simulate
        - [Acheter] -> /achats/form/:id
    [OK] Bouton global "Distribuer tout le stock"
    [OK] Tableau separe pour articles sans stock (achat obligatoire)

Logique metier:
    1. Si stock existe && besoin existe -> Proposer choix
    2. Distribuer = Gratuit, utilise stock existant
    3. Acheter = Coute argent avec frais, ajoute au stock
    4. Articles sans stock -> Achat obligatoire

Avantages:
    - Optimise l'utilisation des ressources
    - Evite achats inutiles
    - Flexibilite selon strategie (renouveler stock vs ecouler existant)

--------------------------------------------------------------------------------
3.4. VALIDATION GLOBALE DES ACHATS
--------------------------------------------------------------------------------

Description:
    Permet de valider tous les achats necessaires en une seule operation
    plutot que de valider chaque achat individuellement.

Implementation:
    [OK] Methode validerGlobal() dans AchatController
    [OK] Route POST /achats/valider-global
    [OK] Bouton "Valider tous les achats" dans simuler_global.php
    [OK] Transaction unique pour tous les achats
    [OK] Confirmation utilisateur avant execution
    [OK] Message recapitulatif: nombre achats + montant total

Fonctionnalite:
    - Recupere tous besoins restants (exclud satisfaits)
    - Ignore articles deja en stock (doivent etre distribues)
    - Calcule cout total avec frais
    - Verifie solde suffisant
    - Execute tous achats en une transaction
    - Debite argent et met a jour stock pour chaque article
    - Rollback complet en cas d'erreur

Avantages:
    - Gain de temps considerable
    - Transaction atomique = securite
    - Evite erreurs de manipulation
    - Vue d'ensemble avant engagement

--------------------------------------------------------------------------------
3.5. INTERFACE SIMULATION GLOBALE AMELIOREE
--------------------------------------------------------------------------------

Fichiers:
    [OK] app/views/achats/simuler_global.php

Fonctionnalite:
    - 3 cartes financieres: Solde disponible, Cout total, Solde apres
    - Alerte visuelle selon suffisance du solde (vert/rouge)
    - Configuration frais avec recalcul automatique (GET)
    - Zone articles en stock (tableau jaune) avec 2 actions par ligne
    - Zone articles sans stock avec calcul detaille
    - Ligne totale recapitulative
    - Notes explicatives pour guider l'utilisateur
    - Boutons d'action contextuels
    - Indicateurs visuels (icones Bootstrap)

Elements visuels:
    - Tableau jaune: Articles en stock (warning)
    - Tableau blanc: Articles a acheter
    - Lignes rouges: Achats impossibles (solde insuffisant)
    - Cartes colorees: Success/Warning/Danger selon contexte
    - Icones intuitives: wallet, cart, piggy-bank, box, lightning, etc.

================================================================================
[OK] V-3. Mode de Distribution par Quantite (Plus Petit d'abord)
================================================================================

Description:
    Distribution automatique privilegiant les besoins de plus petite quantite
    pour maximiser le nombre de beneficiaires satisfaits.

Principe:
    Traiter les besoins du plus petit au plus grand (ORDER BY quantite ASC)
    au lieu de l'ordre chronologique par defaut.

Implementation:
    [OK] Service: AutoDistributor
        - Ajout propriete $sortMode ('date' ou 'quantite')
        - Modification constructeur: __construct(PDO $pdo, string $sortMode = 'date')
        - Modification getPendingBesoins():
            * si sortMode = 'date': ORDER BY date_besoin ASC, created_at ASC
            * si sortMode = 'quantite': ORDER BY quantite ASC, created_at ASC
    
    [OK] Controller: DistributionController::autoDistribution()
        - Recuperer parametre sortMode depuis $_GET['sortMode'] (default: 'date')
        - Passer sortMode au constructeur AutoDistributor
        - Stocker sortMode en session pour affichage resultat
    
    [OK] Views:
        - dashboard/index.php: 2 boutons (Par Date / Par Quantite)
        - distribution/list.php: 2 boutons (Par Date / Par Quantite)
        - distribution/result.php: Badge affichant le mode utilise

Routes:
    GET /autoDistribution?mode=simulate&sortMode=date
    GET /autoDistribution?mode=simulate&sortMode=quantite
    GET /autoDistribution?mode=execute&sortMode=date
    GET /autoDistribution?mode=execute&sortMode=quantite

Tests:
    [A TESTER] Distribution par date: besoins tries chronologiquement
    [A TESTER] Distribution par quantite: petits besoins traites en priorite
    [A TESTER] Conservation du mode lors validation (simulate -> execute)

================================================================================
[ ] V-4. Mode de Distribution Proportionnelle
================================================================================

Description:
    Distribution equitable ou chaque beneficiaire recoit une part proportionnelle
    a sa demande, en fonction du stock disponible.

Principe:
    Pour chaque article en stock:
    1. Recuperer TOUS les besoins non satisfaits pour cet article
    2. Calculer le total des demandes
    3. Distribuer proportionnellement: (Demande / Total demandes) × Stock disponible
    4. Arrondir a l'entier inferieur (floor) pour ne pas depasser le stock
    5. Creer les distributions et mettre a jour les besoins

Formule:
    Quantite distribuee = floor((Quantite demandee / Total demandes) × Stock disponible)

Exemple:
    Stock disponible: 5 unites
    Demandes: Ville A (1), Ville B (3), Ville C (5)
    Total demandes: 9
    
    Calcul:
    - Ville A: floor((1/9) × 5) = floor(0.55) = 0
    - Ville B: floor((3/9) × 5) = floor(1.66) = 1
    - Ville C: floor((5/9) × 5) = floor(2.77) = 2
    Total distribue: 3 unites (reste 2 en stock)

Regles:
    - Arrondi inferieur (floor) pour garantir stock suffisant
    - Traiter article par article (grouper besoins par article_id)
    - Ignorer besoins deja satisfaits (status_id = 3)
    - Mettre a jour status_id selon quantite restante:
        * status_id = 3 si quantite devient 0 (satisfait)
        * status_id = 2 si quantite > 0 (partiel)

Implementation:
    [ ] Service: AutoDistributor
        - Ajouter methode runProportional(): array
            1. Recuperer tous les stocks (article_id, quantite_stock)
            2. Pour chaque stock avec quantite > 0:
                a. SELECT besoins WHERE article_id = X AND status_id <> 3
                b. Calculer total demandes: SUM(quantite)
                c. Pour chaque besoin:
                    - ratio = quantite_besoin / total_demandes
                    - qte_dist = floor(ratio × stock_disponible)
                    - Si qte_dist > 0:
                        * INSERT bn_distribution
                        * UPDATE bn_besoin SET quantite = quantite - qte_dist
                        * UPDATE bn_stock SET quantite_stock = quantite_stock - qte_dist
                        * UPDATE status_id selon quantite restante
            3. Logger les operations
            4. Return log
        
        - Ajouter methode simulateProportional(): array
            1. Meme logique que runProportional()
            2. Travailler sur copie memoire (pas de modifications DB)
            3. Return log simulation
        
        - Modifier run() et simulate():
            if ($this->sortMode === 'proportionnelle') {
                return $this->runProportional(); // ou simulateProportional()
            } else {
                // logique existante (date ou quantite)
            }
    
    [ ] Controller: DistributionController::autoDistribution()
        - Supporter sortMode = 'proportionnelle'
        - Passer a AutoDistributor normalement
        - Gestion identique aux autres modes
    
    [ ] Views:
        - dashboard/index.php: Ajouter bouton "Par Proportionnalite"
        - distribution/list.php: Ajouter bouton "Par Proportionnalite"
        - distribution/result.php: Badge "Distribution Proportionnelle"

Routes:
    GET /autoDistribution?mode=simulate&sortMode=proportionnelle
    GET /autoDistribution?mode=execute&sortMode=proportionnelle

Code SQL cle:
    -- Recuperer besoins par article
    SELECT id, ville_id, quantite 
    FROM bn_besoin 
    WHERE article_id = ? AND status_id <> 3
    
    -- Creer distribution
    INSERT INTO bn_distribution (besoin_id, article_id, quantite_distribuee, date_distribution)
    VALUES (?, ?, ?, CURDATE())
    
    -- Reduire besoin
    UPDATE bn_besoin 
    SET quantite = quantite - ?, 
        status_id = CASE WHEN (quantite - ?) = 0 THEN 3 ELSE 2 END
    WHERE id = ?
    
    -- Reduire stock
    UPDATE bn_stock 
    SET quantite_stock = quantite_stock - ?
    WHERE article_id = ?

Gestion des arrondis:
    - Utiliser floor() pour arrondir a l'entier inferieur
    - Exemple PHP: $qte_dist = floor($ratio * $stock);
    - Garantit: SUM(distributions) <= stock_disponible

Avantages:
    - Equite: tous les beneficiaires recoivent une part
    - Evite la frustration du "premier arrivé, premier servi"
    - Optimise satisfaction globale
    - Transparent et comprehensible

Cas limites:
    - Si ratio trop faible: certains recoivent 0 (arrondi inferieur)
    - Stock restant apres distribution (du aux arrondis)
    - Gerer besoins deja partiellement satisfaits

Tests:
    [ ] Cas 1: Stock suffisant pour tous (chacun recoit sa demande complete)
    [ ] Cas 2: Stock insuffisant (distribution proportionnelle)
    [ ] Cas 3: Demandes tres inegales (1 vs 100)
    [ ] Cas 4: Arrondis inferieurs donnant 0 pour petites demandes
    [ ] Cas 5: Verification stock final (ne doit pas etre negatif)
    [ ] Cas 6: Multiple articles simultanement

Logs attendus:
    "Distribution proportionnelle pour Article X (stock: 5)"
    "Total demandes: 9 unites"
    "Ville A: 0 distribue (1 demande, ratio 11%)"
    "Ville B: 1 distribue (3 demande, ratio 33%)"
    "Ville C: 2 distribue (5 demande, ratio 55%)"
    "Stock restant: 2 unites"

================================================================================
FIN DU TODO
================================================================================ 