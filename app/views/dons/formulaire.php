<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">
                        <?php echo isset($don) ? 'Modifier un don' : 'Enregistrer un don'; ?>
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

                    <form method="POST" action="<?php echo isset($don) ? '/dons/' . $don['id'] : '/dons'; ?>">
                        
                        <!-- Sélection Article -->
                        <div class="mb-3">
                            <label for="article_id" class="form-label">Article <span class="text-danger">*</span></label>
                            <select class="form-select" id="article_id" name="article_id" required>
                                <option value="">-- Sélectionner un article --</option>
                                <?php foreach ($articles as $article): ?>
                                    <option value="<?php echo $article['id']; ?>"
                                        <?php echo (isset($don) && $don['article_id'] == $article['id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($article['libelle']); ?>
                                        (<?php echo htmlspecialchars($article['categorie_libelle'] ?? ''); ?> - 
                                        <?php echo number_format($article['prix_unitaire'], 2); ?> Ar)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="form-text">
                                L'article n'existe pas ? 
                                <a href="/articles/create" target="_blank" class="text-primary">Créer un nouvel article</a>
                            </div>
                        </div>

                        <!-- Quantité donnée -->
                        <div class="mb-3">
                            <label for="quantite_donnee" class="form-label">Quantité donnée <span class="text-danger">*</span></label>
                            <input type="number" 
                                   class="form-control" 
                                   id="quantite_donnee" 
                                   name="quantite_donnee" 
                                   min="1" 
                                   step="1"
                                   value="<?php echo isset($don) ? htmlspecialchars($don['quantite_donnee']) : ''; ?>" 
                                   required>
                            <div class="form-text">La quantité doit être supérieure à 0.</div>
                        </div>

                        <!-- Date du don -->
                        <div class="mb-3">
                            <label for="date_don" class="form-label">Date du don <span class="text-danger">*</span></label>
                            <input type="date" 
                                   class="form-control" 
                                   id="date_don" 
                                   name="date_don" 
                                   value="<?php echo isset($don) ? htmlspecialchars($don['date_don']) : date('Y-m-d'); ?>" 
                                   required>
                        </div>

                        <!-- Boutons -->
                        <div class="d-flex justify-content-between mt-4">
                            <a href="/dons" class="btn btn-secondary">
                                <i class="bi bi-arrow-left"></i> Retour
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> <?php echo isset($don) ? 'Modifier' : 'Enregistrer'; ?>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
