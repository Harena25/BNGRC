USE bngrc;

-- View: stock with article name
DROP VIEW IF EXISTS bn_stock_v;
CREATE VIEW bn_stock_v AS
SELECT s.id AS stock_id, s.article_id, s.quantite_stock, a.libelle AS article_name
FROM bn_stock s
JOIN bn_article a ON s.article_id = a.id;

-- View: besoins (ordered by date_besoin then created_at)
DROP VIEW IF EXISTS bn_besoin_v;
CREATE VIEW bn_besoin_v AS
SELECT * FROM bn_besoin
ORDER BY date_besoin ASC, created_at ASC;
