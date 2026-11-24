-- =========================================================
--  Ta3m Bledi : module Terrains et Récoltes
--  À exécuter dans phpMyAdmin → onglet SQL → Exécuter
-- =========================================================

CREATE DATABASE IF NOT EXISTS ta3m_bledi
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE ta3m_bledi;

-- ---------------------------------------------------------
-- Table utilisateur (module d'un autre membre de l'équipe)
-- Créée SEULEMENT si elle n'existe pas encore, pour pouvoir tester.
-- Si l'équipe a déjà sa table utilisateur, cette partie ne fait rien.
-- ---------------------------------------------------------
CREATE TABLE IF NOT EXISTS utilisateur (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nom VARCHAR(50) NOT NULL,
  prenom VARCHAR(50) NOT NULL,
  email VARCHAR(100) NOT NULL UNIQUE,
  motDePasse VARCHAR(255) NOT NULL,
  role VARCHAR(20) NOT NULL DEFAULT 'AGRICULTEUR'
) ENGINE=InnoDB;

-- Utilisateurs de test (ignorés s'ils existent déjà)
INSERT IGNORE INTO utilisateur (id, nom, prenom, email, motDePasse, role) VALUES
  (1, 'Admin',    'Taam',  'admin@taambledi.tn', 'a-remplacer', 'ADMIN'),
  (2, 'Ben Ali',  'Ahmed', 'ahmed@taambledi.tn', 'a-remplacer', 'AGRICULTEUR'),
  (3, 'Trabelsi', 'Salma', 'salma@taambledi.tn', 'a-remplacer', 'AGRICULTEUR');

-- ---------------------------------------------------------
-- Table terrain
-- ---------------------------------------------------------
CREATE TABLE IF NOT EXISTS terrain (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nom VARCHAR(100) NOT NULL,
  localisation VARCHAR(50) NOT NULL,
  superficie DECIMAL(10,2) NOT NULL,
  typeSol VARCHAR(30) NOT NULL,
  idUtilisateur INT NOT NULL,
  CONSTRAINT fk_terrain_utilisateur
    FOREIGN KEY (idUtilisateur) REFERENCES utilisateur(id)
    ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------
-- Table recolte
-- ---------------------------------------------------------
CREATE TABLE IF NOT EXISTS recolte (
  id INT AUTO_INCREMENT PRIMARY KEY,
  culture VARCHAR(100) NOT NULL,
  datePlantation DATE NOT NULL,
  dateRecolte DATE NOT NULL,
  quantite DECIMAL(12,2) NOT NULL,
  unite VARCHAR(10) NOT NULL,
  idTerrain INT NOT NULL,
  CONSTRAINT fk_recolte_terrain
    FOREIGN KEY (idTerrain) REFERENCES terrain(id)
    ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------
-- Données de test
-- ---------------------------------------------------------
INSERT INTO terrain (nom, localisation, superficie, typeSol, idUtilisateur) VALUES
  ('Terrain Zaghouan', 'Zaghouan', 2.50, 'Argileux', 2),
  ('Terrain Nabeul',   'Nabeul',   1.20, 'Sableux',  2),
  ('Terrain Béja',     'Béja',     4.00, 'Calcaire', 3);

INSERT INTO recolte (culture, datePlantation, dateRecolte, quantite, unite, idTerrain) VALUES
  ('Blé',    '2025-11-10', '2026-06-15', 800,  'kg',    1),
  ('Orge',   '2025-12-01', '2026-07-02', 1.2,  'tonne', 1),
  ('Olives', '2026-03-20', '2026-11-05', 350,  'kg',    3);
