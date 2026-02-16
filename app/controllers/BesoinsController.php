<?php

require_once __DIR__ . '/../repositories/BesoinsRepository.php';
require_once __DIR__ . '/../repositories/VillesRepository.php';
require_once __DIR__ . '/../repositories/ArticlesRepository.php';

class BesoinsController
{
	private BesoinsRepository $besoinsRepo;
	private VillesRepository $villesRepo;
	private ArticlesRepository $articlesRepo;

	public function __construct(PDO $pdo)
	{
		$this->besoinsRepo = new BesoinsRepository($pdo);
		$this->villesRepo = new VillesRepository($pdo);
		$this->articlesRepo = new ArticlesRepository($pdo);
	}

	public function index()
	{
		$villes   = $this->villesRepo->getAll();
		$articles = $this->articlesRepo->findAll();
		$success  = isset($_SESSION['flash_success']) ? $_SESSION['flash_success'] : null;
		unset($_SESSION['flash_success']);
		$pagename='besoins/formulaire.php';
		Flight::render('modele', [
			'villes'   => $villes,
			'articles' => $articles,
			'success'  => $success,
			'error'    => null,
			'old'      => [],
			'pagename' => $pagename,
		]);
	}

	public function listPage()
	{	
		$pagename = 'besoins/liste.php';
		$besoins = $this->besoinsRepo->getAll();
		Flight::render('modele', [
			'besoins' => $besoins,
			'pagename' => $pagename,
		]);
	}

	public function store()
	{
		$data = Flight::request()->data;
		$pagename = 'besoins/formulaire.php';
		$villeId    = (int) $data->ville_id;
		$articleId  = (int) $data->article_id;
		$quantite   = (int) $data->quantite;
		$dateBesoin = (string) $data->date_besoin;
		$statusId   = (int) ($data->status_id ?: 1);

		if ($villeId <= 0 || $articleId <= 0 || $quantite <= 0 || $dateBesoin === '' || $statusId <= 0) {
			$villes   = $this->villesRepo->getAll();
			$articles = $this->articlesRepo->findAll();
			Flight::render('modele', [
				'villes'   => $villes,
				'articles' => $articles,
				'error'    => 'Tous les champs sont obligatoires et doivent être valides.',
				'success'  => null,
				'old'      => [
					'ville_id'    => $villeId,
					'article_id'  => $articleId,
					'quantite'    => $quantite,
					'date_besoin' => $dateBesoin,
					'status_id'   => $statusId,
					
				],
				'pagename' => $pagename,
			]);
			return;
		}

		$this->besoinsRepo->create($villeId, $articleId, $quantite, $dateBesoin, $statusId);
		$_SESSION['flash_success'] = 'Besoin enregistré avec succès.';
		Flight::redirect('/needs');
	}
}

