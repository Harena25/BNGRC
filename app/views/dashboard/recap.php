<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Récapitulatif — Besoins & Montants</h2>
        <div>
            <select id="villeFilter" class="form-select d-inline-block" style="width:220px">
                <option value="">Toutes les villes</option>
                <?php foreach ($villes as $v): ?>
                    <option value="<?php echo $v['id']; ?>"><?php echo htmlspecialchars($v['libelle']); ?></option>
                <?php endforeach; ?>
            </select>
            <button id="refreshBtn" class="btn btn-primary ms-2">Actualiser</button>
        </div>
    </div>

    <div id="recapTotals" class="mb-3">
        <!-- Totaux chargés par AJAX -->
    </div>

    <div class="table-responsive">
        <table class="table table-sm table-striped table-hover" id="recapTable">
            <thead>
                <tr>
                    <th>Région</th>
                    <th>Ville</th>
                    <th class="text-end">Besoin total (MAD)</th>
                    <th class="text-end">Distribué (dons) (MAD)</th>
                    <th class="text-end">Achats (MAD)</th>
                    <th class="text-end">Montant satisfait (MAD)</th>
                    <th class="text-end">Montant restant (MAD)</th>
                    <th class="text-end">Couverture (%)</th>
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
                        <td class="text-end"><?php echo number_format($besoin, 2); ?></td>
                        <td class="text-end text-success"><?php echo number_format($dist, 2); ?></td>
                        <td class="text-end text-info"><?php echo number_format($ach, 2); ?></td>
                        <td class="text-end"><?php echo number_format($satisfait, 2); ?></td>
                        <td class="text-end text-danger"><?php echo number_format($restant, 2); ?></td>
                        <td class="text-end"><?php echo number_format($couverture, 1); ?>%</td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <script src="/assets/js/recap.js"></script>
</div>