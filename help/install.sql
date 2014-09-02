-- MySQL dump 10.13  Distrib 5.5.38, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: afwezig
-- ------------------------------------------------------
-- Server version	5.5.38-0ubuntu0.14.04.1

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
-- Table structure for table `admin_variables`
--

DROP TABLE IF EXISTS `admin_variables`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `admin_variables` (
  `name` varchar(255) DEFAULT NULL,
  `value` varchar(255) DEFAULT NULL,
  `id` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`),
  UNIQUE KEY `admin_var_name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=UTF8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `admin_variables`
--

LOCK TABLES `admin_variables` WRITE;
/*!40000 ALTER TABLE `admin_variables` DISABLE KEYS */;
INSERT INTO `admin_variables` VALUES ('lockApp','false',1),('lastPersist','2014-02',4);
/*!40000 ALTER TABLE `admin_variables` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `auth_items`
--

DROP TABLE IF EXISTS `auth_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `auth_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `request_id` int(11) NOT NULL,
  `supervisor_id` varchar(12) NOT NULL COMMENT 'The code for Schaubroeck to identify the employee',
  `authorized` tinyint(1) NOT NULL,
  `authorization_date` timestamp NULL DEFAULT NULL COMMENT 'Timestamp of when the authorization has happened',
  `message` varchar(255) DEFAULT NULL COMMENT 'Is used to see where a auth_item originated from, e.g. stream. Not used with standard requests',
  PRIMARY KEY (`id`),
  UNIQUE KEY `auth_item_id` (`id`),
  UNIQUE KEY `request_id` (`request_id`),
  KEY `fk_ai_supervisor_id` (`supervisor_id`),
  CONSTRAINT `fk_ai_request_id` FOREIGN KEY (`request_id`) REFERENCES `requests` (`id`),
  CONSTRAINT `fk_ai_supervisor_id` FOREIGN KEY (`supervisor_id`) REFERENCES `employees` (`internal_id`)
) ENGINE=InnoDB DEFAULT CHARSET=UTF8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `auth_items`
--

LOCK TABLES `auth_items` WRITE;
/*!40000 ALTER TABLE `auth_items` DISABLE KEYS */;
/*!40000 ALTER TABLE `auth_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `calendar_days`
--

DROP TABLE IF EXISTS `calendar_days`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `calendar_days` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `employee_id` int(10) unsigned DEFAULT NULL,
  `day_date` date NOT NULL,
  `day_time` varchar(3) NOT NULL,
  `calendar_item_type_id` int(11) NOT NULL,
  `replacement_id` varchar(12) DEFAULT NULL,
  `last_update` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `natural_key` (`employee_id`,`day_date`,`day_time`),
  KEY `date` (`day_date`),
  KEY `time` (`day_time`),
  KEY `employee` (`employee_id`),
  KEY `fk_cd_calendar_item_type_id` (`calendar_item_type_id`),
  KEY `fk_cd_replacement_id` (`replacement_id`),
  CONSTRAINT `fk_cd_calendar_item_type_id` FOREIGN KEY (`calendar_item_type_id`) REFERENCES `calendar_item_types` (`id`),
  CONSTRAINT `fk_cd_employe_id` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`),
  CONSTRAINT `fk_cd_replacement_id` FOREIGN KEY (`replacement_id`) REFERENCES `employees` (`internal_id`)
) ENGINE=InnoDB DEFAULT CHARSET=UTF8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `calendar_days`
--

LOCK TABLES `calendar_days` WRITE;
/*!40000 ALTER TABLE `calendar_days` DISABLE KEYS */;
/*!40000 ALTER TABLE `calendar_days` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `calendar_item_types`
--

DROP TABLE IF EXISTS `calendar_item_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `calendar_item_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `code` varchar(4) NOT NULL COMMENT 'Shorthand identifier',
  `user_allowed` tinyint(1) NOT NULL COMMENT 'Boolean to let a user see the option.',
  `dinner_cheque` tinyint(1) NOT NULL COMMENT 'Boolean to see if a user gets a dinner cheque with this type',
  `ext_schaubroek` int(5) DEFAULT NULL COMMENT 'Arbitrary code for Schaubroeck',
  `code_schaubroek` int(5) DEFAULT NULL COMMENT 'Arbitrary code for Schaubroeck',
  `aard_schaubroek` int(5) DEFAULT NULL COMMENT 'Arbitrary code for Schaubroeck',
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=UTF8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `calendar_item_types`
--

LOCK TABLES `calendar_item_types` WRITE;
/*!40000 ALTER TABLE `calendar_item_types` DISABLE KEYS */;
INSERT INTO `calendar_item_types` VALUES (1,'Adoptieverlof','AD',1,0,1,1,117),(2,'Arbeidsongeval','AO',1,0,2,1,111),(3,'Betaalde Feestdag','F',0,0,11,1,103),(4,'Bloedafgifte','BL',0,0,1,1,176),(5,'Co-moederschapsverlof','CMV',1,0,1,1,105),(6,'Dienstvrijstelling','DV',1,0,1,1,101),(7,'Erkende Staking','ES',1,0,5,1,399),(8,'Europese Vakantie','EV',1,0,NULL,1,NULL),(9,'Gewerkt','G',1,1,1,1,101),(10,'Jeugdvakantie','JV',1,0,5,1,250),(11,'Loopbaanonderbreking','LO',1,0,15,1,303),(12,'Niet Gewerkt','X',0,0,1,1,398),(13,'Omstandigheidsverlof Bezoldigd','OM',1,0,1,1,106),(14,'Onbezoldigd Verlof','ON',1,0,5,1,399),(15,'Onwettig Afwezig','OA',0,0,5,1,323),(16,'Ouderschapsverlof','OU',1,0,1,1,307),(17,'Politiek Verlof Bezoldigd','PV',0,0,1,1,123),(18,'Recuperatieverlof','R',1,1,1,1,101),(19,'Seniorverlof','SV',1,0,5,1,249),(20,'Sollicitatieverlof','SOL',1,0,NULL,1,NULL),(21,'Syndicaal Verlof Bezoldigd','SY',1,1,1,1,199),(22,'Vaderschapsverlof','VV',1,0,1,1,117),(23,'Verlof','V',1,0,1,1,105),(24,'Ziek','Z',0,0,21,1,110),(25,'Ziek >1 Maand','Z>1M',0,0,5,1,210),(26,'Zwangerschapsverlof','ZW',0,0,5,1,241);
/*!40000 ALTER TABLE `calendar_item_types` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `employee_counts`
--

DROP TABLE IF EXISTS `employee_counts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `employee_counts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `employee_id` int(11) DEFAULT NULL,
  `year` int(4) DEFAULT NULL,
  `dinner_cheques` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=UTF8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `employee_counts`
--

LOCK TABLES `employee_counts` WRITE;
/*!40000 ALTER TABLE `employee_counts` DISABLE KEYS */;
INSERT INTO `employee_counts` VALUES (1,2,2014,43),(2,1,2014,43),(3,3,2014,43);
/*!40000 ALTER TABLE `employee_counts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `employee_departments`
--

DROP TABLE IF EXISTS `employee_departments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `employee_departments` (
  `id` int(3) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  UNIQUE KEY `employee_department_id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=UTF8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `employee_departments`
--

LOCK TABLES `employee_departments` WRITE;
/*!40000 ALTER TABLE `employee_departments` DISABLE KEYS */;
INSERT INTO `employee_departments` VALUES (1,'PAF'),(3,'GEERT');
/*!40000 ALTER TABLE `employee_departments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `employees`
--

DROP TABLE IF EXISTS `employees`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `employees` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `role_id` int(11) DEFAULT NULL,
  `employee_department_id` int(11) NOT NULL,
  `3gram` varchar(255) CHARACTER SET utf8 NOT NULL COMMENT 'A 3gram as well as an email adress is allowed',
  `telephone` text CHARACTER SET utf8 NOT NULL,
  `name` varchar(255) CHARACTER SET utf8 NOT NULL,
  `surname` varchar(255) CHARACTER SET utf8 NOT NULL,
  `note` mediumtext CHARACTER SET utf8 NOT NULL,
  `daysleft` decimal(10,0) NOT NULL,
  `status` varchar(9) CHARACTER SET utf8 NOT NULL COMMENT 'Allows the deactivation of an employee',
  `supervisor_id` int(11) NOT NULL,
  `gsm` varchar(16) CHARACTER SET utf8 NOT NULL,
  `internal_id` varchar(12) CHARACTER SET utf8 NOT NULL COMMENT 'The code for Schaubroeck to identify the employee',
  `indexed_on_schaubroeck` tinyint(1) DEFAULT NULL,
  `dinner_cheques` tinyint(1) NOT NULL,
  `dinner_cheques_counter` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `employee_internal_id` (`internal_id`),
  KEY `role_id` (`role_id`),
  KEY `departement_id` (`employee_department_id`),
  KEY `supervisor_id` (`supervisor_id`),
  KEY `indexed_on_schaubroeck` (`indexed_on_schaubroeck`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `employees`
--

LOCK TABLES `employees` WRITE;
/*!40000 ALTER TABLE `employees` DISABLE KEYS */;
INSERT INTO `employees` VALUES (1,3,0,'','','','','',0,'1',4,'','-1',1,1,NULL);
/*!40000 ALTER TABLE `employees` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `exports`
--

DROP TABLE IF EXISTS `exports`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `exports` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `json_path` varchar(255) NOT NULL,
  `xls_path` varchar(255) DEFAULT NULL,
  `ignored` tinyint(1) DEFAULT NULL,
  `employee_id` int(10) unsigned DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=UTF8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `exports`
--

LOCK TABLES `exports` WRITE;
/*!40000 ALTER TABLE `exports` DISABLE KEYS */;
/*!40000 ALTER TABLE `exports` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `request_to_calendar_days`
--

DROP TABLE IF EXISTS `request_to_calendar_days`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `request_to_calendar_days` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `request_id` int(11) NOT NULL,
  `employee_id` int(10) unsigned DEFAULT NULL,
  `calendar_day_id` varchar(55) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `request_to_calendar_days_id` (`id`),
  KEY `fk_request` (`request_id`),
  KEY `fk_rtcd_employee` (`employee_id`),
  CONSTRAINT `fk_request` FOREIGN KEY (`request_id`) REFERENCES `requests` (`id`),
  CONSTRAINT `fk_rtcd_employee` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=UTF8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `request_to_calendar_days`
--

LOCK TABLES `request_to_calendar_days` WRITE;
/*!40000 ALTER TABLE `request_to_calendar_days` DISABLE KEYS */;
/*!40000 ALTER TABLE `request_to_calendar_days` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `requests`
--

DROP TABLE IF EXISTS `requests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `requests` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `employee_id` int(10) unsigned DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `start_date` date NOT NULL,
  `start_time` varchar(2) NOT NULL,
  `end_date` date NOT NULL,
  `end_time` varchar(2) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `calendar_item_type_id` int(11) NOT NULL,
  `replacement_id` varchar(12) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_employee` (`employee_id`),
  KEY `fk_replacement` (`replacement_id`),
  KEY `fk_type` (`calendar_item_type_id`),
  KEY `timestamp` (`timestamp`),
  CONSTRAINT `fk_employee` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`),
  CONSTRAINT `fk_replacement` FOREIGN KEY (`replacement_id`) REFERENCES `employees` (`internal_id`),
  CONSTRAINT `fk_type` FOREIGN KEY (`calendar_item_type_id`) REFERENCES `calendar_item_types` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=UTF8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `requests`
--

LOCK TABLES `requests` WRITE;
/*!40000 ALTER TABLE `requests` DISABLE KEYS */;
/*!40000 ALTER TABLE `requests` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `roles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(12) NOT NULL,
  `adminpanel` tinyint(1) NOT NULL,
  `allow` tinyint(1) NOT NULL,
  `verifyuser` tinyint(1) NOT NULL,
  `edituser` tinyint(1) NOT NULL,
  `removeuser` tinyint(1) NOT NULL,
  `editcalendaritem` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `role_id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=UTF8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` VALUES (1,'admin',1,1,1,1,1,1),(2,'hr',1,1,1,1,1,1),(3,'standard',0,0,0,1,0,0),(4,'supervisor',0,1,0,1,0,0);
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `streams`
--

DROP TABLE IF EXISTS `streams`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `streams` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `employee_id` varchar(12) DEFAULT NULL,
  `calendar_item_type_id` int(11) DEFAULT NULL,
  `relative_nr` int(11) DEFAULT NULL,
  `day_nr` int(11) DEFAULT NULL,
  `day_time` varchar(2) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `stream_natural_key` (`employee_id`,`calendar_item_type_id`,`relative_nr`,`day_nr`,`day_time`),
  KEY `calendar_item_type_id` (`calendar_item_type_id`),
  KEY `employee_id` (`employee_id`),
  CONSTRAINT `fk_st_calendarItem` FOREIGN KEY (`calendar_item_type_id`) REFERENCES `calendar_item_types` (`id`),
  CONSTRAINT `fk_st_employee` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`internal_id`)
) ENGINE=InnoDB DEFAULT CHARSET=UTF8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `streams`
--

LOCK TABLES `streams` WRITE;
/*!40000 ALTER TABLE `streams` DISABLE KEYS */;
/*!40000 ALTER TABLE `streams` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `streams_executions`
--

DROP TABLE IF EXISTS `streams_executions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `streams_executions` (
  `id` int(11) NOT NULL,
  `employee_id` int(11) DEFAULT NULL,
  `day_relative` int(2) DEFAULT NULL,
  `day_absolute` date DEFAULT NULL,
  `day_next` date DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=UTF8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `streams_executions`
--

LOCK TABLES `streams_executions` WRITE;
/*!40000 ALTER TABLE `streams_executions` DISABLE KEYS */;
/*!40000 ALTER TABLE `streams_executions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `employee_id` int(10) unsigned DEFAULT NULL,
  `email` varchar(250) NOT NULL,
  `uitid` varchar(50) NOT NULL,
  `status` varchar(9) NOT NULL DEFAULT 'requested',
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `user_id` (`id`),
  UNIQUE KEY `user_uitid` (`uitid`),
  KEY `fk_users_employee_id` (`employee_id`),
  CONSTRAINT `fk_users_employee_id` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=UTF8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2014-09-02  9:38:41