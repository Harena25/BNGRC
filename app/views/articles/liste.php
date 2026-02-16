<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <!-- En-tête avec titre et bouton d'ajout -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="mb-0">Liste des articles</h2>
                <a href="<?php echo BASE_PATH; ?>/articles/create" class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i> Ajouter un article
                </a>
            </div>

            <!-- Messages de succès -->
            <?php if (isset($_GET['success'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?php
                    if ($_GET['success'] == 'article_cree') {
                        echo 'Article créé avec succès !';
                    } elseif ($_GET['success'] == 'article_modifie') {
                        echo 'Article modifié avec succès !';
                    } elseif ($_GET['success'] == 'article_supprime') {
                        echo 'Article supprimé avec succès !';
                    }
                    ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <!-- Tableau des articles -->
            <div class="card shadow-sm">
                <div class="card-body">
                    <?php if (empty($articles)): ?>
                        <div class="text-center py-5">
                            <i class="bi bi-box-seam" style="font-size: 3rem; color: #ccc;"></i>
                            <p class="text-muted mt-3">Aucun article enregistré pour le moment.</p>
                            <a href="<?php echo BASE_PATH; ?>/articles/create" class="btn btn-primary mt-2">
                                <i class="bi bi-plus-circle"></i> Ajouter le premier article
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover table-striped align-middle">
                                <thead class="table-dark">
                                    <tr>
                                        <th>#</th>
                                        <th>Libellé</th>
                                        <th>Catégorie</th>
                                        <th>Prix unitaire</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($articles as $article): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($article['id']); ?></td>
                                            <td>
                                                <strong><?php echo htmlspecialchars($article['libelle']); ?></strong>
                                            </td>
                                            <td>
                                                <span class="badge bg-info">
                                                    <?php echo htmlspecialchars($article['categorie_libelle'] ?? 'N/A'); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <strong><?php echo number_format($article['prix_unitaire'], 2); ?> Ar</strong>
                                            </td>
                                            <td class="text-center">
                                                <div class="btn-group btn-group-sm" role="group">
                                                    <!-- Bouton Modifier -->
                                                    <a href="<?php echo BASE_PATH; ?>/articles/<?php echo $article['id']; ?>/edit"
                                                        class="btn btn-outline-primary" title="Modifier">
                                                        <!-- <i class="bi bi-pencil"></i> -->
                                                        Modifer
                                                    </a>

                                                    <!-- Bouton Supprimer -->
                                                    <form method="POST"
                                                        action="<?php echo BASE_PATH; ?>/articles/<?php echo $article['id']; ?>/delete"
                                                        style="display: inline;"
                                                        onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet article ?');">
                                                        <button type="submit" class="btn btn-outline-danger btn-sm"
                                                            title="Supprimer">
                                                            <!-- <i class="bi bi-trash"></i> -->
                                                            Supprimer
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Statistiques -->
                        <div class="mt-3 text-muted small">
                            <i class="bi bi-info-circle"></i>
                            Total : <?php echo count($articles); ?> article(s) enregistré(s)
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>