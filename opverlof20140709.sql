-- phpMyAdmin SQL Dump
-- version 4.1.12
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jul 09, 2014 at 06:46 PM
-- Server version: 5.6.16
-- PHP Version: 5.5.11

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `westtoer-op-verlof`
--

-- --------------------------------------------------------

--
-- Table structure for table `auth_items`
--

CREATE TABLE IF NOT EXISTS `auth_items` (
  `id` int(11) NOT NULL,
  `calendaritem_id` int(11) NOT NULL,
  `supervisor_id` int(11) NOT NULL,
  `supervisor_auth` tinyint(1) NOT NULL,
  `authorized` tinyint(1) NOT NULL,
  `authorization_date` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `auth_supervisors`
--

CREATE TABLE IF NOT EXISTS `auth_supervisors` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `employee_supervisor_id` int(11) NOT NULL,
  `employee_supervisee_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `calendar_items`
--

CREATE TABLE IF NOT EXISTS `calendar_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `employee_id` int(11) NOT NULL,
  `auth_item_id` int(11) NOT NULL,
  `start_date` date NOT NULL,
  `start_time` varchar(3) NOT NULL,
  `end_date` date NOT NULL,
  `end_time` varchar(3) NOT NULL,
  `replacement_id` int(11) NOT NULL,
  `note` mediumtext NOT NULL,
  `calendar_item_type_id` int(11) NOT NULL,
  `approved` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=16 ;

--
-- Dumping data for table `calendar_items`
--

INSERT INTO `calendar_items` (`id`, `employee_id`, `auth_item_id`, `start_date`, `start_time`, `end_date`, `end_time`, `replacement_id`, `note`, `calendar_item_type_id`, `approved`) VALUES
(3, 0, 0, '2014-07-07', 'AM', '2014-07-09', 'DAY', 0, 'Dit is een algemene feestdag', 1, 1),
(4, 2, 0, '2014-07-05', '', '2014-07-15', '', 2, 'Ik ben op verlof', 2, 1),
(6, 2, 0, '2014-07-04', '', '2014-07-05', '', 1, 'Hier zit wel een tekstje in', 0, 1),
(8, 1, 0, '2014-07-05', 'AM', '2014-07-05', 'AM', 2, '', 1, 1),
(9, 1, 0, '2014-07-08', 'AM', '2014-07-10', 'AM', 3, '', 1, 1),
(11, 2, 0, '2014-07-10', 'AM', '2014-07-10', 'AM', 1, '', 16, 1),
(12, 2, 0, '2014-07-10', 'AM', '2014-07-10', 'AM', 1, '', 16, 0),
(13, 2, 0, '2014-07-10', 'AM', '2014-07-10', 'AM', 1, '', 16, 1),
(14, 2, 0, '2014-07-10', 'AM', '2014-07-10', 'AM', 1, '', 16, 1),
(15, 3, 0, '2014-09-07', 'DAG', '2014-11-07', 'AM', 1, '', 23, 0);

-- --------------------------------------------------------

--
-- Table structure for table `calendar_item_types`
--

CREATE TABLE IF NOT EXISTS `calendar_item_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `code` varchar(4) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=27 ;

--
-- Dumping data for table `calendar_item_types`
--

INSERT INTO `calendar_item_types` (`id`, `name`, `code`) VALUES
(1, 'Adoptieverlof', 'AD'),
(2, 'Arbeidsongeval', 'AO'),
(3, 'Betaalde Feestdag', 'F'),
(4, 'Bloedafgifte', 'BL'),
(5, 'Co-moederschapsverlof', 'CMV'),
(6, 'Dienstvrijstelling', 'DV'),
(7, 'Erkende Staking', 'ES'),
(8, 'Europese Vakantie', 'EV'),
(9, 'Gewerkt', 'G'),
(10, 'Jeugdvakantie', 'JV'),
(11, 'Loopbaanonderbreking', 'LO'),
(12, 'Niet Gewerkt', 'X'),
(13, 'Omstandigheidsverlof Bezoldigd', 'OM'),
(14, 'Onbezoldigd Verlof', 'ON'),
(15, 'Onwettig Afwezig', 'OA'),
(16, 'Ouderschapsverlof', 'OU'),
(17, 'Politiek Verlof Bezoldigd', 'PV'),
(18, 'Recuperatieverlof', 'R'),
(19, 'Seniorverlof', 'SV'),
(20, 'Sollicitatieverlof', 'SOL'),
(21, 'Syndicaal Verlof Bezoldigd', 'SY'),
(22, 'Vaderschapsverlof', 'VV'),
(23, 'Verlof', 'V'),
(24, 'Ziek', 'Z'),
(25, 'Ziek >1 Maand', 'Z>1M'),
(26, 'Zwangerschapsverlof', 'ZW');

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
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `employees`
--

INSERT INTO `employees` (`id`, `username`, `password`, `role_id`, `employee_department_id`, `created`, `modified`, `3gram`, `telephone`, `name`, `surname`, `note`, `daysleft`, `status`, `linked`) VALUES
(1, 'Niels', 'c3baba003ecca8a5b1e17472960ceeeb9e808066', 1, 1, '2014-06-26 14:48:49', '2014-06-26 14:48:49', 'nielsvermaut@gmail.com', '0472499286', 'Niels', 'Vermaut', '0', '0', '0', 1),
(2, 'Test', 'e1e12230852adf58ba22a2d83ce2dd235a81b402', 0, 1, '2014-06-26 17:12:15', '2014-06-26 17:12:15', 'mooi@email.com', '0', 'Test', 'Persoon', '0', '0', '0', 0),
(3, 'timvanholle', NULL, 3, 1, NULL, NULL, '', '', 'Tim', 'Vanholle', '', '0', '', 1);

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
(10, 2, 'marc.portier@gmail.com', '0e8ff3bb-43a1-4b18-ac6c-6172f9ae4961', 'requested'),
(11, 3, 'tim.vanholle@westtoer.be', 'fcd13164-ccaf-49ea-aa74-3123ce44359f', 'active');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
