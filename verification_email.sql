-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               10.3.16-MariaDB - mariadb.org binary distribution
-- Server OS:                    Win64
-- HeidiSQL Version:             9.5.0.5196
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;


-- Dumping database structure for smartads_dev
CREATE DATABASE IF NOT EXISTS `dextratamadev` /*!40100 DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci */;
USE `dextratamadev`;

-- Dumping structure for table smartads_dev.verification_email
CREATE TABLE IF NOT EXISTS `verification_email` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `token` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `expires` datetime DEFAULT NULL,
  `code` varchar(4) COLLATE utf8_unicode_ci DEFAULT NULL,
  `email` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `verified_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Dumping data for table smartads_dev.verification_email: ~0 rows (approximately)
/*!40000 ALTER TABLE `verification_email` DISABLE KEYS */;
REPLACE INTO `verification_email` (`id`, `token`, `expires`, `code`, `email`, `verified_at`) VALUES
	(8, '692018e6a63ffa8dda5c08d7b55734c6', '2019-07-17 11:30:05', '2', 'sopianahmad120@gmail.com', NULL),
	(9, '22aed92708c8ba30cfe52392095572d4', '2019-07-18 11:31:19', '1365', 'sopianahmad120@gmail.com', '2019-07-17 12:57:55');
/*!40000 ALTER TABLE `verification_email` ENABLE KEYS */;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
