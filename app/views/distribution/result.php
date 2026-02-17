<?php
// Variables: $log, $summary (provided by controller)
$totalSatisfied = $summary['satisfied'] ?? 0;
$totalPartial = $summary['partial'] ?? 0;
$totalSkipped = $summary['skipped'] ?? 0;
$totalProcessed = $totalSatisfied + $totalPartial + $totalSkipped;
$sortModeLabel = ($sortMode ?? 'date') === 'quantite' ? 'Par Quantité (du plus petit au plus grand)' : 'Par Date (ordre chronologique)';
?>

<div class="container py-4" style="max-width: 900px;">

    <!-- Header -->
    <div class="text-center mb-4">
        <div class="d-inline-block p-3 rounded-circle mb-3" style="background: var(--bonbon-1);">
            <i class="bi bi-lightning-charge-fill fs-1" style="color: var(--bonbon-4);"></i>
        </div>
        <h2 class="mb-1">Allocation automatique terminée</h2>
        <p class="text-muted">Voici le détail des opérations effectuées</p>
        <span class="badge bg-info">
            <i class="bi bi-<?php echo $sortMode === 'quantite' ? 'sort-numeric-up' : 'calendar-event'; ?> me-1"></i>
            <?php echo $sortModeLabel; ?>
        </span>
    </div>

    <!-- Summary Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card shadow-sm border-0 h-100" style="background: linear-gradient(135deg, #d4edda, #c3e6cb);">
                <div class="card-body text-center">
                    <i class="bi bi-check-circle-fill fs-2 text-success"></i>
                    <h3 class="mb-0 mt-2"><?php echo $totalSatisfied; ?></h3>
                    <small class="text-muted">Satisfaits totalement</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm border-0 h-100" style="background: linear-gradient(135deg, #fff3cd, #ffeeba);">
                <div class="card-body text-center">
                    <i class="bi bi-pie-chart-fill fs-2 text-warning"></i>
                    <h3 class="mb-0 mt-2"><?php echo $totalPartial; ?></h3>
                    <small class="text-muted">Partiellement satisfaits</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm border-0 h-100" style="background: linear-gradient(135deg, #f8d7da, #f5c6cb);">
                <div class="card-body text-center">
                    <i class="bi bi-x-circle-fill fs-2 text-danger"></i>
                    <h3 class="mb-0 mt-2"><?php echo $totalSkipped; ?></h3>
                    <small class="text-muted">Sans stock disponible</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Progress -->
    <?php if ($totalProcessed > 0):
        $pctSatisfied = round(($totalSatisfied / $totalProcessed) * 100);
        $pctPartial = round(($totalPartial / $totalProcessed) * 100);
        $pctSkipped = round(($totalSkipped / $totalProcessed) * 100);
        ?>
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <h6 class="mb-3"><i class="bi bi-bar-chart me-2"></i>Répartition des résultats</h6>
                <div class="progress" style="height: 30px;">
                    <div class="progress-bar bg-success" style="width: <?php echo $pctSatisfied; ?>%;" title="Satisfaits">
                        <?php if ($pctSatisfied > 10): ?>        <?php echo $pctSatisfied; ?>%<?php endif; ?>
                    </div>
                    <div class="progress-bar bg-warning" style="width: <?php echo $pctPartial; ?>%;" title="Partiels">
                        <?php if ($pctPartial > 10): ?>        <?php echo $pctPartial; ?>%<?php endif; ?>
                    </div>
                    <div class="progress-bar bg-danger" style="width: <?php echo $pctSkipped; ?>%;" title="Sans stock">
                        <?php if ($pctSkipped > 10): ?>        <?php echo $pctSkipped; ?>%<?php endif; ?>
                    </div>
                </div>
                <div class="d-flex justify-content-between mt-2 small text-muted">
                    <span><i class="bi bi-circle-fill text-success me-1"></i>Satisfaits</span>
                    <span><i class="bi bi-circle-fill text-warning me-1"></i>Partiels</span>
                    <span><i class="bi bi-circle-fill text-danger me-1"></i>Sans stock</span>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Detailed Log -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-primary text-white">
            <i class="bi bi-journal-text me-2"></i>Journal détaillé des opérations
        </div>
        <div class="card-body p-0">
            <div class="list-group list-group-flush" style="max-height: 400px; overflow-y: auto;">
                <?php if (empty($log)): ?>
                    <div class="list-group-item text-muted">Aucune opération effectuée.</div>
                <?php else: ?>
                    <?php foreach ($log as $idx => $line):
                        // Determine icon and color based on content
                        $icon = 'bi-info-circle';
                        $color = 'text-muted';
                        if (strpos($line, 'satisfait totalement') !== false) {
                            $icon = 'bi-check-circle-fill';
                            $color = 'text-success';
                        } elseif (strpos($line, 'partiellement') !== false) {
                            $icon = 'bi-pie-chart-fill';
                            $color = 'text-warning';
                        } elseif (strpos($line, 'pas de stock') !== false) {
                            $icon = 'bi-x-circle-fill';
                            $color = 'text-danger';
                        } elseif (strpos($line, 'completed') !== false || strpos($line, 'terminée') !== false) {
                            $icon = 'bi-flag-fill';
                            $color = 'text-primary';
                        } elseif (strpos($line, 'Error') !== false) {
                            $icon = 'bi-exclamation-triangle-fill';
                            $color = 'text-danger';
                        }
                        ?>
                        <div class="list-group-item d-flex align-items-start">
                            <span class="badge bg-light text-dark me-3"><?php echo $idx + 1; ?></span>
                            <i class="bi <?php echo $icon; ?> <?php echo $color; ?> me-2 mt-1"></i>
                            <span><?php echo htmlspecialchars($line); ?></span>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Actions -->
    <?php if (isset($mode) && $mode === 'simulate'): ?>
        <!-- Simulation mode: show Annuler/Valider buttons -->
        <div class="alert alert-warning mb-4" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            <strong>Mode simulation:</strong> Aucune modification n'a ete effectuee dans la base de donnees.
            Cliquez sur <strong>Valider</strong> pour executer reellement la distribution.
        </div>
        <div class="d-flex justify-content-center gap-3">
            <a href="<?php echo BASE_PATH; ?>/dashboard" class="btn btn-outline-secondary btn-lg">
                <i class="bi bi-x-circle me-2"></i>Annuler
            </a>
            <a href="<?php echo BASE_PATH; ?>/autoDistribution?mode=execute&sortMode=<?php echo $sortMode ?? 'date'; ?>" class="btn btn-success btn-lg"
                onclick="return confirm('Confirmer l execution de la distribution automatique ?\n\nCette action va modifier la base de donnees.')">
                <i class="bi bi-check-circle-fill me-2"></i>Valider la distribution
            </a>
        </div>
    <?php else: ?>
        <!-- Execution mode: show normal navigation -->
        <div class="alert alert-success mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>
            <strong>Distribution executee avec succes!</strong> Les modifications ont ete enregistrees dans la base de
            donnees.
        </div>
        <div class="d-flex justify-content-center gap-3">
            <a href="<?php echo BASE_PATH; ?>/dashboard" class="btn btn-primary btn-lg">
                <i class="bi bi-speedometer2 me-2"></i>Retour au tableau de bord
            </a>
            <a href="<?php echo BASE_PATH; ?>/distribution" class="btn btn-outline-primary btn-lg">
                <i class="bi bi-table me-2"></i>Voir toutes les distributions
            </a>
        </div>
    <?php endif; ?>

</div>