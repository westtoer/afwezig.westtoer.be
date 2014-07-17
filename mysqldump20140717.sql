-- phpMyAdmin SQL Dump
-- version 4.1.12
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jul 17, 2014 at 03:45 PM
-- Server version: 5.6.16
-- PHP Version: 5.5.11

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `afwezig`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin_bookingdates`
--

CREATE TABLE IF NOT EXISTS `admin_bookingdates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `month` int(11) NOT NULL,
  `day_of_month` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `auth_items`
--

CREATE TABLE IF NOT EXISTS `auth_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `request_id` int(11) NOT NULL,
  `supervisor_id` int(11) NOT NULL,
  `authorized` tinyint(1) NOT NULL,
  `authorization_date` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=102 ;

--
-- Dumping data for table `auth_items`
--

INSERT INTO `auth_items` (`id`, `request_id`, `supervisor_id`, `authorized`, `authorization_date`) VALUES
(99, 224, 1, 1, '2014-07-17 12:20:58'),
(100, 225, 0, 0, NULL),
(101, 226, 0, 0, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `calendar_days`
--

CREATE TABLE IF NOT EXISTS `calendar_days` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `employee_id` int(11) NOT NULL,
  `day_date` date NOT NULL,
  `day_time` varchar(3) NOT NULL,
  `calendar_item_type_id` int(11) NOT NULL,
  `replacement_id` int(11) NOT NULL,
  `request_to_calendar_days_id` int(11) NOT NULL,
  `auth_item_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=522 ;

--
-- Dumping data for table `calendar_days`
--

INSERT INTO `calendar_days` (`id`, `employee_id`, `day_date`, `day_time`, `calendar_item_type_id`, `replacement_id`, `request_to_calendar_days_id`, `auth_item_id`) VALUES
(514, 1, '2014-07-18', 'AM', 0, 3, 142, 100),
(515, 1, '2014-07-18', 'PM', 0, 3, 143, 100),
(516, 1, '2014-07-20', 'AM', 0, 3, 144, 100),
(517, 1, '2014-07-20', 'PM', 0, 3, 145, 100),
(518, 1, '2014-12-01', 'AM', 0, 3, 146, 101),
(519, 1, '2014-12-01', 'PM', 0, 3, 147, 101),
(520, 1, '2014-12-02', 'AM', 0, 3, 148, 101),
(521, 1, '2014-12-02', 'PM', 0, 3, 149, 101);

-- --------------------------------------------------------

--
-- Table structure for table `calendar_item_types`
--

CREATE TABLE IF NOT EXISTS `calendar_item_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `code` varchar(4) NOT NULL,
  `user_allowed` tinyint(1) NOT NULL,
  `dinner_cheque` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=27 ;

--
-- Dumping data for table `calendar_item_types`
--

INSERT INTO `calendar_item_types` (`id`, `name`, `code`, `user_allowed`, `dinner_cheque`) VALUES
(1, 'Adoptieverlof', 'AD', 1, 0),
(2, 'Arbeidsongeval', 'AO', 1, 0),
(3, 'Betaalde Feestdag', 'F', 0, 0),
(4, 'Bloedafgifte', 'BL', 1, 0),
(5, 'Co-moederschapsverlof', 'CMV', 1, 0),
(6, 'Dienstvrijstelling', 'DV', 1, 0),
(7, 'Erkende Staking', 'ES', 1, 0),
(8, 'Europese Vakantie', 'EV', 1, 0),
(9, 'Gewerkt', 'G', 1, 1),
(10, 'Jeugdvakantie', 'JV', 1, 0),
(11, 'Loopbaanonderbreking', 'LO', 1, 0),
(12, 'Niet Gewerkt', 'X', 0, 0),
(13, 'Omstandigheidsverlof Bezoldigd', 'OM', 1, 0),
(14, 'Onbezoldigd Verlof', 'ON', 1, 0),
(15, 'Onwettig Afwezig', 'OA', 1, 0),
(16, 'Ouderschapsverlof', 'OU', 1, 0),
(17, 'Politiek Verlof Bezoldigd', 'PV', 1, 0),
(18, 'Recuperatieverlof', 'R', 1, 1),
(19, 'Seniorverlof', 'SV', 1, 0),
(20, 'Sollicitatieverlof', 'SOL', 1, 0),
(21, 'Syndicaal Verlof Bezoldigd', 'SY', 1, 1),
(22, 'Vaderschapsverlof', 'VV', 1, 0),
(23, 'Verlof', 'V', 1, 0),
(24, 'Ziek', 'Z', 1, 0),
(25, 'Ziek >1 Maand', 'Z>1M', 1, 0),
(26, 'Zwangerschapsverlof', 'ZW', 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `employee_departments`
--

CREATE TABLE IF NOT EXISTS `employee_departments` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `employee_departments`
--

INSERT INTO `employee_departments` (`id`, `name`) VALUES
(1, 'PAF');

-- --------------------------------------------------------

--
-- Table structure for table `employees`
--

CREATE TABLE IF NOT EXISTS `employees` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(50) DEFAULT NULL,
  `password` varchar(50) DEFAULT NULL,
  `role_id` int(11) DEFAULT NULL,
  `employee_department_id` int(11) NOT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `3gram` varchar(255) NOT NULL,
  `telephone` text NOT NULL,
  `name` varchar(255) NOT NULL,
  `surname` varchar(255) NOT NULL,
  `note` mediumtext NOT NULL,
  `daysleft` decimal(10,0) NOT NULL,
  `status` varchar(9) NOT NULL,
  `linked` tinyint(1) NOT NULL,
  `supervisor_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `employees`
--

INSERT INTO `employees` (`id`, `username`, `password`, `role_id`, `employee_department_id`, `created`, `modified`, `3gram`, `telephone`, `name`, `surname`, `note`, `daysleft`, `status`, `linked`, `supervisor_id`) VALUES
(1, 'Niels', 'c3baba003ecca8a5b1e17472960ceeeb9e808066', 1, 1, '2014-06-26 14:48:49', '2014-06-26 14:48:49', 'niels.vermaut@westtoer.be', '0472499286', 'Niels', 'Vermaut', 'Hallo', '0', '0', 1, 0),
(2, 'Test', 'e1e12230852adf58ba22a2d83ce2dd235a81b402', 0, 1, '2014-06-26 17:12:15', '2014-06-26 17:12:15', 'mooi@email.com', '0', 'Test', 'Persoon', '0', '0', '0', 0, 0),
(3, 'timvanholle', NULL, 3, 1, NULL, NULL, '', '', 'Tim', 'Vanholle', '', '0', '', 1, 0),
(4, 'Niemand', NULL, NULL, 0, NULL, NULL, '', '', '', '', '', '0', '', 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `request_to_calendar_days`
--

CREATE TABLE IF NOT EXISTS `request_to_calendar_days` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `request_id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `calendar_day_id` int(11) NOT NULL,
  `auth_item_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=150 ;

--
-- Dumping data for table `request_to_calendar_days`
--

INSERT INTO `request_to_calendar_days` (`id`, `request_id`, `employee_id`, `calendar_day_id`, `auth_item_id`) VALUES
(142, 225, 1, 514, 100),
(143, 225, 1, 515, 100),
(144, 225, 1, 516, 100),
(145, 225, 1, 517, 100),
(146, 226, 1, 518, 101),
(147, 226, 1, 519, 101),
(148, 226, 1, 520, 101),
(149, 226, 1, 521, 101);

-- --------------------------------------------------------

--
-- Table structure for table `requests`
--

CREATE TABLE IF NOT EXISTS `requests` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `employee_id` int(11) NOT NULL,
  `auth_item_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `start_date` date NOT NULL,
  `start_time` varchar(2) NOT NULL,
  `end_date` date NOT NULL,
  `end_time` varchar(2) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `calendar_item_type_id` int(11) NOT NULL,
  `replacement_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=227 ;

--
-- Dumping data for table `requests`
--

INSERT INTO `requests` (`id`, `employee_id`, `auth_item_id`, `name`, `start_date`, `start_time`, `end_date`, `end_time`, `timestamp`, `calendar_item_type_id`, `replacement_id`) VALUES
(224, 1, 99, '', '2014-07-18', 'AM', '2014-07-20', 'PM', '2014-07-16 14:16:53', 5, 3),
(225, 1, 100, '', '2014-07-18', 'AM', '2014-07-20', 'PM', '2014-07-16 14:17:43', 5, 3),
(226, 1, 101, '', '2014-12-01', 'AM', '2014-12-02', 'PM', '2014-07-17 12:21:31', 6, 3);

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE IF NOT EXISTS `roles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(12) NOT NULL,
  `adminpanel` tinyint(1) NOT NULL,
  `allow` tinyint(1) NOT NULL,
  `verifyuser` tinyint(1) NOT NULL,
  `edituser` tinyint(1) NOT NULL,
  `removeuser` tinyint(1) NOT NULL,
  `editcalendaritem` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `adminpanel`, `allow`, `verifyuser`, `edituser`, `removeuser`, `editcalendaritem`) VALUES
(1, 'admin', 1, 1, 1, 1, 1, 1),
(2, 'hr', 1, 1, 1, 1, 1, 1),
(3, 'standard', 0, 0, 0, 1, 0, 0),
(4, 'supervisor', 0, 1, 0, 1, 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `employee_id` int(11) NOT NULL,
  `email` varchar(250) NOT NULL,
  `uitid` varchar(50) NOT NULL,
  `status` varchar(9) NOT NULL DEFAULT 'requested',
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=12 ;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `employee_id`, `email`, `uitid`, `status`) VALUES
(1, 1, 'nielsvermaut@gmail.com', '4965feb8-9e28-479b-ba5a-0849622bc154', 'active'),
(10, 2, 'marc.portier@gmail.com', '0e8ff3bb-43a1-4b18-ac6c-6172f9ae4961', 'active'),
(11, 3, 'tim.vanholle@westtoer.be', 'fcd13164-ccaf-49ea-aa74-3123ce44359f', 'active');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
