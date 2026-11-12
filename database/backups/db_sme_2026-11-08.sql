-- MySQL dump 10.13  Distrib 8.0.19, for Win64 (x86_64)
--
-- Host: localhost    Database: db_sme
-- ------------------------------------------------------
-- Server version	8.0.30

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `alerts_detect`
--

DROP TABLE IF EXISTS `alerts_detect`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `alerts_detect` (
  `id_alert` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_profile` bigint unsigned NOT NULL,
  `alert_type` varchar(100) NOT NULL,
  `saverity` enum('Low','Medium','High','Critical') NOT NULL,
  `message` text NOT NULL,
  `is_resolved` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL,
  PRIMARY KEY (`id_alert`),
  KEY `alerts_detect_sme_profiles_FK` (`id_profile`),
  CONSTRAINT `alerts_detect_sme_profiles_FK` FOREIGN KEY (`id_profile`) REFERENCES `sme_profiles` (`id_profile`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `alerts_detect`
--

LOCK TABLES `alerts_detect` WRITE;
/*!40000 ALTER TABLE `alerts_detect` DISABLE KEYS */;
/*!40000 ALTER TABLE `alerts_detect` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `autopsy_audit_logs`
--

DROP TABLE IF EXISTS `autopsy_audit_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `autopsy_audit_logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_user` bigint unsigned DEFAULT NULL,
  `action` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `entity_type` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `entity_id` bigint unsigned DEFAULT NULL,
  `old_values` json DEFAULT NULL,
  `new_values` json DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `autopsy_audit_logs_id_user_foreign` (`id_user`),
  KEY `autopsy_audit_logs_entity_type_entity_id_index` (`entity_type`,`entity_id`),
  KEY `autopsy_audit_logs_created_at_index` (`created_at`),
  CONSTRAINT `autopsy_audit_logs_id_user_foreign` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `autopsy_audit_logs`
--

LOCK TABLES `autopsy_audit_logs` WRITE;
/*!40000 ALTER TABLE `autopsy_audit_logs` DISABLE KEYS */;
INSERT INTO `autopsy_audit_logs` VALUES (1,1,'admin.user.updated','user',3,'{\"role\": \"sme_owner\", \"email\": \"admin1234@gmail.com\", \"full_name\": \"Admin1\", \"is_verified\": false}','{\"role\": \"sme_owner\", \"email\": \"admin1234@gmail.com\", \"full_name\": \"Admin1\", \"is_verified\": false}','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','2026-11-04 06:11:48'),(2,1,'admin.user.updated','user',3,'{\"role\": \"sme_owner\", \"email\": \"admin1234@gmail.com\", \"full_name\": \"Admin1\", \"is_verified\": false}','{\"role\": \"sme_owner\", \"email\": \"admin1234@gmail.com\", \"full_name\": \"Admin1\", \"is_verified\": true}','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','2026-11-05 05:56:56'),(3,1,'admin.user.updated','user',3,'{\"role\": \"sme_owner\", \"email\": \"admin1234@gmail.com\", \"full_name\": \"Admin1\", \"is_verified\": true}','{\"role\": \"user\", \"email\": \"admin1234@gmail.com\", \"full_name\": \"Admin1\", \"is_verified\": true}','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','2026-11-05 05:58:17'),(4,1,'admin.user.updated','user',3,'{\"role\": \"user\", \"email\": \"admin1234@gmail.com\", \"full_name\": \"Admin1\", \"is_verified\": true}','{\"role\": \"sme_owner\", \"email\": \"admin1234@gmail.com\", \"full_name\": \"Admin1\", \"is_verified\": true}','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','2026-11-05 05:58:57'),(5,1,'admin.symptom.updated','autopsy_symptom',3,'{\"code\": \"high_operating_cost\", \"name\": \"Biaya operasional tinggi\", \"operator\": \"greater_than\", \"severity\": \"high\", \"is_active\": true, \"threshold\": \"70.0000\", \"metric_key\": \"operating_expense_ratio\", \"description\": \"Rasio biaya operasional terhadap pendapatan melebihi 70%.\"}','{\"code\": \"high_operating_cost\", \"name\": \"Biaya operasional tinggi\", \"operator\": \"greater_than\", \"severity\": \"high\", \"is_active\": true, \"threshold\": \"70.0000\", \"metric_key\": \"operating_expense_ratio\", \"updated_by\": 1, \"description\": \"Rasio biaya operasional terhadap pendapatan melebihi 70%.\"}','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','2026-11-05 05:59:22'),(6,1,'admin.profile.created','sme_profile',1,NULL,'{\"city\": \"Surabaya\", \"address\": \"Jalanin aja dulu\", \"id_user\": \"3\", \"legal_type\": \"Perorangan\", \"id_category\": \"3\", \"business_name\": \"Kocak\", \"established_year\": \"2020\"}','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.145.0 Chrome/150.0.7871.250 Electron/43.8.3 Safari/537.36','2026-11-05 07:17:22'),(7,1,'admin.profile.updated','sme_profile',1,'{\"city\": \"Surabaya\", \"address\": \"Jalanin aja dulu\", \"id_user\": 3, \"legal_type\": \"Perorangan\", \"id_category\": 3, \"business_name\": \"Kocak\", \"established_year\": 2020}','{\"city\": \"Surabaya\", \"address\": \"Jalanin aja dulu\", \"id_user\": \"3\", \"legal_type\": \"Perorangan\", \"id_category\": \"3\", \"business_name\": \"Kocak\", \"established_year\": \"2020\"}','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.145.1 Chrome/150.0.7871.250 Electron/43.8.3 Safari/537.36','2026-11-07 09:10:40'),(8,1,'admin.user.deleted','user',7,'{\"role\": \"sme_owner\", \"email\": \"qwerty@gmail.com\", \"full_name\": \"Kicentt\", \"is_verified\": false}',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.145.1 Chrome/150.0.7871.250 Electron/43.8.3 Safari/537.36','2026-11-07 09:18:40'),(9,1,'admin.user.updated','user',11,'{\"role\": \"sme_owner\", \"email\": \"qwerty@gmail.com\", \"full_name\": \"Kicentt\", \"is_verified\": false}','{\"role\": \"sme_owner\", \"email\": \"kicentt1234@gmail.com\", \"full_name\": \"Kicentt\", \"is_verified\": false}','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.145.1 Chrome/150.0.7871.250 Electron/43.8.3 Safari/537.36','2026-11-07 09:38:20'),(10,1,'admin.user.updated','user',11,'{\"role\": \"sme_owner\", \"email\": \"kicentt1234@gmail.com\", \"full_name\": \"Kicentt\", \"is_verified\": false}','{\"role\": \"sme_owner\", \"email\": \"kicentt1234@gmail.com\", \"full_name\": \"Kicentt\", \"is_verified\": false}','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.145.1 Chrome/150.0.7871.250 Electron/43.8.3 Safari/537.36','2026-11-07 09:39:54'),(11,1,'admin.user.updated','user',11,'{\"role\": \"sme_owner\", \"email\": \"kicentt1234@gmail.com\", \"full_name\": \"Kicentt\", \"is_verified\": false}','{\"role\": \"sme_owner\", \"email\": \"kicentt1234@gmail.com\", \"full_name\": \"Kicentt\", \"is_verified\": false}','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.145.1 Chrome/150.0.7871.250 Electron/43.8.3 Safari/537.36','2026-11-07 09:41:06'),(12,1,'admin.user.updated','user',11,'{\"role\": \"sme_owner\", \"email\": \"kicentt1234@gmail.com\", \"full_name\": \"Kicentt\", \"is_verified\": false}','{\"role\": \"sme_owner\", \"email\": \"kicentt1234@gmail.com\", \"full_name\": \"Kicentt\", \"is_verified\": false}','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.145.1 Chrome/150.0.7871.250 Electron/43.8.3 Safari/537.36','2026-11-07 09:42:26');
/*!40000 ALTER TABLE `autopsy_audit_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `autopsy_diagnoses`
--

DROP TABLE IF EXISTS `autopsy_diagnoses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `autopsy_diagnoses` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `severity` enum('critical','high','medium','low') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'medium',
  `priority` smallint unsigned NOT NULL DEFAULT '100',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_by` bigint unsigned DEFAULT NULL,
  `updated_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `autopsy_diagnoses_code_unique` (`code`),
  KEY `autopsy_diagnoses_created_by_foreign` (`created_by`),
  KEY `autopsy_diagnoses_updated_by_foreign` (`updated_by`),
  KEY `autopsy_diagnoses_is_active_severity_priority_index` (`is_active`,`severity`,`priority`),
  CONSTRAINT `autopsy_diagnoses_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id_user`) ON DELETE SET NULL,
  CONSTRAINT `autopsy_diagnoses_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id_user`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `autopsy_diagnoses`
--

LOCK TABLES `autopsy_diagnoses` WRITE;
/*!40000 ALTER TABLE `autopsy_diagnoses` DISABLE KEYS */;
/*!40000 ALTER TABLE `autopsy_diagnoses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `autopsy_recommendation_steps`
--

DROP TABLE IF EXISTS `autopsy_recommendation_steps`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `autopsy_recommendation_steps` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_recommendation` bigint unsigned NOT NULL,
  `step_order` smallint unsigned NOT NULL DEFAULT '1',
  `step` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `autopsy_recommendation_steps_id_recommendation_step_order_unique` (`id_recommendation`,`step_order`),
  CONSTRAINT `autopsy_recommendation_steps_id_recommendation_foreign` FOREIGN KEY (`id_recommendation`) REFERENCES `autopsy_recommendations` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `autopsy_recommendation_steps`
--

LOCK TABLES `autopsy_recommendation_steps` WRITE;
/*!40000 ALTER TABLE `autopsy_recommendation_steps` DISABLE KEYS */;
/*!40000 ALTER TABLE `autopsy_recommendation_steps` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `autopsy_recommendations`
--

DROP TABLE IF EXISTS `autopsy_recommendations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `autopsy_recommendations` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_diagnosis` bigint unsigned NOT NULL,
  `code` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `priority` smallint unsigned NOT NULL DEFAULT '100',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_by` bigint unsigned DEFAULT NULL,
  `updated_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `autopsy_recommendations_code_unique` (`code`),
  KEY `autopsy_recommendations_created_by_foreign` (`created_by`),
  KEY `autopsy_recommendations_updated_by_foreign` (`updated_by`),
  KEY `autopsy_recommendations_id_diagnosis_is_active_index` (`id_diagnosis`,`is_active`),
  CONSTRAINT `autopsy_recommendations_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id_user`) ON DELETE SET NULL,
  CONSTRAINT `autopsy_recommendations_id_diagnosis_foreign` FOREIGN KEY (`id_diagnosis`) REFERENCES `autopsy_diagnoses` (`id`) ON DELETE CASCADE,
  CONSTRAINT `autopsy_recommendations_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id_user`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `autopsy_recommendations`
--

LOCK TABLES `autopsy_recommendations` WRITE;
/*!40000 ALTER TABLE `autopsy_recommendations` DISABLE KEYS */;
/*!40000 ALTER TABLE `autopsy_recommendations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `autopsy_rule_conditions`
--

DROP TABLE IF EXISTS `autopsy_rule_conditions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `autopsy_rule_conditions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_rule_group` bigint unsigned NOT NULL,
  `id_symptom` bigint unsigned NOT NULL,
  `is_negated` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `autopsy_rule_conditions_id_rule_group_id_symptom_unique` (`id_rule_group`,`id_symptom`),
  KEY `autopsy_rule_conditions_id_symptom_foreign` (`id_symptom`),
  CONSTRAINT `autopsy_rule_conditions_id_rule_group_foreign` FOREIGN KEY (`id_rule_group`) REFERENCES `autopsy_rule_groups` (`id`) ON DELETE CASCADE,
  CONSTRAINT `autopsy_rule_conditions_id_symptom_foreign` FOREIGN KEY (`id_symptom`) REFERENCES `autopsy_symptoms` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `autopsy_rule_conditions`
--

LOCK TABLES `autopsy_rule_conditions` WRITE;
/*!40000 ALTER TABLE `autopsy_rule_conditions` DISABLE KEYS */;
/*!40000 ALTER TABLE `autopsy_rule_conditions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `autopsy_rule_groups`
--

DROP TABLE IF EXISTS `autopsy_rule_groups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `autopsy_rule_groups` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_rule_version` bigint unsigned NOT NULL,
  `group_order` smallint unsigned NOT NULL DEFAULT '1',
  `label` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `autopsy_rule_groups_id_rule_version_group_order_unique` (`id_rule_version`,`group_order`),
  CONSTRAINT `autopsy_rule_groups_id_rule_version_foreign` FOREIGN KEY (`id_rule_version`) REFERENCES `autopsy_rule_versions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `autopsy_rule_groups`
--

LOCK TABLES `autopsy_rule_groups` WRITE;
/*!40000 ALTER TABLE `autopsy_rule_groups` DISABLE KEYS */;
/*!40000 ALTER TABLE `autopsy_rule_groups` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `autopsy_rule_versions`
--

DROP TABLE IF EXISTS `autopsy_rule_versions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `autopsy_rule_versions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_rule` bigint unsigned NOT NULL,
  `version` int unsigned NOT NULL DEFAULT '1',
  `status` enum('draft','published','archived') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `is_active` tinyint(1) NOT NULL DEFAULT '0',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `published_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `id_diagnosis` bigint unsigned DEFAULT NULL,
  `severity` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `priority` smallint unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `autopsy_rule_versions_id_rule_version_unique` (`id_rule`,`version`),
  KEY `autopsy_rule_versions_created_by_foreign` (`created_by`),
  KEY `autopsy_rule_versions_id_rule_is_active_index` (`id_rule`,`is_active`),
  KEY `autopsy_rule_versions_id_diagnosis_index` (`id_diagnosis`),
  CONSTRAINT `autopsy_rule_versions_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id_user`) ON DELETE SET NULL,
  CONSTRAINT `autopsy_rule_versions_id_rule_foreign` FOREIGN KEY (`id_rule`) REFERENCES `autopsy_rules` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `autopsy_rule_versions`
--

LOCK TABLES `autopsy_rule_versions` WRITE;
/*!40000 ALTER TABLE `autopsy_rule_versions` DISABLE KEYS */;
/*!40000 ALTER TABLE `autopsy_rule_versions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `autopsy_rules`
--

DROP TABLE IF EXISTS `autopsy_rules`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `autopsy_rules` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `id_diagnosis` bigint unsigned NOT NULL,
  `severity` enum('critical','high','medium','low') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'medium',
  `priority` smallint unsigned NOT NULL DEFAULT '100',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_by` bigint unsigned DEFAULT NULL,
  `updated_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `autopsy_rules_code_unique` (`code`),
  KEY `autopsy_rules_id_diagnosis_foreign` (`id_diagnosis`),
  KEY `autopsy_rules_created_by_foreign` (`created_by`),
  KEY `autopsy_rules_updated_by_foreign` (`updated_by`),
  KEY `autopsy_rules_is_active_id_diagnosis_index` (`is_active`,`id_diagnosis`),
  CONSTRAINT `autopsy_rules_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id_user`) ON DELETE SET NULL,
  CONSTRAINT `autopsy_rules_id_diagnosis_foreign` FOREIGN KEY (`id_diagnosis`) REFERENCES `autopsy_diagnoses` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `autopsy_rules_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id_user`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `autopsy_rules`
--

LOCK TABLES `autopsy_rules` WRITE;
/*!40000 ALTER TABLE `autopsy_rules` DISABLE KEYS */;
/*!40000 ALTER TABLE `autopsy_rules` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `autopsy_symptoms`
--

DROP TABLE IF EXISTS `autopsy_symptoms`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `autopsy_symptoms` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `metric_key` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `operator` enum('greater_than','greater_than_or_equal','less_than','less_than_or_equal','equal','not_equal') COLLATE utf8mb4_unicode_ci NOT NULL,
  `threshold` decimal(15,4) NOT NULL,
  `severity` enum('critical','high','medium','low') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'medium',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_by` bigint unsigned DEFAULT NULL,
  `updated_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `autopsy_symptoms_code_unique` (`code`),
  KEY `autopsy_symptoms_created_by_foreign` (`created_by`),
  KEY `autopsy_symptoms_updated_by_foreign` (`updated_by`),
  KEY `autopsy_symptoms_is_active_metric_key_index` (`is_active`,`metric_key`),
  CONSTRAINT `autopsy_symptoms_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id_user`) ON DELETE SET NULL,
  CONSTRAINT `autopsy_symptoms_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id_user`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `autopsy_symptoms`
--

LOCK TABLES `autopsy_symptoms` WRITE;
/*!40000 ALTER TABLE `autopsy_symptoms` DISABLE KEYS */;
INSERT INTO `autopsy_symptoms` VALUES (1,'revenue_declined','Penurunan omzet','Omzet/pendapatan turun lebih dari 10% dibanding periode sebelumnya.','revenue_change_percent','less_than',-10.0000,'high',1,NULL,NULL,'2026-11-02 21:36:26','2026-11-02 21:36:26'),(2,'customer_declined','Penurunan jumlah pelanggan','Jumlah pelanggan turun lebih dari 20% dibanding periode sebelumnya.','customer_change_percent','less_than',-20.0000,'medium',1,NULL,NULL,'2026-11-02 21:36:26','2026-11-02 21:36:26'),(3,'high_operating_cost','Biaya operasional tinggi','Rasio biaya operasional terhadap pendapatan melebihi 70%.','operating_expense_ratio','greater_than',70.0000,'high',1,NULL,1,'2026-11-02 21:36:26','2026-11-05 05:59:22'),(4,'low_profit_margin','Margin laba rendah','Margin laba di bawah 10%.','profit_margin','less_than',10.0000,'medium',1,NULL,NULL,'2026-11-02 21:36:26','2026-11-02 21:36:26');
/*!40000 ALTER TABLE `autopsy_symptoms` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `business_categories`
--

DROP TABLE IF EXISTS `business_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `business_categories` (
  `id_category` bigint unsigned NOT NULL AUTO_INCREMENT,
  `category_name` varchar(100) NOT NULL,
  `deskripsi` text,
  PRIMARY KEY (`id_category`),
  UNIQUE KEY `business_categories_unique` (`category_name`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `business_categories`
--

LOCK TABLES `business_categories` WRITE;
/*!40000 ALTER TABLE `business_categories` DISABLE KEYS */;
INSERT INTO `business_categories` VALUES (1,'Kuliner','Usaha makanan dan minuman'),(2,'Retail','Perdagangan barang eceran'),(3,'Jasa','Usaha jasa dan konsultasi'),(4,'Manufaktur','Produksi dan pengolahan barang'),(5,'Pertanian','Pertanian, perkebunan, dan agribisnis'),(6,'Teknologi','Produk digital dan teknologi');
/*!40000 ALTER TABLE `business_categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `business_data`
--

DROP TABLE IF EXISTS `business_data`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `business_data` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_profile` bigint unsigned NOT NULL,
  `period_date` date NOT NULL,
  `omzet` decimal(15,2) NOT NULL DEFAULT '0.00',
  `pendapatan` decimal(15,2) NOT NULL DEFAULT '0.00',
  `pengeluaran` decimal(15,2) NOT NULL DEFAULT '0.00',
  `laba` decimal(15,2) NOT NULL DEFAULT '0.00',
  `jumlah_transaksi` int NOT NULL DEFAULT '0',
  `jumlah_pelanggan` int NOT NULL DEFAULT '0',
  `produk_terjual` int NOT NULL DEFAULT '0',
  `produk_tidak_terjual` int NOT NULL DEFAULT '0',
  `stok` int NOT NULL DEFAULT '0',
  `biaya_operasional` decimal(15,2) NOT NULL DEFAULT '0.00',
  `biaya_pemasaran` decimal(15,2) NOT NULL DEFAULT '0.00',
  `data_pendukung` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `business_data_id_profile_period_date_unique` (`id_profile`,`period_date`),
  CONSTRAINT `business_data_id_profile_foreign` FOREIGN KEY (`id_profile`) REFERENCES `sme_profiles` (`id_profile`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `business_data`
--

LOCK TABLES `business_data` WRITE;
/*!40000 ALTER TABLE `business_data` DISABLE KEYS */;
/*!40000 ALTER TABLE `business_data` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `business_profiles`
--

DROP TABLE IF EXISTS `business_profiles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `business_profiles` (
  `id_business_profile` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_user` bigint unsigned NOT NULL,
  `id_category` bigint unsigned NOT NULL,
  `business_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `legal_type` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `city` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `province` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `postal_code` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text COLLATE utf8mb4_unicode_ci,
  `phone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `business_email` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `website` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nib` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `npwp` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `employee_count` int unsigned DEFAULT NULL,
  `established_year` int unsigned NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_business_profile`),
  KEY `business_profiles_id_user_foreign` (`id_user`),
  KEY `business_profiles_id_category_foreign` (`id_category`),
  CONSTRAINT `business_profiles_id_category_foreign` FOREIGN KEY (`id_category`) REFERENCES `business_categories` (`id_category`),
  CONSTRAINT `business_profiles_id_user_foreign` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `business_profiles`
--

LOCK TABLES `business_profiles` WRITE;
/*!40000 ALTER TABLE `business_profiles` DISABLE KEYS */;
/*!40000 ALTER TABLE `business_profiles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache`
--

LOCK TABLES `cache` WRITE;
/*!40000 ALTER TABLE `cache` DISABLE KEYS */;
INSERT INTO `cache` VALUES ('sme_nexus_cache_17ba0791499db908433b80f37c5fbc89b870084b','i:1;',1794069838),('sme_nexus_cache_17ba0791499db908433b80f37c5fbc89b870084b:timer','i:1794069838;',1794069838),('sme_nexus_cache_5c785c036466adea360111aa28563bfd556b5fba','i:1;',1794071647),('sme_nexus_cache_5c785c036466adea360111aa28563bfd556b5fba:timer','i:1794071647;',1794071647),('sme_nexus_cache_7b52009b64fd0a2a49e6d8a939753077792b0554','i:1;',1794070603),('sme_nexus_cache_7b52009b64fd0a2a49e6d8a939753077792b0554:timer','i:1794070603;',1794070603),('sme_nexus_cache_902ba3cda1883801594b6e1b452790cc53948fda','i:1;',1794067638),('sme_nexus_cache_902ba3cda1883801594b6e1b452790cc53948fda:timer','i:1794067638;',1794067638),('sme_nexus_cache_admin@gmail.com|127.0.0.1','i:2;',1794102399),('sme_nexus_cache_admin@gmail.com|127.0.0.1:timer','i:1794102399;',1794102399),('sme_nexus_cache_b1d5781111d84f7b3fe45a0852e59758cd7a87e5','i:1;',1794069011),('sme_nexus_cache_b1d5781111d84f7b3fe45a0852e59758cd7a87e5:timer','i:1794069011;',1794069011),('sme_nexus_cache_email@example.com|127.0.0.1','i:2;',1794067791),('sme_nexus_cache_email@example.com|127.0.0.1:timer','i:1794067791;',1794067791),('sme_nexus_cache_fa35e192121eabf3dabf9f5ea6abdbcbc107ac3b','i:1;',1794071672),('sme_nexus_cache_fa35e192121eabf3dabf9f5ea6abdbcbc107ac3b:timer','i:1794071672;',1794071672),('sme_nexus_cache_kicentt1234@gmailkicentt0212.com|127.0.0.1','i:1;',1794070723),('sme_nexus_cache_kicentt1234@gmailkicentt0212.com|127.0.0.1:timer','i:1794070723;',1794070723),('sme_nexus_cache_qwerty@gmail.com|127.0.0.1','i:1;',1794069742),('sme_nexus_cache_qwerty@gmail.com|127.0.0.1:timer','i:1794069742;',1794069742);
/*!40000 ALTER TABLE `cache` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache_locks`
--

DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache_locks`
--

LOCK TABLES `cache_locks` WRITE;
/*!40000 ALTER TABLE `cache_locks` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache_locks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `collaboration_rooms`
--

DROP TABLE IF EXISTS `collaboration_rooms`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `collaboration_rooms` (
  `id_room` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_match` bigint unsigned NOT NULL,
  `room_status` enum('Active','Archived') NOT NULL DEFAULT 'Active',
  `created_at` timestamp NOT NULL,
  PRIMARY KEY (`id_room`),
  KEY `collaboration_rooms_match_requests_FK` (`id_match`),
  CONSTRAINT `collaboration_rooms_match_requests_FK` FOREIGN KEY (`id_match`) REFERENCES `match_requests` (`id_match`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `collaboration_rooms`
--

LOCK TABLES `collaboration_rooms` WRITE;
/*!40000 ALTER TABLE `collaboration_rooms` DISABLE KEYS */;
/*!40000 ALTER TABLE `collaboration_rooms` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `connection_listings`
--

DROP TABLE IF EXISTS `connection_listings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `connection_listings` (
  `id_listing` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_user` bigint unsigned NOT NULL,
  `id_profile` bigint unsigned NOT NULL,
  `type` enum('Mentor','Supplier','B2B_Partner','Investor') NOT NULL,
  `title` varchar(150) NOT NULL,
  `description` text NOT NULL,
  `status` enum('Open','Closed','Draft') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT 'Open',
  `created_at` timestamp NOT NULL,
  PRIMARY KEY (`id_listing`),
  KEY `connection_listings_users_FK` (`id_user`),
  KEY `connection_listings_sme_profiles_FK` (`id_profile`),
  CONSTRAINT `connection_listings_sme_profiles_FK` FOREIGN KEY (`id_profile`) REFERENCES `sme_profiles` (`id_profile`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `connection_listings_users_FK` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `connection_listings`
--

LOCK TABLES `connection_listings` WRITE;
/*!40000 ALTER TABLE `connection_listings` DISABLE KEYS */;
/*!40000 ALTER TABLE `connection_listings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `diagnostic_assessments`
--

DROP TABLE IF EXISTS `diagnostic_assessments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `diagnostic_assessments` (
  `id_assessment` int NOT NULL AUTO_INCREMENT,
  `id_profile` bigint unsigned NOT NULL,
  `overall_score` decimal(5,2) DEFAULT NULL,
  `health_status` enum('Healthy','At Risk','Critical') DEFAULT NULL,
  `assessment_date` timestamp NOT NULL,
  PRIMARY KEY (`id_assessment`),
  KEY `diagnostic_assessments_sme_profiles_FK` (`id_profile`),
  CONSTRAINT `diagnostic_assessments_sme_profiles_FK` FOREIGN KEY (`id_profile`) REFERENCES `sme_profiles` (`id_profile`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `diagnostic_assessments`
--

LOCK TABLES `diagnostic_assessments` WRITE;
/*!40000 ALTER TABLE `diagnostic_assessments` DISABLE KEYS */;
/*!40000 ALTER TABLE `diagnostic_assessments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `diagnostic_frameworks`
--

DROP TABLE IF EXISTS `diagnostic_frameworks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `diagnostic_frameworks` (
  `id_framework` int NOT NULL AUTO_INCREMENT,
  `parameter_key` varchar(100) NOT NULL,
  `title` varchar(150) NOT NULL,
  `weight` decimal(5,2) NOT NULL,
  `benchmark_value` decimal(15,2) DEFAULT NULL,
  PRIMARY KEY (`id_framework`),
  UNIQUE KEY `diagnostic_frameworks_unique` (`parameter_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `diagnostic_frameworks`
--

LOCK TABLES `diagnostic_frameworks` WRITE;
/*!40000 ALTER TABLE `diagnostic_frameworks` DISABLE KEYS */;
/*!40000 ALTER TABLE `diagnostic_frameworks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `diagnostic_results`
--

DROP TABLE IF EXISTS `diagnostic_results`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `diagnostic_results` (
  `id_result` int NOT NULL AUTO_INCREMENT,
  `id_assessment` int NOT NULL,
  `id_framework` int NOT NULL,
  `score` decimal(5,2) NOT NULL,
  `finding_notes` text,
  `action_recommendation` text,
  PRIMARY KEY (`id_result`),
  KEY `diagnostic_results_diagnostic_assessments_FK` (`id_assessment`),
  KEY `diagnostic_results_diagnostic_frameworks_FK` (`id_framework`),
  CONSTRAINT `diagnostic_results_diagnostic_assessments_FK` FOREIGN KEY (`id_assessment`) REFERENCES `diagnostic_assessments` (`id_assessment`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `diagnostic_results_diagnostic_frameworks_FK` FOREIGN KEY (`id_framework`) REFERENCES `diagnostic_frameworks` (`id_framework`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `diagnostic_results`
--

LOCK TABLES `diagnostic_results` WRITE;
/*!40000 ALTER TABLE `diagnostic_results` DISABLE KEYS */;
/*!40000 ALTER TABLE `diagnostic_results` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `failed_jobs`
--

LOCK TABLES `failed_jobs` WRITE;
/*!40000 ALTER TABLE `failed_jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `failed_jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `intelligence_analyses`
--

DROP TABLE IF EXISTS `intelligence_analyses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `intelligence_analyses` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_user` bigint unsigned NOT NULL,
  `period_prev_date` date DEFAULT NULL,
  `period_current_date` date DEFAULT NULL,
  `omzet_prev` decimal(15,2) NOT NULL,
  `omzet_current` decimal(15,2) NOT NULL,
  `customer_prev` decimal(15,2) NOT NULL,
  `customer_current` decimal(15,2) NOT NULL,
  `sales_prev` decimal(15,2) NOT NULL,
  `sales_current` decimal(15,2) NOT NULL,
  `product_count` decimal(10,2) NOT NULL DEFAULT '0.00',
  `avg_price` decimal(15,2) NOT NULL,
  `marketing_spend` decimal(15,2) NOT NULL,
  `operational_cost` decimal(15,2) NOT NULL,
  `omzet_change` decimal(8,2) NOT NULL,
  `customer_change` decimal(8,2) NOT NULL,
  `sales_change` decimal(8,2) NOT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `primary_diagnosis_id` bigint unsigned DEFAULT NULL,
  `primary_rule_version_id` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `intelligence_analyses_id_user_created_at_index` (`id_user`,`created_at`),
  KEY `intelligence_analyses_primary_diagnosis_id_foreign` (`primary_diagnosis_id`),
  KEY `intelligence_analyses_primary_rule_version_id_foreign` (`primary_rule_version_id`),
  CONSTRAINT `intelligence_analyses_id_user_foreign` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON DELETE CASCADE,
  CONSTRAINT `intelligence_analyses_primary_diagnosis_id_foreign` FOREIGN KEY (`primary_diagnosis_id`) REFERENCES `autopsy_diagnoses` (`id`) ON DELETE SET NULL,
  CONSTRAINT `intelligence_analyses_primary_rule_version_id_foreign` FOREIGN KEY (`primary_rule_version_id`) REFERENCES `autopsy_rule_versions` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `intelligence_analyses`
--

LOCK TABLES `intelligence_analyses` WRITE;
/*!40000 ALTER TABLE `intelligence_analyses` DISABLE KEYS */;
INSERT INTO `intelligence_analyses` VALUES (1,4,'2026-10-01','2026-11-01',50000000.00,40000000.00,500.00,400.00,1000.00,800.00,15.00,50000.00,5000000.00,30000000.00,-20.00,-20.00,-20.00,'critical',NULL,NULL,'2026-11-06 07:46:37','2026-11-06 07:46:37'),(2,4,'2026-10-01','2026-11-01',50000000.00,40000000.00,500.00,400.00,1000.00,800.00,15.00,50000.00,5000000.00,30000000.00,-20.00,-20.00,-20.00,'critical',NULL,NULL,'2026-11-06 07:47:09','2026-11-06 07:47:09'),(3,3,'2026-09-01','2026-10-01',1000000.00,1250000.00,120.00,200.00,200.00,300.00,10000.00,15000.00,100000.00,120000.00,25.00,66.67,50.00,'ok',NULL,NULL,'2026-11-06 08:00:27','2026-11-06 08:00:27');
/*!40000 ALTER TABLE `intelligence_analyses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `intelligence_analysis_candidates`
--

DROP TABLE IF EXISTS `intelligence_analysis_candidates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `intelligence_analysis_candidates` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_analysis` bigint unsigned NOT NULL,
  `id_rule` bigint unsigned DEFAULT NULL,
  `id_rule_version` bigint unsigned DEFAULT NULL,
  `id_diagnosis` bigint unsigned DEFAULT NULL,
  `code` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `severity` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `priority` smallint unsigned DEFAULT NULL,
  `specificity` smallint unsigned DEFAULT NULL,
  `rank_position` smallint unsigned NOT NULL DEFAULT '1',
  `is_primary` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `intelligence_analysis_candidates_id_analysis_id_rule_unique` (`id_analysis`,`id_rule`),
  KEY `intelligence_analysis_candidates_id_rule_foreign` (`id_rule`),
  KEY `intelligence_analysis_candidates_id_rule_version_foreign` (`id_rule_version`),
  KEY `intelligence_analysis_candidates_id_diagnosis_foreign` (`id_diagnosis`),
  KEY `intelligence_analysis_candidates_id_analysis_rank_position_index` (`id_analysis`,`rank_position`),
  CONSTRAINT `intelligence_analysis_candidates_id_analysis_foreign` FOREIGN KEY (`id_analysis`) REFERENCES `intelligence_analyses` (`id`) ON DELETE CASCADE,
  CONSTRAINT `intelligence_analysis_candidates_id_diagnosis_foreign` FOREIGN KEY (`id_diagnosis`) REFERENCES `autopsy_diagnoses` (`id`) ON DELETE SET NULL,
  CONSTRAINT `intelligence_analysis_candidates_id_rule_foreign` FOREIGN KEY (`id_rule`) REFERENCES `autopsy_rules` (`id`) ON DELETE SET NULL,
  CONSTRAINT `intelligence_analysis_candidates_id_rule_version_foreign` FOREIGN KEY (`id_rule_version`) REFERENCES `autopsy_rule_versions` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `intelligence_analysis_candidates`
--

LOCK TABLES `intelligence_analysis_candidates` WRITE;
/*!40000 ALTER TABLE `intelligence_analysis_candidates` DISABLE KEYS */;
/*!40000 ALTER TABLE `intelligence_analysis_candidates` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `intelligence_analysis_finding_steps`
--

DROP TABLE IF EXISTS `intelligence_analysis_finding_steps`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `intelligence_analysis_finding_steps` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_finding` bigint unsigned NOT NULL,
  `step_order` smallint unsigned NOT NULL DEFAULT '1',
  `step` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `intelligence_analysis_finding_steps_id_finding_step_order_unique` (`id_finding`,`step_order`),
  CONSTRAINT `intelligence_analysis_finding_steps_id_finding_foreign` FOREIGN KEY (`id_finding`) REFERENCES `intelligence_analysis_findings` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `intelligence_analysis_finding_steps`
--

LOCK TABLES `intelligence_analysis_finding_steps` WRITE;
/*!40000 ALTER TABLE `intelligence_analysis_finding_steps` DISABLE KEYS */;
/*!40000 ALTER TABLE `intelligence_analysis_finding_steps` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `intelligence_analysis_findings`
--

DROP TABLE IF EXISTS `intelligence_analysis_findings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `intelligence_analysis_findings` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_analysis` bigint unsigned NOT NULL,
  `type` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `source` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'fallback',
  `ref_id` bigint unsigned DEFAULT NULL,
  `id_rule_version` bigint unsigned DEFAULT NULL,
  `code` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `severity` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `priority` smallint unsigned DEFAULT NULL,
  `priority_label` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sort_order` smallint unsigned NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `intelligence_analysis_findings_id_rule_version_foreign` (`id_rule_version`),
  KEY `iaf_analysis_type_order_index` (`id_analysis`,`type`,`sort_order`),
  CONSTRAINT `intelligence_analysis_findings_id_analysis_foreign` FOREIGN KEY (`id_analysis`) REFERENCES `intelligence_analyses` (`id`) ON DELETE CASCADE,
  CONSTRAINT `intelligence_analysis_findings_id_rule_version_foreign` FOREIGN KEY (`id_rule_version`) REFERENCES `autopsy_rule_versions` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `intelligence_analysis_findings`
--

LOCK TABLES `intelligence_analysis_findings` WRITE;
/*!40000 ALTER TABLE `intelligence_analysis_findings` DISABLE KEYS */;
INSERT INTO `intelligence_analysis_findings` VALUES (1,1,'diagnosis','fallback',NULL,NULL,NULL,'Omzet Menurun Signifikan','Omzet menurun 20% dari periode sebelumnya. ','high',NULL,NULL,0,'2026-11-06 09:15:11','2026-11-06 09:15:11'),(2,1,'diagnosis','fallback',NULL,NULL,NULL,'Jumlah Pelanggan Menurun Signifikan','Jumlah pelanggan menurun 20%.','high',NULL,NULL,1,'2026-11-06 09:15:11','2026-11-06 09:15:11'),(3,1,'diagnosis','fallback',NULL,NULL,NULL,'Volume Penjualan Menurun Signifikan','Volume penjualan menurun 20%.','high',NULL,NULL,2,'2026-11-06 09:15:11','2026-11-06 09:15:11'),(4,1,'diagnosis','fallback',NULL,NULL,NULL,'Semua Indikator Turun Bersama','Omzet, customer, dan penjualan turun bersamaan. Pola ini mengindikasikan masalah bisnis yang lebih luas, bukan fluktuasi satu indikator.','high',NULL,NULL,3,'2026-11-06 09:15:11','2026-11-06 09:15:11'),(5,1,'diagnosis','fallback',NULL,NULL,NULL,'Biaya Operasional Tinggi','Biaya operasional mencapai 75% dari omzet.','high',NULL,NULL,4,'2026-11-06 09:15:11','2026-11-06 09:15:11'),(6,1,'recommendation','fallback',NULL,NULL,NULL,'Evaluasi Penurunan Omzet','Tinjau kanal penjualan dan strategi harga yang paling berdampak, lalu terapkan perbaikan bertahap.',NULL,NULL,'high',0,'2026-11-06 09:15:11','2026-11-06 09:15:11'),(7,1,'recommendation','fallback',NULL,NULL,NULL,'Tingkatkan Akuisisi Pelanggan','Fokuskan pada strategi marketing untuk menarik pelanggan baru dan perbaiki retensi pelanggan lama.',NULL,NULL,'high',1,'2026-11-06 09:15:11','2026-11-06 09:15:11'),(8,1,'recommendation','fallback',NULL,NULL,NULL,'Tingkatkan Retensi Pelanggan','Buat program loyalitas atau penawaran khusus untuk pelanggan existing.',NULL,NULL,'high',2,'2026-11-06 09:15:11','2026-11-06 09:15:11'),(9,1,'recommendation','fallback',NULL,NULL,NULL,'Evaluasi Menyeluruh','Lakukan evaluasi menyeluruh atas produk, harga, marketing, dan operasional karena penurunan terjadi di semua indikator.',NULL,NULL,'high',3,'2026-11-06 09:15:11','2026-11-06 09:15:11'),(10,1,'recommendation','fallback',NULL,NULL,NULL,'Kurangi Biaya Operasional','Identifikasi area pengurangan biaya seperti negosiasi ulang dengan supplier.',NULL,NULL,'high',4,'2026-11-06 09:15:11','2026-11-06 09:15:11'),(11,2,'diagnosis','fallback',NULL,NULL,NULL,'Omzet Menurun Signifikan','Omzet menurun 20% dari periode sebelumnya. ','high',NULL,NULL,0,'2026-11-06 09:15:11','2026-11-06 09:15:11'),(12,2,'diagnosis','fallback',NULL,NULL,NULL,'Jumlah Pelanggan Menurun Signifikan','Jumlah pelanggan menurun 20%.','high',NULL,NULL,1,'2026-11-06 09:15:11','2026-11-06 09:15:11'),(13,2,'diagnosis','fallback',NULL,NULL,NULL,'Volume Penjualan Menurun Signifikan','Volume penjualan menurun 20%.','high',NULL,NULL,2,'2026-11-06 09:15:11','2026-11-06 09:15:11'),(14,2,'diagnosis','fallback',NULL,NULL,NULL,'Semua Indikator Turun Bersama','Omzet, customer, dan penjualan turun bersamaan. Pola ini mengindikasikan masalah bisnis yang lebih luas, bukan fluktuasi satu indikator.','high',NULL,NULL,3,'2026-11-06 09:15:11','2026-11-06 09:15:11'),(15,2,'diagnosis','fallback',NULL,NULL,NULL,'Biaya Operasional Tinggi','Biaya operasional mencapai 75% dari omzet.','high',NULL,NULL,4,'2026-11-06 09:15:11','2026-11-06 09:15:11'),(16,2,'recommendation','fallback',NULL,NULL,NULL,'Evaluasi Penurunan Omzet','Tinjau kanal penjualan dan strategi harga yang paling berdampak, lalu terapkan perbaikan bertahap.',NULL,NULL,'high',0,'2026-11-06 09:15:11','2026-11-06 09:15:11'),(17,2,'recommendation','fallback',NULL,NULL,NULL,'Tingkatkan Akuisisi Pelanggan','Fokuskan pada strategi marketing untuk menarik pelanggan baru dan perbaiki retensi pelanggan lama.',NULL,NULL,'high',1,'2026-11-06 09:15:11','2026-11-06 09:15:11'),(18,2,'recommendation','fallback',NULL,NULL,NULL,'Tingkatkan Retensi Pelanggan','Buat program loyalitas atau penawaran khusus untuk pelanggan existing.',NULL,NULL,'high',2,'2026-11-06 09:15:11','2026-11-06 09:15:11'),(19,2,'recommendation','fallback',NULL,NULL,NULL,'Evaluasi Menyeluruh','Lakukan evaluasi menyeluruh atas produk, harga, marketing, dan operasional karena penurunan terjadi di semua indikator.',NULL,NULL,'high',3,'2026-11-06 09:15:11','2026-11-06 09:15:11'),(20,2,'recommendation','fallback',NULL,NULL,NULL,'Kurangi Biaya Operasional','Identifikasi area pengurangan biaya seperti negosiasi ulang dengan supplier.',NULL,NULL,'high',4,'2026-11-06 09:15:11','2026-11-06 09:15:11'),(21,3,'diagnosis','fallback',NULL,NULL,NULL,'Bisnis Dalam Kondisi Baik','Tidak ada penurunan yang terdeteksi. Kondisi bisnis Anda masih tergolong baik.','low',NULL,NULL,0,'2026-11-06 09:15:11','2026-11-06 09:15:11'),(22,3,'recommendation','fallback',NULL,NULL,NULL,'Pertahankan Performa','Teruskan strategi yang sudah baik dan pantau secara berkala.',NULL,NULL,'low',0,'2026-11-06 09:15:11','2026-11-06 09:15:11');
/*!40000 ALTER TABLE `intelligence_analysis_findings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `intelligence_analysis_symptoms`
--

DROP TABLE IF EXISTS `intelligence_analysis_symptoms`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `intelligence_analysis_symptoms` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_analysis` bigint unsigned NOT NULL,
  `id_symptom` bigint unsigned DEFAULT NULL,
  `code` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `metric_key` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `intelligence_analysis_symptoms_id_analysis_id_symptom_unique` (`id_analysis`,`id_symptom`),
  KEY `intelligence_analysis_symptoms_id_symptom_foreign` (`id_symptom`),
  CONSTRAINT `intelligence_analysis_symptoms_id_analysis_foreign` FOREIGN KEY (`id_analysis`) REFERENCES `intelligence_analyses` (`id`) ON DELETE CASCADE,
  CONSTRAINT `intelligence_analysis_symptoms_id_symptom_foreign` FOREIGN KEY (`id_symptom`) REFERENCES `autopsy_symptoms` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `intelligence_analysis_symptoms`
--

LOCK TABLES `intelligence_analysis_symptoms` WRITE;
/*!40000 ALTER TABLE `intelligence_analysis_symptoms` DISABLE KEYS */;
/*!40000 ALTER TABLE `intelligence_analysis_symptoms` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `job_batches`
--

DROP TABLE IF EXISTS `job_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `job_batches`
--

LOCK TABLES `job_batches` WRITE;
/*!40000 ALTER TABLE `job_batches` DISABLE KEYS */;
/*!40000 ALTER TABLE `job_batches` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint unsigned NOT NULL,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jobs`
--

LOCK TABLES `jobs` WRITE;
/*!40000 ALTER TABLE `jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `market_indicators`
--

DROP TABLE IF EXISTS `market_indicators`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `market_indicators` (
  `id_indicator` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_category` bigint unsigned NOT NULL,
  `indicator_name` varchar(100) NOT NULL,
  `value` decimal(15,2) NOT NULL,
  `unit` varchar(100) NOT NULL,
  `recorded_at` timestamp NOT NULL,
  PRIMARY KEY (`id_indicator`),
  KEY `market_indicators_business_categories_FK` (`id_category`),
  CONSTRAINT `market_indicators_business_categories_FK` FOREIGN KEY (`id_category`) REFERENCES `business_categories` (`id_category`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `market_indicators`
--

LOCK TABLES `market_indicators` WRITE;
/*!40000 ALTER TABLE `market_indicators` DISABLE KEYS */;
/*!40000 ALTER TABLE `market_indicators` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `match_requests`
--

DROP TABLE IF EXISTS `match_requests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `match_requests` (
  `id_match` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_listing` bigint unsigned NOT NULL,
  `id_requester_user` bigint unsigned NOT NULL,
  `pitch_message` text,
  `status` enum('Pending','Accepted','Rejected') NOT NULL DEFAULT 'Pending',
  `created_at` timestamp NOT NULL,
  PRIMARY KEY (`id_match`),
  KEY `match_requests_users_FK` (`id_requester_user`),
  KEY `match_requests_connection_listings_FK` (`id_listing`),
  CONSTRAINT `match_requests_connection_listings_FK` FOREIGN KEY (`id_listing`) REFERENCES `connection_listings` (`id_listing`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `match_requests_users_FK` FOREIGN KEY (`id_requester_user`) REFERENCES `users` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `match_requests`
--

LOCK TABLES `match_requests` WRITE;
/*!40000 ALTER TABLE `match_requests` DISABLE KEYS */;
/*!40000 ALTER TABLE `match_requests` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `media_attachments`
--

DROP TABLE IF EXISTS `media_attachments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `media_attachments` (
  `id_media` bigint unsigned NOT NULL AUTO_INCREMENT,
  `entity_type` varchar(50) NOT NULL,
  `entity_id` bigint NOT NULL,
  `file_url` varchar(100) NOT NULL,
  `file_type` varchar(100) NOT NULL,
  `uploaded_at` timestamp NOT NULL,
  PRIMARY KEY (`id_media`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `media_attachments`
--

LOCK TABLES `media_attachments` WRITE;
/*!40000 ALTER TABLE `media_attachments` DISABLE KEYS */;
/*!40000 ALTER TABLE `media_attachments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `messages`
--

DROP TABLE IF EXISTS `messages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `messages` (
  `id_messages` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_room` bigint unsigned NOT NULL,
  `id_sender` bigint unsigned NOT NULL,
  `message_body` text NOT NULL,
  `sent_at` timestamp NOT NULL,
  PRIMARY KEY (`id_messages`),
  KEY `messages_collaboration_rooms_FK` (`id_room`),
  KEY `messages_users_FK` (`id_sender`),
  CONSTRAINT `messages_collaboration_rooms_FK` FOREIGN KEY (`id_room`) REFERENCES `collaboration_rooms` (`id_room`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `messages_users_FK` FOREIGN KEY (`id_sender`) REFERENCES `users` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `messages`
--

LOCK TABLES `messages` WRITE;
/*!40000 ALTER TABLE `messages` DISABLE KEYS */;
/*!40000 ALTER TABLE `messages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'0001_01_01_000000_create_users_table',1),(2,'0001_01_01_000001_create_cache_table',2),(3,'0001_01_01_000002_create_jobs_table',2),(4,'2026_11_02_000000_create_business_data_table',3),(5,'2026_11_03_000000_create_legacy_auth_support_tables',3),(6,'2026_11_03_000100_create_autopsy_master_tables',4),(7,'2026_11_01_000000_create_legacy_core_tables',5),(8,'2026_11_05_000000_add_metadata_to_autopsy_rule_versions',5),(9,'2026_11_06_100000_create_intelligence_analyses_table',6),(10,'2026_11_06_110000_add_rule_evaluation_to_intelligence_analyses',7),(11,'2026_11_06_120000_create_intelligence_analysis_findings_table',8),(12,'2026_11_06_130000_create_intelligence_analysis_symptoms_table',9),(13,'2026_11_06_140000_create_autopsy_recommendation_steps_table',10),(14,'2026_11_06_150000_create_intelligence_analysis_candidates_table',10),(15,'2026_11_06_160000_create_intelligence_analysis_finding_steps_table',10),(16,'2026_11_07_000000_create_business_profiles_table',10);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notifications`
--

DROP TABLE IF EXISTS `notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `notifications` (
  `id_notif` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_user` bigint unsigned NOT NULL,
  `title` varchar(100) NOT NULL,
  `message` text NOT NULL,
  `action_url` varchar(100) DEFAULT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL,
  PRIMARY KEY (`id_notif`),
  KEY `notifications_users_FK` (`id_user`),
  CONSTRAINT `notifications_users_FK` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notifications`
--

LOCK TABLES `notifications` WRITE;
/*!40000 ALTER TABLE `notifications` DISABLE KEYS */;
/*!40000 ALTER TABLE `notifications` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_reset_tokens`
--

LOCK TABLES `password_reset_tokens` WRITE;
/*!40000 ALTER TABLE `password_reset_tokens` DISABLE KEYS */;
INSERT INTO `password_reset_tokens` VALUES ('admin@example.com','$2y$12$U5IyRn9FItwqPKecvJfZEO6LBASRucze/G.W7NJKtEpslvXmcaJGO','2026-11-05 07:21:28');
/*!40000 ALTER TABLE `password_reset_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `reviews_ratings`
--

DROP TABLE IF EXISTS `reviews_ratings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `reviews_ratings` (
  `id_rating` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_reviewer_user` bigint unsigned NOT NULL,
  `id_target_user` bigint unsigned NOT NULL,
  `id_room` bigint unsigned NOT NULL,
  `rating` decimal(2,1) NOT NULL,
  `review_text` text,
  `created_at` timestamp NOT NULL,
  PRIMARY KEY (`id_rating`),
  KEY `reviews_ratings_users_FK` (`id_reviewer_user`),
  KEY `reviews_ratings_users_FK_1` (`id_target_user`),
  KEY `reviews_ratings_collaboration_rooms_FK` (`id_room`),
  CONSTRAINT `reviews_ratings_collaboration_rooms_FK` FOREIGN KEY (`id_room`) REFERENCES `collaboration_rooms` (`id_room`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `reviews_ratings_users_FK` FOREIGN KEY (`id_reviewer_user`) REFERENCES `users` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `reviews_ratings_users_FK_1` FOREIGN KEY (`id_target_user`) REFERENCES `users` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reviews_ratings`
--

LOCK TABLES `reviews_ratings` WRITE;
/*!40000 ALTER TABLE `reviews_ratings` DISABLE KEYS */;
/*!40000 ALTER TABLE `reviews_ratings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sessions`
--

LOCK TABLES `sessions` WRITE;
/*!40000 ALTER TABLE `sessions` DISABLE KEYS */;
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `simulation_outputs`
--

DROP TABLE IF EXISTS `simulation_outputs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `simulation_outputs` (
  `id_output` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_scenario` bigint unsigned NOT NULL,
  `projected_revenue` decimal(15,2) DEFAULT NULL,
  `projected_profit` decimal(15,2) DEFAULT NULL,
  `projected_cash_runway` int DEFAULT NULL,
  `risk_score` decimal(5,2) DEFAULT NULL,
  `calculated_at` timestamp NOT NULL,
  PRIMARY KEY (`id_output`),
  KEY `simulation_outputs_simulation_scenarios_FK` (`id_scenario`),
  CONSTRAINT `simulation_outputs_simulation_scenarios_FK` FOREIGN KEY (`id_scenario`) REFERENCES `simulation_scenarios` (`id_scenario`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `simulation_outputs`
--

LOCK TABLES `simulation_outputs` WRITE;
/*!40000 ALTER TABLE `simulation_outputs` DISABLE KEYS */;
/*!40000 ALTER TABLE `simulation_outputs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `simulation_scenarios`
--

DROP TABLE IF EXISTS `simulation_scenarios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `simulation_scenarios` (
  `id_scenario` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_profile` bigint unsigned NOT NULL,
  `scenario_name` varchar(150) NOT NULL,
  `description` text,
  `created_at` timestamp NOT NULL,
  PRIMARY KEY (`id_scenario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `simulation_scenarios`
--

LOCK TABLES `simulation_scenarios` WRITE;
/*!40000 ALTER TABLE `simulation_scenarios` DISABLE KEYS */;
/*!40000 ALTER TABLE `simulation_scenarios` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `simulation_variables`
--

DROP TABLE IF EXISTS `simulation_variables`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `simulation_variables` (
  `id_variable` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_scenario` bigint unsigned NOT NULL,
  `variable_name` varchar(100) NOT NULL,
  `change_type` enum('percentage','fixed_amount') NOT NULL,
  `value_change` decimal(15,2) NOT NULL,
  PRIMARY KEY (`id_variable`),
  KEY `simulation_variables_simulation_scenarios_FK` (`id_scenario`),
  CONSTRAINT `simulation_variables_simulation_scenarios_FK` FOREIGN KEY (`id_scenario`) REFERENCES `simulation_scenarios` (`id_scenario`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `simulation_variables`
--

LOCK TABLES `simulation_variables` WRITE;
/*!40000 ALTER TABLE `simulation_variables` DISABLE KEYS */;
/*!40000 ALTER TABLE `simulation_variables` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sme_metrics_logs`
--

DROP TABLE IF EXISTS `sme_metrics_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sme_metrics_logs` (
  `id_metrics` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_profile` bigint unsigned NOT NULL,
  `period_date` date NOT NULL,
  `revenue` decimal(15,2) NOT NULL DEFAULT '0.00',
  `expenses` decimal(15,2) NOT NULL DEFAULT '0.00',
  `order_count` int NOT NULL DEFAULT '0',
  `cash_on_hand` decimal(15,2) DEFAULT NULL,
  PRIMARY KEY (`id_metrics`),
  KEY `sme_metrics_logs_sme_profiles_FK` (`id_profile`),
  CONSTRAINT `sme_metrics_logs_sme_profiles_FK` FOREIGN KEY (`id_profile`) REFERENCES `sme_profiles` (`id_profile`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sme_metrics_logs`
--

LOCK TABLES `sme_metrics_logs` WRITE;
/*!40000 ALTER TABLE `sme_metrics_logs` DISABLE KEYS */;
/*!40000 ALTER TABLE `sme_metrics_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sme_profiles`
--

DROP TABLE IF EXISTS `sme_profiles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sme_profiles` (
  `id_profile` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_user` bigint unsigned NOT NULL,
  `id_category` bigint unsigned NOT NULL,
  `business_name` varchar(100) NOT NULL,
  `legal_type` varchar(100) DEFAULT NULL,
  `address` text,
  `city` varchar(100) NOT NULL,
  `established_year` int NOT NULL,
  `created_at` timestamp NOT NULL,
  PRIMARY KEY (`id_profile`),
  KEY `sme_profiles_users_FK` (`id_user`),
  KEY `sme_profiles_business_categories_FK` (`id_category`),
  CONSTRAINT `sme_profiles_business_categories_FK` FOREIGN KEY (`id_category`) REFERENCES `business_categories` (`id_category`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `sme_profiles_users_FK` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sme_profiles`
--

LOCK TABLES `sme_profiles` WRITE;
/*!40000 ALTER TABLE `sme_profiles` DISABLE KEYS */;
INSERT INTO `sme_profiles` VALUES (1,3,3,'Kocak','Perorangan','Jalanin aja dulu','Surabaya',2020,'2026-11-05 07:17:22');
/*!40000 ALTER TABLE `sme_profiles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `system_logs`
--

DROP TABLE IF EXISTS `system_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `system_logs` (
  `id_logs` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_user` bigint unsigned NOT NULL,
  `action_type` varchar(100) NOT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text,
  `created_at` timestamp NOT NULL,
  PRIMARY KEY (`id_logs`),
  KEY `system_logs_users_FK` (`id_user`),
  CONSTRAINT `system_logs_users_FK` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `system_logs`
--

LOCK TABLES `system_logs` WRITE;
/*!40000 ALTER TABLE `system_logs` DISABLE KEYS */;
INSERT INTO `system_logs` VALUES (1,1,'admin.user.updated','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','2026-11-04 06:11:48'),(2,1,'admin.user.updated','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','2026-11-05 05:56:56'),(3,1,'admin.user.updated','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','2026-11-05 05:58:17'),(4,1,'admin.user.updated','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','2026-11-05 05:58:57'),(5,1,'admin.symptom.updated','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','2026-11-05 05:59:22'),(6,1,'admin.profile.created','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.145.0 Chrome/150.0.7871.250 Electron/43.8.3 Safari/537.36','2026-11-05 07:17:22'),(7,1,'admin.profile.updated','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.145.1 Chrome/150.0.7871.250 Electron/43.8.3 Safari/537.36','2026-11-07 09:10:40'),(8,1,'admin.user.deleted','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.145.1 Chrome/150.0.7871.250 Electron/43.8.3 Safari/537.36','2026-11-07 09:18:40'),(9,1,'admin.user.updated','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.145.1 Chrome/150.0.7871.250 Electron/43.8.3 Safari/537.36','2026-11-07 09:38:20'),(10,1,'admin.user.updated','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.145.1 Chrome/150.0.7871.250 Electron/43.8.3 Safari/537.36','2026-11-07 09:39:54'),(11,1,'admin.user.updated','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.145.1 Chrome/150.0.7871.250 Electron/43.8.3 Safari/537.36','2026-11-07 09:41:06'),(12,1,'admin.user.updated','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.145.1 Chrome/150.0.7871.250 Electron/43.8.3 Safari/537.36','2026-11-07 09:42:26');
/*!40000 ALTER TABLE `system_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id_user` bigint unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `role` enum('sme_owner','mentor','investor','admin','user') NOT NULL,
  `is_verified` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id_user`),
  UNIQUE KEY `users_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'admin@example.com','$2y$12$iIMTTOyjfdHZGdzeu2ilNeDm8RScHsYcBMuo7T6hjkBjDSUwQfC0y','Administrator','admin',1,'2026-11-02 21:16:18','2026-11-03 23:29:25',NULL),(2,'test@example.com','$2y$12$P0yWa9PA/Z4D5w2usyUdWu4NNsBuGH1ml6QdO03MCib0jwhVWYWtC','Test User','sme_owner',1,'2026-11-02 21:16:19','2026-11-02 21:16:19',NULL),(3,'admin1234@gmail.com','$2y$12$FqAL9HEU5qU7uaJQnE0JLOM31AMloCWvG5rDd/BO4YFfHne4HeLUi','Admin1','sme_owner',1,'2026-11-03 21:33:05','2026-11-05 05:58:57',NULL),(4,'smoke-test@local.test','$2y$12$WcM2zis2Gif3I6WxD16Fs.KmyT6pq/w88wqh6eG2X9G/Zy5eOq/fm','Smoke Test','sme_owner',1,'2026-11-06 07:43:49','2026-11-06 07:43:49',NULL),(11,'kicentt1234@gmail.com','$2y$12$yMFfMVq8YtYJnneuPKOGq.vxrVeQa.Dj/usHiQnU0nEa3MaA0qO0O','Kicentt','sme_owner',1,'2026-11-07 09:34:25','2026-11-07 09:42:26',NULL);
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping routines for database 'db_sme'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-11-08 22:11:58
