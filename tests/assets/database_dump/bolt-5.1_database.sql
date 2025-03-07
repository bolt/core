-- MySQL dump 10.13  Distrib 5.7.44, for Linux (x86_64)
--
-- Host: localhost    Database: bolt
-- ------------------------------------------------------
-- Server version	5.7.44

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `bolt_content`
--

DROP TABLE IF EXISTS `bolt_content`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bolt_content` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `author_id` int(11) DEFAULT NULL,
  `content_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL,
  `modified_at` datetime DEFAULT NULL,
  `published_at` datetime DEFAULT NULL,
  `depublished_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_F5AB2E9CF675F31B` (`author_id`),
  KEY `content_type_idx` (`content_type`),
  KEY `status_idx` (`status`),
  CONSTRAINT `FK_F5AB2E9CF675F31B` FOREIGN KEY (`author_id`) REFERENCES `bolt_user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bolt_content`
--

LOCK TABLES `bolt_content` WRITE;
/*!40000 ALTER TABLE `bolt_content` DISABLE KEYS */;
/*!40000 ALTER TABLE `bolt_content` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bolt_field`
--

DROP TABLE IF EXISTS `bolt_field`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bolt_field` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `content_id` int(11) NOT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sortorder` int(11) NOT NULL,
  `version` int(11) DEFAULT NULL,
  `type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_4A2EBBE584A0A3ED` (`content_id`),
  KEY `IDX_4A2EBBE5727ACA70` (`parent_id`),
  CONSTRAINT `FK_4A2EBBE5727ACA70` FOREIGN KEY (`parent_id`) REFERENCES `bolt_field` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_4A2EBBE584A0A3ED` FOREIGN KEY (`content_id`) REFERENCES `bolt_content` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bolt_field`
--

LOCK TABLES `bolt_field` WRITE;
/*!40000 ALTER TABLE `bolt_field` DISABLE KEYS */;
/*!40000 ALTER TABLE `bolt_field` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bolt_field_translation`
--

DROP TABLE IF EXISTS `bolt_field_translation`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bolt_field_translation` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `translatable_id` int(11) DEFAULT NULL,
  `value` json NOT NULL,
  `locale` varchar(5) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `field_translation_unique_translation` (`translatable_id`,`locale`),
  KEY `IDX_5C60C0542C2AC5D3` (`translatable_id`),
  CONSTRAINT `FK_5C60C0542C2AC5D3` FOREIGN KEY (`translatable_id`) REFERENCES `bolt_field` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bolt_field_translation`
--

LOCK TABLES `bolt_field_translation` WRITE;
/*!40000 ALTER TABLE `bolt_field_translation` DISABLE KEYS */;
/*!40000 ALTER TABLE `bolt_field_translation` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bolt_log`
--

DROP TABLE IF EXISTS `bolt_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bolt_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `message` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `context` longtext COLLATE utf8mb4_unicode_ci COMMENT '(DC2Type:array)',
  `level` smallint(6) NOT NULL,
  `level_name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL,
  `extra` longtext COLLATE utf8mb4_unicode_ci COMMENT '(DC2Type:array)',
  `user` longtext COLLATE utf8mb4_unicode_ci COMMENT '(DC2Type:array)',
  `content` int(11) DEFAULT NULL,
  `location` longtext COLLATE utf8mb4_unicode_ci COMMENT '(DC2Type:array)',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bolt_log`
--

LOCK TABLES `bolt_log` WRITE;
/*!40000 ALTER TABLE `bolt_log` DISABLE KEYS */;
/*!40000 ALTER TABLE `bolt_log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bolt_media`
--

DROP TABLE IF EXISTS `bolt_media`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bolt_media` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `author_id` int(11) DEFAULT NULL,
  `location` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `path` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `filename` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `width` int(11) DEFAULT NULL,
  `height` int(11) DEFAULT NULL,
  `filesize` int(11) DEFAULT NULL,
  `crop_x` int(11) DEFAULT NULL,
  `crop_y` int(11) DEFAULT NULL,
  `crop_zoom` double DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `modified_at` datetime NOT NULL,
  `title` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` varchar(1000) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `original_filename` varchar(1000) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `copyright` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_7BF75FB1F675F31B` (`author_id`),
  CONSTRAINT `FK_7BF75FB1F675F31B` FOREIGN KEY (`author_id`) REFERENCES `bolt_user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bolt_media`
--

LOCK TABLES `bolt_media` WRITE;
/*!40000 ALTER TABLE `bolt_media` DISABLE KEYS */;
/*!40000 ALTER TABLE `bolt_media` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bolt_relation`
--

DROP TABLE IF EXISTS `bolt_relation`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bolt_relation` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `from_content_id` int(11) NOT NULL,
  `to_content_id` int(11) NOT NULL,
  `position` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_3431ED74AF0465EA` (`from_content_id`),
  KEY `IDX_3431ED74A3934190` (`to_content_id`),
  CONSTRAINT `FK_3431ED74A3934190` FOREIGN KEY (`to_content_id`) REFERENCES `bolt_content` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_3431ED74AF0465EA` FOREIGN KEY (`from_content_id`) REFERENCES `bolt_content` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bolt_relation`
--

LOCK TABLES `bolt_relation` WRITE;
/*!40000 ALTER TABLE `bolt_relation` DISABLE KEYS */;
/*!40000 ALTER TABLE `bolt_relation` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bolt_reset_password_request`
--

DROP TABLE IF EXISTS `bolt_reset_password_request`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bolt_reset_password_request` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `selector` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `hashed_token` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `requested_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `expires_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  PRIMARY KEY (`id`),
  KEY `IDX_D04070DCA76ED395` (`user_id`),
  CONSTRAINT `FK_D04070DCA76ED395` FOREIGN KEY (`user_id`) REFERENCES `bolt_user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bolt_reset_password_request`
--

LOCK TABLES `bolt_reset_password_request` WRITE;
/*!40000 ALTER TABLE `bolt_reset_password_request` DISABLE KEYS */;
/*!40000 ALTER TABLE `bolt_reset_password_request` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bolt_taxonomy`
--

DROP TABLE IF EXISTS `bolt_taxonomy`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bolt_taxonomy` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sortorder` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bolt_taxonomy`
--

LOCK TABLES `bolt_taxonomy` WRITE;
/*!40000 ALTER TABLE `bolt_taxonomy` DISABLE KEYS */;
/*!40000 ALTER TABLE `bolt_taxonomy` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bolt_taxonomy_content`
--

DROP TABLE IF EXISTS `bolt_taxonomy_content`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bolt_taxonomy_content` (
  `taxonomy_id` int(11) NOT NULL,
  `content_id` int(11) NOT NULL,
  PRIMARY KEY (`taxonomy_id`,`content_id`),
  KEY `IDX_C5BCC03C9557E6F6` (`taxonomy_id`),
  KEY `IDX_C5BCC03C84A0A3ED` (`content_id`),
  CONSTRAINT `FK_C5BCC03C84A0A3ED` FOREIGN KEY (`content_id`) REFERENCES `bolt_content` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_C5BCC03C9557E6F6` FOREIGN KEY (`taxonomy_id`) REFERENCES `bolt_taxonomy` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bolt_taxonomy_content`
--

LOCK TABLES `bolt_taxonomy_content` WRITE;
/*!40000 ALTER TABLE `bolt_taxonomy_content` DISABLE KEYS */;
/*!40000 ALTER TABLE `bolt_taxonomy_content` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bolt_user`
--

DROP TABLE IF EXISTS `bolt_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bolt_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `display_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `username` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `roles` json NOT NULL,
  `lastseen_at` datetime DEFAULT NULL,
  `last_ip` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `locale` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `backend_theme` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'enabled',
  `avatar` varchar(250) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_57663792F85E0677` (`username`),
  UNIQUE KEY `UNIQ_57663792E7927C74` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bolt_user`
--

LOCK TABLES `bolt_user` WRITE;
/*!40000 ALTER TABLE `bolt_user` DISABLE KEYS */;
/*!40000 ALTER TABLE `bolt_user` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bolt_user_auth_token`
--

DROP TABLE IF EXISTS `bolt_user_auth_token`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bolt_user_auth_token` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `useragent` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `validity` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_8B90D313A76ED395` (`user_id`),
  CONSTRAINT `FK_8B90D313A76ED395` FOREIGN KEY (`user_id`) REFERENCES `bolt_user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bolt_user_auth_token`
--

LOCK TABLES `bolt_user_auth_token` WRITE;
/*!40000 ALTER TABLE `bolt_user_auth_token` DISABLE KEYS */;
/*!40000 ALTER TABLE `bolt_user_auth_token` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `doctrine_migration_versions`
--

DROP TABLE IF EXISTS `doctrine_migration_versions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `doctrine_migration_versions` (
  `version` varchar(191) COLLATE utf8_unicode_ci NOT NULL,
  `executed_at` datetime DEFAULT NULL,
  `execution_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `doctrine_migration_versions`
--

LOCK TABLES `doctrine_migration_versions` WRITE;
/*!40000 ALTER TABLE `doctrine_migration_versions` DISABLE KEYS */;
INSERT INTO `doctrine_migration_versions` VALUES ('Bolt\\DoctrineMigrations\\Version20201210105836','2025-03-08 00:01:22',453),('Bolt\\DoctrineMigrations\\Version20211123103530','2025-03-08 00:01:22',16),('DoctrineMigrations\\Version20250307155305','2025-03-08 00:01:22',0);
/*!40000 ALTER TABLE `doctrine_migration_versions` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-03-07 23:02:31
