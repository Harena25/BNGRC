<?php
// Variables: $cities, $besoinsByCity, $distributionsByCity, $stats, $recentBesoins, $recentDons
?>

<?php
// Show reset data message if just ran
if (isset($_SESSION['reset_success'])):
    $isSuccess = $_SESSION['reset_success'];
    $message = $_SESSION['reset_message'] ?? '';
    unset($_SESSION['reset_success'], $_SESSION['reset_message']);
    ?>
    <div class="alert alert-<?php echo $isSuccess ? 'success' : 'danger'; ?> alert-dismissible fade show mb-4" role="alert">
        <h5 class="alert-heading">
            <i class="bi bi-<?php echo $isSuccess ? 'check-circle-fill' : 'exclamation-triangle-fill'; ?> me-2"></i>
            <?php echo $isSuccess ? 'Réinitialisation réussie' : 'Erreur de réinitialisation'; ?>
        </h5>
        <p class="mb-0"><?php echo htmlspecialchars($message); ?></p>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<?php
// Show allocation result if just ran
if (isset($_GET['allocation']) && $_GET['allocation'] === 'done' && !empty($_SESSION['allocation_log'])):
    $log = $_SESSION['allocation_log'];
    unset($_SESSION['allocation_log'], $_SESSION['allocation_success']);
    ?>
    <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
        <h5 class="alert-heading"><i class="bi bi-check-circle-fill me-2"></i>Allocation terminée</h5>
        <hr>
        <ul class="mb-0 small">
            <?php foreach ($log as $line): ?>
                <li><?php echo htmlspecialchars($line); ?></li>
            <?php endforeach; ?>
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<div class="row g-3 mb-4">
    <div class="col-12">
        <h1 class="h3 mb-0">Tableau de bord</h1>
    </div>
</div>

<!-- ═══ STATS CARDS ═══ -->
<div class="row g-3 mb-4">
    <div class="col-6 col-md-4 col-lg-2">
        <div class="card shadow-sm h-100">
            <div class="card-body text-center">
                <i class="bi bi-geo-alt-fill fs-2" style="color:var(--bonbon-3);"></i>
                <h3 class="mb-0 mt-2"><?php echo number_format($stats['total_villes']); ?></h3>
                <small class="text-muted">Villes</small>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-4 col-lg-2">
        <div class="card shadow-sm h-100">
            <div class="card-body text-center">
                <i class="bi bi-clipboard-heart-fill fs-2" style="color:var(--bonbon-4);"></i>
                <h3 class="mb-0 mt-2"><?php echo number_format($stats['total_besoins']); ?></h3>
                <small class="text-muted">Besoins</small>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-4 col-lg-2">
        <div class="card shadow-sm h-100">
            <div class="card-body text-center">
                <i class="bi bi-exclamation-triangle-fill fs-2 text-warning"></i>
                <h3 class="mb-0 mt-2"><?php echo number_format($stats['besoins_ouverts']); ?></h3>
                <small class="text-muted">En attente</small>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-4 col-lg-2">
        <div class="card shadow-sm h-100">
            <div class="card-body text-center">
                <i class="bi bi-gift-fill fs-2" style="color:var(--bonbon-3);"></i>
                <h3 class="mb-0 mt-2"><?php echo number_format($stats['total_dons']); ?></h3>
                <small class="text-muted">Dons</small>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-4 col-lg-2">
        <div class="card shadow-sm h-100">
            <div class="card-body text-center">
                <i class="bi bi-truck fs-2" style="color:var(--bonbon-4);"></i>
                <h3 class="mb-0 mt-2"><?php echo number_format($stats['total_distributions']); ?></h3>
                <small class="text-muted">Distributions</small>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-4 col-lg-2">
        <div class="card shadow-sm h-100">
            <div class="card-body text-center">
                <i class="bi bi-cash-stack fs-2 text-success"></i>
                <h3 class="mb-0 mt-2"><?php echo number_format($stats['valeur_stock'], 0, ',', ' '); ?></h3>
                <small class="text-muted">Stock (Ar)</small>
            </div>
        </div>
    </div>
</div>

<!-- ═══ MAIN TABLE: VILLES + BESOINS + DISTRIBUTIONS ═══ -->
<div class="card shadow-sm mb-4">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <span><i class="bi bi-bar-chart-fill me-2"></i>Tableau de bord par ville</span>
        <div class="btn-group" role="group">
            <a href="<?php echo BASE_PATH; ?>/autoDistribution?mode=simulate&sortMode=date" class="btn btn-sm btn-light">
                <i class="bi bi-calendar-event"></i> Par Date
            </a>
            <a href="<?php echo BASE_PATH; ?>/autoDistribution?mode=simulate&sortMode=quantite" class="btn btn-sm btn-warning">
                <i class="bi bi-sort-numeric-up"></i> Par Quantité
            </a>
        </div>
        <a href="<?= BASE_PATH ?>/dashboard/resetData" class="btn btn-sm btn-light">
            <i class="bi bi-arrow-clockwise"></i>
            Réinitialiser données
        </a>
     
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover table-striped mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>Région</th>
                        <th>Ville</th>
                        <th class="text-center">Besoins</th>
                        <th class="text-center">Ouverts</th>
                        <th class="text-center">Partiels</th>
                        <th class="text-center">Satisfaits</th>
                        <th class="text-end">Qté besoins</th>
                        <th class="text-center">Distributions</th>
                        <th class="text-end">Qté distribuée</th>
                        <th class="text-end">Reste</th>
                        <th class="text-center">Couverture</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($villeResume as $v):
                        $couverture = $v['pourcentage_couverture'] ?? 0;
                        $progressColor = ($couverture >= 80) ? 'success' : (($couverture >= 40) ? 'warning' : 'danger');
                        ?>
                        <tr>
                            <td><span class="badge bg-info"><?php echo htmlspecialchars($v['region_name'] ?? '-'); ?></span>
                            </td>
                            <td><strong><?php echo htmlspecialchars($v['ville_name']); ?></strong></td>
                            <td class="text-center"><?php echo $v['nb_besoins'] ?? 0; ?></td>
                            <td class="text-center">
                                <?php if (($v['nb_ouverts'] ?? 0) > 0): ?>
                                    <span class="badge bg-warning text-dark"><?php echo $v['nb_ouverts']; ?></span>
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <?php if (($v['nb_partiels'] ?? 0) > 0): ?>
                                    <span class="badge"
                                        style="background:#ffc107;color:#5a3a44;"><?php echo $v['nb_partiels']; ?></span>
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <?php if (($v['nb_satisfaits'] ?? 0) > 0): ?>
                                    <span class="badge bg-success"><?php echo $v['nb_satisfaits']; ?></span>
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end"><?php echo number_format($v['qte_besoin_total'] ?? 0, 0, ',', ' '); ?></td>
                            <td class="text-center"><?php echo $v['nb_distributions'] ?? 0; ?></td>
                            <td class="text-end"><?php echo number_format($v['qte_distribuee_total'] ?? 0, 0, ',', ' '); ?>
                            </td>
                            <td class="text-end">
                                <?php $reste = $v['qte_reste'] ?? 0; ?>
                                <?php if ($reste > 0): ?>
                                    <span class="text-danger fw-bold"><?php echo number_format($reste, 0, ',', ' '); ?></span>
                                <?php else: ?>
                                    <span class="text-success"><i class="bi bi-check-circle"></i> 0</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="progress" style="height: 20px; min-width: 80px;">
                                    <div class="progress-bar bg-<?php echo $progressColor; ?>" role="progressbar"
                                        style="width: <?php echo min($couverture, 100); ?>%;"
                                        aria-valuenow="<?php echo $couverture; ?>" aria-valuemin="0" aria-valuemax="100">
                                        <?php echo $couverture; ?>%
                                    </div>
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
<div class="row g-4">
    <!-- Recent Besoins -->
    <div class="col-lg-6">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-primary text-white">
                <i class="bi bi-clock-history me-2"></i>Derniers besoins enregistrés
            </div>
            <div class="card-body p-0">
                <?php if (empty($recentBesoins)): ?>
                    <div class="p-3 text-muted">Aucun besoin enregistré.</div>
                <?php else: ?>
                    <ul class="list-group list-group-flush">
                        <?php foreach ($recentBesoins as $rb): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="fw-semibold"><?php echo htmlspecialchars($rb['article_name'] ?? ''); ?></span>
                                    <small
                                        class="text-muted ms-2">(<?php echo htmlspecialchars($rb['ville_name'] ?? ''); ?>)</small>
                                    <br>
                                    <small class="text-muted">Qté: <?php echo $rb['quantite']; ?> &bull;
                                        <?php echo $rb['date_besoin']; ?></small>
                                </div>
                                <?php
                                $statusClass = 'secondary';
                                if ($rb['status_id'] == 1)
                                    $statusClass = 'warning';
                                elseif ($rb['status_id'] == 2)
                                    $statusClass = 'info';
                                elseif ($rb['status_id'] == 3)
                                    $statusClass = 'success';
                                ?>
                                <span
                                    class="badge bg-<?php echo $statusClass; ?>"><?php echo htmlspecialchars($rb['status_name'] ?? ''); ?></span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
            <div class="card-footer text-end">
                <a href="<?php echo BASE_PATH; ?>/needs/list" class="btn btn-sm btn-outline-primary">Voir tous</a>
            </div>
        </div>
    </div>

    <!-- Recent Dons -->
    <div class="col-lg-6">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-primary text-white">
                <i class="bi bi-gift me-2"></i>Derniers dons reçus
            </div>
            <div class="card-body p-0">
                <?php if (empty($recentDons)): ?>
                    <div class="p-3 text-muted">Aucun don enregistré.</div>
                <?php else: ?>
                    <ul class="list-group list-group-flush">
                        <?php foreach ($recentDons as $rd): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="fw-semibold"><?php echo htmlspecialchars($rd['article_name'] ?? ''); ?></span>
                                    <br>
                                    <small class="text-muted">Qté: <?php echo $rd['quantite_donnee']; ?> &bull;
                                        <?php echo $rd['date_don']; ?></small>
                                </div>
                                <span class="badge bg-success"><i class="bi bi-check2"></i> Reçu</span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
            <div class="card-footer text-end">
                <a href="<?php echo BASE_PATH; ?>/dons" class="btn btn-sm btn-outline-primary">Voir tous</a>
            </div>
        </div>
    </div>
</div>