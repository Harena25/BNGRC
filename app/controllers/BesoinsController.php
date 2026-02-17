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
		$villes = $this->villesRepo->getAll();
		$articles = $this->articlesRepo->findAll();
		$success = isset($_SESSION['flash_success']) ? $_SESSION['flash_success'] : null;
		unset($_SESSION['flash_success']);
		$pagename = 'besoins/formulaire.php';
		Flight::render('modele', [
			'villes' => $villes,
			'articles' => $articles,
			'success' => $success,
			'error' => null,
			'old' => [],
			'pagename' => $pagename,
		]);
	}

	public function listPage()
	{
		$pdo = Flight::db();

		// Récupérer les filtres
		$filtreRegionId = Flight::request()->query->region_id;
		$filtreVilleId = Flight::request()->query->ville_id;
		$filtreArticleId = Flight::request()->query->article_id;
		$filtreCategorieId = Flight::request()->query->categorie_id;
		$filtreStatusId = Flight::request()->query->status_id;
		$filtreDateMin = Flight::request()->query->date_min;
		$filtreDateMax = Flight::request()->query->date_max;

		// Convertir en int si nécessaire
		$filtreRegionId = $filtreRegionId ? (int) $filtreRegionId : null;
		$filtreVilleId = $filtreVilleId ? (int) $filtreVilleId : null;
		$filtreArticleId = $filtreArticleId ? (int) $filtreArticleId : null;
		$filtreCategorieId = $filtreCategorieId ? (int) $filtreCategorieId : null;
		$filtreStatusId = $filtreStatusId ? (int) $filtreStatusId : null;

		// Construire la requête SQL avec des conditions WHERE dynamiques
		$sql = "SELECT b.id, b.ville_id, v.libelle AS ville, v.region_id, r.libelle AS region,
		               b.article_id, a.libelle AS article, a.prix_unitaire,
		               c.id AS categorie_id, c.libelle AS categorie,
		               b.quantite, b.quantite_initiale, b.date_besoin,
		               b.status_id, s.libelle AS status, b.created_at
		        FROM bn_besoin b
		        JOIN bn_ville v ON v.id = b.ville_id
		        JOIN bn_region r ON r.id = v.region_id
		        JOIN bn_article a ON a.id = b.article_id
		        JOIN bn_categorie c ON c.id = a.categorie_id
		        JOIN bn_status s ON s.id = b.status_id
		        WHERE 1=1";

		$params = [];

		// Appliquer les filtres
		if ($filtreRegionId) {
			$sql .= " AND v.region_id = :region_id";
			$params[':region_id'] = $filtreRegionId;
		}
		if ($filtreVilleId) {
			$sql .= " AND b.ville_id = :ville_id";
			$params[':ville_id'] = $filtreVilleId;
		}
		if ($filtreArticleId) {
			$sql .= " AND b.article_id = :article_id";
			$params[':article_id'] = $filtreArticleId;
		}
		if ($filtreCategorieId) {
			$sql .= " AND a.categorie_id = :categorie_id";
			$params[':categorie_id'] = $filtreCategorieId;
		}
		if ($filtreStatusId) {
			$sql .= " AND b.status_id = :status_id";
			$params[':status_id'] = $filtreStatusId;
		}
		if ($filtreDateMin) {
			$sql .= " AND DATE(b.date_besoin) >= :date_min";
			$params[':date_min'] = $filtreDateMin;
		}
		if ($filtreDateMax) {
			$sql .= " AND DATE(b.date_besoin) <= :date_max";
			$params[':date_max'] = $filtreDateMax;
		}

		$sql .= " ORDER BY b.created_at DESC, b.id DESC";

		$stmt = $pdo->prepare($sql);
		$stmt->execute($params);
		$besoins = $stmt->fetchAll(PDO::FETCH_ASSOC);

		// Récupérer les listes pour les filtres
		$regions = $pdo->query("SELECT id, libelle FROM bn_region ORDER BY libelle")->fetchAll(PDO::FETCH_ASSOC);
		$villes = $pdo->query("SELECT id, libelle, region_id FROM bn_ville ORDER BY libelle")->fetchAll(PDO::FETCH_ASSOC);
		$articles = $pdo->query("SELECT id, libelle FROM bn_article ORDER BY libelle")->fetchAll(PDO::FETCH_ASSOC);
		$categories = $pdo->query("SELECT id, libelle FROM bn_categorie ORDER BY libelle")->fetchAll(PDO::FETCH_ASSOC);
		$statuses = $pdo->query("SELECT id, libelle FROM bn_status ORDER BY id")->fetchAll(PDO::FETCH_ASSOC);

		$pagename = 'besoins/liste.php';
		Flight::render('modele', [
			'besoins' => $besoins,
			'pagename' => $pagename,
			'regions' => $regions,
			'villes' => $villes,
			'articles' => $articles,
			'categories' => $categories,
			'statuses' => $statuses,
			'filtreRegionId' => $filtreRegionId,
			'filtreVilleId' => $filtreVilleId,
			'filtreArticleId' => $filtreArticleId,
			'filtreCategorieId' => $filtreCategorieId,
			'filtreStatusId' => $filtreStatusId,
			'filtreDateMin' => $filtreDateMin,
			'filtreDateMax' => $filtreDateMax
		]);
	}

	public function besoinsRestants()
	{
		$pagename = 'besoins/besoin_restant.php';
		// Recuperer seulement les besoins avec status_id != 3 (non satisfaits)
		$besoins = $this->besoinsRepo->getBesoinsRestants();
		Flight::render('modele', [
			'besoins' => $besoins,
			'pagename' => $pagename,
		]);
	}

	public function store()
	{
		$data = Flight::request()->data;
		$pagename = 'besoins/formulaire.php';
		$villeId = (int) $data->ville_id;
		$articleId = (int) $data->article_id;
		$quantite = (int) $data->quantite;
		$dateBesoin = (string) $data->date_besoin;
		$statusId = (int) ($data->status_id ?: 1);

		if ($villeId <= 0 || $articleId <= 0 || $quantite <= 0 || $dateBesoin === '' || $statusId <= 0) {
			$villes = $this->villesRepo->getAll();
			$articles = $this->articlesRepo->findAll();
			Flight::render('modele', [
				'villes' => $villes,
				'articles' => $articles,
				'error' => 'Tous les champs sont obligatoires et doivent être valides.',
				'success' => null,
				'old' => [
					'ville_id' => $villeId,
					'article_id' => $articleId,
					'quantite' => $quantite,
					'date_besoin' => $dateBesoin,
					'status_id' => $statusId,

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

