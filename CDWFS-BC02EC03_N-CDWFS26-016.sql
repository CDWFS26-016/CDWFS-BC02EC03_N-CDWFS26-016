-- --------------------------------------------------------
-- Hôte:                         127.0.0.1
-- Version du serveur:           11.4.9-MariaDB - MariaDB Server
-- SE du serveur:                Win64
-- HeidiSQL Version:             12.14.0.7165
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Listage de la structure de la base pour app
CREATE DATABASE IF NOT EXISTS `app` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci */;
USE `app`;

-- Listage de la structure de table app. doctrine_migration_versions
CREATE TABLE IF NOT EXISTS `doctrine_migration_versions` (
  `version` varchar(191) NOT NULL,
  `executed_at` datetime DEFAULT NULL,
  `execution_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`version`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Listage des données de la table app.doctrine_migration_versions : 3 rows
/*!40000 ALTER TABLE `doctrine_migration_versions` DISABLE KEYS */;
INSERT INTO `doctrine_migration_versions` (`version`, `executed_at`, `execution_time`) VALUES
	('DoctrineMigrations\\Version20260109080412', '2026-01-09 08:07:30', 70),
	('DoctrineMigrations\\Version20260109081031', '2026-01-09 08:10:55', 102),
	('DoctrineMigrations\\Version20260109082116', '2026-01-09 08:21:20', 54);
/*!40000 ALTER TABLE `doctrine_migration_versions` ENABLE KEYS */;

-- Listage de la structure de table app. event
CREATE TABLE IF NOT EXISTS `event` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `date` datetime NOT NULL,
  `responsable_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_3BAE0AA753C59D72` (`responsable_id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Listage des données de la table app.event : 1 rows
/*!40000 ALTER TABLE `event` DISABLE KEYS */;
INSERT INTO `event` (`id`, `title`, `date`, `responsable_id`) VALUES
	(1, 'djhn dj d', '2026-01-10 10:44:00', 2),
	(2, 'he tej etsdj srt', '2026-01-10 12:29:00', 1);
/*!40000 ALTER TABLE `event` ENABLE KEYS */;

-- Listage de la structure de table app. messenger_messages
CREATE TABLE IF NOT EXISTS `messenger_messages` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `body` longtext NOT NULL,
  `headers` longtext NOT NULL,
  `queue_name` varchar(190) NOT NULL,
  `created_at` datetime NOT NULL,
  `available_at` datetime NOT NULL,
  `delivered_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_75EA56E0FB7336F0E3BD61CE16BA31DBBF396750` (`queue_name`,`available_at`,`delivered_at`,`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Listage des données de la table app.messenger_messages : 0 rows
/*!40000 ALTER TABLE `messenger_messages` DISABLE KEYS */;
/*!40000 ALTER TABLE `messenger_messages` ENABLE KEYS */;

-- Listage de la structure de table app. review
CREATE TABLE IF NOT EXISTS `review` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `rating` int(11) NOT NULL,
  `content` longtext NOT NULL,
  `is_validated` tinyint(4) NOT NULL,
  `author_id` int(11) NOT NULL,
  `event_id` int(11) NOT NULL,
  `comments` longtext DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_794381C6F675F31B` (`author_id`),
  KEY `IDX_794381C671F7E88B` (`event_id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Listage des données de la table app.review : 1 rows
/*!40000 ALTER TABLE `review` DISABLE KEYS */;
INSERT INTO `review` (`id`, `rating`, `content`, `is_validated`, `author_id`, `event_id`, `comments`) VALUES
	(1, 4, 'testfh sh ehe tet', 1, 1, 1, 'test egb hrz hzrhz'),
	(2, 1, '<div>djhnedtje tejhet</div>', 1, 4, 2, '<div>gdh et</div>'),
	(3, 1, '<div>sf gsh sh ethet</div>', 1, 4, 1, NULL);
/*!40000 ALTER TABLE `review` ENABLE KEYS */;

-- Listage de la structure de table app. user
CREATE TABLE IF NOT EXISTS `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL,
  `roles` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`roles`)),
  `password` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Listage des données de la table app.user : 2 rows
/*!40000 ALTER TABLE `user` DISABLE KEYS */;
INSERT INTO `user` (`id`, `email`, `roles`, `password`) VALUES
	(1, 'miloud@lol.fr', '["ROLE_USER","ROLE_RESPONSABLE"]', '$2y$13$XsJakvxSB77jrhK1DFHc.ujHkrXZrGUJfvealCTuv8XxRrpSvCsCy'),
	(2, 'lol@lol.lol', '["ROLE_ADMIN","ROLE_RESPONSABLE","ROLE_USER"]', '$2y$13$.uovnDWULw6xVj2rOU/UDOIwtO8KA5P.7RKodQL/oaUFCiaBhriwe'),
	(3, 'jeanmi@lol.fr', '["ROLE_USER"]', '$2y$13$6Fly7ReQ9RSPAkcHXI1zf.BXcuC5sgBPs.tNa48hrL.z.9xSPr./S'),
	(4, 'jeanmi@lol.com', '["ROLE_USER"]', '$2y$13$On1KHT1qr22FHbXT/9bz2uCO0Jnf9hfbFLelu5oOnXJa9edxGaH.K');
/*!40000 ALTER TABLE `user` ENABLE KEYS */;

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
