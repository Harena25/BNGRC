<div class="container mt-4" style="max-width: 800px;">
    <div class="row">
        <div class="col-12">
            <!-- En-tete -->
            <div class="mb-4">
                <a href="<?php echo BASE_PATH; ?>/needs/restants" class="btn btn-sm btn-outline-secondary mb-3">
                    <i class="bi bi-arrow-left"></i> Retour aux besoins restants
                </a>
                <h2 class="mb-1">
                    <i class="bi bi-cart-plus text-success me-2"></i>
                    Achat d'article
                </h2>
                <p class="text-muted">Utiliser les dons en argent pour acquerir cet article</p>
            </div>

            <!-- Informations sur le besoin -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-info text-white">
                    <i class="bi bi-info-circle me-2"></i>Informations sur le besoin
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="text-muted small">Ville</label>
                            <div class="fw-bold"><?php echo htmlspecialchars($besoin['ville'] ?? ''); ?></div>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small">Region</label>
                            <div><?php echo htmlspecialchars($besoin['region'] ?? ''); ?></div>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small">Article</label>
                            <div class="fw-bold"><?php echo htmlspecialchars($besoin['article'] ?? ''); ?></div>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small">Categorie</label>
                            <div>
                                <span class="badge bg-info">
                                    <?php echo htmlspecialchars($besoin['categorie'] ?? ''); ?>
                                </span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="text-muted small">Prix unitaire</label>
                            <div class="fw-bold text-primary">
                                <?php echo number_format($besoin['prix_unitaire'] ?? 0, 2); ?> Ar
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="text-muted small">Quantite restante</label>
                            <div>
                                <span class="badge bg-warning text-dark">
                                    <?php echo number_format($besoin['quantite'] ?? 0); ?> unites
                                </span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="text-muted small">Frais d'achat</label>
                            <div class="text-danger fw-bold">
                                <?php echo number_format($frais_achat ?? 10, 0); ?>%
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Solde disponible -->
            <div class="alert alert-success mb-4">
                <div class="d-flex justify-content-between align-items-center">
                    <span>
                        <i class="bi bi-wallet2 me-2"></i>
                        <strong>Solde argent disponible:</strong>
                    </span>
                    <span class="fs-5 fw-bold">
                        <?php echo number_format($solde_argent ?? 0, 2); ?> Ar
                    </span>
                </div>
            </div>

            <!-- Information sur le stock disponible -->
            <?php if ($stock_info): ?>
                <div class="alert alert-warning mb-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <span>
                            <i class="bi bi-box-seam me-2"></i>
                            <strong>Stock disponible pour cet article:</strong>
                        </span>
                        <span class="fs-5 fw-bold">
                            <?php echo number_format($stock_info['quantite_stock'] ?? 0, 0); ?> unités
                        </span>
                    </div>
                    <hr class="my-2">
                    <small class="text-muted">
                        <i class="bi bi-info-circle me-1"></i>
                        Cet article existe déjà en stock. Vous pouvez choisir d'acheter pour augmenter le stock.
                    </small>
                </div>
            <?php endif; ?>

            <!-- Formulaire d'achat -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <i class="bi bi-calculator me-2"></i>Calculer l'achat
                </div>
                <div class="card-body">
                    <form id="formAchat">
                        <input type="hidden" name="besoin_id" id="besoin_id" value="<?php echo $besoin['id'] ?? 0; ?>">

                        <div class="mb-3">
                            <label for="quantite" class="form-label fw-bold">
                                Quantite a acheter <span class="text-danger">*</span>
                            </label>
                            <input type="number" class="form-control form-control-lg" id="quantite" name="quantite"
                                min="1" max="<?php echo $besoin['quantite'] ?? 0; ?>"
                                value="<?php echo $besoin['quantite'] ?? 0; ?>" required>
                            <div class="form-text">
                                Maximum: <?php echo number_format($besoin['quantite'] ?? 0); ?> unites
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="frais_pourcent" class="form-label fw-bold">
                                Taux de frais d'achat (%) <span class="text-danger">*</span>
                            </label>
                            <input type="number" class="form-control form-control-lg" id="frais_pourcent"
                                name="frais_pourcent" min="0" max="100" step="0.1"
                                value="<?php echo $frais_achat ?? 10; ?>" required>
                            <div class="form-text">
                                Pourcentage de frais applique sur le montant de l'achat (0-100%)
                            </div>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="button" id="btnSimuler" class="btn btn-warning btn-lg">
                                <i class="bi bi-calculator me-2"></i>Simuler l'achat
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Zone de simulation (masquee par defaut) -->
            <div id="zoneSimulation" class="card shadow-sm mb-4" style="display: none;">
                <div class="card-header bg-success text-white">
                    <i class="bi bi-check-circle me-2"></i>Resultat de la simulation
                </div>
                <div class="card-body">
                    <!-- Zone stock si disponible -->
                    <div id="zoneStockUtilisation" style="display: none;" class="mb-3">
                        <div class="alert alert-info">
                            <label for="stock_utilise" class="form-label fw-bold">
                                <i class="bi bi-box-seam me-2"></i>Quantite de stock a utiliser
                            </label>
                            <input type="number" class="form-control form-control-lg" id="stock_utilise" 
                                   min="0" max="0" value="0">
                            <div class="form-text">
                                Stock disponible: <span id="stock_dispo_label">0</span> unites (gratuit)
                            </div>
                        </div>
                    </div>

                    <table class="table table-bordered mb-0">
                        <tbody>
                            <!-- Ligne stock utilise (si disponible) -->
                            <tr id="ligne_stock_utilise" style="display: none;">
                                <td class="text-end fw-bold text-success">Stock utilise (gratuit):</td>
                                <td class="text-end"><span id="sim_stock_utilise">0</span> unites</td>
                            </tr>
                            <!-- Ligne quantite a acheter -->
                            <tr id="ligne_qte_acheter">
                                <td class="text-end fw-bold">Quantite a acheter:</td>
                                <td class="text-end"><span id="sim_qte_acheter">0</span> unites</td>
                            </tr>
                            <tr>
                                <td class="text-end fw-bold">Sous-total:</td>
                                <td class="text-end"><span id="sim_sous_total">0.00</span> Ar</td>
                            </tr>
                            <tr>
                                <td class="text-end fw-bold">Frais (<span id="sim_frais_pourcent">0</span>%):</td>
                                <td class="text-end text-danger"><span id="sim_frais">0.00</span> Ar</td>
                            </tr>
                            <tr class="table-primary">
                                <td class="text-end fw-bold fs-5">TOTAL A PAYER:</td>
                                <td class="text-end fw-bold fs-5"><span id="sim_montant_total">0.00</span> Ar</td>
                            </tr>
                            <tr>
                                <td class="text-end">Solde actuel:</td>
                                <td class="text-end"><span id="sim_solde_actuel">0.00</span> Ar</td>
                            </tr>
                            <tr class="table-warning">
                                <td class="text-end fw-bold">Solde apres achat:</td>
                                <td class="text-end fw-bold"><span id="sim_solde_apres">0.00</span> Ar</td>
                            </tr>
                        </tbody>
                    </table>

                    <div class="d-grid gap-2 mt-3">
                        <button type="button" id="btnValider" class="btn btn-success btn-lg" disabled>
                            <i class="bi bi-check-circle-fill me-2"></i>Valider l'achat
                        </button>
                    </div>
                </div>
            </div>

            <!-- Zone d'erreur -->
            <div id="zoneErreur" class="alert alert-danger" style="display: none;">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                <span id="messageErreur"></span>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const basePath = '<?php echo BASE_PATH; ?>';
        const btnSimuler = document.getElementById('btnSimuler');
        const btnValider = document.getElementById('btnValider');
        const zoneSimulation = document.getElementById('zoneSimulation');
        const zoneErreur = document.getElementById('zoneErreur');
        const stockDisponible = <?php echo $stock_info ? ($stock_info['quantite_stock'] ?? 0) : 0; ?>;
        
        let simulationEnCours = null;

        btnSimuler.addEventListener('click', function () {
            const besoin_id = document.getElementById('besoin_id').value;
            const quantite = document.getElementById('quantite').value;
            const frais_pourcent = document.getElementById('frais_pourcent').value;

            if (!quantite || quantite <= 0) {
                afficherErreur('Veuillez entrer une quantite valide');
                return;
            }

            if (frais_pourcent === '' || frais_pourcent < 0 || frais_pourcent > 100) {
                afficherErreur('Veuillez entrer un taux de frais valide (0-100%)');
                return;
            }

            // Determiner stock a utiliser par defaut
            const stockMaxUtilisable = Math.min(stockDisponible, parseInt(quantite));
            const stock_utilise = stockMaxUtilisable;

            // Appel Ajax pour simuler
            simulerAchat(besoin_id, quantite, frais_pourcent, stock_utilise);
        });

        // Listener sur changement stock utilise
        document.getElementById('stock_utilise').addEventListener('input', function () {
            const besoin_id = document.getElementById('besoin_id').value;
            const quantite = document.getElementById('quantite').value;
            const frais_pourcent = document.getElementById('frais_pourcent').value;
            const stock_utilise = parseInt(this.value) || 0;

            // Recalculer via AJAX
            simulerAchat(besoin_id, quantite, frais_pourcent, stock_utilise);
        });

        function simulerAchat(besoin_id, quantite, frais_pourcent, stock_utilise) {
            fetch(basePath + '/achats/simuler', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `besoin_id=${besoin_id}&quantite=${quantite}&frais_pourcent=${frais_pourcent}&stock_utilise=${stock_utilise}`
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        simulationEnCours = data.data;
                        afficherSimulation(data.data, quantite, stock_utilise);
                        btnValider.disabled = false;
                    } else {
                        afficherErreur(data.message);
                    }
                })
                .catch(error => {
                    afficherErreur('Erreur de communication: ' + error.message);
                });
        }

        btnValider.addEventListener('click', function () {
            if (!confirm('Confirmer l\'achat de cet article ?\\n\\nCette action va debiter les dons en argent et ajouter l\'article au stock.')) {
                return;
            }

            const besoin_id = document.getElementById('besoin_id').value;
            const quantite = document.getElementById('quantite').value;
            const frais_pourcent = document.getElementById('frais_pourcent').value;
            const stock_utilise = stockDisponible > 0 ? (parseInt(document.getElementById('stock_utilise').value) || 0) : 0;

            btnValider.disabled = true;
            btnValider.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Traitement...';

            fetch(basePath + '/achats/valider', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `besoin_id=${besoin_id}&quantite=${quantite}&frais_pourcent=${frais_pourcent}&stock_utilise=${stock_utilise}`
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Achat effectue avec succes !\\nMontant: ' + data.data.montant_total.toFixed(2) + ' Ar');
                        window.location.href = basePath + '/needs/restants';
                    } else {
                        afficherErreur(data.message);
                        btnValider.disabled = false;
                        btnValider.innerHTML = '<i class="bi bi-check-circle-fill me-2"></i>Valider l\'achat';
                    }
                })
                .catch(error => {
                    afficherErreur('Erreur: ' + error.message);
                    btnValider.disabled = false;
                    btnValider.innerHTML = '<i class="bi bi-check-circle-fill me-2"></i>Valider l\'achat';
                });
        });

        function afficherSimulation(data, quantiteTotale, stockUtilise) {
            zoneErreur.style.display = 'none';
            
            // Afficher zone stock si stock disponible
            if (stockDisponible > 0) {
                const zoneStockUtilisation = document.getElementById('zoneStockUtilisation');
                const inputStockUtilise = document.getElementById('stock_utilise');
                const stockMaxUtilisable = Math.min(stockDisponible, parseInt(quantiteTotale));
                
                inputStockUtilise.max = stockMaxUtilisable;
                inputStockUtilise.value = stockUtilise;
                document.getElementById('stock_dispo_label').textContent = stockMaxUtilisable;
                
                zoneStockUtilisation.style.display = 'block';
                
                // Afficher ligne stock utilise
                document.getElementById('ligne_stock_utilise').style.display = '';
                document.getElementById('sim_stock_utilise').textContent = stockUtilise;
            } else {
                document.getElementById('zoneStockUtilisation').style.display = 'none';
                document.getElementById('ligne_stock_utilise').style.display = 'none';
            }
            
            // Quantite a acheter
            const qteAcheter = data.quantite_a_acheter || 0;
            document.getElementById('sim_qte_acheter').textContent = qteAcheter;
            
            // Valeurs financieres
            document.getElementById('sim_sous_total').textContent = data.sous_total.toFixed(2);
            document.getElementById('sim_frais_pourcent').textContent = data.frais_pourcent.toFixed(0);
            document.getElementById('sim_frais').textContent = data.frais.toFixed(2);
            document.getElementById('sim_montant_total').textContent = data.montant_total.toFixed(2);
            document.getElementById('sim_solde_actuel').textContent = data.solde_actuel.toFixed(2);
            document.getElementById('sim_solde_apres').textContent = data.solde_apres.toFixed(2);
            
            zoneSimulation.style.display = 'block';
        }

        function afficherErreur(message) {
            zoneSimulation.style.display = 'none';
            document.getElementById('messageErreur').textContent = message;
            zoneErreur.style.display = 'block';
            btnValider.disabled = true;
        }
    });
</script>