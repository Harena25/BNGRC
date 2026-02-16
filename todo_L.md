2. Saisie des dons [L]

# generalites : 
    - [x] update et delete
     
-- Dons --
# base
- [x] utilisation des tables dons, articles et categories

# fonction

- [x] creer le formulaire de saisie de don (selection article, quantite, date)
- [x] liste deroulante des articles existants
- [x] valider quantite > 0 et date
- [x] enregistrer le don dans la table dons
- [x] afficher message de succes/erreur

# integration

- [x] creer route GET /dons/create (affiche formulaire)
- [x] creer route POST /dons (enregistre le don)
- [x] creer controller DonsController avec methodes create() et showForm()
- [x] recuperer la liste des articles pour le select

# design

- [] styliser le formulaire (Bootstrap)
- [x] afficher les messages flash
- [x] bouton retour vers liste des dons

-- Articles --
# base
- [x] utilisation des tables articles et categories

# fonction

- [x] creer le formulaire de saisie de article (selection article, quantite, date)
- [x] liste deroulante des articles existants
- [x] valider quantite > 0 et date
- [x] enregistrer le article dans la table articles
- [x] afficher message de succes/erreur

# integration

- [x] creer route GET /articles/create (affiche formulaire)
- [x] creer route POST /articles (enregistre le article)
- [x] creer controller articlesController avec methodes create() et showForm()
- [x] recuperer la liste des articles pour le select

# design

- [x] styliser le formulaire (Bootstrap)
- [x] afficher les messages flash
- [x] bouton retour vers liste des articles

--- version 2 ---

- Page de récapitulation (actualisable en Ajax) [L]

- [x] Créer vue SQL `v_recap_ville` (totaux besoins, satisfaits, restants par ville + global)
- [x] Ajouter `RecapController::index()` et `RecapController::data()` (JSON pour AJAX)
- [x] Ajouter routes GET `/recap` et `/recap/data`
- [x] Créer view `views/dashboard/recap.php` (tableau récapitulatif + filtre ville)
- [x] Implémenter le bouton "Actualiser" en AJAX qui recharge les données
- [x] Ajouter filtrage par ville (récap et liste des achats)
- [x] Styliser la page avec Bootstrap et afficher messages flash/erreurs
- [x] Mettre à jour `todo` avec mode d'emploi et notes d'implémentation