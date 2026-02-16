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
5. Écrire des tests unitaires pour les méthodes du contrôleur et du service.

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

## Routes
1. Configurer les routes backend dans app/routes.php :
   - POST /needs
   - GET /needs
   - GET /cities
   - GET /articles
2. Ajouter les routes frontend :
   - /needs pour le formulaire.
   - /needs/list pour afficher la liste des besoins.

