<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <!-- En-tête avec titre et bouton d'ajout -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="mb-0">Liste des dons</h2>
                <a href="<?= BASE_PATH; ?>/dons/create" class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i> Ajouter un don
                </a>
            </div>

            <!-- Messages de succès -->
            <?php if (isset($_GET['success'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?php
                    if ($_GET['success'] == 'don_enregistre') {
                        echo 'Don enregistré avec succès !';
                    } elseif ($_GET['success'] == 'don_modifie') {
                        echo 'Don modifié avec succès !';
                    } elseif ($_GET['success'] == 'don_supprime') {
                        echo 'Don supprimé avec succès !';
                    }
                    ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <!-- Tableau des dons -->
            <div class="card shadow-sm">
                <div class="card-body">
                    <?php if (empty($dons)): ?>
                        <div class="text-center py-5">
                            <i class="bi bi-inbox" style="font-size: 3rem; color: #ccc;"></i>
                            <p class="text-muted mt-3">Aucun don enregistré pour le moment.</p>
                            <a href="<?= BASE_PATH; ?>/dons/create" class="btn btn-primary mt-2">
                                <i class="bi bi-plus-circle"></i> Ajouter le premier don
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover table-striped align-middle">
                                <thead class="table-dark">
                                    <tr>
                                        <th>#</th>
                                        <th>Article</th>
                                        <th>Catégorie</th>
                                        <th>Quantité</th>
                                        <th>Prix unitaire</th>
                                        <th>Valeur totale</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($dons as $don): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($don['id']); ?></td>
                                            <td>
                                                <strong><?php echo htmlspecialchars($don['article_libelle'] ?? 'N/A'); ?></strong>
                                            </td>
                                            <td>
                                                <span class="badge bg-info">
                                                    <?php echo htmlspecialchars($don['categorie_libelle'] ?? 'N/A'); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge bg-success">
                                                    <?php echo number_format($don['quantite_donnee']); ?>
                                                </span>
                                            </td>
                                            <td><?php echo number_format($don['prix_unitaire'] ?? 0, 2); ?> Ar</td>
                                            <td>
                                                <strong>
                                                    <?php
                                                    $valeur = ($don['quantite_donnee'] ?? 0) * ($don['prix_unitaire'] ?? 0);
                                                    echo number_format($valeur, 2);
                                                    ?> Ar
                                                </strong>
                                            </td>
                                            <td><?php echo date('d/m/Y', strtotime($don['date_don'])); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                                <tfoot class="table-light">
                                    <tr>
                                        <th colspan="5" class="text-end">Total général :</th>
                                        <th>
                                            <?php
                                            $total = 0;
                                            foreach ($dons as $don) {
                                                $total += ($don['quantite_donnee'] ?? 0) * ($don['prix_unitaire'] ?? 0);
                                            }
                                            echo number_format($total, 2);
                                            ?> Ar
                                        </th>
                                        <th colspan="2"></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <!-- Statistiques -->
                        <div class="mt-3 text-muted small">
                            <i class="bi bi-info-circle"></i>
                            Total : <?php echo count($dons); ?> don(s) enregistré(s)
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>