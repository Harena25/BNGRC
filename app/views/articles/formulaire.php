<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">
                        <?php echo isset($article) ? 'Modifier un article' : 'Créer un article'; ?>
                    </h4>
                </div>
                <div class="card-body">
                    <?php if (isset($_GET['error'])): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?php 
                                if ($_GET['error'] == 'donnees_invalides') {
                                    echo 'Données invalides. Veuillez vérifier tous les champs.';
                                }
                            ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="<?php echo isset($article) ? '/articles/' . $article['id'] : '/articles'; ?>">
                        
                        <!-- Libellé -->
                        <div class="mb-3">
                            <label for="libelle" class="form-label">Libellé <span class="text-danger">*</span></label>
                            <input type="text" 
                                   class="form-control" 
                                   id="libelle" 
                                   name="libelle" 
                                   placeholder="Ex: Riz, Huile, Tôle..."
                                   value="<?php echo isset($article) ? htmlspecialchars($article['libelle']) : ''; ?>" 
                                   required>
                        </div>

                        <!-- Catégorie -->
                        <div class="mb-3">
                            <label for="categorie_id" class="form-label">Catégorie <span class="text-danger">*</span></label>
                            <select class="form-select" id="categorie_id" name="categorie_id" required>
                                <option value="">-- Sélectionner une catégorie --</option>
                                <?php foreach ($categories as $categorie): ?>
                                    <option value="<?php echo $categorie['id']; ?>"
                                        <?php echo (isset($article) && $article['categorie_id'] == $categorie['id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($categorie['libelle']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Prix unitaire -->
                        <div class="mb-3">
                            <label for="prix_unitaire" class="form-label">Prix unitaire (Ar) <span class="text-danger">*</span></label>
                            <input type="number" 
                                   class="form-control" 
                                   id="prix_unitaire" 
                                   name="prix_unitaire" 
                                   min="0.01" 
                                   step="0.01"
                                   placeholder="Ex: 1500.00"
                                   value="<?php echo isset($article) ? htmlspecialchars($article['prix_unitaire']) : ''; ?>" 
                                   required>
                            <div class="form-text">Le prix unitaire doit être supérieur à 0.</div>
                        </div>

                        <!-- Boutons -->
                        <div class="d-flex justify-content-between mt-4">
                            <a href="<?= BASE_PATH; ?>/articles" class="btn btn-secondary">
                                <i class="bi bi-arrow-left"></i> Retour
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> <?php echo isset($article) ? 'Modifier' : 'Créer'; ?>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
