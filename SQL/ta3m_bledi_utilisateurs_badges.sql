-- =========================================================
--  Ta3m Bledi : module Utilisateurs et Badges
--  phpMyAdmin → onglet SQL → coller → Exécuter
--  (peut être exécuté avant OU après le script Terrains et Récoltes)
-- =========================================================

CREATE DATABASE IF NOT EXISTS ta3m_bledi
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE ta3m_bledi;

-- ---------------------------------------------------------
-- Table utilisateur
-- ---------------------------------------------------------
CREATE TABLE IF NOT EXISTS utilisateur (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nom VARCHAR(50) NOT NULL,
  prenom VARCHAR(50) NOT NULL,
  email VARCHAR(100) NOT NULL UNIQUE,
  motDePasse VARCHAR(255) NOT NULL,
  role VARCHAR(20) NOT NULL DEFAULT 'AGRICULTEUR'
) ENGINE=InnoDB;

-- Colonnes du module (ajoutées seulement si elles n'existent pas encore)
ALTER TABLE utilisateur
  ADD COLUMN IF NOT EXISTS telephone VARCHAR(8) NULL,
  ADD COLUMN IF NOT EXISTS statut VARCHAR(10) NOT NULL DEFAULT 'ACTIF',
  ADD COLUMN IF NOT EXISTS photo VARCHAR(100) NULL,
  ADD COLUMN IF NOT EXISTS dateInscription DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  ADD COLUMN IF NOT EXISTS resetToken VARCHAR(64) NULL,
  ADD COLUMN IF NOT EXISTS resetExpiration DATETIME NULL;

-- Comptes de test (mots de passe hachés avec password_hash)
--   admin@taambledi.tn  /  Admin@2026   (ADMIN)
--   ahmed@taambledi.tn  /  Ahmed@2026   (AGRICULTEUR)
--   salma@taambledi.tn  /  Salma@2026   (AGRICULTEUR)
INSERT IGNORE INTO utilisateur (id, nom, prenom, email, motDePasse, role) VALUES
  (1, 'Admin',    'Taam',  'admin@taambledi.tn', 'a-remplacer', 'ADMIN'),
  (2, 'Ben Ali',  'Ahmed', 'ahmed@taambledi.tn', 'a-remplacer', 'AGRICULTEUR'),
  (3, 'Trabelsi', 'Salma', 'salma@taambledi.tn', 'a-remplacer', 'AGRICULTEUR');

UPDATE utilisateur SET motDePasse = '$2y$10$AtUJvl0reIlRHPHLF4kWT.OZWWm8hBBKOCP3GtqJpAxJp9MyG/ttG', telephone = '71000000'
  WHERE id = 1 AND motDePasse = 'a-remplacer';
UPDATE utilisateur SET motDePasse = '$2y$10$S3w76cTYEtYUHdeSW6T81O4YKV5K3qTznMAN7Z/yKNNObwgSygwx6', telephone = '22123456'
  WHERE id = 2 AND motDePasse = 'a-remplacer';
UPDATE utilisateur SET motDePasse = '$2y$10$c.9EwfL5EbkDRLmqV15BKu6fk2x/54.EkiidlJ9RaOeKczgDazlju', telephone = '98765432'
  WHERE id = 3 AND motDePasse = 'a-remplacer';

-- ---------------------------------------------------------
-- Table badge
-- ---------------------------------------------------------
CREATE TABLE IF NOT EXISTS badge (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nom VARCHAR(50) NOT NULL UNIQUE,
  description VARCHAR(255) NOT NULL,
  critere VARCHAR(100) NOT NULL,
  points INT NOT NULL,
  icone VARCHAR(100) NULL
) ENGINE=InnoDB;

-- ---------------------------------------------------------
-- Table utilisateur_badge (attribution d'un badge à un utilisateur)
-- ---------------------------------------------------------
CREATE TABLE IF NOT EXISTS utilisateur_badge (
  id INT AUTO_INCREMENT PRIMARY KEY,
  idUtilisateur INT NOT NULL,
  idBadge INT NOT NULL,
  dateAttribution DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  motif VARCHAR(255) NOT NULL,
  CONSTRAINT uq_utilisateur_badge UNIQUE (idUtilisateur, idBadge),
  CONSTRAINT fk_ub_utilisateur FOREIGN KEY (idUtilisateur) REFERENCES utilisateur(id) ON DELETE CASCADE,
  CONSTRAINT fk_ub_badge FOREIGN KEY (idBadge) REFERENCES badge(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------
-- Données de test
-- ---------------------------------------------------------
INSERT IGNORE INTO badge (id, nom, description, critere, points) VALUES
  (1, 'Premier jardinier',    'Première plantation ajoutée.',          '1 plantation',  10),
  (2, 'Échangeur solidaire',  'Premier échange de récoltes réalisé.',  '1 échange',     15),
  (3, 'Apprenti curieux',     'Participation à un atelier.',           '1 atelier',     10),
  (4, 'Gardien des semences', 'Adopter 5 graines.',                    '5 graines',     25);

INSERT IGNORE INTO utilisateur_badge (idUtilisateur, idBadge, motif) VALUES
  (2, 1, 'Première plantation'),
  (2, 2, 'Premier échange'),
  (3, 1, 'Première plantation');
