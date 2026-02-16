(function () {
  const refreshBtn = document.getElementById("refreshBtn");
  const villeFilter = document.getElementById("villeFilter");
  const recapTotals = document.getElementById("recapTotals");
  const recapTableBody = document.querySelector("#recapTable tbody");

  async function loadRecap(villeId) {
    const url = "/recap/data" + (villeId ? "?ville_id=" + villeId : "");
    try {
      const resp = await fetch(url);
      const data = await resp.json();
      if (!data.success) return;

      // Mettre à jour tableau
      recapTableBody.innerHTML = "";
      data.rows.forEach((r) => {
        const dist = Number(r.montant_satisfait_distrib || 0);
        const ach = Number(r.montant_achats || 0);
        const besoin = Number(r.montant_besoin_total || 0);
        const satisf = Number(r.montant_satisfait_total || 0);
        const restant = Number(r.montant_restant || 0);
        const cov = besoin > 0 ? (satisf / besoin) * 100 : 0;

        const tr = document.createElement("tr");
        tr.innerHTML = `
                    <td>${escapeHtml(r.region_name)}</td>
                    <td>${escapeHtml(r.ville_name)}</td>
                    <td class="text-end">${besoin.toFixed(2)}</td>
                    <td class="text-end">${dist.toFixed(2)}</td>
                    <td class="text-end">${ach.toFixed(2)}</td>
                    <td class="text-end">${satisf.toFixed(2)}</td>
                    <td class="text-end">${restant.toFixed(2)}</td>
                    <td class="text-end"><span class="badge bg-${cov >= 75 ? "success" : cov >= 40 ? "warning" : "secondary"}">${cov.toFixed(1)}%</span></td>
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
                <div class="card p-3">
                    <div class="row g-2 align-items-center">
                        <div class="col-auto">
                            <strong>Besoins total</strong>
                            <div class="h4 mb-0 text-end">${Number(t.montant_besoin_total || 0).toFixed(2)} MAD</div>
                        </div>
                        <div class="col-auto">
                            <strong>Distribué</strong>
                            <div class="h5 mb-0 text-end text-success">${Number(t.montant_satisfait_distrib || 0).toFixed(2)} MAD</div>
                        </div>
                        <div class="col-auto">
                            <strong>Achats</strong>
                            <div class="h5 mb-0 text-end text-info">${Number(t.montant_achats || 0).toFixed(2)} MAD</div>
                        </div>
                        <div class="col-auto">
                            <strong>Restant</strong>
                            <div class="h4 mb-0 text-end text-danger">${Number(t.montant_restant || 0).toFixed(2)} MAD</div>
                        </div>
                        <div class="col">
                            <div class="progress" style="height:18px">
                                <div class="progress-bar" role="progressbar" style="width: ${covGlobal.toFixed(1)}%" aria-valuenow="${covGlobal.toFixed(1)}" aria-valuemin="0" aria-valuemax="100">${covGlobal.toFixed(1)}%</div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
    } catch (e) {
      console.error("Erreur lors du chargement du récap:", e);
    }
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

  // Initial load
  document.addEventListener("DOMContentLoaded", () =>
    loadRecap(villeFilter.value),
  );

  // Expose for debugging
  window._recap = { loadRecap };
})();
