-- Views for BNGRC Dashboard
USE bngrc;

-- Drop existing views if they exist
DROP VIEW IF EXISTS v_ville_besoins;
DROP VIEW IF EXISTS v_ville_distributions;
DROP VIEW IF EXISTS v_ville_resume;

-- View 1: Besoins totaux par ville (utilise quantite_initiale pour le total original)
CREATE VIEW v_ville_besoins AS
SELECT 
    v.id AS ville_id,
    v.libelle AS ville_name,
    r.id AS region_id,
    r.libelle AS region_name,
    COUNT(b.id) AS nb_besoins,
    SUM(CASE WHEN b.status_id = 1 THEN 1 ELSE 0 END) AS nb_ouverts,
    SUM(CASE WHEN b.status_id = 2 THEN 1 ELSE 0 END) AS nb_partiels,
    SUM(CASE WHEN b.status_id = 3 THEN 1 ELSE 0 END) AS nb_satisfaits,
    COALESCE(SUM(b.quantite), 0) AS qte_reste,
    COALESCE(SUM(b.quantite_initiale), 0) AS qte_besoin_total
FROM bn_ville v
LEFT JOIN bn_region r ON v.region_id = r.id
LEFT JOIN bn_besoin b ON b.ville_id = v.id
GROUP BY v.id, v.libelle, r.id, r.libelle;

-- View 2: Distributions totales par ville
CREATE VIEW v_ville_distributions AS
SELECT 
    v.id AS ville_id,
    v.libelle AS ville_name,
    COUNT(d.id) AS nb_distributions,
    COALESCE(SUM(d.quantite_distribuee), 0) AS qte_distribuee_total
FROM bn_ville v
LEFT JOIN bn_besoin b ON b.ville_id = v.id
LEFT JOIN bn_distribution d ON d.besoin_id = b.id
GROUP BY v.id, v.libelle;

-- View 3: Résumé complet par ville (vue principale pour le dashboard)
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
    (vb.qte_besoin_total - vd.qte_distribuee_total) AS qte_reste,
    CASE 
        WHEN vb.qte_besoin_total > 0 
        THEN ROUND((vd.qte_distribuee_total / vb.qte_besoin_total) * 100, 1)
        ELSE 0 
    END AS pourcentage_couverture
FROM v_ville_besoins vb
JOIN v_ville_distributions vd ON vb.ville_id = vd.ville_id
ORDER BY vb.region_name, vb.ville_name;
