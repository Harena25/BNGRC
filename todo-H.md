# Taches pour la saisie des besoins par ville

## Backend
1. Intégrer la table besoin pour stocker les données.
2. Créer des endpoints API :
    - POST /needs pour ajouter des besoins.
    - GET /needs pour récupérer les besoins.
    - GET /cities pour les villes.
    - GET /articles pour les articles.
3. Implémenter un contrôleur pour la logique métier.
4. Ajouter une couche de service pour les opérations de base de données.
5. Créer un repository pour les besoins afin de gérer les opérations de base de données.
6. Écrire des tests unitaires pour les méthodes du contrôleur et du service.

## Frontend
1. Créer un formulaire pour la saisie des besoins avec les champs :
    - Ville
    - Article
    - Quantité
    - Date
2. Utiliser Bootstrap pour un design réactif.
3. Remplir dynamiquement les listes déroulantes (villes et articles) via l'API.
4. Ajouter une validation côté client pour les champs requis et les formats valides.
5. Connecter le formulaire à l'API backend.
6. Tester l'interface utilisateur pour l'ergonomie et la réactivité.


- Les dons en argent peuvent acheter les besoins en nature et matériaux selon les prix unitaires
- Frais d'achat de x% configurable (ex: 100 + 10% = 110)
- Message d'erreur si l'achat existe dans les dons restants


## Nouvelle Tâche : Gestion des achats filtrables par ville

## Backend
1. Ajouter une table pour enregistrer les achats avec les colonnes nécessaires (ville, article, quantité, prix, etc.).
2. Créer des endpoints API :
    - POST /purchases pour ajouter un achat.
    - GET /purchases pour récupérer les achats filtrés par ville.
    - GET /cities pour les villes disponibles.
    - GET /articles pour les articles disponibles.
3. Implémenter un contrôleur pour gérer la logique métier des achats.
4. Ajouter une couche de service pour les opérations liées aux achats.
5. Créer un repository pour gérer les interactions avec la base de données pour les achats.
6. Écrire des tests unitaires pour les méthodes du contrôleur et du service.

## Frontend
1. Créer une interface utilisateur pour la saisie des achats avec les champs :
    - Ville
    - Article
    - Quantité
    - Prix
2. Utiliser Bootstrap pour un design réactif et attrayant.
3. Remplir dynamiquement les listes déroulantes (villes et articles) via l'API.
4. Ajouter une validation côté client pour les champs requis et les formats valides.
5. Créer une page pour afficher la liste des achats avec des options de filtrage par ville.
6. Tester l'interface utilisateur pour garantir une bonne expérience utilisateur.

## Routes
1. Configurer les routes backend dans app/routes.php :
    - POST /needs
    - GET /needs
    - GET /cities
    - GET /articles
2. Ajouter les routes frontend :
    - /needs pour le formulaire.
    - /needs/list pour afficher la liste des besoins.
    - /purchases pour le formulaire de saisie des achats.
    - /purchases/list pour afficher la liste des achats filtrables par ville.

