-- Base de données Taam Bledi (Backoffice MVC PHP / MySQL)
CREATE DATABASE IF NOT EXISTS taam_bledi CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE taam_bledi;

-- 1. Table des utilisateurs (admin, farmer = agriculteur, user = simple utilisateur)
CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL,
  email VARCHAR(160) NOT NULL UNIQUE,
  role ENUM('admin', 'farmer', 'user') NOT NULL DEFAULT 'farmer',
  password_hash VARCHAR(255) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 2. Table des variétés (produits)
CREATE TABLE IF NOT EXISTS varieties (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL UNIQUE,
  cat ENUM('Céréales', 'Oliviers', 'Fruits', 'Légumes', 'Plantes') NOT NULL,
  regions VARCHAR(200) NOT NULL,
  description TEXT NOT NULL,
  img VARCHAR(200) DEFAULT '',
  price DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  sales_count INT NOT NULL DEFAULT 0,
  stock INT NOT NULL DEFAULT 50,
  created_by_role ENUM('admin', 'farmer') NOT NULL DEFAULT 'admin',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 3. Table des témoignages clients (lecture seule dans le backoffice)
CREATE TABLE IF NOT EXISTS testimonials (
  id INT AUTO_INCREMENT PRIMARY KEY,
  author VARCHAR(120) NOT NULL,
  region VARCHAR(100) NOT NULL,
  variety_name VARCHAR(120) NOT NULL,
  rating TINYINT NOT NULL,
  status ENUM('Validé', 'En attente') NOT NULL DEFAULT 'Validé',
  text TEXT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Données de démonstration (Variétés tunisiennes)
INSERT IGNORE INTO varieties (name, cat, regions, description, img, price, sales_count, stock, created_by_role) VALUES
('Blé dur', 'Céréales', 'Tunis, Kairouan', 'Céréale emblématique de la cuisine tunisienne, base du couscous.', 'bledur.jpg', 22.00, 184, 95, 'admin'),
('Blé tendre', 'Céréales', 'Béja', 'Cultivé dans les plaines du nord, utilisé pour le pain et la pâtisserie.', 'bletend.jpg', 18.00, 112, 70, 'admin'),
('Orge', 'Céréales', 'Tunis, Sfax, Béja, Médenine', 'Céréale rustique adaptée aux zones semi-arides.', 'orge.jpg', 15.00, 96, 120, 'admin'),
('Olivier', 'Oliviers', 'Sfax, Médenine', 'Arbre emblématique de la Tunisie, source d’huile d’olive réputée.', 'olivier.jpg', 45.00, 248, 64, 'admin'),
('Agrumes', 'Fruits', 'Tunis', 'Oranges, citrons et clémentines cultivés dans la région de Tunis.', 'agrumes.jpg', 28.00, 138, 52, 'admin'),
('Grenadier', 'Fruits', 'Gafsa, Tozeur', 'Grenade des oasis, appréciée pour sa douceur.', 'grenadier.jpg', 26.00, 154, 48, 'admin'),
('Palmier dattier', 'Fruits', 'Gafsa, Tozeur', 'Palmier des oasis du sud tunisien, producteur de dattes.', 'palmier_dattier.jpg', 50.00, 221, 55, 'admin'),
('Piment', 'Légumes', 'Kairouan', 'Ingrédient de base de la harissa tunisienne.', 'piment.jpg', 14.00, 193, 110, 'admin'),
('Tomate', 'Légumes', 'Bizerte', 'Légume incontournable de la cuisine tunisienne.', 'tomate.jpg', 12.00, 167, 130, 'admin'),
('Henné', 'Plantes', 'Tozeur', 'Plante tinctoriale des oasis utilisée dans les soins traditionnels.', 'henne.jpg', 20.00, 146, 72, 'admin');

-- Données de démonstration (Témoignages clients)
INSERT INTO testimonials (author, region, variety_name, rating, status, text) VALUES
('Kamel Ben Salem', 'Kairouan', 'Blé dur', 5, 'Validé', 'Les semences de blé dur local ont donné une récolte exceptionnelle cette année.'),
('Amira Trabelsi', 'Sfax', 'Olivier', 5, 'Validé', 'J’ai parrainé un jeune olivier via Taam Bledi. Suivi transparent et excellente communication.'),
('Fatma Gharbi', 'Bizerte', 'Tomate', 4, 'Validé', 'Très belle variété traditionnelle de tomate de plein champ, très parfumée.'),
('Nizar Ayari', 'Béja', 'Blé tendre', 3, 'Validé', 'Bonne variété dans l’ensemble, mais le délai de livraison était un peu long.'),
('Ines Chaabane', 'Bizerte', 'Artichaut', 2, 'Validé', 'L’emballage a souffert pendant le transport, même si le service client a été réactif.'),
('Rami Hammami', 'Kairouan', 'Pistachier', 5, 'Validé', 'Excellente initiative pour protéger nos variétés locales.');
