<?php
class ArticlesController
{
    public static function list()
    {
        $pdo = Flight::db();
        $repo = new ArticlesRepository($pdo);
        $articles = $repo->findAll();
        $pagename = "articles/liste.php";

        Flight::render('modele', ['articles' => $articles, 'pagename' => $pagename]);
    }

    public static function showForm()
    {
        $pdo = Flight::db();
        $categorieRepo = new CategorieRepository($pdo);
        $categories = $categorieRepo->findAll();
        $pagename = "articles/formulaire.php";

        Flight::render('modele', ['categories' => $categories, 'pagename' => $pagename]);
    }

    public static function create()
    {
        $pdo = Flight::db();
        $repo = new ArticlesRepository($pdo);

        $data = [
            'libelle' => $_POST['libelle'] ?? '',
            'categorie_id' => $_POST['categorie_id'] ?? 0,
            'prix_unitaire' => $_POST['prix_unitaire'] ?? 0
        ];

        // Validation
        if (empty($data['libelle']) || $data['categorie_id'] <= 0 || $data['prix_unitaire'] <= 0) {
            Flight::redirect('/articles/create?error=donnees_invalides');
            return;
        }

        $id = $repo->create($data);
        Flight::redirect('/articles?success=article_cree');
    }

    public static function editForm($id)
    {
        $pdo = Flight::db();
        $repo = new ArticlesRepository($pdo);
        $article = $repo->findById($id);

        if (!$article) {
            Flight::notFound();
            return;
        }

        $categorieRepo = new CategorieRepository($pdo);
        $categories = $categorieRepo->findAll();
        $pagename = "articles/formulaire.php";

        Flight::render('modele', ['article' => $article, 'categories' => $categories, 'pagename' => $pagename]);
    }

    public static function update($id)
    {
        $pdo = Flight::db();
        $repo = new ArticlesRepository($pdo);

        $article = $repo->findById($id);
        if (!$article) {
            Flight::notFound();
            return;
        }

        $data = [
            'libelle' => $_POST['libelle'] ?? '',
            'categorie_id' => $_POST['categorie_id'] ?? 0,
            'prix_unitaire' => $_POST['prix_unitaire'] ?? 0
        ];

        // Validation
        if (empty($data['libelle']) || $data['categorie_id'] <= 0 || $data['prix_unitaire'] <= 0) {
            Flight::redirect('/articles/' . $id . '/edit?error=donnees_invalides');
            return;
        }

        $repo->update($id, $data);
        Flight::redirect('/articles?success=article_modifie');
    }

    public static function delete($id)
    {
        $pdo = Flight::db();
        $repo = new ArticlesRepository($pdo);
        $repo->deleteById($id);
        Flight::redirect('/articles?success=article_supprime');
    }
}
