-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : mer. 05 mars 2025 à 14:49
-- Version du serveur : 9.1.0
-- Version de PHP : 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `jo_project_starter`
--

-- --------------------------------------------------------

--
-- Structure de la table `mainapp_event`
--

DROP TABLE IF EXISTS `mainapp_event`;
CREATE TABLE IF NOT EXISTS `mainapp_event` (
  `id` BIGINT NOT NULL AUTO_INCREMENT,
  `start` DATETIME(6) NOT NULL,
  `stadium_id` BIGINT NOT NULL,
  `team_away_id` BIGINT DEFAULT NULL,
  `team_home_id` BIGINT DEFAULT NULL,
  `score` VARCHAR(10) DEFAULT NULL,
  `winner_id` BIGINT DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `mainapp_event_stadium_id_idx` (`stadium_id`),
  KEY `mainapp_event_team_away_idx` (`team_away_id`),
  KEY `mainapp_event_team_home_idx` (`team_home_id`),
  KEY `mainapp_event_winner_idx` (`winner_id`)
) ENGINE=MyISAM AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `mainapp_event`
--

INSERT INTO `mainapp_event` (`id`, `start`, `stadium_id`, `team_away_id`, `team_home_id`, `score`, `winner_id`) VALUES
(6, '2024-07-26 21:00:00.000000', 6, 11, 12, NULL, NULL),
(5, '2024-07-26 18:00:00.000000', 5, 9, 10, NULL, NULL),
(4, '2024-07-25 21:00:00.000000', 4, 7, 8, NULL, NULL),
(3, '2024-07-25 18:00:00.000000', 3, 5, 6, NULL, NULL),
(2, '2024-07-24 21:00:00.000000', 2, 3, 4, NULL, NULL),
(1, '2024-07-24 18:00:00.000000', 3, 1, 2, NULL, NULL),
(7, '2024-07-27 18:00:00.000000', 7, 13, 14, NULL, NULL),
(8, '2024-07-27 21:00:00.000000', 1, 15, 16, NULL, NULL),
(9, '2024-07-31 18:00:00.000000', 2, NULL, NULL, NULL, NULL),
(10, '2024-07-31 21:00:00.000000', 3, NULL, NULL, NULL, NULL),
(11, '2024-08-01 18:00:00.000000', 4, NULL, NULL, NULL, NULL),
(12, '2024-08-01 21:00:00.000000', 5, NULL, NULL, NULL, NULL),
(13, '2024-08-04 18:00:00.000000', 6, NULL, NULL, NULL, NULL),
(14, '2024-08-04 21:00:00.000000', 7, NULL, NULL, NULL, NULL),
(15, '2024-08-07 20:00:00.000000', 1, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `mainapp_stadium`
--

DROP TABLE IF EXISTS `mainapp_stadium`;
CREATE TABLE IF NOT EXISTS `mainapp_stadium` (
  `id` BIGINT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(100) NOT NULL,
  `location` VARCHAR(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `mainapp_stadium`
--

INSERT INTO `mainapp_stadium` (`id`, `name`, `location`) VALUES
(4, 'Stade Vélodrome', 'Marseille'),
(3, 'Groupama Stadium', 'Lyon'),
(2, 'Parc des Princes', 'Paris'),
(1, 'Stade de France', 'Saint-Denis'),
(5, 'Stade Pierre-Mauroy', 'Lille'),
(6, 'Allianz Riviera', 'Nice'),
(7, 'Matmut Atlantique', 'Bordeaux');

-- --------------------------------------------------------

--
-- Structure de la table `mainapp_team`
--

DROP TABLE IF EXISTS `mainapp_team`;
CREATE TABLE IF NOT EXISTS `mainapp_team` (
  `id` BIGINT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(100) NOT NULL,
  `nickname` VARCHAR(100) NOT NULL,
  `code` VARCHAR(3) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `mainapp_team`
--

INSERT INTO `mainapp_team` (`id`, `name`, `nickname`, `code`) VALUES
(15, 'Mali', 'Les Aigles', 'ML'),
(16, 'Israël', 'The Blues and Whites', 'IL'),
(14, 'Paraguay', 'Los Guaraníes', 'PY'),
(13, 'Japon', 'Samurai Blue', 'JP'),
(12, 'République Dominicaine', 'Los Quisqueyanos', 'DO'),
(11, 'Égypte', 'Les Pharaons', 'EG'),
(10, 'Espagne', 'La Roja', 'ES'),
(9, 'Ouzbékistan', 'Les Loups Blancs', 'UZ'),
(8, 'Ukraine', 'Les Jaunes et Bleus', 'UA'),
(7, 'Irak', 'Lions de Mésopotamie', 'IQ'),
(5, 'Argentine', 'La Albiceleste', 'AR'),
(6, 'Maroc', 'Les Lions de l Atlas', 'MA'),
(4, 'Nouvelle-Zélande', 'All Whites', 'NZ'),
(3, 'Guinée', 'Syli National', 'GN'),
(2, 'États-Unis', 'Team USA', 'US'),
(1, 'France', 'Les Bleus', 'FR');

--

-- Table des utilisateurs d'authentification
CREATE TABLE IF NOT EXISTS `auth_user` (
  `id` BIGINT NOT NULL AUTO_INCREMENT,
  `username` VARCHAR(150) NOT NULL UNIQUE,
  `email` VARCHAR(254) NOT NULL UNIQUE,
  `password` VARCHAR(128) NOT NULL,
  `is_superuser` BOOLEAN NOT NULL DEFAULT 0,
  `is_active` BOOLEAN NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`)
);

-- --------------------------------------------------------

-- Structure de la table `mainapp_ticket`
--

CREATE TABLE IF NOT EXISTS `mainapp_ticket` (
  `id` BIGINT NOT NULL AUTO_INCREMENT,
  `event_id` BIGINT NOT NULL,
  `user_id` BIGINT NOT NULL,
  `category` ENUM('Silver', 'Gold', 'Platinum') NOT NULL,
  `price` DECIMAL(10,2) NOT NULL,
  `qr_code` VARCHAR(255) NOT NULL,  -- Stocke le chemin du QR Code généré
  `used` BOOLEAN NOT NULL DEFAULT 0, -- Indique si le billet a été scanné
  PRIMARY KEY (`id`),
  FOREIGN KEY (`event_id`) REFERENCES `mainapp_event` (`id`),
  FOREIGN KEY (`user_id`) REFERENCES `auth_user` (`id`)
);

-- --------------------------------------------------------

-- Structure de la table `mainapp_ticket_scan`
--

CREATE TABLE IF NOT EXISTS `mainapp_ticket_scan` (
  `id` BIGINT NOT NULL AUTO_INCREMENT,
  `ticket_id` BIGINT NOT NULL,
  `scanned_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `scanner_id` BIGINT NOT NULL,  -- L'ID de l'agent/stadier
  PRIMARY KEY (`id`),
  FOREIGN KEY (`ticket_id`) REFERENCES `mainapp_ticket` (`id`),
  FOREIGN KEY (`scanner_id`) REFERENCES `auth_user` (`id`)
);

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
