<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <!-- En-tête avec titre et bouton d'ajout -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="mb-0"><i class="bi bi-cart3"></i> Liste des achats</h2>
                <div>
                    <span class="badge bg-success fs-6 me-2">
                        Solde argent : <?php echo number_format($solde ?? 0, 2); ?> Ar
                    </span>
                    <a href="<?php echo BASE_PATH; ?>/needs/restants" class="btn btn-primary">
                        <i class="bi bi-cart-plus"></i> Nouvel achat
                    </a>
                </div>
            </div>

            <!-- Filtre par ville -->
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <form method="GET" action="<?php echo BASE_PATH; ?>/purchases/list" class="row g-3 align-items-end">
                        <div class="col-md-6">
                            <label for="ville_id" class="form-label"><i class="bi bi-funnel"></i> Filtrer par ville</label>
                            <select class="form-select" id="ville_id" name="ville_id">
                                <option value="">-- Toutes les villes --</option>
                                <?php foreach ($villes as $v): ?>
                                    <option value="<?php echo $v['id']; ?>"
                                        <?php echo (isset($filtreVilleId) && $filtreVilleId == $v['id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($v['region'] . ' - ' . $v['libelle']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-outline-primary w-100">
                                <i class="bi bi-search"></i> Filtrer
                            </button>
                        </div>
                        <?php if (!empty($filtreVilleId)): ?>
                            <div class="col-md-3">
                                <a href="<?php echo BASE_PATH; ?>/purchases/list" class="btn btn-outline-secondary w-100">
                                    <i class="bi bi-x-circle"></i> Réinitialiser
                                </a>
                            </div>
                        <?php endif; ?>
                    </form>
                </div>
            </div>

            <!-- Tableau des achats -->
            <div class="card shadow-sm">
                <div class="card-body">
                    <?php if (empty($achats)): ?>
                        <div class="text-center py-5">
                            <i class="bi bi-cart-x" style="font-size: 3rem; color: #ccc;"></i>
                            <p class="text-muted mt-3">Aucun achat enregistré pour le moment.</p>
                            <a href="<?php echo BASE_PATH; ?>/purchases" class="btn btn-primary mt-2">
                                <i class="bi bi-cart-plus"></i> Effectuer le premier achat
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover table-striped align-middle">
                                <thead class="table-dark">
                                    <tr>
                                        <th>#</th>
                                        <th>Date</th>
                                        <th>Région</th>
                                        <th>Ville</th>
                                        <th>Catégorie</th>
                                        <th>Article</th>
                                        <th class="text-end">Quantité</th>
                                        <th class="text-end">Prix unitaire</th>
                                        <th class="text-end">Frais (%)</th>
                                        <th class="text-end">Prix total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $totalGeneral = 0;
                                    foreach ($achats as $a): 
                                        $totalGeneral += (float)$a['prix_total'];
                                    ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($a['id']); ?></td>
                                            <td><?php echo htmlspecialchars($a['date_achat']); ?></td>
                                            <td><?php echo htmlspecialchars($a['region_name'] ?? ''); ?></td>
                                            <td><?php echo htmlspecialchars($a['ville_name'] ?? ''); ?></td>
                                            <td>
                                                <span class="badge bg-info">
                                                    <?php echo htmlspecialchars($a['categorie_name'] ?? 'N/A'); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <strong><?php echo htmlspecialchars($a['article_name'] ?? 'N/A'); ?></strong>
                                            </td>
                                            <td class="text-end">
                                                <span class="badge bg-primary">
                                                    <?php echo number_format($a['quantite']); ?>
                                                </span>
                                            </td>
                                            <td class="text-end">
                                                <?php echo number_format($a['prix_unitaire'], 2); ?> Ar
                                            </td>
                                            <td class="text-end">
                                                <?php echo number_format($a['frais_pourcentage'], 1); ?>%
                                            </td>
                                            <td class="text-end">
                                                <strong><?php echo number_format($a['prix_total'], 2); ?> Ar</strong>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                                <tfoot class="table-secondary">
                                    <tr>
                                        <td colspan="9" class="text-end fw-bold">Total des achats :</td>
                                        <td class="text-end fw-bold"><?php echo number_format($totalGeneral, 2); ?> Ar</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <!-- Statistiques -->
                        <div class="mt-3 text-muted small">
                            <i class="bi bi-info-circle"></i>
                            Total : <?php echo count($achats); ?> achat(s) enregistré(s)
                            <?php if (!empty($filtreVilleId)): ?>
                                | Filtre actif : <strong><?php echo htmlspecialchars($achats[0]['ville_name'] ?? ''); ?></strong>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
