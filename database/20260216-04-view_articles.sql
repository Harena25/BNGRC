-- Vue pour les articles avec les informations de catégorie
CREATE
OR REPLACE VIEW vw_articles AS
SELECT
    a.id,
    a.libelle,
    a.categorie_id,
    c.libelle AS categorie_libelle,
    a.prix_unitaire
FROM
    bn_article a
    LEFT JOIN bn_categorie c ON a.categorie_id = c.id
ORDER BY
    a.libelle;