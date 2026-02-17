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
(2,'Centre'),
(3,'Sud');

-- Villes
INSERT INTO bn_ville (id, region_id, libelle) VALUES
(1,1,'Aïn Tala'),
(2,1,'Beni Ouar'),
(3,1,'Chera'),
(4,1,'Douar-Nord'),
(5,2,'El-Medina'),
(6,2,'Fayha'),
(7,2,'Gharb'),
(8,2,'Hammam'),
(9,3,'Ifrane'),
(10,3,'Jebel'),
(11,3,'Ksar'),
(12,3,'Lalla');

-- Status
INSERT INTO bn_status (id, libelle) VALUES
(1,'Ouvert'),
(2,'Partiellement satisfait'),
(3,'Satisfait');

-- Catégories
INSERT INTO bn_categorie (id, libelle) VALUES
(1,'Nourriture'),
(2,'Matériaux'),
(3,'Argent'),
(4,'Hygiène'),
(5,'Vêtements');

-- Articles
INSERT INTO bn_article (id, libelle, categorie_id, prix_unitaire) VALUES
(1,'Riz 50kg',1,35.00),
(2,'Riz 5kg',1,4.00),
(3,'Huile 5L',1,12.50),
(4,'Conserves (lot)',1,6.00),
(5,'Tôle 2x1m',2,25.00),
(6,'Clous 1kg',2,3.50),
(7,'Planche 2m',2,8.00),
(8,'Aide financière (MAD)',3,1.00),
(9,'Savon (lot 5)',4,2.50),
(10,'Couverture',5,15.00);

-- Stock initial (calculé à partir de la somme des dons)
INSERT INTO bn_stock (id, article_id, quantite_stock) VALUES
(1,1,730),   -- Riz 50kg: 100+50+120+80+60+40+90+110+50+30
(2,2,1200),  -- Riz 5kg: 200+80+150+110+140+60+130+170+90+70
(3,3,350),   -- Huile 5L: 50+20+35+60+30+25+40+55+20+15
(4,4,715),   -- Conserves: 120+40+70+50+90+110+60+95+45+35
(5,5,208),   -- Tôle: 30+10+40+20+15+18+25+30+12+8
(6,6,910),   -- Clous: 150+60+90+70+120+95+85+140+60+40
(7,7,383),   -- Planche: 60+30+45+25+55+35+28+65+22+18
(8,8,26200), -- Argent: 8000+2500+1000+1200+5000+3000+1500+2000+1100+900
(9,9,1750),  -- Savon: 250+100+140+160+200+220+180+240+160+100
(10,10,770); -- Couverture: 90+40+60+80+100+120+75+130+55+20

-- Besoins (avec quantite_initiale = quantite car tous neufs)
INSERT INTO bn_besoin (id, ville_id, article_id, quantite, quantite_initiale, date_besoin, status_id, created_at) VALUES
(1,1,1,50,50,'2026-01-05',1,'2026-01-05 08:12:00'),
(2,1,3,20,20,'2026-01-06',1,'2026-01-06 09:00:00'),
(3,2,1,30,30,'2026-01-03',1,'2026-01-03 10:15:00'),
(4,2,6,10,10,'2026-01-04',1,'2026-01-04 11:20:00'),
(5,3,5,15,15,'2026-01-07',1,'2026-01-07 07:50:00'),
(6,3,10,25,25,'2026-01-08',1,'2026-01-08 12:00:00'),
(7,4,2,80,80,'2026-01-02',1,'2026-01-02 13:30:00'),
(8,4,9,40,40,'2026-01-09',1,'2026-01-09 14:40:00'),
(9,5,1,60,60,'2026-01-10',1,'2026-01-10 08:05:00'),
(10,5,4,100,100,'2026-01-11',1,'2026-01-11 09:30:00'),
(11,6,8,5000,5000,'2026-01-05',1,'2026-01-05 10:00:00'),
(12,6,3,30,30,'2026-01-12',1,'2026-01-12 16:00:00'),
(13,7,7,40,40,'2026-01-13',1,'2026-01-13 17:10:00'),
(14,7,6,50,50,'2026-01-14',1,'2026-01-14 18:20:00'),
(15,8,2,120,120,'2026-01-15',1,'2026-01-15 08:00:00'),
(16,8,1,35,35,'2026-01-16',1,'2026-01-16 09:10:00'),
(17,9,10,60,60,'2026-01-17',1,'2026-01-17 10:25:00'),
(18,9,9,70,70,'2026-01-18',1,'2026-01-18 11:35:00'),
(19,10,5,20,20,'2026-01-19',1,'2026-01-19 12:45:00'),
(20,10,3,15,15,'2026-01-20',1,'2026-01-20 13:55:00'),
(21,11,1,40,40,'2026-01-21',1,'2026-01-21 14:05:00'),
(22,11,8,3000,3000,'2026-01-22',1,'2026-01-22 15:15:00'),
(23,12,4,50,50,'2026-01-23',1,'2026-01-23 16:25:00'),
(24,12,10,80,80,'2026-01-24',1,'2026-01-24 17:35:00'),
(25,1,5,10,10,'2026-01-25',1,'2026-01-25 08:45:00'),
(26,2,2,90,90,'2026-01-26',1,'2026-01-26 09:55:00'),
(27,3,3,25,25,'2026-01-27',1,'2026-01-27 11:05:00'),
(28,4,9,30,30,'2026-01-28',1,'2026-01-28 12:15:00'),
(29,5,6,60,60,'2026-01-29',1,'2026-01-29 13:25:00'),
(30,6,7,50,50,'2026-01-30',1,'2026-01-30 14:35:00'),
(31,7,1,20,20,'2026-02-01',1,'2026-02-01 09:00:00'),
(32,8,4,40,40,'2026-02-02',1,'2026-02-02 09:30:00'),
(33,9,2,110,110,'2026-02-03',1,'2026-02-03 10:00:00'),
(34,10,10,30,30,'2026-02-04',1,'2026-02-04 10:30:00'),
(35,11,3,45,45,'2026-02-05',1,'2026-02-05 11:00:00'),
(36,12,5,25,25,'2026-02-06',1,'2026-02-06 11:30:00'),
(37,1,8,2000,2000,'2026-02-07',1,'2026-02-07 12:00:00'),
(38,2,9,90,90,'2026-02-08',1,'2026-02-08 12:30:00'),
(39,3,10,55,55,'2026-02-09',1,'2026-02-09 13:00:00'),
(40,4,6,70,70,'2026-02-10',1,'2026-02-10 13:30:00'),
(41,5,1,100,100,'2026-02-11',1,'2026-02-11 14:00:00'),
(42,6,2,200,200,'2026-02-12',1,'2026-02-12 14:30:00'),
(43,7,3,60,60,'2026-02-13',1,'2026-02-13 15:00:00'),
(44,8,5,30,30,'2026-02-14',1,'2026-02-14 15:30:00'),
(45,9,9,120,120,'2026-02-15',1,'2026-02-15 16:00:00'),
(46,10,4,90,90,'2026-02-16',1,'2026-02-16 16:30:00'),
(47,11,7,10,10,'2026-02-16',1,'2026-02-16 17:00:00'),
(48,12,6,80,80,'2026-02-16',1,'2026-02-16 17:30:00');

-- Dons reçus (100 entrées)
INSERT INTO bn_dons (id, article_id, quantite_donnee, date_don) VALUES
(1,1,100,'2026-01-02'),
(2,2,200,'2026-01-02'),
(3,3,50,'2026-01-03'),
(4,4,120,'2026-01-03'),
(5,5,30,'2026-01-04'),
(6,6,150,'2026-01-04'),
(7,7,60,'2026-01-05'),
(8,8,8000,'2026-01-05'),
(9,9,250,'2026-01-06'),
(10,10,90,'2026-01-06'),
(11,1,50,'2026-01-07'),
(12,2,80,'2026-01-07'),
(13,3,20,'2026-01-08'),
(14,4,40,'2026-01-08'),
(15,5,10,'2026-01-09'),
(16,6,60,'2026-01-09'),
(17,7,30,'2026-01-10'),
(18,8,2500,'2026-01-10'),
(19,9,100,'2026-01-11'),
(20,10,40,'2026-01-11'),
(21,1,120,'2026-01-12'),
(22,2,150,'2026-01-12'),
(23,3,35,'2026-01-13'),
(24,4,70,'2026-01-13'),
(25,5,40,'2026-01-14'),
(26,6,90,'2026-01-14'),
(27,7,45,'2026-01-15'),
(28,8,1000,'2026-01-15'),
(29,9,140,'2026-01-16'),
(30,10,60,'2026-01-16'),
(31,1,80,'2026-01-17'),
(32,2,110,'2026-01-17'),
(33,3,60,'2026-01-18'),
(34,4,50,'2026-01-18'),
(35,5,20,'2026-01-19'),
(36,6,70,'2026-01-19'),
(37,7,25,'2026-01-20'),
(38,8,1200,'2026-01-20'),
(39,9,160,'2026-01-21'),
(40,10,80,'2026-01-21'),
(41,1,60,'2026-01-22'),
(42,2,140,'2026-01-22'),
(43,3,30,'2026-01-23'),
(44,4,90,'2026-01-23'),
(45,5,15,'2026-01-24'),
(46,6,120,'2026-01-24'),
(47,7,55,'2026-01-25'),
(48,8,5000,'2026-01-25'),
(49,9,200,'2026-01-26'),
(50,10,100,'2026-01-26'),
(51,1,40,'2026-01-27'),
(52,2,60,'2026-01-27'),
(53,3,25,'2026-01-28'),
(54,4,110,'2026-01-28'),
(55,5,18,'2026-01-29'),
(56,6,95,'2026-01-29'),
(57,7,35,'2026-01-30'),
(58,8,3000,'2026-01-30'),
(59,9,220,'2026-01-31'),
(60,10,120,'2026-01-31'),
(61,1,90,'2026-02-01'),
(62,2,130,'2026-02-01'),
(63,3,40,'2026-02-02'),
(64,4,60,'2026-02-02'),
(65,5,25,'2026-02-03'),
(66,6,85,'2026-02-03'),
(67,7,28,'2026-02-04'),
(68,8,1500,'2026-02-04'),
(69,9,180,'2026-02-05'),
(70,10,75,'2026-02-05'),
(71,1,110,'2026-02-06'),
(72,2,170,'2026-02-06'),
(73,3,55,'2026-02-07'),
(74,4,95,'2026-02-07'),
(75,5,30,'2026-02-08'),
(76,6,140,'2026-02-08'),
(77,7,65,'2026-02-09'),
(78,8,2000,'2026-02-09'),
(79,9,240,'2026-02-10'),
(80,10,130,'2026-02-10'),
(81,1,50,'2026-02-11'),
(82,2,90,'2026-02-11'),
(83,3,20,'2026-02-12'),
(84,4,45,'2026-02-12'),
(85,5,12,'2026-02-13'),
(86,6,60,'2026-02-13'),
(87,7,22,'2026-02-14'),
(88,8,1100,'2026-02-14'),
(89,9,160,'2026-02-15'),
(90,10,55,'2026-02-15'),
(91,1,30,'2026-02-16'),
(92,2,70,'2026-02-16'),
(93,3,15,'2026-02-16'),
(94,4,35,'2026-02-16'),
(95,5,8,'2026-02-16'),
(96,6,40,'2026-02-16'),
(97,7,18,'2026-02-16'),
(98,8,900,'2026-02-16'),
(99,9,100,'2026-02-16'),
(100,10,20,'2026-02-16');

-- Exemples de distributions (pour tests)
-- Note: Ces distributions ne sont pas réfléchies dans quantite des besoins
-- Utilisez l'AutoDistributor pour de vraies distributions


-- ============================================================================
-- FIN DU FICHIER
-- ============================================================================
