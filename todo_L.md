2. Saisie des dons [L]

# base

- corriger le schema: dons(id, article_id, quantite, date)
- verifier la table article avec prix_unitaire
- ajouter FK dons.article_id -> article.id

# fonction

- creer le formulaire de saisie de don (selection article, quantite, date)
- liste deroulante des articles existants
- valider quantite > 0 et date
- enregistrer le don dans la table dons
- afficher message de succes/erreur

# integration

- creer route GET /dons/create (affiche formulaire)
- creer route POST /dons/store (enregistre le don)
- creer controller DonController avec methodes create() et store()
- recuperer la liste des articles pour le select

# design

- styliser le formulaire (Bootstrap)
- afficher les messages flash
- bouton retour vers liste des dons
