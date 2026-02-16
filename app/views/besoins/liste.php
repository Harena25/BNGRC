<!doctype html>
<html lang="fr">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Liste des besoins</title>
	<link rel="stylesheet" href="/assets/bootstrap/css/bootstrap.min.css">
</head>
<body class="bg-light">
<div class="container py-4">
	<div class="d-flex justify-content-between align-items-center mb-3">
		<h1 class="h4 mb-0">Liste des besoins</h1>
		<a class="btn btn-outline-secondary btn-sm" href="/needs">Nouveau besoin</a>
	</div>

	<?php if (empty($besoins)): ?>
		<div class="alert alert-info">Aucun besoin enregistré pour le moment.</div>
	<?php else: ?>
	<div class="card">
		<div class="card-body">
			<div class="table-responsive">
				<table class="table table-sm align-middle">
					<thead>
					<tr>
						<th>ID</th>
						<th>Date</th>
						<th>Région</th>
						<th>Ville</th>
						<th>Catégorie</th>
						<th>Article</th>
						<th>Quantité</th>
						<th>Status</th>
					</tr>
					</thead>
					<tbody>
					<?php foreach ($besoins as $b): ?>
						<tr>
							<td><?= htmlspecialchars($b['id']) ?></td>
							<td><?= htmlspecialchars($b['date_besoin'] ?? '') ?></td>
							<td><?= htmlspecialchars($b['region'] ?? '') ?></td>
							<td><?= htmlspecialchars($b['ville'] ?? '') ?></td>
							<td><?= htmlspecialchars($b['categorie'] ?? '') ?></td>
							<td><?= htmlspecialchars($b['article'] ?? '') ?></td>
							<td><?= htmlspecialchars($b['quantite']) ?></td>
							<td><?= htmlspecialchars($b['status'] ?? '') ?></td>
						</tr>
					<?php endforeach; ?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
	<?php endif; ?>
</div>

<script src="/assets/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>

