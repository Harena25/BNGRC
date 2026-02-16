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

- [ ] Créer vue SQL `v_recap_ville` (totaux besoins, satisfaits, restants par ville + global)
- [ ] Ajouter `RecapController::index()` et `RecapController::data()` (JSON pour AJAX)
- [ ] Ajouter routes GET `/recap` et `/recap/data`
- [ ] Créer view `views/dashboard/recap.php` (tableau récapitulatif + filtre ville)
- [ ] Implémenter le bouton "Actualiser" en AJAX qui recharge les données
- [ ] Lier les pages de simulation/validation existantes aux données récapitulatives
- [ ] Ajouter filtrage par ville (récap et liste des achats)
- [ ] Rendre `purchase_fee` configurable dans `config.php` (utilisé pour calcul prix total)
- [ ] Rendre la liste des achats filtrable par ville (interface + requêtes)
- [ ] Ajouter tests unitaires pour vérifier les calculs (totaux, pourcentages)
- [ ] Styliser la page avec Bootstrap et afficher messages flash/erreurs
- [ ] Mettre à jour `README.md` et `todo` avec mode d'emploi et notes d'implémentation