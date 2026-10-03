-- Base du service d'authentification (MySQL / MariaDB).
-- Projet étudiant : le compte ci-dessous est un exemple inventé.
-- Mise en place : voir README.md, section « Installation ».
--
-- Compte de démonstration : identifiant « coach », mot de passe « demo1234 ».

CREATE DATABASE IF NOT EXISTS equipe_sport_auth CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE equipe_sport_auth;

DROP TABLE IF EXISTS Entraineur;
CREATE TABLE Entraineur (
  Id_Entraineur INT AUTO_INCREMENT PRIMARY KEY,
  identifiant   VARCHAR(50)  NOT NULL UNIQUE,
  mdp           VARCHAR(255) NOT NULL COMMENT 'mot de passe haché (password_hash), jamais en clair',
  nom           VARCHAR(50)  NOT NULL,
  prenom        VARCHAR(50)  NOT NULL,
  email         VARCHAR(100) DEFAULT NULL
) ENGINE=InnoDB;

INSERT INTO Entraineur (identifiant, mdp, nom, prenom, email) VALUES
('coach', '$2y$10$xAkuZyVqOmbFZsE3l/tkQu9XWcblJc8tSJroOXa8/lDENEE5bKgwi', 'Dupont', 'Camille', 'coach@example.com');
