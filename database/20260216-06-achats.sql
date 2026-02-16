-- ============================================================================
-- BNGRC - Migration : Table des achats
-- Fichier généré le 2026-02-16
-- Les dons en argent peuvent acheter les besoins en nature et matériaux
-- ============================================================================

USE bngrc;

-- Table: Achats (achat de besoins avec les dons en argent)
CREATE TABLE IF NOT EXISTS bn_achats (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ville_id INT NOT NULL,
    article_id INT NOT NULL,
    quantite INT NOT NULL,
    prix_unitaire DECIMAL(10, 2) NOT NULL,
    frais_pourcentage DECIMAL(5, 2) NOT NULL DEFAULT 0,
    prix_total DECIMAL(12, 2) NOT NULL,
    date_achat DATE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (ville_id) REFERENCES bn_ville(id),
    FOREIGN KEY (article_id) REFERENCES bn_article(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Vue: Achats avec détails ville et article
DROP VIEW IF EXISTS v_achats;
CREATE VIEW v_achats AS
SELECT 
    a.id,
    a.ville_id,
    v.libelle AS ville_name,
    r.libelle AS region_name,
    a.article_id,
    ar.libelle AS article_name,
    c.libelle AS categorie_name,
    a.quantite,
    a.prix_unitaire,
    a.frais_pourcentage,
    a.prix_total,
    a.date_achat,
    a.created_at
FROM bn_achats a
JOIN bn_ville v ON a.ville_id = v.id
JOIN bn_region r ON v.region_id = r.id
JOIN bn_article ar ON a.article_id = ar.id
JOIN bn_categorie c ON ar.categorie_id = c.id
ORDER BY a.date_achat DESC, a.created_at DESC;

-- Données de test
INSERT INTO bn_achats (ville_id, article_id, quantite, prix_unitaire, frais_pourcentage, prix_total, date_achat) VALUES
(1, 1, 10, 35.00, 10, 385.00, '2026-02-10'),
(2, 3, 5, 12.50, 10, 68.75, '2026-02-11'),
(3, 5, 8, 25.00, 10, 220.00, '2026-02-12'),
(5, 2, 20, 4.00, 10, 88.00, '2026-02-13'),
(7, 10, 15, 15.00, 10, 247.50, '2026-02-14');
