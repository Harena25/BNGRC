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
[EN COURS] 2. Systeme d'Achats avec Page de Simulation
================================================================================

Description:
    Permet d'acheter des articles en nature/materiaux en utilisant les dons 
    en argent. Avec frais d'achat configurable et page de simulation avant 
    validation.
    
    Formule: Cout Total = Quantite x Prix Unitaire x (1 + Frais%)
    Exemple: Acheter 100 avec 10% de frais => 100 x prix x 1.10

Regles metier:
    1. Verifier si l'article existe deja en stock -> ERREUR si oui
       Message: "Cet article existe deja en stock. Utilisez d'abord les dons existants."
    
    2. Verifier solde argent >= cout total
       Message: "Solde insuffisant. Disponible: X MAD, Requis: Y MAD"
    
    3. Validation quantite: 0 < qte <= quantite restante du besoin
       Message: "Quantite invalide"
    
    4. Articles achetables: categories 1,2,4,5 (Nourriture, Materiaux, Hygiene, Vetements)
       Exception: categorie 3 (Argent) NON achetable

Workflow:
    Page Besoins -> [Bouton Acheter] -> Page Simulation 
    -> [Simuler] calcule et affiche -> [Valider] execute l'achat
    -> Debite argent + Ajoute au stock + Enregistre achat

--------------------------------------------------------------------------------
2.1. BASE DE DONNEES
--------------------------------------------------------------------------------

Fichiers:
    [OK] database/migration_achat.sql

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
    [ ] app/config.php (modifier)

Code:
    define('FRAIS_ACHAT_POURCENT', 10); // 10% de frais par defaut

--------------------------------------------------------------------------------
2.3. BACKEND - REPOSITORIES
--------------------------------------------------------------------------------

Fichiers:
    [ ] app/repositories/AchatRepository.php (nouveau)
    [ ] app/repositories/StockRepository.php (modifier)

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
        [ ] hasStock($article_id)
            SELECT quantite_stock WHERE article_id = ?
            Return: bool (quantite > 0)
        
        [ ] getSoldeArgent()
            SELECT quantite_stock WHERE article_id = 8
            Return: int (quantite d'argent en MAD)
        
        [ ] debitArgent($montant)
            UPDATE bn_stock SET quantite_stock = quantite_stock - ?
            WHERE article_id = 8
            Return: bool
        
        [ ] addStock($article_id, $quantite)
            INSERT ou UPDATE bn_stock
            Return: bool

--------------------------------------------------------------------------------
2.4. BACKEND - CONTROLLER
--------------------------------------------------------------------------------

Fichiers:
    [ ] app/controllers/AchatController.php (nouveau)

Methodes:
    [ ] form($besoin_id)
        GET /achats/form/:besoin_id
        - Recuperer infos besoin (ville, article, quantite restante, prix)
        - Recuperer solde argent
        - Recuperer frais d'achat config
        - Render: app/views/achats/form.php
    
    [ ] simuler()
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
    
    [ ] valider()
        POST /achats/valider
        Input: besoin_id, quantite
        Process:
            1. Re-verifier toutes les conditions (securite)
            2. Debiter argent -> debitArgent(montant_total)
            3. Ajouter au stock -> addStock(article_id, quantite)
            4. Enregistrer achat -> AchatRepository::create()
            5. Return JSON success/error
    
    [ ] liste()
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
    [ ] app/routes.php (modifier)

Code:
    // Achats
    Flight::route('GET /achats/form/@besoin_id', function($besoin_id) {
        $controller = new AchatController();
        $controller->form($besoin_id);
    });

    Flight::route('POST /achats/simuler', function() {
        $controller = new AchatController();
        $controller->simuler();
    });

    Flight::route('POST /achats/valider', function() {
        $controller = new AchatController();
        $controller->valider();
    });

    Flight::route('GET /achats/liste', function() {
        $controller = new AchatController();
        $controller->liste();
    });

--------------------------------------------------------------------------------
2.6. FRONTEND - VUES
--------------------------------------------------------------------------------

--- Page 1: Formulaire Achat ---
Fichiers:
    [ ] app/views/achats/form.php

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

--- Page 2: Liste des Achats ---
Fichiers:
    [ ] app/views/achats/liste.php

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
    [ ] public/assets/js/achats.js (nouveau)

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
2.8. INTEGRATION AVEC PAGE BESOINS
--------------------------------------------------------------------------------

Fichiers:
    [ ] app/views/besoins/liste.php (modifier)

Modification:
    Ajouter colonne "Actions" avec bouton:
        [Acheter]
        -> Visible seulement si:
            1. Status = Ouvert ou Partiellement satisfait (status_id IN (1,2))
            2. Categorie IN (1,2,4,5) - Pas argent
            3. Quantite restante > 0
        -> Lien: /achats/form/{besoin_id}

================================================================================
CHECKLIST COMPLETE
================================================================================

Base de donnees:
    [ ] Creer migration_achat.sql
    [ ] Executer migration

Backend:
    [ ] Modifier config.php (FRAIS_ACHAT_POURCENT)
    [ ] Creer AchatRepository.php
    [ ] Modifier StockRepository.php (4 methodes)
    [ ] Creer AchatController.php (4 methodes)
    [ ] Modifier routes.php (4 routes)

Frontend:
    [ ] Creer achats/form.php
    [ ] Creer achats/liste.php
    [ ] Creer achats.js
    [ ] Modifier besoins/liste.php (bouton Acheter)

Tests:
    [ ] Test simulation avec stock existant -> ERREUR
    [ ] Test simulation avec solde insuffisant -> ERREUR
    [ ] Test simulation valide -> OK
    [ ] Test validation achat -> OK
    [ ] Test liste achats par ville -> OK
    [ ] Test integration avec besoins -> OK

================================================================================
FIN DU TODO
================================================================================ 