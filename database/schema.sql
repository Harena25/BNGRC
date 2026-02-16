CREATE DATABASE IF NOT EXIST bngrc;
USE bngrc;

CREATE TABLE IF NOT EXISTS bn_categorie (
    id INT AUTO_INCREMENT PRIMARY KEY,
    libelle VARCHAR(255) NOT NULL
);

CREATE TABLE IF NOT EXISTS bn_article (
    id INT AUTO_INCREMENT PRIMARY KEY,
    libelle VARCHAR(255) NOT NULL,
    cat_article VARCHAR(255) NOT NULL,
    prix_unitaire DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (cat_article) REFERENCES bn_categorie(id)
); 

CREATE TABLE IF NOT EXISTS bn_besoin (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ville_id INT NOT NULL,
    article_id INT NOT NULL,
    quantite INT NOT NULL,
    quantite_initiale INT NOT NULL,
    date_besoin DATE NOT NULL,
    status_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (article_id) REFERENCES bn_article(id),
    FOREIGN KEY (ville_id) REFERENCES bn_ville(id),
    FOREIGN KEY (status_id) REFERENCES bn_status(id)
);

CREATE TABLE IF NOT EXISTS bn_status (
    id INT AUTO_INCREMENT PRIMARY KEY,
    libelle VARCHAR(255) NOT NULL
);

CREATE TABLE IF NOT EXISTS bn_ville (
    id INT AUTO_INCREMENT PRIMARY KEY,
    region_id INT NOT NULL,
    libelle VARCHAR(255) NOT NULL,
    FOREIGN KEY (region_id) REFERENCES bn_region(id)
);

CREATE TABLE IF NOT EXISTS bn_region (
    id INT AUTO_INCREMENT PRIMARY KEY,
    libelle VARCHAR(255) NOT NULL
);

CREATE TABLE IF NOT EXISTS bn_distribution (
    id INT AUTO_INCREMENT PRIMARY KEY,
    besoin_id INT NOT NULL,
    quantite_distribuee INT NOT NULL,
    date_distribution DATE NOT NULL,
    article_id INT NOT NULL,
    FOREIGN KEY (besoin_id) REFERENCES bn_besoin(id),
    FOREIGN KEY (article_id) REFERENCES bn_article(id)
);

CREATE TABLE IF NOT EXISTS bn_dons (
    id INT AUTO_INCREMENT PRIMARY KEY,
    article_id INT NOT NULL,
    quantite_donnee INT NOT NULL,
    date_don DATE NOT NULL,
    FOREIGN KEY (article_id) REFERENCES bn_article(id)
);

CREATE TABLE IF NOT EXISTS bn_stock (
    id INT AUTO_INCREMENT PRIMARY KEY,
    article_id INT NOT NULL,
    quantite_stock INT NOT NULL,
    FOREIGN KEY (article_id) REFERENCES bn_article(id)
);