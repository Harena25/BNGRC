<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <!-- En-tete -->
            <div class="mb-4">
                <a href="/needs/restants" class="btn btn-sm btn-outline-secondary mb-3">
                    <i class="bi bi-arrow-left"></i> Retour aux besoins restants
                </a>
                <h2 class="mb-1">
                    <i class="bi bi-calculator text-warning me-2"></i>
                    Simulation globale des achats
                </h2>
                <p class="text-muted">Estimation des achats possibles avec le solde disponible</p>
            </div>

            <!-- Resume financier -->
            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <div class="card shadow-sm border-success">
                        <div class="card-body text-center">
                            <i class="bi bi-wallet2 fs-1 text-success"></i>
                            <h5 class="mt-2 mb-0"><?php echo number_format($solde_argent ?? 0, 2); ?> MAD</h5>
                            <small class="text-muted">Solde argent disponible</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card shadow-sm border-warning">
                        <div class="card-body text-center">
                            <i class="bi bi-cart-check fs-1 text-warning"></i>
                            <h5 class="mt-2 mb-0"><?php echo number_format($total_cout ?? 0, 2); ?> MAD</h5>
                            <small class="text-muted">Cout total des achats</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div
                        class="card shadow-sm <?php echo ($solde_argent ?? 0) >= ($total_cout ?? 0) ? 'border-success' : 'border-danger'; ?>">
                        <div class="card-body text-center">
                            <i
                                class="bi bi-piggy-bank fs-1 <?php echo ($solde_argent ?? 0) >= ($total_cout ?? 0) ? 'text-success' : 'text-danger'; ?>"></i>
                            <h5 class="mt-2 mb-0">
                                <?php echo number_format(($solde_argent ?? 0) - ($total_cout ?? 0), 2); ?> MAD</h5>
                            <small class="text-muted">Solde apres achats</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Message d'alerte -->
            <?php if (($solde_argent ?? 0) < ($total_cout ?? 0)): ?>
                <div class="alert alert-danger mb-4">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    <strong>Solde insuffisant !</strong> Le cout total des achats depasse le solde disponible.
                    Vous devez prioriser certains besoins.
                </div>
            <?php else: ?>
                <div class="alert alert-success mb-4 d-flex justify-content-between align-items-center">
                    <div>
                        <i class="bi bi-check-circle-fill me-2"></i>
                        <strong>Solde suffisant !</strong> Vous pouvez acheter tous les articles necessaires.
                    </div>
                    <?php if (!empty($achats)): ?>
                        <button type="button" class="btn btn-success" id="btnValiderGlobal">
                            <i class="bi bi-check-circle me-2"></i>Valider tous les achats
                        </button>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <!-- Frais d'achat -->
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <form method="GET" action="<?php echo BASE_PATH; ?>/achats/simuler-global"
                        class="row g-3 align-items-end">
                        <div class="col-md-8">
                            <label for="frais" class="form-label fw-bold">
                                <i class="bi bi-percent me-2"></i>Taux de frais d'achat (%)
                            </label>
                            <input type="number" class="form-control" id="frais" name="frais" min="0" max="100"
                                step="0.1" value="<?php echo number_format($frais_achat ?? 10, 1); ?>"
                                placeholder="Ex: 10">
                            <div class="form-text">
                                Pourcentage de frais applique sur tous les achats (0-100%)
                            </div>
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-arrow-clockwise me-2"></i>Recalculer
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Note explicative -->
            <div class="alert alert-info mb-4">
                <i class="bi bi-info-circle me-2"></i>
                <strong>Note:</strong> Un frais d'achat de
                <strong><?php echo number_format($frais_achat ?? 10, 1); ?>%</strong>
                est applique sur tous les achats simules ci-dessous.
            </div>

            <!-- Tableau des achats possibles -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <i class="bi bi-list-check me-2"></i>Details des achats possibles
                </div>
                <div class="card-body p-0">
                    <?php if (empty($achats) && empty($articles_stock)): ?>
                        <div class="text-center py-5">
                            <i class="bi bi-check-circle" style="font-size: 3rem; color: #28a745;"></i>
                            <p class="text-success mt-3 fs-5">
                                Aucun besoin restant !<br>
                                Tous les besoins sont satisfaits.
                            </p>
                        </div>
                    <?php else: ?>

                        <!-- Section: Articles en stock -->
                        <?php if (!empty($articles_stock)): ?>
                            <div class="alert alert-warning m-3 mb-0 d-flex justify-content-between align-items-center">
                                <div>
                                    <i class="bi bi-box-seam-fill me-2"></i>
                                    <strong>Articles disponibles en stock</strong> - Choisissez entre distribuer le stock
                                    existant ou acheter de nouveaux articles
                                </div>
                                <a href="/autoDistribution?mode=simulate" class="btn btn-primary btn-sm">
                                    <i class="bi bi-lightning-fill me-2"></i>Distribuer tout le stock
                                </a>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-hover table-warning align-middle mb-0">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>#</th>
                                            <th>Ville</th>
                                            <th>Article</th>
                                            <th>Categorie</th>
                                            <th class="text-end">Besoin</th>
                                            <th class="text-end">En stock</th>
                                            <th class="text-center" colspan="2">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($articles_stock as $idx => $item):
                                            $peutSatisfaire = $item['quantite_stock'] >= $item['quantite_besoin'];
                                            ?>
                                            <tr>
                                                <td><?php echo $idx + 1; ?></td>
                                                <td><?php echo htmlspecialchars($item['ville'] ?? ''); ?></td>
                                                <td><strong><?php echo htmlspecialchars($item['article'] ?? ''); ?></strong></td>
                                                <td>
                                                    <span class="badge bg-info">
                                                        <?php echo htmlspecialchars($item['categorie'] ?? ''); ?>
                                                    </span>
                                                </td>
                                                <td class="text-end">
                                                    <span class="badge bg-danger">
                                                        <?php echo number_format($item['quantite_besoin']); ?>
                                                    </span>
                                                </td>
                                                <td class="text-end">
                                                    <span class="badge bg-success">
                                                        <?php echo number_format($item['quantite_stock']); ?>
                                                    </span>
                                                    <?php if ($peutSatisfaire): ?>
                                                        <i class="bi bi-check-circle-fill text-success ms-1"
                                                            title="Stock suffisant"></i>
                                                    <?php else: ?>
                                                        <i class="bi bi-exclamation-triangle-fill text-warning ms-1"
                                                            title="Stock insuffisant"></i>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="text-center">
                                                    <a href="/autoDistribution?mode=simulate" class="btn btn-sm btn-primary"
                                                        title="Distribuer le stock existant">
                                                        <i class="bi bi-truck"></i> Distribuer
                                                    </a>
                                                </td>
                                                <td class="text-center">
                                                    <a href="/achats/form/<?php echo $item['besoin_id']; ?>"
                                                        class="btn btn-sm btn-warning" title="Acheter de nouveaux articles">
                                                        <i class="bi bi-cart-plus"></i> Acheter
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            <div class="m-3 alert alert-secondary small mb-0">
                                <i class="bi bi-info-circle me-2"></i>
                                <strong>Distribuer:</strong> Utilise le stock existant pour satisfaire les besoins (recommande
                                si stock suffisant)<br>
                                <strong>Acheter:</strong> Achete de nouveaux articles en utilisant les dons en argent (ignore le
                                stock existant)
                            </div>
                        <?php endif; ?>

                        <!-- Section: Achats necessaires -->
                        <?php if (!empty($achats)): ?>
                            <?php if (!empty($articles_stock)): ?>
                                <div class="alert alert-info m-3 mb-0 mt-3">
                                    <i class="bi bi-cart-check-fill me-2"></i>
                                    <strong>Articles non disponibles en stock</strong> - Ces articles doivent etre achetes
                                </div>
                            <?php endif; ?>
                            <div class="table-responsive">
                                <table class="table table-hover table-striped align-middle mb-0">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>#</th>
                                            <th>Ville</th>
                                            <th>Article</th>
                                            <th>Categorie</th>
                                            <th class="text-end">Qte</th>
                                            <th class="text-end">Prix Unit.</th>
                                            <th class="text-end">Sous-total</th>
                                            <th class="text-end">Frais</th>
                                            <th class="text-end">Total</th>
                                            <th class="text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $cumulCout = 0;
                                        foreach ($achats as $idx => $achat):
                                            $cumulCout += $achat['montant_total'];
                                            $possible = $cumulCout <= ($solde_argent ?? 0);
                                            ?>
                                            <tr class="<?php echo $possible ? '' : 'table-danger'; ?>">
                                                <td><?php echo $idx + 1; ?></td>
                                                <td><?php echo htmlspecialchars($achat['ville'] ?? ''); ?></td>
                                                <td><strong><?php echo htmlspecialchars($achat['article'] ?? ''); ?></strong></td>
                                                <td>
                                                    <span class="badge bg-info">
                                                        <?php echo htmlspecialchars($achat['categorie'] ?? ''); ?>
                                                    </span>
                                                </td>
                                                <td class="text-end"><?php echo number_format($achat['quantite']); ?></td>
                                                <td class="text-end"><?php echo number_format($achat['prix_unitaire'], 2); ?></td>
                                                <td class="text-end"><?php echo number_format($achat['sous_total'], 2); ?></td>
                                                <td class="text-end text-danger"><?php echo number_format($achat['frais'], 2); ?>
                                                </td>
                                                <td class="text-end fw-bold">
                                                    <?php echo number_format($achat['montant_total'], 2); ?></td>
                                                <td class="text-center">
                                                    <a href="/achats/form/<?php echo $achat['besoin_id']; ?>"
                                                        class="btn btn-sm btn-success" title="Acheter">
                                                        <i class="bi bi-cart-plus"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                    <tfoot class="table-secondary">
                                        <tr>
                                            <td colspan="8" class="text-end fw-bold">TOTAL GENERAL:</td>
                                            <td class="text-end fw-bold fs-5"><?php echo number_format($total_cout ?? 0, 2); ?>
                                                MAD</td>
                                            <td></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Note explicative -->
            <div class="alert alert-secondary">
                <i class="bi bi-lightbulb me-2"></i>
                <strong>Comment ca marche ?</strong>
                <ul class="mb-0 mt-2">
                    <li><strong>Articles en stock (tableau jaune):</strong> Vous avez le choix entre distribuer le stock
                        existant ou acheter de nouveaux articles</li>
                    <li><strong>Distribuer:</strong> Utilise la distribution automatique pour allouer le stock existant
                        aux besoins (gratuit)</li>
                    <li><strong>Acheter:</strong> Utilise les dons en argent pour acquerir de nouveaux articles (avec
                        frais)</li>
                    <li><strong>Articles non en stock:</strong> Doivent obligatoirement etre achetes avec les dons en
                        argent</li>
                    <li><strong>Valider tous les achats:</strong> Execute tous les achats des articles non en stock en
                        une seule operation</li>
                    <li>Les lignes en rouge indiquent les achats impossibles par manque de fonds</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<script>
    // Validation globale des achats
    document.getElementById('btnValiderGlobal')?.addEventListener('click', function () {
        if (!confirm('Voulez-vous vraiment valider tous ces achats ?\n\nCette action est irreversible.')) {
            return;
        }

        const btn = this;
        const originalText = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Traitement en cours...';

        const fraisPourcent = parseFloat(document.getElementById('frais').value) || 10;

        const basePath = '<?php echo BASE_PATH; ?>';
        fetch(basePath + '/achats/valider-global', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'frais_pourcent=' + encodeURIComponent(fraisPourcent)
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('✓ ' + data.message);
                    window.location.reload();
                } else {
                    alert('✗ Erreur: ' + data.message);
                    btn.disabled = false;
                    btn.innerHTML = originalText;
                }
            })
            .catch(error => {
                alert('✗ Erreur de communication: ' + error);
                btn.disabled = false;
                btn.innerHTML = originalText;
            });
    });
</script>