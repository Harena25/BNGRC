<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <!-- En-tete avec titre et bouton simuler achats -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="mb-0">
                    <i class="bi bi-exclamation-triangle-fill text-warning me-2"></i>
                    Besoins Restants
                </h2>
                <a href="<?php echo BASE_PATH; ?>/achats/simuler-global" class="btn btn-warning">
                    <i class="bi bi-calculator"></i> Simuler achats
                </a>
            </div>

            <!-- Tableau des besoins restants -->
            <div class="card shadow-sm">
                <div class="card-body">
                    <?php if (empty($besoins)): ?>
                        <div class="text-center py-5">
                            <i class="bi bi-check-circle" style="font-size: 3rem; color: #28a745;"></i>
                            <p class="text-success mt-3 fs-5">Aucun besoin restant ! Tous les besoins sont satisfaits.</p>
                            <a href="<?php echo BASE_PATH; ?>/needs/list" class="btn btn-primary mt-2">
                                <i class="bi bi-list"></i> Voir tous les besoins
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info mb-3">
                            <i class="bi bi-info-circle me-2"></i>
                            Cette page affiche uniquement les besoins <strong>non satisfaits</strong> (Ouvert ou
                            Partiellement satisfait).
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover table-striped align-middle">
                                <thead class="table-dark">
                                    <tr>
                                        <th>#</th>
                                        <th>Date</th>
                                        <th>Region</th>
                                        <th>Ville</th>
                                        <th>Categorie</th>
                                        <th>Article</th>
                                        <th class="text-end">Prix Unit.</th>
                                        <th class="text-end">Qte initiale</th>
                                        <th class="text-end">Reste</th>
                                        <th>Status</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($besoins as $b):
                                        $qteInit = $b['quantite_initiale'] ?? $b['quantite'];
                                        $qteReste = $b['quantite'];
                                        $prixUnit = $b['prix_unitaire'] ?? 0;
                                        $statusClass = 'secondary';
                                        if (($b['status_id'] ?? 1) == 3)
                                            $statusClass = 'success';
                                        elseif (($b['status_id'] ?? 1) == 2)
                                            $statusClass = 'warning';
                                        elseif (($b['status_id'] ?? 1) == 1)
                                            $statusClass = 'danger';
                                        ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($b['id']); ?></td>
                                            <td><?php echo htmlspecialchars($b['date_besoin'] ?? ''); ?></td>
                                            <td><?php echo htmlspecialchars($b['region'] ?? ''); ?></td>
                                            <td><?php echo htmlspecialchars($b['ville'] ?? ''); ?></td>
                                            <td>
                                                <span class="badge bg-info">
                                                    <?php echo htmlspecialchars($b['categorie'] ?? 'N/A'); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <strong><?php echo htmlspecialchars($b['article'] ?? 'N/A'); ?></strong>
                                            </td>
                                            <td class="text-end">
                                                <span class="text-muted small">
                                                    <?php echo number_format($prixUnit, 2); ?> Ar
                                                </span>
                                            </td>
                                            <td class="text-end">
                                                <span class="badge bg-primary">
                                                    <?php echo number_format($qteInit); ?>
                                                </span>
                                            </td>
                                            <td class="text-end">
                                                <?php if ($qteReste > 0): ?>
                                                    <span class="badge bg-warning text-dark">
                                                        <?php echo number_format($qteReste); ?>
                                                    </span>
                                                <?php else: ?>
                                                    <span class="text-muted">0</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <span class="badge bg-<?php echo $statusClass; ?>">
                                                    <?php echo htmlspecialchars($b['status'] ?? ''); ?>
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <?php if ($qteReste > 0): ?>
                                                    <a href="<?php echo BASE_PATH; ?>/achats/form/<?php echo $b['id']; ?>"
                                                        class="btn btn-sm btn-success" title="Acheter cet article">
                                                        <i class="bi bi-cart-plus"></i> Acheter
                                                    </a>
                                                <?php else: ?>
                                                    <span class="text-muted small">-</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Statistiques -->
                        <div class="mt-4">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <div class="card bg-light">
                                        <div class="card-body text-center">
                                            <h5 class="mb-0"><?php echo count($besoins); ?></h5>
                                            <small class="text-muted">Besoins restants</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card bg-light">
                                        <div class="card-body text-center">
                                            <h5 class="mb-0">
                                                <?php
                                                $totalQte = array_sum(array_column($besoins, 'quantite'));
                                                echo number_format($totalQte);
                                                ?>
                                            </h5>
                                            <small class="text-muted">Quantite totale restante</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card bg-light">
                                        <div class="card-body text-center">
                                            <h5 class="mb-0">
                                                <?php
                                                $totalValeur = 0;
                                                foreach ($besoins as $b) {
                                                    $totalValeur += ($b['quantite'] ?? 0) * ($b['prix_unitaire'] ?? 0);
                                                }
                                                echo number_format($totalValeur, 2);
                                                ?> Ar
                                            </h5>
                                            <small class="text-muted">Valeur totale restante</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Note explicative -->
                        <div class="mt-3 alert alert-secondary small">
                            <i class="bi bi-lightbulb me-2"></i>
                            <strong>Astuce:</strong> Cliquez sur "Acheter" pour utiliser les dons en argent disponibles pour
                            acquerir cet article.
                            Le bouton "Simuler achats" permet de voir une simulation globale des achats possibles.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>