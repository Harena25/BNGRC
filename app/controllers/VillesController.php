<?php

require_once __DIR__ . '/../repositories/VillesRepository.php';

class VillesController
{
    private VillesRepository $repo;

    public function __construct(PDO $pdo)
    {
        $this->repo = new VillesRepository($pdo);
    }

    public function index()
    {
        $villes = $this->repo->getAll();
        $pagename = 'villes/liste.php';
        Flight::render('modele', [
            'villes' => $villes,
            'pagename' => $pagename,
        ]);
    }
}
