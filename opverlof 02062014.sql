-- phpMyAdmin SQL Dump
-- version 4.0.9
-- http://www.phpmyadmin.net
--
-- Machine: 127.0.0.1
-- Genereertijd: 02 jul 2014 om 19:20
-- Serverversie: 5.6.14
-- PHP-versie: 5.5.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Databank: `opverlof`
--

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `acl`
--

CREATE TABLE IF NOT EXISTS `acl` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `IT` tinyint(1) NOT NULL,
  `HR` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Gegevens worden uitgevoerd voor tabel `acl`
--

INSERT INTO `acl` (`id`, `user_id`, `IT`, `HR`) VALUES
(1, 2, 0, 1);

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `acls`
--

CREATE TABLE IF NOT EXISTS `acls` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `IT` tinyint(1) NOT NULL,
  `HR` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

--
-- Gegevens worden uitgevoerd voor tabel `acls`
--

INSERT INTO `acls` (`id`, `user_id`, `IT`, `HR`) VALUES
(1, 2, 1, 1),
(2, 1, 1, 1),
(3, 2, 0, 0);

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(50) DEFAULT NULL,
  `password` varchar(50) DEFAULT NULL,
  `role` varchar(20) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `telephone` text NOT NULL,
  `hoursleft` int(11) NOT NULL,
  `stewards` varchar(255) NOT NULL,
  `group` varchar(99) NOT NULL,
  `name` varchar(255) NOT NULL,
  `surname` varchar(255) NOT NULL,
  `note` mediumtext NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

--
-- Gegevens worden uitgevoerd voor tabel `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `role`, `created`, `modified`, `email`, `telephone`, `hoursleft`, `stewards`, `group`, `name`, `surname`, `note`) VALUES
(1, 'Niels', 'c3baba003ecca8a5b1e17472960ceeeb9e808066', 'admin', '2014-06-26 14:48:49', '2014-06-26 14:48:49', 'nielsvermaut@gmail.com', '0472499286', 0, '', 'IT', 'Niels', 'Vermaut', '0'),
(2, 'Test', 'e1e12230852adf58ba22a2d83ce2dd235a81b402', 'steward', '2014-06-26 17:12:15', '2014-06-26 17:12:15', 'mooi@email.com', '0', 0, 'IT', 'IT', 'Test', 'Persoon', '0'),
(3, 'User01', '1284411e8061876f1679c6d2707930b986e7c255', 'standard', '2014-06-27 12:51:50', '2014-06-27 12:51:50', '', '472499286', 0, 'IT', 'HR', '', '', '0');

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `verlofs`
--

CREATE TABLE IF NOT EXISTS `verlofs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `start` datetime NOT NULL,
  `end` datetime NOT NULL,
  `replacement_id` int(11) NOT NULL,
  `note` mediumtext NOT NULL,
  `allowed` tinyint(1) NOT NULL,
  `replacement_name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=7 ;

--
-- Gegevens worden uitgevoerd voor tabel `verlofs`
--

INSERT INTO `verlofs` (`id`, `user_id`, `start`, `end`, `replacement_id`, `note`, `allowed`, `replacement_name`) VALUES
(3, 1, '2014-06-27 10:10:00', '2014-06-27 21:03:00', 2, '', 0, ''),
(4, 1, '2014-06-28 10:00:00', '2014-06-28 21:03:00', 1, '', 1, ''),
(6, 2, '2014-06-30 10:10:00', '2014-06-30 21:03:00', 1, 'Hier zit wel een tekstje in', 1, '');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
