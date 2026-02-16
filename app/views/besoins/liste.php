<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <!-- En-tête avec titre et bouton d'ajout -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="mb-0">Liste des besoins</h2>
                <a href="/needs" class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i> Nouveau besoin
                </a>
            </div>

            <!-- Tableau des besoins -->
            <div class="card shadow-sm">
                <div class="card-body">
                    <?php if (empty($besoins)): ?>
                        <div class="text-center py-5">
                            <i class="bi bi-card-checklist" style="font-size: 3rem; color: #ccc;"></i>
                            <p class="text-muted mt-3">Aucun besoin enregistré pour le moment.</p>
                            <a href="/needs" class="btn btn-primary mt-2">
                                <i class="bi bi-plus-circle"></i> Ajouter le premier besoin
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover table-striped align-middle">
                                <thead class="table-dark">
                                    <tr>
                                        <th>#</th>
                                        <th>Date</th>
                                        <th>Région</th>
                                        <th>Ville</th>
                                        <th>Catégorie</th>
                                        <th>Article</th>
                                        <th class="text-end">Qté initiale</th>
                                        <th class="text-end">Reste</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($besoins as $b): 
                                        $qteInit = $b['quantite_initiale'] ?? $b['quantite'];
                                        $qteReste = $b['quantite'];
                                        $statusClass = 'secondary';
                                        if (($b['status_id'] ?? 1) == 3) $statusClass = 'success';
                                        elseif (($b['status_id'] ?? 1) == 2) $statusClass = 'warning';
                                        elseif (($b['status_id'] ?? 1) == 1) $statusClass = 'danger';
                                    ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($b['id']); ?></td>
                                            <td><?php echo htmlspecialchars($b['date_besoin'] ?? ''); ?></td>
                                            <td><?php echo htmlspecialchars($b['region'] ?? ''); ?></td>
                                            <td><?php echo htmlspecialchars($b['ville'] ?? ''); ?></td>
                                            <td>
                                                <span class="badge bg-info">
                                                    <?php echo htmlspecialchars($b['categorie'] ?? 'N/A'); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <strong><?php echo htmlspecialchars($b['article'] ?? 'N/A'); ?></strong>
                                            </td>
                                            <td class="text-end">
                                                <span class="badge bg-primary">
                                                    <?php echo number_format($qteInit); ?>
                                                </span>
                                            </td>
                                            <td class="text-end">
                                                <?php if (($b['status_id'] ?? 1) == 3): ?>
                                                    <span class="text-success"><i class="bi bi-check-circle"></i></span>
                                                <?php elseif ($qteReste > 0): ?>
                                                    <span class="badge bg-warning text-dark">
                                                        <?php echo number_format($qteReste); ?>
                                                    </span>
                                                <?php else: ?>
                                                    <span class="text-muted">0</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <span class="badge bg-<?php echo $statusClass; ?>">
                                                    <?php echo htmlspecialchars($b['status'] ?? ''); ?>
                                                </span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Statistiques -->
                        <div class="mt-3 text-muted small">
                            <i class="bi bi-info-circle"></i>
                            Total : <?php echo count($besoins); ?> besoin(s) enregistré(s)
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

