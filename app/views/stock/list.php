<?php
// $stocks provided by controller
// Séparation argent / autres
$stocksArgent = [];
$stocksAutres = [];
$totalValAutres = 0;
$totalQtyAutres = 0;
$totalQtyArgent = 0;

foreach ($stocks as $s) {
    $categorie_id = intval($s['categorie_id'] ?? 0);
    if ($categorie_id === 3) {
        $stocksArgent[] = $s;
        $totalQtyArgent += intval($s['quantite_stock'] ?? 0);
    } else {
        $stocksAutres[] = $s;
        $totalValAutres += floatval($s['valeur'] ?? 0);
        $totalQtyAutres += intval($s['quantite_stock'] ?? 0);
    }
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-box-seam me-2"></i>État du stock</h2>
</div>

<?php if (empty($stocks)): ?>
    <div class="alert alert-warning"><i class="bi bi-exclamation-triangle me-2"></i>Aucun stock enregistré.</div>
<?php else: ?>

    <!-- Section Argent -->
    <?php if (!empty($stocksArgent)): ?>
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-success text-white">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-cash-coin me-2"></i>Stock Argent</h5>
                    <span class="badge bg-light text-dark fs-6">Total: <?php echo number_format($totalQtyArgent, 2); ?> Ar</span>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-striped mb-0">
                        <thead class="table-success">
                            <tr>
                                <th>#</th>
                                <th>Catégorie</th>
                                <th>Article</th>
                                <th class="text-end">Valeur (Ar)</th>
                                <th class="text-center">Niveau</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php 
                        $idx = 1;
                        foreach ($stocksArgent as $stock): 
                            $qty = floatval($stock['quantite_stock']);
                            $niveau = ($qty > 50000) ? 'success' : (($qty > 10000) ? 'warning' : 'danger');
                        ?>
                            <tr>
                                <td><?php echo $idx++; ?></td>
                                <td><span class="badge bg-success"><?php echo htmlspecialchars($stock['categorie_name'] ?? '-'); ?></span></td>
                                <td><strong><?php echo htmlspecialchars($stock['article_name'] ?? '-'); ?></strong></td>
                                <td class="text-end"><strong><?php echo number_format($qty, 2); ?> Ar</strong></td>
                                <td class="text-center">
                                    <span class="badge bg-<?php echo $niveau; ?>">
                                        <?php 
                                        if ($niveau === 'success') echo 'Bon';
                                        elseif ($niveau === 'warning') echo 'Moyen';
                                        else echo 'Faible';
                                        ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <th colspan="3" class="text-end">Total Argent</th>
                                <th class="text-end"><?php echo number_format($totalQtyArgent, 2); ?> Ar</th>
                                <th></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Section Articles Nature/Matériaux -->
    <?php if (!empty($stocksAutres)): ?>
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-box-seam me-2"></i>Stock Nature et Matériaux</h5>
                    <div>
                        <span class="badge bg-light text-dark fs-6 me-2">Total: <?php echo number_format($totalQtyAutres); ?> unités</span>
                        <span class="badge bg-warning text-dark fs-6">Valeur: <?php echo number_format($totalValAutres, 0, ',', ' '); ?> Ar</span>
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-striped mb-0">
                        <thead class="table-primary">
                            <tr>
                                <th>#</th>
                                <th>Catégorie</th>
                                <th>Article</th>
                                <th class="text-end">Prix unitaire</th>
                                <th class="text-end">Quantité</th>
                                <th class="text-end">Valeur</th>
                                <th class="text-center">Niveau</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php 
                        $idx = 1;
                        foreach ($stocksAutres as $stock): 
                            $qty = intval($stock['quantite_stock']);
                            $valeur = floatval($stock['valeur'] ?? 0);
                            $niveau = ($qty > 100) ? 'success' : (($qty > 20) ? 'warning' : 'danger');
                        ?>
                            <tr>
                                <td><?php echo $idx++; ?></td>
                                <td><span class="badge bg-info"><?php echo htmlspecialchars($stock['categorie_name'] ?? '-'); ?></span></td>
                                <td><strong><?php echo htmlspecialchars($stock['article_name'] ?? '-'); ?></strong></td>
                                <td class="text-end"><?php echo number_format($stock['prix_unitaire'] ?? 0, 2, ',', ' '); ?></td>
                                <td class="text-end"><?php echo number_format($qty); ?></td>
                                <td class="text-end"><?php echo number_format($valeur, 0, ',', ' '); ?></td>
                                <td class="text-center">
                                    <span class="badge bg-<?php echo $niveau; ?>">
                                        <?php 
                                        if ($niveau === 'success') echo 'Bon';
                                        elseif ($niveau === 'warning') echo 'Moyen';
                                        else echo 'Faible';
                                        ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <th colspan="4" class="text-end">Total</th>
                                <th class="text-end"><?php echo number_format($totalQtyAutres); ?></th>
                                <th class="text-end"><?php echo number_format($totalValAutres, 0, ',', ' '); ?></th>
                                <th></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    <?php endif; ?>

<?php endif; ?>
