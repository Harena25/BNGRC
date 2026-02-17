<?php
// $stocks provided by controller
$totalVal = 0;
$totalQty = 0;
foreach ($stocks as $s) {
    $totalVal += floatval($s['valeur'] ?? 0);
    $totalQty += intval($s['quantite_stock'] ?? 0);
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-box-seam me-2"></i>État du stock</h2>
    <div>
        <span class="badge bg-info fs-6 me-2">Total: <?php echo number_format($totalQty); ?> unités</span>
        <span class="badge bg-success fs-6">Valeur: <?php echo number_format($totalVal, 0, ',', ' '); ?> Ar</span>
    </div>
</div>

<?php if (empty($stocks)): ?>
    <div class="alert alert-warning"><i class="bi bi-exclamation-triangle me-2"></i>Aucun stock enregistré.</div>
<?php else: ?>
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped mb-0">
                    <thead class="table-dark">
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
                    <?php foreach ($stocks as $idx => $stock): 
                        $qty = intval($stock['quantite_stock']);
                        $valeur = floatval($stock['valeur'] ?? 0);
                        $niveau = ($qty > 100) ? 'success' : (($qty > 20) ? 'warning' : 'danger');
                    ?>
                        <tr>
                            <td><?php echo $idx + 1; ?></td>
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
                            <th class="text-end"><?php echo number_format($totalQty); ?></th>
                            <th class="text-end"><?php echo number_format($totalVal, 0, ',', ' '); ?></th>
                            <th></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
<?php endif; ?>
