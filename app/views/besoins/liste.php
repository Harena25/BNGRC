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
                                        <th>Quantité</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($besoins as $b): ?>
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
                                            <td>
                                                <span class="badge bg-success">
                                                    <?php echo number_format($b['quantite']); ?>
                                                </span>
                                            </td>
											<td>
												<?php
													$status = $b['status'] ?? '';
													$badgeClass = match($status) {
														'Satisfait' => 'bg-success',
														'Partiellement satisfait' => 'bg-warning',
														'Ouvert' => 'bg-danger',
														default => 'bg-secondary',
													};
												?>
												<span class="badge <?php echo $badgeClass; ?>">
													<?php echo htmlspecialchars($status); ?>
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

