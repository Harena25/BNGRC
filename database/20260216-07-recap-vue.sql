-- Vue récapitulative par ville: montants (besoin total, satisfaits, restant)
CREATE OR REPLACE VIEW v_recap_ville AS
SELECT
    v.id AS ville_id,
    v.libelle AS ville_name,
    r.id AS region_id,
    r.libelle AS region_name,
    COALESCE(SUM(b.quantite_initiale * a.prix_unitaire), 0) AS montant_besoin_total,
    COALESCE(SUM(d.quantite_distribuee * a.prix_unitaire), 0) AS montant_satisfait_distrib,
    COALESCE(SUM(ach.quantite * a.prix_unitaire), 0) AS montant_achats,
    (COALESCE(SUM(d.quantite_distribuee * a.prix_unitaire), 0) + COALESCE(SUM(ach.quantite * a.prix_unitaire), 0)) AS montant_satisfait_total,
    (COALESCE(SUM(b.quantite_initiale * a.prix_unitaire), 0) - (COALESCE(SUM(d.quantite_distribuee * a.prix_unitaire), 0) + COALESCE(SUM(ach.quantite * a.prix_unitaire), 0))) AS montant_restant
FROM bn_ville v
LEFT JOIN bn_region r ON v.region_id = r.id
LEFT JOIN bn_besoin b ON b.ville_id = v.id
LEFT JOIN bn_article a ON a.id = b.article_id
LEFT JOIN bn_distribution d ON d.besoin_id = b.id
LEFT JOIN bn_achats ach ON ach.ville_id = v.id AND ach.article_id = a.id
GROUP BY v.id, v.libelle, r.id, r.libelle;
