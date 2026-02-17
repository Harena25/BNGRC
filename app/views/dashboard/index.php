<?php
// Variables: $villeResume, $stats, $recentBesoins, $recentDons
?>

<?php
// Show reset data message
if (isset($_SESSION['reset_success'])):
    $isSuccess = $_SESSION['reset_success'];
    $message = $_SESSION['reset_message'] ?? '';
    unset($_SESSION['reset_success'], $_SESSION['reset_message']);
    ?>
    <div class="alert alert-<?php echo $isSuccess ? 'success' : 'danger'; ?> alert-dismissible fade show mb-3" role="alert">
        <div class="d-flex align-items-center gap-2">
            <i class="bi bi-<?php echo $isSuccess ? 'check-circle-fill' : 'exclamation-triangle-fill'; ?> fs-5"></i>
            <div>
                <strong><?php echo $isSuccess ? 'Réinitialisation réussie' : 'Erreur'; ?></strong>
                <span class="ms-1"><?php echo htmlspecialchars($message); ?></span>
            </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<?php
// Show allocation result
if (isset($_GET['allocation']) && $_GET['allocation'] === 'done' && !empty($_SESSION['allocation_log'])):
    $log = $_SESSION['allocation_log'];
    unset($_SESSION['allocation_log'], $_SESSION['allocation_success']);
    ?>
    <div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
        <div class="d-flex align-items-start gap-2">
            <i class="bi bi-check-circle-fill fs-5 mt-1"></i>
            <div>
                <strong>Allocation terminée</strong>
                <ul class="mb-0 mt-1 small ps-3">
                    <?php foreach ($log as $line): ?>
                        <li><?php echo htmlspecialchars($line); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<!-- ═══ PAGE HEADER ═══ -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-1">Tableau de bord</h1>
        <p class="text-muted mb-0" style="font-size:0.82rem;">Vue d'ensemble des besoins, dons et distributions</p>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= BASE_PATH ?>/dashboard/resetData" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-clockwise me-1"></i>Réinitialiser
        </a>
    </div>
</div>

<!-- ═══ STATS CARDS ═══ -->
<div class="row g-3 mb-4">
    <div class="col-6 col-md-4 col-lg-2">
        <div class="card h-100">
            <div class="card-body py-3 px-3">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <div class="stat-icon-wrap" style="background: rgba(108,117,125,.08); color: #6c757d;">
                        <i class="bi bi-geo-alt-fill"></i>
                    </div>
                    <small class="text-muted fw-medium">Villes</small>
                </div>
                <h3 class="mb-0 fw-bold" style="font-size:1.5rem;"><?php echo number_format($stats['total_villes']); ?></h3>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-4 col-lg-2">
        <div class="card h-100">
            <div class="card-body py-3 px-3">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <div class="stat-icon-wrap" style="background: rgba(13,110,253,.08); color: #0d6efd;">
                        <i class="bi bi-clipboard-heart-fill"></i>
                    </div>
                    <small class="text-muted fw-medium">Besoins</small>
                </div>
                <h3 class="mb-0 fw-bold" style="font-size:1.5rem;"><?php echo number_format($stats['total_besoins']); ?></h3>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-4 col-lg-2">
        <div class="card h-100">
            <div class="card-body py-3 px-3">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <div class="stat-icon-wrap" style="background: rgba(255,193,7,.1); color: #e6a800;">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                    </div>
                    <small class="text-muted fw-medium">En attente</small>
                </div>
                <h3 class="mb-0 fw-bold" style="font-size:1.5rem;"><?php echo number_format($stats['besoins_ouverts']); ?></h3>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-4 col-lg-2">
        <div class="card h-100">
            <div class="card-body py-3 px-3">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <div class="stat-icon-wrap" style="background: rgba(25,135,84,.08); color: #198754;">
                        <i class="bi bi-gift-fill"></i>
                    </div>
                    <small class="text-muted fw-medium">Dons</small>
                </div>
                <h3 class="mb-0 fw-bold" style="font-size:1.5rem;"><?php echo number_format($stats['total_dons']); ?></h3>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-4 col-lg-2">
        <div class="card h-100">
            <div class="card-body py-3 px-3">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <div class="stat-icon-wrap" style="background: rgba(111,66,193,.08); color: #6f42c1;">
                        <i class="bi bi-truck"></i>
                    </div>
                    <small class="text-muted fw-medium">Distributions</small>
                </div>
                <h3 class="mb-0 fw-bold" style="font-size:1.5rem;"><?php echo number_format($stats['total_distributions']); ?></h3>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-4 col-lg-2">
        <div class="card h-100">
            <div class="card-body py-3 px-3">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <div class="stat-icon-wrap" style="background: rgba(25,135,84,.08); color: #198754;">
                        <i class="bi bi-cash-stack"></i>
                    </div>
                    <small class="text-muted fw-medium">Stock (Ar)</small>
                </div>
                <h3 class="mb-0 fw-bold" style="font-size:1.3rem;"><?php echo number_format($stats['valeur_stock'], 0, ',', ' '); ?></h3>
            </div>
        </div>
    </div>
</div>

<style>
.stat-icon-wrap {
    width: 28px; height: 28px;
    border-radius: 6px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.85rem;
    flex-shrink: 0;
}
</style>

<!-- ═══ MAIN TABLE ═══ -->
<div class="card mb-4">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center flex-wrap gap-2">
        <span class="d-flex align-items-center gap-2">
            <i class="bi bi-bar-chart-fill"></i>
            <span>Résumé par ville</span>
        </span>
        <div class="d-flex gap-1 flex-wrap">
            <div class="btn-group btn-group-sm" role="group">
                <a href="<?php echo BASE_PATH; ?>/autoDistribution?mode=simulate&sortMode=date" class="btn btn-light btn-sm">
                    <i class="bi bi-calendar-event me-1"></i>Date
                </a>
                <a href="<?php echo BASE_PATH; ?>/autoDistribution?mode=simulate&sortMode=quantite" class="btn btn-warning btn-sm">
                    <i class="bi bi-sort-numeric-up me-1"></i>Quantité
                </a>
                <a href="<?php echo BASE_PATH; ?>/autoDistribution?mode=simulate&sortMode=proportionnelle" class="btn btn-success btn-sm">
                    <i class="bi bi-pie-chart me-1"></i>Proport.
                </a>
            </div>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover table-striped mb-0 align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>Région</th>
                        <th>Ville</th>
                        <th class="text-center">Besoins</th>
                        <th class="text-center">Ouverts</th>
                        <th class="text-center">Partiels</th>
                        <th class="text-center">Satisfaits</th>
                        <th class="text-end">Qté besoins</th>
                        <th class="text-center">Distrib.</th>
                        <th class="text-end">Qté dist.</th>
                        <th class="text-end">Reste</th>
                        <th class="text-center" style="min-width:100px;">Couverture</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($villeResume as $v):
                        $couverture = $v['pourcentage_couverture'] ?? 0;
                        $progressColor = ($couverture >= 80) ? 'success' : (($couverture >= 40) ? 'warning' : 'danger');
                        ?>
                        <tr>
                            <td><span class="badge bg-info"><?php echo htmlspecialchars($v['region_name'] ?? '-'); ?></span></td>
                            <td class="fw-semibold"><?php echo htmlspecialchars($v['ville_name']); ?></td>
                            <td class="text-center"><?php echo $v['nb_besoins'] ?? 0; ?></td>
                            <td class="text-center">
                                <?php if (($v['nb_ouverts'] ?? 0) > 0): ?>
                                    <span class="badge bg-warning text-dark"><?php echo $v['nb_ouverts']; ?></span>
                                <?php else: ?>
                                    <span class="text-muted">—</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <?php if (($v['nb_partiels'] ?? 0) > 0): ?>
                                    <span class="badge bg-warning bg-opacity-75 text-dark"><?php echo $v['nb_partiels']; ?></span>
                                <?php else: ?>
                                    <span class="text-muted">—</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <?php if (($v['nb_satisfaits'] ?? 0) > 0): ?>
                                    <span class="badge bg-success"><?php echo $v['nb_satisfaits']; ?></span>
                                <?php else: ?>
                                    <span class="text-muted">—</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end"><?php echo number_format($v['qte_besoin_total'] ?? 0, 0, ',', ' '); ?></td>
                            <td class="text-center"><?php echo $v['nb_distributions'] ?? 0; ?></td>
                            <td class="text-end"><?php echo number_format($v['qte_distribuee_total'] ?? 0, 0, ',', ' '); ?></td>
                            <td class="text-end">
                                <?php $reste = $v['qte_reste'] ?? 0; ?>
                                <?php if ($reste > 0): ?>
                                    <span class="text-danger fw-semibold"><?php echo number_format($reste, 0, ',', ' '); ?></span>
                                <?php else: ?>
                                    <span class="text-success"><i class="bi bi-check-circle-fill"></i> 0</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="progress flex-grow-1" style="height: 6px;">
                                        <div class="progress-bar bg-<?php echo $progressColor; ?>" role="progressbar"
                                            style="width: <?php echo min($couverture, 100); ?>%;"
                                            aria-valuenow="<?php echo $couverture; ?>" aria-valuemin="0" aria-valuemax="100">
                                        </div>
                                    </div>
                                    <small class="fw-semibold text-nowrap" style="font-size:0.72rem; min-width:32px; text-align:right;"><?php echo $couverture; ?>%</small>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- ═══ RECENT ACTIVITIES ═══ -->
<div class="row g-3">
    <!-- Recent Besoins -->
    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-header bg-primary text-white d-flex align-items-center gap-2">
                <i class="bi bi-clock-history"></i>
                <span>Derniers besoins</span>
            </div>
            <div class="card-body p-0">
                <?php if (empty($recentBesoins)): ?>
                    <div class="p-3 text-muted small">Aucun besoin enregistré.</div>
                <?php else: ?>
                    <div class="list-group list-group-flush">
                        <?php foreach ($recentBesoins as $rb): ?>
                            <div class="list-group-item d-flex justify-content-between align-items-center py-2">
                                <div>
                                    <span class="fw-semibold" style="font-size:0.85rem;"><?php echo htmlspecialchars($rb['article_name'] ?? ''); ?></span>
                                    <small class="text-muted ms-1">(<?php echo htmlspecialchars($rb['ville_name'] ?? ''); ?>)</small>
                                    <br>
                                    <small class="text-muted" style="font-size:0.75rem;">
                                        Qté: <?php echo $rb['quantite']; ?> &bull; <?php echo $rb['date_besoin']; ?>
                                    </small>
                                </div>
                                <?php
                                $statusClass = 'secondary';
                                if ($rb['status_id'] == 1) $statusClass = 'warning';
                                elseif ($rb['status_id'] == 2) $statusClass = 'info';
                                elseif ($rb['status_id'] == 3) $statusClass = 'success';
                                ?>
                                <span class="badge bg-<?php echo $statusClass; ?>"><?php echo htmlspecialchars($rb['status_name'] ?? ''); ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
            <div class="card-footer text-end">
                <a href="<?php echo BASE_PATH; ?>/needs/list" class="btn btn-sm btn-outline-primary">Voir tous</a>
            </div>
        </div>
    </div>

    <!-- Recent Dons -->
    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-header bg-primary text-white d-flex align-items-center gap-2">
                <i class="bi bi-gift"></i>
                <span>Derniers dons</span>
            </div>
            <div class="card-body p-0">
                <?php if (empty($recentDons)): ?>
                    <div class="p-3 text-muted small">Aucun don enregistré.</div>
                <?php else: ?>
                    <div class="list-group list-group-flush">
                        <?php foreach ($recentDons as $rd): ?>
                            <div class="list-group-item d-flex justify-content-between align-items-center py-2">
                                <div>
                                    <span class="fw-semibold" style="font-size:0.85rem;"><?php echo htmlspecialchars($rd['article_name'] ?? ''); ?></span>
                                    <br>
                                    <small class="text-muted" style="font-size:0.75rem;">
                                        Qté: <?php echo $rd['quantite_donnee']; ?> &bull; <?php echo $rd['date_don']; ?>
                                    </small>
                                </div>
                                <span class="badge bg-success"><i class="bi bi-check2"></i> Reçu</span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
            <div class="card-footer text-end">
                <a href="<?php echo BASE_PATH; ?>/dons" class="btn btn-sm btn-outline-primary">Voir tous</a>
            </div>
        </div>
    </div>
</div>
