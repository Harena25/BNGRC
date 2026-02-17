-- Vue récapitulative par ville: montants (besoin total, satisfaits, restant)
-- Correction: calculs séparés pour éviter le produit cartésien
CREATE OR REPLACE VIEW v_recap_ville AS
SELECT
    v.id AS ville_id,
    v.libelle AS ville_name,
    r.id AS region_id,
    r.libelle AS region_name,
    COALESCE(besoins.montant_besoin_total, 0) AS montant_besoin_total,
    COALESCE(distributions.montant_satisfait_distrib, 0) AS montant_satisfait_distrib,
    COALESCE(achats.montant_achats, 0) AS montant_achats,
    (COALESCE(distributions.montant_satisfait_distrib, 0) + COALESCE(achats.montant_achats, 0)) AS montant_satisfait_total,
    (COALESCE(besoins.montant_besoin_total, 0) - (COALESCE(distributions.montant_satisfait_distrib, 0) + COALESCE(achats.montant_achats, 0))) AS montant_restant
FROM bn_ville v
LEFT JOIN bn_region r ON v.region_id = r.id

-- Sous-requête: besoins totaux par ville
LEFT JOIN (
    SELECT 
        b.ville_id,
        SUM(b.quantite_initiale * a.prix_unitaire) AS montant_besoin_total
    FROM bn_besoin b
    JOIN bn_article a ON a.id = b.article_id
    GROUP BY b.ville_id
) besoins ON besoins.ville_id = v.id

-- Sous-requête: distributions totales par ville
LEFT JOIN (
    SELECT 
        b.ville_id,
        SUM(d.quantite_distribuee * a.prix_unitaire) AS montant_satisfait_distrib
    FROM bn_distribution d
    JOIN bn_besoin b ON d.besoin_id = b.id
    JOIN bn_article a ON d.article_id = a.id
    GROUP BY b.ville_id
) distributions ON distributions.ville_id = v.id

-- Sous-requête: achats totaux par ville
LEFT JOIN (
    SELECT 
        ach.ville_id,
        SUM(ach.prix_total) AS montant_achats
    FROM bn_achats ach
    GROUP BY ach.ville_id
) achats ON achats.ville_id = v.id

GROUP BY v.id, v.libelle, r.id, r.libelle, besoins.montant_besoin_total, distributions.montant_satisfait_distrib, achats.montant_achats;
