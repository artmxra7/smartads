-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               10.3.16-MariaDB - mariadb.org binary distribution
-- Server OS:                    Win64
-- HeidiSQL Version:             9.5.0.5196
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;


-- Dumping database structure for smartads_dev
CREATE DATABASE IF NOT EXISTS `smartads_dev` /*!40100 DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci */;
USE `smartads_dev`;

-- Dumping structure for table smartads_dev.ads
CREATE TABLE IF NOT EXISTS `ads` (
  `idx` int(11) NOT NULL AUTO_INCREMENT,
  `ads_code` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `users_code` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `ads_cart_code` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `ads_cart_order_code` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `product_code` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `product_type` tinyint(1) DEFAULT NULL COMMENT '0 = fleet, 1 = non fleet',
  `product_car_code` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `product_car_loc_code` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `partner_code` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `partner_car_code` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `ads_name` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `ads_url` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `ads_amount` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `ads_status` tinyint(1) DEFAULT 0 COMMENT 'tar tanyain dulu',
  `ads_start_date` date DEFAULT NULL,
  `ads_end_date` date DEFAULT NULL,
  `ads_price` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `is_deleted` tinyint(1) DEFAULT 0 COMMENT '0 = tidak delete, 1 = delete',
  `ads_date_created` timestamp NULL DEFAULT current_timestamp(),
  `ads_date_updated` datetime DEFAULT NULL,
  PRIMARY KEY (`idx`),
  UNIQUE KEY `ads_code` (`ads_code`),
  KEY `user_code` (`users_code`),
  KEY `ads_cart_code` (`ads_cart_code`),
  KEY `ads_cart_order_code` (`ads_cart_order_code`),
  KEY `product_code` (`product_code`),
  KEY `product_mobil_code` (`product_car_code`),
  KEY `product_mobil_loc_code` (`product_car_loc_code`),
  KEY `partner_code` (`partner_code`),
  KEY `partner_car_code` (`partner_car_code`),
  KEY `ads_date_created` (`ads_date_created`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Dumping data for table smartads_dev.ads: 0 rows
/*!40000 ALTER TABLE `ads` DISABLE KEYS */;
/*!40000 ALTER TABLE `ads` ENABLE KEYS */;

-- Dumping structure for table smartads_dev.ads_cart
CREATE TABLE IF NOT EXISTS `ads_cart` (
  `idx` int(11) NOT NULL AUTO_INCREMENT,
  `ads_cart_code` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `ads_cart_order_code` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `users_code` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `product_code` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `product_type` tinyint(1) DEFAULT NULL COMMENT '0 = fleet, 1 = non fleet',
  `product_car_code` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `product_car_loc_code` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `ads_cart_name` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `ads_cart_url` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `ads_cart_amount` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `ads_cart_status` tinyint(1) DEFAULT NULL COMMENT '0 = on cart, 1 = bid, 2 = paid, 3 = expired, 4 = dll',
  `ads_cart_start_date` date DEFAULT NULL,
  `ads_cart_end_date` date DEFAULT NULL,
  `is_deleted` tinyint(4) DEFAULT 0 COMMENT '0 = tidak delete, 1 = delete',
  `ads_cart_date_created` timestamp NULL DEFAULT current_timestamp(),
  `ads_cart_date_updated` datetime DEFAULT NULL,
  PRIMARY KEY (`idx`),
  UNIQUE KEY `ads_cart_code` (`ads_cart_code`),
  KEY `user_code` (`users_code`),
  KEY `product_code` (`product_code`),
  KEY `product_mobil_code` (`product_car_code`),
  KEY `product_mobil_loc_code` (`product_car_loc_code`),
  KEY `ads_cart_date_created` (`ads_cart_date_created`),
  KEY `ads_cart_order_code` (`ads_cart_order_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Dumping data for table smartads_dev.ads_cart: 0 rows
/*!40000 ALTER TABLE `ads_cart` DISABLE KEYS */;
/*!40000 ALTER TABLE `ads_cart` ENABLE KEYS */;

-- Dumping structure for table smartads_dev.ads_cart_order
CREATE TABLE IF NOT EXISTS `ads_cart_order` (
  `idx` int(11) NOT NULL AUTO_INCREMENT,
  `ads_cart_order_code` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `ads_cart_order_invoice` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `ads_cart_order_grand_total` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `ads_cart_order_payment_type` tinyint(1) DEFAULT NULL COMMENT '0 = wallet, 1 = IPG',
  `ads_cart_order_payment_status` tinyint(1) DEFAULT NULL COMMENT '0 = unpaid, 1 = paid, 2 = sebagian, 3 = cancelled',
  `ads_cart_order_date_created` timestamp NULL DEFAULT NULL,
  `ads_cart_order_date_updated` datetime DEFAULT NULL,
  PRIMARY KEY (`idx`),
  UNIQUE KEY `ads_cart_order_code` (`ads_cart_order_code`),
  KEY `ads_cart_order_date_created` (`ads_cart_order_date_created`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Dumping data for table smartads_dev.ads_cart_order: 0 rows
/*!40000 ALTER TABLE `ads_cart_order` DISABLE KEYS */;
/*!40000 ALTER TABLE `ads_cart_order` ENABLE KEYS */;

-- Dumping structure for table smartads_dev.migrations
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Dumping data for table smartads_dev.migrations: ~6 rows (approximately)
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
REPLACE INTO `migrations` (`id`, `migration`, `batch`) VALUES
	(1, '2014_10_12_100000_create_password_resets_table', 1),
	(2, '2016_06_01_000001_create_oauth_auth_codes_table', 1),
	(3, '2016_06_01_000002_create_oauth_access_tokens_table', 1),
	(4, '2016_06_01_000003_create_oauth_refresh_tokens_table', 1),
	(5, '2016_06_01_000004_create_oauth_clients_table', 1),
	(6, '2016_06_01_000005_create_oauth_personal_access_clients_table', 1);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;

-- Dumping structure for table smartads_dev.oauth_access_tokens
CREATE TABLE IF NOT EXISTS `oauth_access_tokens` (
  `id` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `client_id` int(10) unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `scopes` text COLLATE utf8_unicode_ci DEFAULT NULL,
  `revoked` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `expires_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `oauth_access_tokens_user_id_index` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Dumping data for table smartads_dev.oauth_access_tokens: ~11 rows (approximately)
/*!40000 ALTER TABLE `oauth_access_tokens` DISABLE KEYS */;
REPLACE INTO `oauth_access_tokens` (`id`, `user_id`, `client_id`, `name`, `scopes`, `revoked`, `created_at`, `updated_at`, `expires_at`) VALUES
	('1ddf961406db04ca48f7cbc30f3d732f5eacc5af1924887665b87c4640a7502d49aac7c4bde68ce5', 9, 1, 'kuniaiskandarnur@gmail.com', '[]', 0, '2019-07-09 16:35:18', '2019-07-09 16:35:18', '2019-07-11 16:35:18'),
	('37ec3a84f38d24d2d051355ff3c6b9552c98cb7f2fe1891242e2170ebe2bea96c0ab7fc1214250c5', 1, 1, 'sopianahmad120@gmail.com', '[]', 0, '2019-07-09 14:51:28', '2019-07-09 14:51:28', '2019-07-11 14:51:28'),
	('603cfc8edd2bc36412dca3608aecedb8194cb65cae93c720e5896170bd0c8cf64091463c39f5543e', 1, 1, 'kuniaiskandarnur@gmail.com', '[]', 0, '2019-07-09 14:40:32', '2019-07-09 14:40:32', '2019-07-11 14:40:32'),
	('66462768e52c38ef0d2781305c56c5c13e2cb7083334aace4c3843dfe13ce0f583df8cfc61ea3d8e', 9, 1, 'kuniaiskandarnur@gmail.com', '[]', 0, '2019-07-09 14:55:59', '2019-07-09 14:55:59', '2019-07-11 14:55:59'),
	('844b015694d7811852794cd858a8b2e6ddb1a9f10598f03fd7e3617283666f56233d75d684028a8b', 3, 1, 'sopianahmad120@gmail.com', '[]', 0, '2019-07-09 16:35:42', '2019-07-09 16:35:42', '2019-07-11 16:35:42'),
	('c8c5151115ef6b5a57540369c7be1f1bbeca836af84c5d5ccd09dd4ab8eae041cbb424c6d34eff7d', 3, 1, 'sopianahmad120@gmail.com', '[]', 0, '2019-07-09 14:55:08', '2019-07-09 14:55:08', '2019-07-11 14:55:08'),
	('ee47c58c72cd6f6173262c3ea9a0d7c3d7cba057c8599ca8b1a38cd7f74ce36a4c15e17c4d23ff55', 3, 3, 'sopianahmad120@gmail.com', '[]', 0, '2019-07-09 17:06:21', '2019-07-09 17:06:21', '2019-07-11 17:06:21');
/*!40000 ALTER TABLE `oauth_access_tokens` ENABLE KEYS */;

-- Dumping structure for table smartads_dev.oauth_auth_codes
CREATE TABLE IF NOT EXISTS `oauth_auth_codes` (
  `id` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `user_id` int(11) NOT NULL,
  `client_id` int(10) unsigned NOT NULL,
  `scopes` text COLLATE utf8_unicode_ci DEFAULT NULL,
  `revoked` tinyint(1) NOT NULL,
  `expires_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Dumping data for table smartads_dev.oauth_auth_codes: ~0 rows (approximately)
/*!40000 ALTER TABLE `oauth_auth_codes` DISABLE KEYS */;
/*!40000 ALTER TABLE `oauth_auth_codes` ENABLE KEYS */;

-- Dumping structure for table smartads_dev.oauth_clients
CREATE TABLE IF NOT EXISTS `oauth_clients` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `secret` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `redirect` text COLLATE utf8_unicode_ci NOT NULL,
  `personal_access_client` tinyint(1) NOT NULL,
  `password_client` tinyint(1) NOT NULL,
  `revoked` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `oauth_clients_user_id_index` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Dumping data for table smartads_dev.oauth_clients: ~2 rows (approximately)
/*!40000 ALTER TABLE `oauth_clients` DISABLE KEYS */;
REPLACE INTO `oauth_clients` (`id`, `user_id`, `name`, `secret`, `redirect`, `personal_access_client`, `password_client`, `revoked`, `created_at`, `updated_at`) VALUES
	(1, NULL, 'Laravel Personal Access Client', 'H282d9m7cgHYj8b174bnRYGPvuUBjVocRleWjL2C', 'http://localhost', 1, 0, 0, '2019-07-09 04:59:34', '2019-07-09 04:59:34'),
	(2, NULL, 'Laravel Password Grant Client', 'SIoMC4VjNVHv4o3t3O2RD0pimrml4uo3c8JmC6wO', 'http://localhost', 0, 1, 0, '2019-07-09 04:59:34', '2019-07-09 04:59:34'),
	(3, NULL, 'Laravel Personal Access Client', 'yN9EOldfKevMOKn4QGqzwPOOcG04O2Jsj5dCTU8v', 'http://localhost', 1, 0, 0, '2019-07-09 17:06:03', '2019-07-09 17:06:03'),
	(4, NULL, 'Laravel Password Grant Client', 'snNLwDcVY2cSGmxtrizWGyerjJI83ZTIsQtPDAaT', 'http://localhost', 0, 1, 0, '2019-07-09 17:06:03', '2019-07-09 17:06:03');
/*!40000 ALTER TABLE `oauth_clients` ENABLE KEYS */;

-- Dumping structure for table smartads_dev.oauth_personal_access_clients
CREATE TABLE IF NOT EXISTS `oauth_personal_access_clients` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `client_id` int(10) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `oauth_personal_access_clients_client_id_index` (`client_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Dumping data for table smartads_dev.oauth_personal_access_clients: ~1 rows (approximately)
/*!40000 ALTER TABLE `oauth_personal_access_clients` DISABLE KEYS */;
REPLACE INTO `oauth_personal_access_clients` (`id`, `client_id`, `created_at`, `updated_at`) VALUES
	(1, 1, '2019-07-09 04:59:34', '2019-07-09 04:59:34'),
	(2, 3, '2019-07-09 17:06:03', '2019-07-09 17:06:03');
/*!40000 ALTER TABLE `oauth_personal_access_clients` ENABLE KEYS */;

-- Dumping structure for table smartads_dev.oauth_refresh_tokens
CREATE TABLE IF NOT EXISTS `oauth_refresh_tokens` (
  `id` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `access_token_id` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `revoked` tinyint(1) NOT NULL,
  `expires_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `oauth_refresh_tokens_access_token_id_index` (`access_token_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Dumping data for table smartads_dev.oauth_refresh_tokens: ~0 rows (approximately)
/*!40000 ALTER TABLE `oauth_refresh_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `oauth_refresh_tokens` ENABLE KEYS */;

-- Dumping structure for table smartads_dev.partners
CREATE TABLE IF NOT EXISTS `partners` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `partner_code` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `name` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `partner_ktp` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `partner_hp` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `partner_company` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `kel_code` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `kec_code` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `kota_code` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `provinsi_code` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `partner_address` text COLLATE utf8_unicode_ci DEFAULT NULL,
  `partner_npwp` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `email` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `email_verified_at` datetime DEFAULT NULL,
  `remember_token` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `password` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `partner_activation_status` tinyint(1) DEFAULT 0 COMMENT '0=non aktivasi, 1=sudah aktivasi',
  `partner_verification_type` tinyint(1) DEFAULT NULL COMMENT '1 = email, 2 = sms',
  `partner_verification_token` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `partner_status` tinyint(1) DEFAULT 1 COMMENT '0=non aktif, 1=aktif, 2=suspend',
  `is_deleted` tinyint(1) DEFAULT 0 COMMENT '0 = tidak delete, 1 = delete',
  `is_login` tinyint(1) DEFAULT 1 COMMENT '0=tidak login, 1=login',
  `partner_forgotten_password_code` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `partner_forgotten_password_time` datetime DEFAULT NULL,
  `partner_date_created` timestamp NULL DEFAULT current_timestamp(),
  `partner_date_updated` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `partner_code` (`partner_code`),
  UNIQUE KEY `partner_email` (`email`),
  KEY `kel_code` (`kel_code`),
  KEY `kec_code` (`kec_code`),
  KEY `kota_code` (`kota_code`),
  KEY `provinsi_code` (`provinsi_code`),
  KEY `partner_date_created` (`partner_date_created`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Dumping data for table smartads_dev.partners: 1 rows
/*!40000 ALTER TABLE `partners` DISABLE KEYS */;
REPLACE INTO `partners` (`id`, `partner_code`, `name`, `partner_ktp`, `partner_hp`, `partner_company`, `kel_code`, `kec_code`, `kota_code`, `provinsi_code`, `partner_address`, `partner_npwp`, `email`, `email_verified_at`, `remember_token`, `password`, `partner_activation_status`, `partner_verification_type`, `partner_verification_token`, `partner_status`, `is_deleted`, `is_login`, `partner_forgotten_password_code`, `partner_forgotten_password_time`, `partner_date_created`, `partner_date_updated`) VALUES
	(3, 'p-01123123', 'Ahmad Sopian', NULL, '08972774965', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'sopianahmad120@gmail.com', NULL, NULL, '$2y$10$mVqE6XL14fTz6WQkDwrbXuD4cdP4jvZG/cqem/2ef61G1Vd/9lGQG', 0, NULL, NULL, 1, 0, 1, NULL, NULL, '2019-07-09 12:58:39', NULL);
/*!40000 ALTER TABLE `partners` ENABLE KEYS */;

-- Dumping structure for table smartads_dev.partner_car
CREATE TABLE IF NOT EXISTS `partner_car` (
  `idx` int(11) NOT NULL AUTO_INCREMENT,
  `partner_car_code` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `product_code` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `product_type` tinyint(1) DEFAULT NULL COMMENT '0 = fleet, 1 = non fleet',
  `product_car_code` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `partner_car_name` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `partner_car_nopol` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `partner_car_merk` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `partner_car_tahun` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `partner_car_nostnk` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `partner_car_filename` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `partner_car_status` tinyint(1) DEFAULT NULL COMMENT '0 = tidak aktif, 1 = aktif',
  `partner_car_date_created` timestamp NULL DEFAULT current_timestamp(),
  `partner_car_date_updated` datetime DEFAULT NULL,
  PRIMARY KEY (`idx`),
  UNIQUE KEY `product_car_code` (`partner_car_code`),
  KEY `product_code` (`product_code`),
  KEY `product_mobil_code` (`product_car_code`),
  KEY `partner_car_date_created` (`partner_car_date_created`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Dumping data for table smartads_dev.partner_car: 0 rows
/*!40000 ALTER TABLE `partner_car` DISABLE KEYS */;
/*!40000 ALTER TABLE `partner_car` ENABLE KEYS */;

-- Dumping structure for table smartads_dev.partner_device
CREATE TABLE IF NOT EXISTS `partner_device` (
  `idx` int(11) NOT NULL AUTO_INCREMENT,
  `user_device_code` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `user_code` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `imei` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `cloud_messaging_token` text COLLATE utf8_unicode_ci DEFAULT NULL,
  `screen_category` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `device_dencity_1` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `device_dencity_2` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `screen_size_in_pixel` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `screen_size_in_inchi` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `os_version` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `api_version` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `device` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `model` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `manufacturer` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `product` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `imsi` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `sim_operator` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `longitude` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `latitude` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `date_created` timestamp NULL DEFAULT current_timestamp(),
  `date_updated` datetime DEFAULT NULL,
  PRIMARY KEY (`idx`),
  UNIQUE KEY `user_device_code` (`user_device_code`),
  KEY `date_created` (`date_created`),
  KEY `user_code` (`user_code`),
  KEY `imei` (`imei`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Dumping data for table smartads_dev.partner_device: 0 rows
/*!40000 ALTER TABLE `partner_device` DISABLE KEYS */;
/*!40000 ALTER TABLE `partner_device` ENABLE KEYS */;

-- Dumping structure for table smartads_dev.password_resets
CREATE TABLE IF NOT EXISTS `password_resets` (
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  KEY `password_resets_email_index` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Dumping data for table smartads_dev.password_resets: ~0 rows (approximately)
/*!40000 ALTER TABLE `password_resets` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_resets` ENABLE KEYS */;

-- Dumping structure for table smartads_dev.product
CREATE TABLE IF NOT EXISTS `product` (
  `idk` int(11) NOT NULL AUTO_INCREMENT,
  `product_code` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `product_name` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `product_type` tinyint(1) DEFAULT NULL COMMENT ' 0 = fleet, 1 = non fleet',
  `product_status` tinyint(1) DEFAULT NULL COMMENT '0 = tidak aktif, 1 = aktif',
  `product_date_created` timestamp NULL DEFAULT current_timestamp(),
  `product_date_updated` datetime DEFAULT NULL,
  PRIMARY KEY (`idk`),
  UNIQUE KEY `product_code` (`product_code`),
  KEY `product_date_created` (`product_date_created`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Dumping data for table smartads_dev.product: 0 rows
/*!40000 ALTER TABLE `product` DISABLE KEYS */;
/*!40000 ALTER TABLE `product` ENABLE KEYS */;

-- Dumping structure for table smartads_dev.product_car
CREATE TABLE IF NOT EXISTS `product_car` (
  `idx` int(11) NOT NULL AUTO_INCREMENT,
  `product_car_code` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `product_car_name` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `product_car_status` tinyint(1) DEFAULT NULL COMMENT '0 = tidak aktif, 1 = aktif',
  `product_car_date_created` timestamp NULL DEFAULT current_timestamp(),
  `product_car_date_updated` datetime DEFAULT NULL,
  PRIMARY KEY (`idx`),
  UNIQUE KEY `product_car_code` (`product_car_code`),
  KEY `product_car_date_created` (`product_car_date_created`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Dumping data for table smartads_dev.product_car: 0 rows
/*!40000 ALTER TABLE `product_car` DISABLE KEYS */;
/*!40000 ALTER TABLE `product_car` ENABLE KEYS */;

-- Dumping structure for table smartads_dev.product_car_loc
CREATE TABLE IF NOT EXISTS `product_car_loc` (
  `idx` int(11) NOT NULL AUTO_INCREMENT,
  `product_car_loc_code` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `product_car_code` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `product_car_loc_name` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `product_car_loc_status` tinyint(1) DEFAULT NULL COMMENT '0 = tidak aktif, 1 = aktif',
  `product_car_loc_date_created` timestamp NULL DEFAULT NULL,
  `product_car_loc_date_updated` datetime DEFAULT NULL,
  PRIMARY KEY (`idx`),
  UNIQUE KEY `product_car_loc_code` (`product_car_loc_code`),
  KEY `product_car_loc_date_created` (`product_car_loc_date_created`),
  KEY `product_car_code` (`product_car_code`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Dumping data for table smartads_dev.product_car_loc: 0 rows
/*!40000 ALTER TABLE `product_car_loc` DISABLE KEYS */;
/*!40000 ALTER TABLE `product_car_loc` ENABLE KEYS */;

-- Dumping structure for table smartads_dev.slider
CREATE TABLE IF NOT EXISTS `slider` (
  `idx` int(11) NOT NULL AUTO_INCREMENT,
  `slider_code` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `slider_name` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `slider_desc` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `slider_filename` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `slider_type` tinyint(1) DEFAULT NULL COMMENT '1= user, 2 = partner, 3 = kombinasi',
  `slider_status` tinyint(1) DEFAULT 1 COMMENT '1= user, 2 = partner, 3 = kombinasi',
  `is_deleted` tinyint(1) DEFAULT 0 COMMENT '0 = tidak aktif, 1 = aktif',
  `slider_date_created` timestamp NULL DEFAULT current_timestamp(),
  `slider_date_updated` datetime DEFAULT NULL,
  PRIMARY KEY (`idx`),
  UNIQUE KEY `slider_code` (`slider_code`),
  KEY `slider_date_created` (`slider_date_created`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Dumping data for table smartads_dev.slider: 0 rows
/*!40000 ALTER TABLE `slider` DISABLE KEYS */;
REPLACE INTO `slider` (`idx`, `slider_code`, `slider_name`, `slider_desc`, `slider_filename`, `slider_type`, `slider_status`, `is_deleted`, `slider_date_created`, `slider_date_updated`) VALUES
	(1, 'b-12312', 'slider #1', 'sdasd', 'asda.png', 1, 1, 0, '2019-07-09 15:06:13', NULL);
/*!40000 ALTER TABLE `slider` ENABLE KEYS */;

-- Dumping structure for table smartads_dev.users
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `users_code` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `name` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `users_hp` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `users_company` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `users_referral_code` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `users_referral_partner` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `users_npwp` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `email` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `email_verified_at` datetime DEFAULT NULL,
  `remember_token` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `password` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `users_activation_status` tinyint(1) DEFAULT 0 COMMENT '0=non aktivasi, 1=sudah aktivasi',
  `users_verification_type` tinyint(1) DEFAULT NULL COMMENT '1 = email, 2 = sms',
  `users_verification_token` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `users_status` tinyint(1) DEFAULT 1 COMMENT '0=non aktif, 1=aktif, 2=suspend',
  `is_deleted` tinyint(1) DEFAULT 0 COMMENT '0 = tidak delete, 1 = delete',
  `is_login` tinyint(1) DEFAULT 1 COMMENT '0=tidak login, 1=login',
  `users_forgotten_password_code` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `users_forgotten_password_time` datetime DEFAULT NULL,
  `users_ip_address` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `users_last_login_date` datetime DEFAULT NULL,
  `users_date_created` timestamp NULL DEFAULT current_timestamp(),
  `users_date_updated` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_code` (`users_code`),
  UNIQUE KEY `user_email` (`email`),
  KEY `user_date_created` (`users_date_created`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Dumping data for table smartads_dev.users: 1 rows
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
REPLACE INTO `users` (`id`, `users_code`, `name`, `users_hp`, `users_company`, `users_referral_code`, `users_referral_partner`, `users_npwp`, `email`, `email_verified_at`, `remember_token`, `password`, `users_activation_status`, `users_verification_type`, `users_verification_token`, `users_status`, `is_deleted`, `is_login`, `users_forgotten_password_code`, `users_forgotten_password_time`, `users_ip_address`, `users_last_login_date`, `users_date_created`, `users_date_updated`) VALUES
	(9, 'u-1231231', 'Kurnia Nur Iskandar', NULL, NULL, NULL, NULL, NULL, 'kuniaiskandarnur@gmail.com', NULL, NULL, '$2y$10$mVqE6XL14fTz6WQkDwrbXuD4cdP4jvZG/cqem/2ef61G1Vd/9lGQG', 0, NULL, NULL, 1, 0, 1, NULL, NULL, NULL, NULL, '2019-07-09 13:52:37', NULL);
/*!40000 ALTER TABLE `users` ENABLE KEYS */;

-- Dumping structure for table smartads_dev.user_device
CREATE TABLE IF NOT EXISTS `user_device` (
  `idx` int(11) NOT NULL AUTO_INCREMENT,
  `user_device_code` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `user_code` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `imei` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `cloud_messaging_token` text COLLATE utf8_unicode_ci DEFAULT NULL,
  `screen_category` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `device_dencity_1` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `device_dencity_2` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `screen_size_in_pixel` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `screen_size_in_inchi` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `os_version` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `api_version` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `device` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `model` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `manufacturer` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `product` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `imsi` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `sim_operator` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `longitude` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `latitude` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `date_created` timestamp NULL DEFAULT current_timestamp(),
  `date_updated` datetime DEFAULT NULL,
  PRIMARY KEY (`idx`),
  UNIQUE KEY `user_device_code` (`user_device_code`),
  KEY `date_created` (`date_created`),
  KEY `user_code` (`user_code`),
  KEY `imei` (`imei`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Dumping data for table smartads_dev.user_device: 0 rows
/*!40000 ALTER TABLE `user_device` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_device` ENABLE KEYS */;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
beskem_devmigrations
