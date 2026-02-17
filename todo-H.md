# Taches pour la saisie des besoins par ville

## Backend
1. [OK] Intégrer la table besoin pour stocker les données.
2. [OK] Créer des endpoints API :
    - POST /needs pour ajouter des besoins.
    - GET /needs pour récupérer les besoins.
    - GET /cities pour les villes.
    - GET /articles pour les articles.
3. [OK] Implémenter un contrôleur pour la logique métier.
4. [OK] Ajouter une couche de service pour les opérations de base de données.
5. [OK] Créer un repository pour les besoins afin de gérer les opérations de base de données.
6. [test] Écrire des tests unitaires pour les méthodes du contrôleur et du service.

## Frontend
1. [OK] Créer un formulaire pour la saisie des besoins avec les champs :
    - Ville
    - Article
    - Quantité
    - Date
2. [OK] Utiliser Bootstrap pour un design réactif.
3. [OK] Remplir dynamiquement les listes déroulantes (villes et articles) via l'API.
4. [OK] Ajouter une validation côté client pour les champs requis et les formats valides.
5. [OK] Connecter le formulaire à l'API backend.
6. [OK] Tester l'interface utilisateur pour l'ergonomie et la réactivité.


## Fonctionnalités supplémentaires
- [OK] Les dons en argent peuvent acheter les besoins en nature et matériaux selon les prix unitaires
- [OK] Frais d'achat de x% configurable (ex: 100 + 10% = 110)
- [OK] Message d'erreur si l'achat dépasse les besoins restants
- [OK] Synchronisation achats ↔ besoins via reduireParAchat()
- [OK] Séparation affichage Stock Argent / Nature-Matériaux


## Nouvelle Tâche : Gestion des achats filtrables par ville

## Backend
1. [OK] Ajouter une table pour enregistrer les achats avec les colonnes nécessaires (ville, article, quantité, prix, etc.).
2. [OK] Créer des endpoints API :
    - POST /purchases pour ajouter un achat.
    - GET /purchases pour récupérer les achats filtrés par ville.
    - GET /cities pour les villes disponibles.
    - GET /articles pour les articles disponibles.
3. [OK] Implémenter un contrôleur pour gérer la logique métier des achats.
4. [OK] Ajouter une couche de service pour les opérations liées aux achats.
5. [OK] Créer un repository pour gérer les interactions avec la base de données pour les achats.
6. [test]Écrire des tests unitaires pour les méthodes du contrôleur et du service.

## Frontend
1. [OK] Créer une interface utilisateur pour la saisie des achats avec les champs :
    - Ville
    - Article
    - Quantité
    - Prix (calculé automatiquement avec frais)
2. [OK] Utiliser Bootstrap pour un design réactif et attrayant.
3. [OK] Remplir dynamiquement les listes déroulantes (villes et articles) via l'API.
4. [OK] Ajouter une validation côté client pour les champs requis et les formats valides.
5. [OK] Créer une page pour afficher la liste des achats avec des options de filtrage par ville.
6. [OK] Tester l'interface utilisateur pour garantir une bonne expérience utilisateur.

## Routes
1. [OK] Configurer les routes backend dans app/routes.php :
    - POST /needs
    - GET /needs
    - GET /cities
    - GET /articles
2. [OK] Ajouter les routes frontend :
    - /needs pour le formulaire.
    - /needs/list pour afficher la liste des besoins.
    - /purchases pour le formulaire de saisie des achats.
    - /purchases/list pour afficher la liste des achats filtrables par ville.
    - /purchases/articles pour l'API JSON des articles achetables par ville.

