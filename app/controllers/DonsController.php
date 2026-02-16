<?php
class DonsController
{

    public static function list()
    {
        $pdo = Flight::db();
        $repo = new DonsRepository($pdo);
        $dons = $repo->findAll();
        $pagename = "dons/liste.php";

        Flight::render('modele', ['dons' => $dons, 'pagename' => $pagename]);
    }

    public static function showForm()
    {
        $pdo = Flight::db();
        $articlesRepo = new ArticlesRepository($pdo);
        $articles = $articlesRepo->findAll();
        $pagename = "dons/formulaire.php";

        Flight::render('modele', ['articles' => $articles, 'pagename' => $pagename]);
    }

    public static function create()
    {
        $pdo = Flight::db();
        $repo = new DonsRepository($pdo);
        $stockRepo = new StockRepository($pdo);

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

        try {
            $pdo->beginTransaction();
            $repo->create($data);
            $stockRepo->upsertQuantity($data['article_id'], $data['quantite_donnee']);
            $pdo->commit();
            Flight::redirect('/dons?success=don_enregistre');
        } catch (Exception $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            $msg = $e->getMessage();
            error_log('Stock update failed: ' . $msg);
            Flight::redirect('/dons/create?error=stock_update_failed&msg=' . urlencode($msg));
        }
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
        $pagename = "dons/formulaire.php";

        Flight::render('modele', ['don' => $don, 'articles' => $articles, 'pagename' => $pagename]);
    }

    public static function delete($id)
    {
        $pdo = Flight::db();
        $repo = new DonsRepository($pdo);
        $repo->deleteById($id);
        Flight::redirect('/dons?success=don_supprime');
    }
}
