-- ============================================================================
-- FIX: Intégrer les achats dans le calcul du RESTE du dashboard
-- Date: 2026-02-16
-- ============================================================================
-- 
-- PROBLÈME: 
-- La vue v_ville_resume ne prenait en compte QUE les distributions
-- pour calculer le "reste", mais pas les achats.
--
-- SOLUTION:
-- 1. Créer une vue v_ville_achats pour agréger les achats par ville
-- 2. Modifier v_ville_resume pour inclure les achats dans les calculs
-- ============================================================================

-- Vue: Achats agrégés par ville
DROP VIEW IF EXISTS v_ville_achats;
CREATE VIEW v_ville_achats AS
SELECT 
    v.id AS ville_id,
    v.libelle AS ville_name,
    COUNT(a.id) AS nb_achats,
    COALESCE(SUM(a.quantite), 0) AS qte_achats_total
FROM bn_ville v
LEFT JOIN bn_achats a ON a.ville_id = v.id
GROUP BY v.id, v.libelle;

-- Vue: Résumé complet par ville (CORRIGÉ pour inclure les achats)
DROP VIEW IF EXISTS v_ville_resume;
CREATE VIEW v_ville_resume AS
SELECT 
    vb.ville_id,
    vb.ville_name,
    vb.region_id,
    vb.region_name,
    vb.nb_besoins,
    vb.nb_ouverts,
    vb.nb_partiels,
    vb.nb_satisfaits,
    vb.qte_besoin_total,
    vd.nb_distributions,
    vd.qte_distribuee_total,
    va.nb_achats,
    va.qte_achats_total,
    -- RESTE = Besoin total - Distribué - Acheté
    (vb.qte_besoin_total - vd.qte_distribuee_total - va.qte_achats_total) AS qte_reste,
    -- COUVERTURE = (Distribué + Acheté) / Besoin total * 100
    CASE 
        WHEN vb.qte_besoin_total > 0 
        THEN ROUND(((vd.qte_distribuee_total + va.qte_achats_total) / vb.qte_besoin_total) * 100, 1)
        ELSE 0 
    END AS pourcentage_couverture
FROM v_ville_besoins vb
JOIN v_ville_distributions vd ON vb.ville_id = vd.ville_id
JOIN v_ville_achats va ON vb.ville_id = va.ville_id
ORDER BY vb.region_name, vb.ville_name;
