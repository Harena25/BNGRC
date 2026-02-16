<div class="container-fluid py-4">
    <!-- Header Section -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col">
                    <h2 class="card-title mb-0">
                        <i class="bi bi-pie-chart-fill text-primary me-2"></i>
                        Récapitulatif — Besoins & Montants
                    </h2>
                    <p class="text-muted mb-0">Visualisation des besoins et satisfactions par ville</p>
                </div>
                <div class="col-auto">
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-geo-alt"></i></span>
                        <select id="villeFilter" class="form-select" style="min-width:220px">
                            <option value="">Toutes les villes</option>
                            <?php foreach ($villes as $v): ?>
                                <option value="<?php echo $v['id']; ?>"><?php echo htmlspecialchars($v['libelle']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <button id="refreshBtn" class="btn btn-primary">
                            <i class="bi bi-arrow-clockwise me-1"></i>Actualiser
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Totals Section (hidden until data is loaded) -->
    <div id="recapTotals" class="mb-4 d-none">
        <!-- Totaux chargés par AJAX (masqué jusqu'à actualisation) -->
    </div>

    <!-- Data Table Section -->
    <div class="card shadow-sm">
        <div class="card-header bg-light">
            <h5 class="card-title mb-0">
                <i class="bi bi-table me-2"></i>Détail par ville
            </h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-sm table-striped table-hover mb-0" id="recapTable">
                    <thead class="table-dark">
                        <tr>
                            <th><i class="bi bi-flag me-1"></i>Région</th>
                            <th><i class="bi bi-geo-alt me-1"></i>Ville</th>
                            <th class="text-end"><i class="bi bi-currency-exchange me-1"></i>Besoin total (MAD)</th>
                            <th class="text-end"><i class="bi bi-gift me-1"></i>Distribué (dons) (MAD)</th>
                            <th class="text-end"><i class="bi bi-cart3 me-1"></i>Achats (MAD)</th>
                            <th class="text-end"><i class="bi bi-check-circle me-1"></i>Montant satisfait (MAD)</th>
                            <th class="text-end"><i class="bi bi-exclamation-triangle me-1"></i>Montant restant (MAD)
                            </th>
                            <th class="text-end"><i class="bi bi-percent me-1"></i>Couverture (%)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($rows as $row):
                            $dist = (float) $row['montant_satisfait_distrib'];
                            $ach = (float) $row['montant_achats'];
                            $satisfait = (float) $row['montant_satisfait_total'];
                            $besoin = (float) $row['montant_besoin_total'];
                            $restant = (float) $row['montant_restant'];
                            $couverture = $besoin > 0 ? ($satisfait / $besoin) * 100 : 0;
                            ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['region_name']); ?></td>
                                <td><?php echo htmlspecialchars($row['ville_name']); ?></td>
                                <td class="text-end fw-bold"><?php echo number_format($besoin, 2); ?></td>
                                <td class="text-end text-success fw-semibold"><?php echo number_format($dist, 2); ?></td>
                                <td class="text-end text-info fw-semibold"><?php echo number_format($ach, 2); ?></td>
                                <td class="text-end fw-bold"><?php echo number_format($satisfait, 2); ?></td>
                                <td class="text-end text-danger fw-semibold"><?php echo number_format($restant, 2); ?></td>
                                <td class="text-end">
                                    <?php
                                    $badgeClass = $couverture >= 75 ? 'success' : ($couverture >= 40 ? 'warning' : 'secondary');
                                    ?>
                                    <span
                                        class="badge bg-<?php echo $badgeClass; ?> fs-6"><?php echo number_format($couverture, 1); ?>%</span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="/assets/js/recap.js"></script>

    <!-- Custom styles for this page -->
    <style>
        .table th {
            font-weight: 600;
            font-size: 0.9em;
        }

        .table td {
            vertical-align: middle;
        }

        .badge {
            font-size: 0.8em;
            padding: 0.4em 0.8em;
        }

        #recapTable tbody tr:hover {
            background-color: var(--bs-light) !important;
        }
    </style>
</div>