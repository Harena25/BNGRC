-- ============================================================================
-- BNGRC - Base de données complète
-- Fichier généré le 2026-02-16
-- Projet S3 Final - Gestion des secours et distributions d'aide
-- ============================================================================

-- ============================================================================
-- SECTION 1: CRÉATION DE LA BASE ET DES TABLES
-- ============================================================================

/* CREATE DATABASE IF NOT EXISTS bngrc;
USE bngrc; */

-- Désactiver les vérifications de clés étrangères pour la création
SET FOREIGN_KEY_CHECKS = 0;

-- Supprimer les tables existantes (ordre inverse des dépendances)
DROP TABLE IF EXISTS bn_achats;
DROP TABLE IF EXISTS bn_distribution;
DROP TABLE IF EXISTS bn_dons;
DROP TABLE IF EXISTS bn_stock;
DROP TABLE IF EXISTS bn_besoin;
DROP TABLE IF EXISTS bn_article;
DROP TABLE IF EXISTS bn_categorie;
DROP TABLE IF EXISTS bn_status;
DROP TABLE IF EXISTS bn_ville;
DROP TABLE IF EXISTS bn_region;

-- Réactiver les vérifications
SET FOREIGN_KEY_CHECKS = 1;

-- Table: Régions
CREATE TABLE bn_region (
    id INT AUTO_INCREMENT PRIMARY KEY,
    libelle VARCHAR(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: Villes
CREATE TABLE bn_ville (
    id INT AUTO_INCREMENT PRIMARY KEY,
    region_id INT NOT NULL,
    libelle VARCHAR(255) NOT NULL,
    FOREIGN KEY (region_id) REFERENCES bn_region(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: Status des besoins
CREATE TABLE bn_status (
    id INT AUTO_INCREMENT PRIMARY KEY,
    libelle VARCHAR(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: Catégories d'articles
CREATE TABLE bn_categorie (
    id INT AUTO_INCREMENT PRIMARY KEY,
    libelle VARCHAR(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: Articles
CREATE TABLE bn_article (
    id INT AUTO_INCREMENT PRIMARY KEY,
    libelle VARCHAR(255) NOT NULL,
    categorie_id INT NOT NULL,
    prix_unitaire DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (categorie_id) REFERENCES bn_categorie(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: Besoins (demandes des villes)
CREATE TABLE bn_besoin (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ville_id INT NOT NULL,
    article_id INT NOT NULL,
    quantite INT NOT NULL COMMENT 'Quantité restante à satisfaire',
    quantite_initiale INT NOT NULL COMMENT 'Quantité originale demandée (ne change jamais)',
    date_besoin DATE NOT NULL,
    status_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (article_id) REFERENCES bn_article(id),
    FOREIGN KEY (ville_id) REFERENCES bn_ville(id),
    FOREIGN KEY (status_id) REFERENCES bn_status(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: Distributions (allocations de stock aux besoins)
CREATE TABLE bn_distribution (
    id INT AUTO_INCREMENT PRIMARY KEY,
    besoin_id INT NOT NULL,
    quantite_distribuee INT NOT NULL,
    date_distribution DATE NOT NULL,
    article_id INT NOT NULL,
    FOREIGN KEY (besoin_id) REFERENCES bn_besoin(id),
    FOREIGN KEY (article_id) REFERENCES bn_article(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: Dons reçus
CREATE TABLE bn_dons (
    id INT AUTO_INCREMENT PRIMARY KEY,
    article_id INT NOT NULL,
    quantite_donnee INT NOT NULL,
    date_don DATE NOT NULL,
    FOREIGN KEY (article_id) REFERENCES bn_article(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: Stock disponible
CREATE TABLE bn_stock (
    id INT AUTO_INCREMENT PRIMARY KEY,
    article_id INT NOT NULL,
    quantite_stock INT NOT NULL,
    FOREIGN KEY (article_id) REFERENCES bn_article(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--Table: Achats (pour les achats effectués)
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

-- ============================================================================
-- SECTION 2: VUES
-- ============================================================================

-- Vue: Stock avec nom d'article
DROP VIEW IF EXISTS bn_stock_v;
CREATE VIEW bn_stock_v AS
SELECT s.id AS stock_id, s.article_id, s.quantite_stock, a.libelle AS article_name
FROM bn_stock s
JOIN bn_article a ON s.article_id = a.id;

-- Vue: Besoins ordonnés par priorité (date_besoin puis created_at)
DROP VIEW IF EXISTS bn_besoin_v;
CREATE VIEW bn_besoin_v AS
SELECT * FROM bn_besoin
ORDER BY date_besoin ASC, created_at ASC;

-- Vue: Articles avec catégorie
DROP VIEW IF EXISTS vw_articles;
CREATE VIEW vw_articles AS
SELECT
    a.id,
    a.libelle,
    a.categorie_id,
    c.libelle AS categorie_libelle,
    a.prix_unitaire
FROM bn_article a
LEFT JOIN bn_categorie c ON a.categorie_id = c.id
ORDER BY a.libelle;

-- Vue: Besoins par ville (pour dashboard)
DROP VIEW IF EXISTS v_ville_besoins;
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

-- Vue: Distributions par ville
DROP VIEW IF EXISTS v_ville_distributions;
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

-- Vue: Résumé complet par ville (vue principale pour le dashboard)
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
    (vb.qte_besoin_total - vd.qte_distribuee_total) AS qte_reste,
    CASE 
        WHEN vb.qte_besoin_total > 0 
        THEN ROUND((vd.qte_distribuee_total / vb.qte_besoin_total) * 100, 1)
        ELSE 0 
    END AS pourcentage_couverture
FROM v_ville_besoins vb
JOIN v_ville_distributions vd ON vb.ville_id = vd.ville_id
ORDER BY vb.region_name, vb.ville_name;

-- Vue : Achats par ville
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

-- ============================================================================
-- SECTION 3: DONNÉES DE TEST
-- ============================================================================

-- Régions
INSERT INTO bn_region (id, libelle) VALUES
(1,'Nord'),
(2,'Ouest'),
(3,'Est'),
(4,'Sud');

-- Villes
INSERT INTO bn_ville (id, region_id, libelle) VALUES
(1,3,'Toamasina'),
(2,3,'Mananjary'),
(3,4,'Farafangana'),
(4,1,'Nosy Be'),
(5,2,'Morondava');

-- Status
INSERT INTO bn_status (id, libelle) VALUES
(1,'Ouvert'),
(2,'Partiellement satisfait'),
(3,'Satisfait');

-- Catégories
INSERT INTO bn_categorie (id, libelle) VALUES
(1,'Nature'),
(2,'Materiel'),
(3,'Argent');

-- Articles
INSERT INTO bn_article (id, libelle, categorie_id, prix_unitaire) VALUES
(1,'Riz (kg)',1,3000.00),
(2,'Eau (L)',1,1000.00),
(3,'Huile (L)',1,6000.00),
(4,'Tôle ',2,25000.00),
(5,'Bâche',2,15000.00),
(6,'Clous (kg)',2,8000.00),
(7,'Bois',2,10000.00),
(8,'Argent',3,1.00),
(9,'Haricots',1,4000.00),
(10,'Groupe',2,6750000.00);

-- Stock initial
-- INSERT INTO bn_stock (id, article_id, quantite_stock) VALUES
-- (1,1,200),
-- (2,2,500),
-- (3,3,150),
-- (4,4,300),
-- (5,5,80),
-- (6,6,200),
-- (7,7,120),
-- (8,8,10000),
-- (9,9,400),
-- (10,10,180);

-- Besoins (avec quantite_initiale = quantite car tous neufs)
INSERT INTO bn_besoin (ville_id, article_id, quantite, quantite_initiale, date_besoin, status_id, created_at) VALUES
-- Ordre 1: Toamasina, Bâche
(1, 5, 200, 200, '2026-02-15', 1, '2026-02-15 08:00:00'),
-- Ordre 2: Nosy Be, Tôle
(4, 4, 40, 40, '2026-02-15', 1, '2026-02-15 08:15:00'),
-- Ordre 3: Mananjary, Argent
(2, 8, 6000000, 6000000, '2026-02-15', 1, '2026-02-15 08:30:00'),
-- Ordre 4: Toamasina, Eau (L)
(1, 2, 1500, 1500, '2026-02-15', 1, '2026-02-15 08:45:00'),
-- Ordre 5: Nosy Be, Riz (kg)
(4, 1, 300, 300, '2026-02-15', 1, '2026-02-15 09:00:00'),
-- Ordre 6: Mananjary, Tôle
(2, 4, 80, 80, '2026-02-15', 1, '2026-02-15 09:15:00'),
-- Ordre 7: Nosy Be, Argent
(4, 8, 4000000, 4000000, '2026-02-15', 1, '2026-02-15 09:30:00'),
-- Ordre 8: Farafangana, Bâche
(3, 5, 150, 150, '2026-02-16', 1, '2026-02-15 09:45:00'),
-- Ordre 9: Mananjary, Riz (kg)
(2, 1, 500, 500, '2026-02-15', 1, '2026-02-15 10:00:00'),
-- Ordre 10: Farafangana, Argent
(3, 8, 8000000, 8000000, '2026-02-16', 1, '2026-02-15 10:15:00'),
-- Ordre 11: Morondava, Riz (kg)
(5, 1, 700, 700, '2026-02-16', 1, '2026-02-15 10:30:00'),
-- Ordre 12: Toamasina, Argent
(1, 8, 1200000, 1200000, '2026-02-16', 1, '2026-02-15 10:45:00'),
-- Ordre 13: Morondava, Argent
(5, 8, 10000000, 10000000, '2026-02-16', 1, '2026-02-15 11:00:00'),
-- Ordre 14: Farafangana, Eau (L)
(3, 2, 1000, 1000, '2026-02-15', 1, '2026-02-15 11:15:00'),
-- Ordre 15: Morondava, Bâche
(5, 5, 180, 180, '2026-02-16', 1, '2026-02-15 11:30:00'),
-- Ordre 16: Toamasina, groupe
(1, 10, 3, 3, '2026-02-15', 1, '2026-02-15 11:45:00'),
-- Ordre 17: Toamasina, Riz (kg)
(1, 1, 800, 800, '2026-02-16', 1, '2026-02-16 08:00:00'),
-- Ordre 18: Nosy Be, Haricots
(4, 9, 200, 200, '2026-02-16', 1, '2026-02-16 08:15:00'),
-- Ordre 19: Mananjary, Clous (kg)
(2, 6, 60, 60, '2026-02-16', 1, '2026-02-16 08:30:00'),
-- Ordre 20: Morondava, Eau (L)
(5, 2, 1200, 1200, '2026-02-15', 1, '2026-02-16 08:45:00'),
-- Ordre 21: Farafangana, Riz (kg)
(3, 1, 600, 600, '2026-02-16', 1, '2026-02-16 09:00:00'),
-- Ordre 22: Morondava, Bois
(5, 7, 150, 150, '2026-02-15', 1, '2026-02-16 09:15:00'),
-- Ordre 23: Toamasina, Tôle
(1, 4, 120, 120, '2026-02-16', 1, '2026-02-16 09:30:00'),
-- Ordre 24: Nosy Be, Clous (kg)
(4, 6, 30, 30, '2026-02-16', 1, '2026-02-16 09:45:00'),
-- Ordre 25: Mananjary, Huile (L)
(2, 3, 120, 120, '2026-02-16', 1, '2026-02-16 10:00:00'),
-- Ordre 26: Farafangana, Bois
(3, 7, 100, 100, '2026-02-15', 1, '2026-02-16 10:15:00');

-- Dons reçus (100 entrées)

-- Exemples de distributions (pour tests)
-- Note: Ces distributions ne sont pas réfléchies dans quantite des besoins
-- Utilisez l'AutoDistributor pour de vraies distributions


-- ============================================================================
-- FIN DU FICHIER
-- ============================================================================
