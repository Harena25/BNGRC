<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <!-- En-tête avec titre -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="mb-0">Liste des villes</h2>
            </div>

            <!-- Tableau des villes -->
            <div class="card shadow-sm">
                <div class="card-body">
                    <?php if (empty($villes)): ?>
                        <div class="text-center py-5">
                            <i class="bi bi-geo-alt" style="font-size: 3rem; color: #ccc;"></i>
                            <p class="text-muted mt-3">Aucune ville enregistrée pour le moment.</p>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover table-striped align-middle">
                                <thead class="table-dark">
                                    <tr>
                                        <th>#</th>
                                        <th>Région</th>
                                        <th>Ville</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($villes as $v): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($v['id']); ?></td>
                                            <td>
                                                <span class="badge bg-info">
                                                    <?php echo htmlspecialchars($v['region'] ?? 'N/A'); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <strong><?php echo htmlspecialchars($v['libelle']); ?></strong>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Statistiques -->
                        <div class="mt-3 text-muted small">
                            <i class="bi bi-info-circle"></i>
                            Total : <?php echo count($villes); ?> ville(s) enregistrée(s)
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
