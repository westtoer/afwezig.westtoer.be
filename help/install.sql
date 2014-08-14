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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `auth_items`
--

DROP TABLE IF EXISTS `auth_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `auth_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `request_id` int(11) NOT NULL,
  `supervisor_id` int(10) unsigned DEFAULT NULL,
  `authorized` tinyint(1) NOT NULL,
  `authorization_date` timestamp NULL DEFAULT NULL COMMENT 'Timestamp of when the authorization has happened',
  `message` varchar(255) DEFAULT NULL COMMENT 'Is used to see where a auth_item originated from, e.g. stream. Not used with standard requests',
  PRIMARY KEY (`id`),
  UNIQUE KEY `auth_item_id` (`id`),
  UNIQUE KEY `request_id` (`request_id`),
  KEY `fk_ai_supervisor_id` (`supervisor_id`),
  CONSTRAINT `fk_ai_request_id` FOREIGN KEY (`request_id`) REFERENCES `requests` (`id`),
  CONSTRAINT `fk_ai_supervisor_id` FOREIGN KEY (`supervisor_id`) REFERENCES `employees` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=246 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

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
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

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
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `employee_departments`
--

DROP TABLE IF EXISTS `employee_departments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `employee_departments` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  UNIQUE KEY `employee_department_id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

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
  `3gram` varchar(255) NOT NULL COMMENT 'A 3gram as well as an email adress is allowed',
  `telephone` text NOT NULL,
  `name` varchar(255) NOT NULL,
  `surname` varchar(255) NOT NULL,
  `note` mediumtext NOT NULL,
  `daysleft` decimal(10,0) NOT NULL,
  `status` varchar(9) NOT NULL COMMENT 'Allows the deactivation of an employee',
  `supervisor_id` int(11) NOT NULL,
  `gsm` varchar(16) NOT NULL,
  `internal_id` varchar(12) NOT NULL COMMENT 'The code for Schaubroeck to identify the employee',
  PRIMARY KEY (`id`),
  UNIQUE KEY `employee_internal_id` (`internal_id`),
  KEY `role_id` (`role_id`),
  KEY `departement_id` (`employee_department_id`),
  KEY `supervisor_id` (`supervisor_id`),
  CONSTRAINT `fk_em_role_id` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1101 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

/* Insert a null user*/
LOCK TABLES `employees` WRITE;
INSERT INTO `employees` VALUES (1,3,1,'','','','','',0,'1','-1','0','-1');
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
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

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
  CONSTRAINT `fk_type` FOREIGN KEY (`calendar_item_type_id`) REFERENCES `calendar_item_types` (`id`),
  CONSTRAINT `fk_employee` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`),
  CONSTRAINT `fk_replacement` FOREIGN KEY (`replacement_id`) REFERENCES `employees` (`internal_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

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
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `streams`
--

DROP TABLE IF EXISTS `streams`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `streams` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `rule_type` varchar(2) DEFAULT NULL,
  `day_relative` int(2) DEFAULT NULL,
  `employee_id` int(11) DEFAULT NULL,
  `calendar_item_type_id` int(11) DEFAULT NULL,
  `day_time` varchar(4) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

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
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
