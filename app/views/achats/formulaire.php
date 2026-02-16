<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0"><i class="bi bi-cart-plus"></i> Saisie d'un achat</h4>
                    <span class="badge bg-light text-dark fs-6">
                        Solde disponible : <?php echo number_format($solde ?? 0, 2); ?> MAD
                    </span>
                </div>
                <div class="card-body">
                    <?php if (!empty($success)): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <?php echo htmlspecialchars($success); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                    <?php if (!empty($error)): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?php echo htmlspecialchars($error); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i>
                        Les dons en argent peuvent acheter des besoins en nature et matériaux.
                        Un frais d'achat configurable est appliqué au prix total.
                    </div>

                    <form action="/purchases" method="POST">

                        <!-- Ville -->
                        <div class="mb-3">
                            <label for="ville_id" class="form-label">Ville <span class="text-danger">*</span></label>
                            <select class="form-select" id="ville_id" name="ville_id" required>
                                <option value="">-- Sélectionner une ville --</option>
                                <?php foreach ($villes as $v): ?>
                                    <option value="<?php echo $v['id']; ?>"
                                        <?php echo (isset($old['ville_id']) && $old['ville_id'] == $v['id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($v['region'] . ' - ' . $v['libelle']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Article (excluant la catégorie Argent) -->
                        <div class="mb-3">
                            <label for="article_id" class="form-label">Article <span class="text-danger">*</span></label>
                            <select class="form-select" id="article_id" name="article_id" required>
                                <option value="">-- Sélectionner un article --</option>
                                <?php foreach ($articles as $a): ?>
                                    <?php if ((int)($a['categorie_id'] ?? 0) !== 3): // Exclure catégorie Argent ?>
                                        <option value="<?php echo $a['id']; ?>"
                                            data-prix="<?php echo $a['prix_unitaire']; ?>"
                                            <?php echo (isset($old['article_id']) && $old['article_id'] == $a['id']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($a['categorie_libelle'] . ' - ' . $a['libelle'] . ' (' . number_format($a['prix_unitaire'], 2) . ' MAD)'); ?>
                                        </option>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Quantité -->
                        <div class="mb-3">
                            <label for="quantite" class="form-label">Quantité <span class="text-danger">*</span></label>
                            <input type="number"
                                   class="form-control"
                                   id="quantite"
                                   name="quantite"
                                   min="1"
                                   step="1"
                                   value="<?php echo htmlspecialchars($old['quantite'] ?? ''); ?>"
                                   required>
                        </div>

                        <!-- Frais d'achat -->
                        <div class="mb-3">
                            <label for="frais_pourcentage" class="form-label">Frais d'achat (%)</label>
                            <input type="number"
                                   class="form-control"
                                   id="frais_pourcentage"
                                   name="frais_pourcentage"
                                   min="0"
                                   max="100"
                                   step="0.01"
                                   value="<?php echo htmlspecialchars($old['frais_pourcentage'] ?? '10'); ?>"
                                   placeholder="10">
                            <div class="form-text">Pourcentage de frais ajouté au prix de base (par défaut 10%).</div>
                        </div>

                        <!-- Date -->
                        <div class="mb-3">
                            <label for="date_achat" class="form-label">Date de l'achat <span class="text-danger">*</span></label>
                            <input type="date"
                                   class="form-control"
                                   id="date_achat"
                                   name="date_achat"
                                   value="<?php echo htmlspecialchars($old['date_achat'] ?? date('Y-m-d')); ?>"
                                   required>
                        </div>

                        <!-- Estimation du prix -->
                        <div class="card bg-light mb-3">
                            <div class="card-body">
                                <h6 class="card-title"><i class="bi bi-calculator"></i> Estimation du coût</h6>
                                <div class="row">
                                    <div class="col-md-4">
                                        <small class="text-muted">Prix unitaire</small>
                                        <div id="prixUnitaireDisplay" class="fw-bold">-</div>
                                    </div>
                                    <div class="col-md-4">
                                        <small class="text-muted">Prix de base</small>
                                        <div id="prixBaseDisplay" class="fw-bold">-</div>
                                    </div>
                                    <div class="col-md-4">
                                        <small class="text-muted">Prix total (avec frais)</small>
                                        <div id="prixTotalDisplay" class="fw-bold text-primary">-</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Boutons -->
                        <div class="d-flex justify-content-between mt-4">
                            <a href="/purchases/list" class="btn btn-secondary">
                                <i class="bi bi-arrow-left"></i> Liste des achats
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-cart-check"></i> Enregistrer l'achat
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Calcul dynamique du prix estimé
(function() {
    const articleSelect = document.getElementById('article_id');
    const quantiteInput = document.getElementById('quantite');
    const fraisInput = document.getElementById('frais_pourcentage');
    const prixUnitaireEl = document.getElementById('prixUnitaireDisplay');
    const prixBaseEl = document.getElementById('prixBaseDisplay');
    const prixTotalEl = document.getElementById('prixTotalDisplay');

    function updateEstimation() {
        const selectedOption = articleSelect.options[articleSelect.selectedIndex];
        const prix = selectedOption ? parseFloat(selectedOption.getAttribute('data-prix')) : 0;
        const qte = parseInt(quantiteInput.value) || 0;
        const frais = parseFloat(fraisInput.value) || 0;

        if (prix && qte > 0) {
            const prixBase = prix * qte;
            const prixTotal = prixBase * (1 + frais / 100);
            prixUnitaireEl.textContent = prix.toFixed(2) + ' MAD';
            prixBaseEl.textContent = prixBase.toFixed(2) + ' MAD';
            prixTotalEl.textContent = prixTotal.toFixed(2) + ' MAD';
        } else {
            prixUnitaireEl.textContent = prix ? prix.toFixed(2) + ' MAD' : '-';
            prixBaseEl.textContent = '-';
            prixTotalEl.textContent = '-';
        }
    }

    articleSelect.addEventListener('change', updateEstimation);
    quantiteInput.addEventListener('input', updateEstimation);
    fraisInput.addEventListener('input', updateEstimation);

    // Initialiser au chargement
    updateEstimation();
})();
</script>
