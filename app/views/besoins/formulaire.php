<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Saisie des besoins</h4>
                </div>
                <div class="card-body">
                    <?php if (!empty($success)): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <?php echo htmlspecialchars($success); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                    <?php if (!empty($error)): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?php echo htmlspecialchars($error); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <form action="<?php echo BASE_PATH; ?>/needs" method="POST">

                        <!-- Ville -->
                        <div class="mb-3">
                            <label for="ville_id" class="form-label">Ville <span class="text-danger">*</span></label>
                            <select class="form-select" id="ville_id" name="ville_id" required>
                                <option value="">-- Sélectionner une ville --</option>
                                <?php foreach ($villes as $v): ?>
                                    <option value="<?php echo $v['id']; ?>"
                                        <?php echo (isset($old['ville_id']) && $old['ville_id'] == $v['id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($v['region'] . ' - ' . $v['libelle']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Article -->
                        <div class="mb-3">
                            <label for="article_id" class="form-label">Article <span class="text-danger">*</span></label>
                            <select class="form-select" id="article_id" name="article_id" required>
                                <option value="">-- Sélectionner un article --</option>
                                <?php foreach ($articles as $a): ?>
                                    <option value="<?php echo $a['id']; ?>"
                                        <?php echo (isset($old['article_id']) && $old['article_id'] == $a['id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($a['categorie_libelle'] . ' - (' . $a['libelle'] . ')'); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Quantité -->
                        <div class="mb-3">
                            <label for="quantite" class="form-label">Quantité <span class="text-danger">*</span></label>
                            <input type="number"
                                   class="form-control"
                                   id="quantite"
                                   name="quantite"
                                   min="1"
                                   step="1"
                                   value="<?php echo htmlspecialchars($old['quantite'] ?? ''); ?>"
                                   required>
                            <div class="form-text">La quantité doit être supérieure à 0.</div>
                        </div>

                        <!-- Date -->
                        <div class="mb-3">
                            <label for="date_besoin" class="form-label">Date du besoin <span class="text-danger">*</span></label>
                            <input type="date"
                                   class="form-control"
                                   id="date_besoin"
                                   name="date_besoin"
                                   value="<?php echo htmlspecialchars($old['date_besoin'] ?? date('Y-m-d')); ?>"
                                   required>
                        </div>

                        <!-- Status caché (toujours 1) -->
                        <input type="hidden" name="status_id" value="1">

                        <!-- Boutons -->
                        <div class="d-flex justify-content-between mt-4">
                            <a href="<?php echo BASE_PATH; ?>/needs/list" class="btn btn-secondary">
                                <i class="bi bi-arrow-left"></i> Retour
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Enregistrer
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

