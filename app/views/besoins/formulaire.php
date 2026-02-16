<!doctype html>
<html lang="fr">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Saisie des besoins</title>
	<link rel="stylesheet" href="/assets/bootstrap/css/bootstrap.min.css">
</head>
<body class="bg-light">
<div class="container py-4">
	<div class="d-flex justify-content-between align-items-center mb-3">
		<h1 class="h4 mb-0">Saisie des besoins</h1>
		<a class="btn btn-outline-secondary btn-sm" href="/needs/list">Voir la liste</a>
	</div>

	<?php if (!empty($success)): ?>
		<div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
	<?php endif; ?>
	<?php if (!empty($error)): ?>
		<div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
	<?php endif; ?>

	<div class="card">
		<div class="card-body">
			<form action="/needs" method="POST" class="row g-3">

				<div class="col-md-6">
					<label class="form-label" for="ville_id">Ville</label>
					<select class="form-select" id="ville_id" name="ville_id" required>
						<option value="">-- Choisir --</option>
						<?php foreach ($villes as $v): ?>
							<option value="<?= $v['id'] ?>"
								<?= (isset($old['ville_id']) && $old['ville_id'] == $v['id']) ? 'selected' : '' ?>>
								<?= htmlspecialchars($v['region'] . ' - ' . $v['libelle']) ?>
							</option>
						<?php endforeach; ?>
					</select>
				</div>

				<div class="col-md-6">
					<label class="form-label" for="article_id">Article</label>
					<select class="form-select" id="article_id" name="article_id" required>
						<option value="">-- Choisir --</option>
						<?php foreach ($articles as $a): ?>
							<option value="<?= $a['id'] ?>"
								<?= (isset($old['article_id']) && $old['article_id'] == $a['id']) ? 'selected' : '' ?>>
								<?= htmlspecialchars($a['categorie_id'] . ' - ' . $a['libelle']) ?>
							</option>
						<?php endforeach; ?>
					</select>
				</div>

				<div class="col-md-4">
					<label class="form-label" for="quantite">Quantité</label>
					<input class="form-control" type="number" id="quantite" name="quantite" min="1" required
						value="<?= htmlspecialchars($old['quantite'] ?? '') ?>">
				</div>

				<div class="col-md-4">
					<label class="form-label" for="date_besoin">Date</label>
					<input class="form-control" type="date" id="date_besoin" name="date_besoin" required
						value="<?= htmlspecialchars($old['date_besoin'] ?? '') ?>">
				</div>

				<div class="col-md-4">
					<label class="form-label" for="status_id">Status</label>
					<input class="form-control" type="number" id="status_id" name="status_id" min="1" required
						value="<?= htmlspecialchars($old['status_id'] ?? '1') ?>">
					<div class="form-text">Par défaut : 1</div>
				</div>

				<div class="col-12">
					<button class="btn btn-primary" type="submit">Enregistrer</button>
				</div>
			</form>
		</div>
	</div>
</div>

<script src="/assets/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>

</script>
</body>
</html>

