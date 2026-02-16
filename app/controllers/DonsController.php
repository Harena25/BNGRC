<?php
class DonsController
{

    public static function list()
    {
        $pdo = Flight::db();
        $repo = new DonsRepository($pdo);
        $dons = $repo->findAll();

        Flight::render('dons/liste', ['dons' => $dons]);
    }

    public static function showForm()
    {
        $pdo = Flight::db();
        $articlesRepo = new ArticlesRepository($pdo);
        $articles = $articlesRepo->findAll();

        Flight::render('dons/formulaire', ['articles' => $articles]);
    }

    public static function create()
    {
        $pdo = Flight::db();
        $repo = new DonsRepository($pdo);

        $data = [
            'article_id' => $_POST['article_id'] ?? 0,
            'quantite_donnee' => $_POST['quantite_donnee'] ?? 0,
            'date_don' => $_POST['date_don'] ?? date('Y-m-d')
        ];

        // Validation
        if ($data['article_id'] <= 0 || $data['quantite_donnee'] <= 0) {
            Flight::redirect('/dons/create?error=donnees_invalides');
            return;
        }

        $id = $repo->create($data);
        Flight::redirect('/dons?success=don_enregistre');
    }

    public static function update($id)
    {
        $pdo = Flight::db();
        $repo = new DonsRepository($pdo);

        $don = $repo->findById($id);
        if (!$don) {
            Flight::notFound();
            return;
        }

        $data = [
            'article_id' => $_POST['article_id'] ?? 0,
            'quantite_donnee' => $_POST['quantite_donnee'] ?? 0,
            'date_don' => $_POST['date_don'] ?? date('Y-m-d')
        ];

        // Validation
        if ($data['article_id'] <= 0 || $data['quantite_donnee'] <= 0) {
            Flight::redirect('/dons/' . $id . '/edit?error=donnees_invalides');
            return;
        }

        $repo->update($id, $data);
        Flight::redirect('/dons?success=don_modifie');
    }

    public static function editForm($id)
    {
        $pdo = Flight::db();
        $donsRepo = new DonsRepository($pdo);
        $don = $donsRepo->findById($id);

        if (!$don) {
            Flight::notFound();
            return;
        }

        $articlesRepo = new ArticlesRepository($pdo);
        $articles = $articlesRepo->findAll();

        Flight::render('dons/formulaire', ['don' => $don, 'articles' => $articles]);
    }

    public static function delete($id)
    {
        $pdo = Flight::db();
        $repo = new DonsRepository($pdo);
        $repo->deleteById($id);
        Flight::redirect('/dons?success=don_supprime');
    }
}
