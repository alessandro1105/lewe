-- phpMyAdmin SQL Dump
-- version 3.3.7deb7
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generato il: 19 mag, 2013 at 04:02 PM
-- Versione MySQL: 5.1.63
-- Versione PHP: 5.3.3-7+squeeze14

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `lewe_tk`
--

-- --------------------------------------------------------

--
-- Struttura della tabella `pages`
--

CREATE TABLE IF NOT EXISTS `pages` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `owner` text NOT NULL,
  `name` text NOT NULL,
  `controller` text NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=23 ;

--
-- Dump dei dati per la tabella `pages`
--

INSERT INTO `pages` (`id`, `owner`, `name`, `controller`) VALUES
(22, 'script', 'remote_script_add_surveys', '\\Controller\\Scripts\\AddSurveys');

-- --------------------------------------------------------

--
-- Struttura della tabella `surveys`
--

CREATE TABLE IF NOT EXISTS `surveys` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sensor_name` text NOT NULL,
  `sensor_value` text NOT NULL,
  `sensor_timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `sensor_user_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dump dei dati per la tabella `surveys`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `email` text NOT NULL,
  `username` text NOT NULL,
  `password` text NOT NULL,
  `typeUser` text NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=29 ;

--
-- Dump dei dati per la tabella `users`
--

INSERT INTO `users` (`id`, `email`, `username`, `password`, `typeUser`, `active`) VALUES
(28, 'ale@ale.it', 'alessandro1105', 'alepas1105', 'normal', 1);
