(function () {
  const refreshBtn = document.getElementById("refreshBtn");
  const villeFilter = document.getElementById("villeFilter");
  const recapTotals = document.getElementById("recapTotals");
  const recapTableBody = document.querySelector("#recapTable tbody");

  async function loadRecap(villeId) {
    const basePath = document.querySelector("base")?.getAttribute("href") || "";
    const url =
      basePath + "/recap/data" + (villeId ? "?ville_id=" + villeId : "");

    // Add loading state
    if (refreshBtn) {
      refreshBtn.innerHTML =
        '<i class="bi bi-arrow-clockwise spinner-border spinner-border-sm me-1"></i>Chargement...';
      refreshBtn.disabled = true;
    }

    try {
      const resp = await fetch(url);
      const data = await resp.json();
      if (!data.success) return;

      // Mettre à jour tableau avec animations
      recapTableBody.innerHTML = "";
      data.rows.forEach((r, index) => {
        const dist = Number(r.montant_satisfait_distrib || 0);
        const ach = Number(r.montant_achats || 0);
        const besoin = Number(r.montant_besoin_total || 0);
        const satisf = Number(r.montant_satisfait_total || 0);
        const restant = Number(r.montant_restant || 0);
        const cov = besoin > 0 ? (satisf / besoin) * 100 : 0;

        const tr = document.createElement("tr");
        tr.style.animationDelay = `${index * 50}ms`;
        tr.className = "fade-in-row";
        tr.innerHTML = `
            <td><i class="bi bi-flag-fill text-muted me-1"></i>${escapeHtml(r.region_name)}</td>
            <td><i class="bi bi-geo-alt-fill text-primary me-1"></i><strong>${escapeHtml(r.ville_name)}</strong></td>
            <td class="text-end fw-bold">${formatNumber(besoin)}</td>
            <td class="text-end text-success fw-semibold">${formatNumber(dist)}</td>
            <td class="text-end text-info fw-semibold">${formatNumber(ach)}</td>
            <td class="text-end fw-bold">${formatNumber(satisf)}</td>
            <td class="text-end text-danger fw-semibold">${formatNumber(restant)}</td>
            <td class="text-end"><span class="badge bg-${cov >= 75 ? "success" : cov >= 40 ? "warning" : "secondary"} fs-6">${cov.toFixed(1)}%</span></td>
        `;
        recapTableBody.appendChild(tr);
      });

      // Totaux
      const t = data.totals;
      const covGlobal =
        t.montant_besoin_total > 0
          ? (t.montant_satisfait_total / t.montant_besoin_total) * 100
          : 0;

      recapTotals.innerHTML = `
        <div class="card shadow-sm">
          <div class="card-header bg-primary text-white">
            <h5 class="card-title mb-0">
              <i class="bi bi-graph-up me-2"></i>Résumé Financier Global
            </h5>
          </div>
          <div class="table-responsive">
            <table class="table table-sm mb-0">
              <thead class="table-light">
                <tr>
                  <th>Type</th>
                  <th class="text-end">Montant (MAD)</th>
                  <th class="text-end">Pourcentage</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td><i class="bi bi-currency-exchange text-primary me-2"></i><strong>Besoins Total</strong></td>
                  <td class="text-end fw-bold">${formatNumber(t.montant_besoin_total || 0)}</td>
                  <td class="text-end"><span class="badge bg-primary">100.0%</span></td>
                </tr>
                <tr>
                  <td><i class="bi bi-gift-fill text-success me-2"></i>Distribué (dons)</td>
                  <td class="text-end text-success fw-semibold">${formatNumber(t.montant_satisfait_distrib || 0)}</td>
                  <td class="text-end"><span class="badge bg-success">${(((t.montant_satisfait_distrib || 0) / (t.montant_besoin_total || 1)) * 100).toFixed(1)}%</span></td>
                </tr>
                <tr>
                  <td><i class="bi bi-cart3-fill text-info me-2"></i>Achats effectués</td>
                  <td class="text-end text-info fw-semibold">${formatNumber(t.montant_achats || 0)}</td>
                  <td class="text-end"><span class="badge bg-info">${(((t.montant_achats || 0) / (t.montant_besoin_total || 1)) * 100).toFixed(1)}%</span></td>
                </tr>
                <tr>
                  <td><i class="bi bi-exclamation-triangle-fill text-danger me-2"></i>Montant Restant</td>
                  <td class="text-end text-danger fw-bold">${formatNumber(t.montant_restant || 0)}</td>
                  <td class="text-end"><span class="badge bg-danger">${(((t.montant_restant || 0) / (t.montant_besoin_total || 1)) * 100).toFixed(1)}%</span></td>
                </tr>
                <tr class="table-warning">
                  <td><i class="bi bi-percent text-warning me-2"></i><strong>Couverture Globale</strong></td>
                  <td class="text-end fw-bold">${formatNumber(t.montant_satisfait_total || 0)}</td>
                  <td class="text-end"><span class="badge bg-${covGlobal >= 75 ? "success" : covGlobal >= 40 ? "warning" : "secondary"} fs-6">${covGlobal.toFixed(1)}%</span></td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      `;
      // Montrer la section des totaux avec animation
      if (recapTotals.classList.contains("d-none")) {
        recapTotals.classList.remove("d-none");
        recapTotals.style.animation = "fadeInUp 0.5s ease-out";
      }
    } catch (e) {
      console.error("Erreur lors du chargement du récap:", e);
      showError("Erreur lors du chargement des données. Veuillez réessayer.");
    } finally {
      // Reset button state
      if (refreshBtn) {
        refreshBtn.innerHTML =
          '<i class="bi bi-arrow-clockwise me-1"></i>Actualiser';
        refreshBtn.disabled = false;
      }
    }
  }

  function formatNumber(num) {
    return (
      new Intl.NumberFormat("fr-FR", {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
      }).format(num) + " MAD"
    );
  }

  function showError(message) {
    const errorDiv = document.createElement("div");
    errorDiv.className =
      "alert alert-danger alert-dismissible fade show position-fixed";
    errorDiv.style.cssText =
      "top: 20px; right: 20px; z-index: 9999; min-width: 300px;";
    errorDiv.innerHTML = `
      <i class="bi bi-exclamation-triangle me-2"></i>${message}
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    document.body.appendChild(errorDiv);

    // Auto remove after 5 seconds
    setTimeout(() => {
      if (errorDiv.parentNode) {
        errorDiv.remove();
      }
    }, 5000);
  }

  function escapeHtml(s) {
    if (!s) return "";
    return s.replace(/[&<>"']/g, function (c) {
      return {
        "&": "&amp;",
        "<": "&lt;",
        ">": "&gt;",
        '"': "&quot;",
        "'": "&#39;",
      }[c];
    });
  }

  // Events
  if (refreshBtn)
    refreshBtn.addEventListener("click", () => loadRecap(villeFilter.value));
  if (villeFilter)
    villeFilter.addEventListener("change", () => loadRecap(villeFilter.value));

  // Ne pas charger automatiquement au démarrage — chargement uniquement via bouton Actualiser

  // Expose for debugging
  window._recap = { loadRecap };

  // Add CSS animations
  const styles = `
    <style>
      @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
      }
      
      @keyframes fadeInRow {
        from { opacity: 0; transform: translateX(-10px); }
        to { opacity: 1; transform: translateX(0); }
      }
      
      .fade-in-row {
        animation: fadeInRow 0.3s ease-out forwards;
        opacity: 0;
      }
      
      .spinner-border-sm {
        animation: spin 1s linear infinite;
      }
      
      @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
      }
      
      .card:hover {
        transform: translateY(-2px);
        transition: transform 0.2s ease-in-out;
      }
    </style>
  `;

  document.head.insertAdjacentHTML("beforeend", styles);
})();
