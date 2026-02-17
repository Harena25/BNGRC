<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <!-- En-tête avec titre et bouton d'ajout -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="mb-0">Liste des besoins</h2>
                <a href="<?php echo BASE_PATH; ?>/needs" class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i> Nouveau besoin
                </a>
            </div>

            <!-- Filtres multicritères -->
            <?php 
            $hasActiveFilters = !empty($filtreRegionId) || !empty($filtreVilleId) || !empty($filtreArticleId) 
                                || !empty($filtreCategorieId) || !empty($filtreStatusId) 
                                || !empty($filtreDateMin) || !empty($filtreDateMax);
            ?>
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <form method="GET" action="<?php echo BASE_PATH; ?>/needs/list" class="row g-3 align-items-end">
                        
                        <!-- Ligne 1: Dates et Région/Ville -->
                        <div class="col-md-2">
                            <label for="date_min" class="form-label"><i class="bi bi-calendar-range"></i> Date début</label>
                            <input type="date" class="form-control" id="date_min" name="date_min" 
                                   value="<?php echo htmlspecialchars($filtreDateMin ?? ''); ?>">
                        </div>
                        <div class="col-md-2">
                            <label for="date_max" class="form-label"><i class="bi bi-calendar-range"></i> Date fin</label>
                            <input type="date" class="form-control" id="date_max" name="date_max" 
                                   value="<?php echo htmlspecialchars($filtreDateMax ?? ''); ?>">
                        </div>
                        <div class="col-md-2">
                            <label for="region_id" class="form-label"><i class="bi bi-map"></i> Région</label>
                            <select class="form-select" id="region_id" name="region_id">
                                <option value="">-- Toutes --</option>
                                <?php foreach ($regions as $r): ?>
                                    <option value="<?php echo $r['id']; ?>"
                                        <?php echo (isset($filtreRegionId) && $filtreRegionId == $r['id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($r['libelle']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="ville_id" class="form-label"><i class="bi bi-geo-alt"></i> Ville</label>
                            <select class="form-select" id="ville_id" name="ville_id">
                                <option value="">-- Toutes --</option>
                                <?php foreach ($villes as $v): ?>
                                    <option value="<?php echo $v['id']; ?>" data-region="<?php echo $v['region_id']; ?>"
                                        <?php echo (isset($filtreVilleId) && $filtreVilleId == $v['id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($v['libelle']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="article_id" class="form-label"><i class="bi bi-box"></i> Article</label>
                            <select class="form-select" id="article_id" name="article_id">
                                <option value="">-- Tous --</option>
                                <?php foreach ($articles as $art): ?>
                                    <option value="<?php echo $art['id']; ?>"
                                        <?php echo (isset($filtreArticleId) && $filtreArticleId == $art['id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($art['libelle']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="categorie_id" class="form-label"><i class="bi bi-tag"></i> Catégorie</label>
                            <select class="form-select" id="categorie_id" name="categorie_id">
                                <option value="">-- Toutes --</option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?php echo $cat['id']; ?>"
                                        <?php echo (isset($filtreCategorieId) && $filtreCategorieId == $cat['id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($cat['libelle']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <!-- Ligne 2: Statut et Boutons -->
                        <div class="col-md-3">
                            <label for="status_id" class="form-label"><i class="bi bi-flag"></i> Statut</label>
                            <select class="form-select" id="status_id" name="status_id">
                                <option value="">-- Tous --</option>
                                <?php foreach ($statuses as $st): ?>
                                    <option value="<?php echo $st['id']; ?>"
                                        <?php echo (isset($filtreStatusId) && $filtreStatusId == $st['id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($st['libelle']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-outline-primary w-100">
                                <i class="bi bi-search"></i> Filtrer
                            </button>
                        </div>
                        <?php if ($hasActiveFilters): ?>
                            <div class="col-md-3">
                                <a href="<?php echo BASE_PATH; ?>/needs/list" class="btn btn-outline-secondary w-100">
                                    <i class="bi bi-x-circle"></i> Réinitialiser
                                </a>
                            </div>
                        <?php endif; ?>
                    </form>
                </div>
            </div>

            <!-- Tableau des besoins -->
            <div class="card shadow-sm">
                <div class="card-body">
                    <?php if (empty($besoins)): ?>
                        <div class="text-center py-5">
                            <i class="bi bi-card-checklist" style="font-size: 3rem; color: #ccc;"></i>
                            <p class="text-muted mt-3">Aucun besoin enregistré pour le moment.</p>
                            <a href="<?php echo BASE_PATH; ?>/needs" class="btn btn-primary mt-2">
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
                                        if (($b['status_id'] ?? 1) == 3)
                                            $statusClass = 'success';
                                        elseif (($b['status_id'] ?? 1) == 2)
                                            $statusClass = 'warning';
                                        elseif (($b['status_id'] ?? 1) == 1)
                                            $statusClass = 'danger';
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

<script>
// Filtrer dynamiquement les villes selon la région sélectionnée
document.getElementById('region_id').addEventListener('change', function() {
    const regionId = this.value;
    const villeSelect = document.getElementById('ville_id');
    const villeOptions = villeSelect.querySelectorAll('option');
    
    villeOptions.forEach(option => {
        if (option.value === '') {
            // Toujours afficher l'option "-- Toutes --"
            option.style.display = '';
            return;
        }
        
        const optionRegion = option.getAttribute('data-region');
        
        if (!regionId || optionRegion === regionId) {
            option.style.display = '';
        } else {
            option.style.display = 'none';
            // Désélectionner si l'option est cachée
            if (option.selected) {
                option.selected = false;
                villeSelect.value = '';
            }
        }
    });
});

// Déclencher le filtrage au chargement si une région est déjà sélectionnée
if (document.getElementById('region_id').value) {
    document.getElementById('region_id').dispatchEvent(new Event('change'));
}
</script>