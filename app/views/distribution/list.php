<?php
// $distributions and $stats provided by controller
$stats = $stats ?? ['total' => 0, 'quantite' => 0, 'valeur' => 0];
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0"><i class="bi bi-clock-history me-2"></i>Historique des distributions</h2>
    <div class="btn-group" role="group">
        <a href="<?php echo BASE_PATH; ?>/autoDistribution?mode=simulate&sortMode=date" class="btn btn-primary">
            <i class="bi bi-calendar-event me-1"></i>Par Date
        </a>
        <a href="<?php echo BASE_PATH; ?>/autoDistribution?mode=simulate&sortMode=quantite" class="btn btn-warning">
            <i class="bi bi-sort-numeric-up me-1"></i>Par Quantité
        </a>
        <a href="<?php echo BASE_PATH; ?>/autoDistribution?mode=simulate&sortMode=proportionnelle" class="btn btn-success">
            <i class="bi bi-pie-chart me-1"></i>Proportionnelle
        </a>
    </div>
</div>

<!-- Stats Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-subtitle mb-1 text-white-50">Total distributions</h6>
                        <h3 class="card-title mb-0"><?php echo number_format($stats['total']); ?></h3>
                    </div>
                    <i class="bi bi-box-seam fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-success text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-subtitle mb-1 text-white-50">Articles distribués</h6>
                        <h3 class="card-title mb-0"><?php echo number_format($stats['quantite']); ?></h3>
                    </div>
                    <i class="bi bi-boxes fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-info text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-subtitle mb-1 text-white-50">Valeur totale</h6>
                        <h3 class="card-title mb-0"><?php echo number_format($stats['valeur'], 0, ',', ' '); ?> Ar</h3>
                    </div>
                    <i class="bi bi-currency-exchange fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if (empty($distributions)): ?>
    <div class="alert alert-info">
        <i class="bi bi-info-circle me-2"></i>Aucune distribution trouvée.
    </div>
<?php else: ?>
    <div class="card shadow-sm">
        <div class="card-header bg-light">
            <h5 class="mb-0"><i class="bi bi-list-ul me-2"></i>Détails des distributions</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th class="text-center" style="width: 60px;">#</th>
                            <th>Date distribution</th>
                            <th>Région</th>
                            <th>Ville</th>
                            <th>Article</th>
                            <th>Catégorie</th>
                            <th class="text-end">Qté distribuée</th>
                            <th class="text-end">Reste besoin</th>
                            <th class="text-end">Prix unit.</th>
                            <th class="text-end">Valeur</th>
                            <th>Date besoin</th>
                            <th class="text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($distributions as $d):
                            $valeurLigne = ($d['quantite_distribuee'] ?? 0) * ($d['prix_unitaire'] ?? 0);
                            $statusClass = 'secondary';
                            if (($d['status_id'] ?? 0) == 3)
                                $statusClass = 'success';
                            elseif (($d['status_id'] ?? 0) == 2)
                                $statusClass = 'warning';
                            elseif (($d['status_id'] ?? 0) == 1)
                                $statusClass = 'danger';
                            ?>
                            <tr>
                                <td class="text-center"><small class="text-muted"><?php echo $d['id']; ?></small></td>
                                <td>
                                    <i class="bi bi-calendar-event me-1 text-muted"></i>
                                    <?php echo date('d/m/Y', strtotime($d['date_distribution'])); ?>
                                    <small
                                        class="text-muted d-block"><?php echo date('H:i', strtotime($d['date_distribution'])); ?></small>
                                </td>
                                <td>
                                    <span
                                        class="badge bg-secondary"><?php echo htmlspecialchars($d['region_name'] ?? '-'); ?></span>
                                </td>
                                <td>
                                    <i class="bi bi-geo-alt text-primary me-1"></i>
                                    <strong><?php echo htmlspecialchars($d['ville_name'] ?? '-'); ?></strong>
                                </td>
                                <td>
                                    <i class="bi bi-box text-info me-1"></i>
                                    <?php echo htmlspecialchars($d['article_name'] ?? '-'); ?>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border">
                                        <?php echo htmlspecialchars($d['categorie_name'] ?? '-'); ?>
                                    </span>
                                </td>
                                <td class="text-end">
                                    <strong
                                        class="text-success"><?php echo number_format($d['quantite_distribuee']); ?></strong>
                                </td>
                                <td class="text-end">
                                    <?php if (($d['status_id'] ?? 0) == 2): ?>
                                        <span class="badge bg-warning text-dark">
                                            <i
                                                class="bi bi-exclamation-triangle me-1"></i><?php echo number_format($d['besoin_reste'] ?? 0); ?>
                                        </span>
                                    <?php elseif (($d['status_id'] ?? 0) == 3): ?>
                                        <span class="text-success"><i class="bi bi-check-circle"></i> 0</span>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end">
                                    <small><?php echo number_format($d['prix_unitaire'] ?? 0, 0, ',', ' '); ?> Ar</small>
                                </td>
                                <td class="text-end">
                                    <strong><?php echo number_format($valeurLigne, 0, ',', ' '); ?> Ar</strong>
                                </td>
                                <td>
                                    <?php if (!empty($d['date_besoin'])): ?>
                                        <small class="text-muted">
                                            <i class="bi bi-calendar3 me-1"></i>
                                            <?php echo date('d/m/Y', strtotime($d['date_besoin'])); ?>
                                        </small>
                                    <?php else: ?>
                                        <small class="text-muted">-</small>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-<?php echo $statusClass; ?>">
                                        <?php echo htmlspecialchars($d['status_name'] ?? 'N/A'); ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-light">
            <small class="text-muted">
                <i class="bi bi-info-circle me-1"></i>
                Affichage de <?php echo count($distributions); ?> distribution(s)
            </small>
        </div>
    </div>
<?php endif; ?>