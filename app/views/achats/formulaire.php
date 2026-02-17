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
                        Seules les villes avec des besoins restants sont affichées. Le stock existant est pris en compte.
                    </div>

                    <form action="<?php echo BASE_PATH; ?>/purchases" method="POST">

                        <!-- Ville (seulement celles avec besoins restants) -->
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
                            <div class="form-text">Seules les villes ayant des besoins non satisfaits sont listées.</div>
                        </div>

                        <!-- Article (chargé dynamiquement selon la ville) -->
                        <div class="mb-3">
                            <label for="article_id" class="form-label">Article <span class="text-danger">*</span></label>
                            <select class="form-select" id="article_id" name="article_id" required disabled>
                                <option value="">-- Sélectionnez d'abord une ville --</option>
                            </select>
                            <div id="articleInfo" class="form-text"></div>
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
                                   required
                                   disabled>
                            <div id="quantiteInfo" class="form-text"></div>
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
                                    <div class="col-md-3">
                                        <small class="text-muted">Prix unitaire</small>
                                        <div id="prixUnitaireDisplay" class="fw-bold">-</div>
                                    </div>
                                    <div class="col-md-3">
                                        <small class="text-muted">Prix de base</small>
                                        <div id="prixBaseDisplay" class="fw-bold">-</div>
                                    </div>
                                    <div class="col-md-3">
                                        <small class="text-muted">Prix total (+ frais)</small>
                                        <div id="prixTotalDisplay" class="fw-bold text-primary">-</div>
                                    </div>
                                    <div class="col-md-3">
                                        <small class="text-muted">Max achetable</small>
                                        <div id="maxAchetableDisplay" class="fw-bold text-warning">-</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Boutons -->
                        <div class="d-flex justify-content-between mt-4">
                            <a href="<?php echo BASE_PATH; ?>/purchases/list" class="btn btn-secondary">
                                <i class="bi bi-arrow-left"></i> Liste des achats
                            </a>
                            <button type="submit" class="btn btn-primary" id="btnSubmit">
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
(function() {
    const villeSelect = document.getElementById('ville_id');
    const articleSelect = document.getElementById('article_id');
    const quantiteInput = document.getElementById('quantite');
    const fraisInput = document.getElementById('frais_pourcentage');
    const articleInfo = document.getElementById('articleInfo');
    const quantiteInfo = document.getElementById('quantiteInfo');
    const prixUnitaireEl = document.getElementById('prixUnitaireDisplay');
    const prixBaseEl = document.getElementById('prixBaseDisplay');
    const prixTotalEl = document.getElementById('prixTotalDisplay');
    const maxAchetableEl = document.getElementById('maxAchetableDisplay');

    let articlesData = [];

    // Quand on change de ville, charger les articles achetables via AJAX
    villeSelect.addEventListener('change', function() {
        const villeId = this.value;
        articleSelect.innerHTML = '<option value="">-- Chargement... --</option>';
        articleSelect.disabled = true;
        quantiteInput.disabled = true;
        quantiteInput.value = '';
        quantiteInput.removeAttribute('max');
        articlesData = [];
        resetEstimation();

        if (!villeId) {
            articleSelect.innerHTML = '<option value="">-- Sélectionnez d\'abord une ville --</option>';
            articleInfo.textContent = '';
            return;
        }

        const basePath = '<?php echo BASE_PATH; ?>';
        fetch(basePath + '/purchases/articles?ville_id=' + villeId)
            .then(r => r.json())
            .then(data => {
                articlesData = data;
                articleSelect.innerHTML = '<option value="">-- Sélectionner un article --</option>';
                
                if (data.length === 0) {
                    articleSelect.innerHTML = '<option value="">-- Aucun article achetable --</option>';
                    articleInfo.innerHTML = '<span class="text-success"><i class="bi bi-check-circle"></i> Tous les besoins de cette ville sont couverts par le stock ou déjà achetés.</span>';
                    return;
                }

                data.forEach(function(a) {
                    const opt = document.createElement('option');
                    opt.value = a.article_id;
                    opt.setAttribute('data-prix', a.prix_unitaire);
                    opt.setAttribute('data-max', a.quantite_achetable);
                    opt.setAttribute('data-besoin', a.besoin_restant);
                    opt.setAttribute('data-stock', a.stock_disponible);
                    opt.setAttribute('data-achete', a.deja_achete);
                    opt.textContent = a.categorie_name + ' - ' + a.article_name 
                        + ' (' + parseFloat(a.prix_unitaire).toFixed(2) + ' MAD) — max: ' + a.quantite_achetable;
                    articleSelect.appendChild(opt);
                });

                articleSelect.disabled = false;
                articleInfo.textContent = data.length + ' article(s) disponible(s) à l\'achat.';

                // Restaurer la sélection si old data
                <?php if (!empty($old['article_id'])): ?>
                const oldArticleId = '<?php echo $old['article_id']; ?>';
                if (articleSelect.querySelector('option[value="' + oldArticleId + '"]')) {
                    articleSelect.value = oldArticleId;
                    articleSelect.dispatchEvent(new Event('change'));
                }
                <?php endif; ?>
            })
            .catch(() => {
                articleSelect.innerHTML = '<option value="">-- Erreur de chargement --</option>';
            });
    });

    // Quand on change d'article, mettre à jour la quantité max
    articleSelect.addEventListener('change', function() {
        const opt = this.options[this.selectedIndex];
        if (!opt || !opt.value) {
            quantiteInput.disabled = true;
            quantiteInput.value = '';
            quantiteInput.removeAttribute('max');
            quantiteInfo.textContent = '';
            resetEstimation();
            return;
        }

        const maxQte = parseInt(opt.getAttribute('data-max')) || 0;
        const besoin = parseInt(opt.getAttribute('data-besoin')) || 0;
        const stock = parseInt(opt.getAttribute('data-stock')) || 0;
        const achete = parseInt(opt.getAttribute('data-achete')) || 0;

        quantiteInput.disabled = false;
        quantiteInput.max = maxQte;
        quantiteInput.min = 1;
        quantiteInfo.innerHTML = 
            'Besoin restant : <strong>' + besoin + '</strong>' +
            ' | Stock : <strong>' + stock + '</strong>' +
            ' | Déjà acheté : <strong>' + achete + '</strong>' +
            ' | <span class="text-primary fw-bold">Max achetable : ' + maxQte + '</span>';

        maxAchetableEl.textContent = maxQte;
        updateEstimation();
    });

    // Mise à jour de l'estimation dynamique
    function updateEstimation() {
        const opt = articleSelect.options[articleSelect.selectedIndex];
        const prix = opt ? parseFloat(opt.getAttribute('data-prix')) : 0;
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

    function resetEstimation() {
        prixUnitaireEl.textContent = '-';
        prixBaseEl.textContent = '-';
        prixTotalEl.textContent = '-';
        maxAchetableEl.textContent = '-';
    }

    quantiteInput.addEventListener('input', function() {
        const max = parseInt(this.max) || 0;
        if (max > 0 && parseInt(this.value) > max) {
            this.value = max;
        }
        updateEstimation();
    });
    fraisInput.addEventListener('input', updateEstimation);

    // Si old ville_id, déclencher le chargement
    <?php if (!empty($old['ville_id'])): ?>
    villeSelect.dispatchEvent(new Event('change'));
    <?php endif; ?>
})();
</script>
