/**
 * Gestion du formulaire d'achat d'articles
 * Permet de simuler et valider un achat avec utilisation optionnelle du stock
 */

// Variables globales (injectées depuis PHP via data-attributes)
let basePath = '';
let stockDisponible = 0;
let simulationEnCours = null;

/**
 * Initialisation du formulaire d'achat
 */
function initAchatForm() {
    // Récupérer les paramètres depuis le container
    const container = document.querySelector('[data-achat-form]');
    if (!container) return;

    basePath = container.dataset.basePath || '';
    stockDisponible = parseInt(container.dataset.stockDisponible) || 0;

    // Récupérer les éléments DOM
    const btnSimuler = document.getElementById('btnSimuler');
    const btnValider = document.getElementById('btnValider');
    const zoneSimulation = document.getElementById('zoneSimulation');
    const zoneErreur = document.getElementById('zoneErreur');

    // Listener bouton Simuler
    if (btnSimuler) {
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

            // Utiliser la valeur du champ stock si elle existe, sinon calculer
            let stock_utilise = 0;
            const stockUtiliseInput = document.getElementById('stock_utilise');
            if (stockDisponible > 0 && stockUtiliseInput) {
                stock_utilise = parseInt(stockUtiliseInput.value) || 0;
            }

            // Appel Ajax pour simuler
            simulerAchat(besoin_id, quantite, frais_pourcent, stock_utilise);
        });
    }

    // Listener sur changement stock utilise
    const stockUtiliseInput = document.getElementById('stock_utilise');
    if (stockUtiliseInput) {
        stockUtiliseInput.addEventListener('input', function () {
            if (!simulationEnCours) return; // Ne recalculer que si simulation déjà lancée

            const besoin_id = document.getElementById('besoin_id').value;
            const quantite = document.getElementById('quantite').value;
            const frais_pourcent = document.getElementById('frais_pourcent').value;
            const stock_utilise = parseInt(this.value) || 0;

            // Recalculer via AJAX
            simulerAchat(besoin_id, quantite, frais_pourcent, stock_utilise);
        });
    }

    // Listener sur changement taux de frais
    const fraisPourcentInput = document.getElementById('frais_pourcent');
    if (fraisPourcentInput) {
        fraisPourcentInput.addEventListener('input', function () {
            if (!simulationEnCours) return; // Ne recalculer que si simulation déjà lancée

            const besoin_id = document.getElementById('besoin_id').value;
            const quantite = document.getElementById('quantite').value;
            const frais_pourcent = parseFloat(this.value) || 0;
            const stock_utilise = stockDisponible > 0 ? (parseInt(document.getElementById('stock_utilise').value) || 0) : 0;

            // Validation rapide
            if (frais_pourcent < 0 || frais_pourcent > 100) {
                afficherErreur('Taux de frais invalide (0-100%)');
                return;
            }

            // Recalculer via AJAX
            simulerAchat(besoin_id, quantite, frais_pourcent, stock_utilise);
        });
    }

    // Listener bouton Valider
    if (btnValider) {
        btnValider.addEventListener('click', function () {
            if (!confirm('Confirmer l\'achat de cet article ?\n\nCette action va debiter les dons en argent et ajouter l\'article au stock.')) {
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
                        alert('Achat effectue avec succes !\nMontant: ' + data.data.montant_total.toFixed(2) + ' Ar');
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
    }
}

/**
 * Simuler un achat via AJAX
 * @param {number} besoin_id - ID du besoin
 * @param {number} quantite - Quantité totale demandée
 * @param {number} frais_pourcent - Taux de frais en %
 * @param {number} stock_utilise - Quantité de stock à utiliser
 */
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
                
                const btnValider = document.getElementById('btnValider');
                if (btnValider) {
                    btnValider.disabled = false;
                }
            } else {
                afficherErreur(data.message);
            }
        })
        .catch(error => {
            afficherErreur('Erreur de communication: ' + error.message);
        });
}

/**
 * Afficher les résultats de la simulation
 * @param {object} data - Données de la simulation
 * @param {number} quantiteTotale - Quantité totale demandée
 * @param {number} stockUtilise - Quantité de stock utilisée
 */
function afficherSimulation(data, quantiteTotale, stockUtilise) {
    const zoneSimulation = document.getElementById('zoneSimulation');
    const zoneErreur = document.getElementById('zoneErreur');

    if (zoneErreur) {
        zoneErreur.style.display = 'none';
    }

    // Afficher zone stock si stock disponible
    if (stockDisponible > 0) {
        const zoneStockUtilisation = document.getElementById('zoneStockUtilisation');
        const inputStockUtilise = document.getElementById('stock_utilise');
        const stockMaxUtilisable = Math.min(stockDisponible, parseInt(quantiteTotale));

        if (inputStockUtilise) {
            inputStockUtilise.max = stockMaxUtilisable;
            inputStockUtilise.value = stockUtilise;
        }

        const stockDispoLabel = document.getElementById('stock_dispo_label');
        if (stockDispoLabel) {
            stockDispoLabel.textContent = stockDisponible;
        }

        if (zoneStockUtilisation) {
            zoneStockUtilisation.style.display = 'block';
        }

        // Afficher ligne stock utilise
        const ligneStockUtilise = document.getElementById('ligne_stock_utilise');
        if (ligneStockUtilise) {
            ligneStockUtilise.style.display = '';
        }

        const simStockUtilise = document.getElementById('sim_stock_utilise');
        if (simStockUtilise) {
            simStockUtilise.textContent = stockUtilise;
        }
    } else {
        const zoneStockUtilisation = document.getElementById('zoneStockUtilisation');
        if (zoneStockUtilisation) {
            zoneStockUtilisation.style.display = 'none';
        }

        const ligneStockUtilise = document.getElementById('ligne_stock_utilise');
        if (ligneStockUtilise) {
            ligneStockUtilise.style.display = 'none';
        }
    }

    // Quantite a acheter
    const qteAcheter = data.quantite_a_acheter || 0;
    const simQteAcheter = document.getElementById('sim_qte_acheter');
    if (simQteAcheter) {
        simQteAcheter.textContent = qteAcheter;
    }

    // Valeurs financieres
    const fields = {
        'sim_sous_total': data.sous_total,
        'sim_frais_pourcent': data.frais_pourcent,
        'sim_frais': data.frais,
        'sim_montant_total': data.montant_total,
        'sim_solde_actuel': data.solde_actuel,
        'sim_solde_apres': data.solde_apres
    };

    for (const [id, value] of Object.entries(fields)) {
        const element = document.getElementById(id);
        if (element) {
            if (id === 'sim_frais_pourcent') {
                element.textContent = value.toFixed(0);
            } else {
                element.textContent = value.toFixed(2);
            }
        }
    }

    if (zoneSimulation) {
        zoneSimulation.style.display = 'block';
    }
}

/**
 * Afficher un message d'erreur
 * @param {string} message - Message d'erreur
 */
function afficherErreur(message) {
    const zoneSimulation = document.getElementById('zoneSimulation');
    const zoneErreur = document.getElementById('zoneErreur');
    const messageErreur = document.getElementById('messageErreur');
    const btnValider = document.getElementById('btnValider');

    if (zoneSimulation) {
        zoneSimulation.style.display = 'none';
    }

    if (messageErreur) {
        messageErreur.textContent = message;
    }

    if (zoneErreur) {
        zoneErreur.style.display = 'block';
    }

    if (btnValider) {
        btnValider.disabled = true;
    }
}

// Initialiser au chargement du DOM
document.addEventListener('DOMContentLoaded', initAchatForm);
