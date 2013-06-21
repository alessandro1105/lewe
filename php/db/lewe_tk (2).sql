-- phpMyAdmin SQL Dump
-- version 3.3.7deb7
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generato il: 22 giu, 2013 at 01:31 AM
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
-- Struttura della tabella `authorization`
--

CREATE TABLE IF NOT EXISTS `authorization` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `user_allowed_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=31 ;

--
-- Dump dei dati per la tabella `authorization`
--

INSERT INTO `authorization` (`id`, `user_id`, `user_allowed_id`) VALUES
(27, 28, 33),
(7, 30, 29),
(21, 31, 29),
(20, 31, 28),
(28, 33, 28),
(22, 31, 30),
(26, 28, 30),
(25, 28, 29),
(29, 33, 29),
(30, 33, 30);

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
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=36 ;

--
-- Dump dei dati per la tabella `pages`
--

INSERT INTO `pages` (`id`, `owner`, `name`, `controller`) VALUES
(35, 'site', 'how-it-works', '\\Controller\\Pages\\PresentationSiteHowItWorks'),
(34, 'site', 'contact', '\\Controller\\Pages\\PresentationSiteContact'),
(33, 'site', 'code', '\\Controller\\Pages\\PresentationSiteCode'),
(32, 'site', 'friend_lewe_details', '\\Controller\\Pages\\ProtectedSiteFriendLeweDetails'),
(31, 'site', 'registration', '\\Controller\\Pages\\PresentationSiteRegistration'),
(30, 'site', 'friend_lewe', '\\Controller\\Pages\\ProtectedSiteFriendLewe'),
(29, 'site', 'public_lewe', '\\Controller\\Pages\\ProtectedSitePublicLewe'),
(28, 'site', 'my_lewe', '\\Controller\\Pages\\ProtectedSiteMyLewe'),
(27, 'site', 'logout', '\\Controller\\Pages\\ProtectedSiteLogout'),
(26, 'site', 'home', '\\Controller\\Pages\\ProtectedSiteHome'),
(25, 'site', 'login', '\\Controller\\Pages\\PresentationSiteLogin'),
(23, 'site', 'default', '\\Controller\\Pages\\PresentationSiteIndex'),
(22, 'script', 'remote_script_add_surveys', '\\Controller\\Scripts\\AddSurveys');

-- --------------------------------------------------------

--
-- Struttura della tabella `surveys`
--

CREATE TABLE IF NOT EXISTS `surveys` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sensor_name` text NOT NULL,
  `sensor_value` text NOT NULL,
  `sensor_timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `user_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=482 ;

--
-- Dump dei dati per la tabella `surveys`
--

INSERT INTO `surveys` (`id`, `sensor_name`, `sensor_value`, `sensor_timestamp`, `user_id`) VALUES
(481, 'TEMPERATURE', '35.5', '2013-06-04 14:00:30', 28),
(480, 'GSR', '2700', '2013-06-04 14:00:30', 28),
(479, 'TEMPERATURE', '36.5', '2013-06-04 14:01:36', 28),
(478, 'GSR', '2600', '2013-06-04 14:01:36', 28),
(477, 'TEMPERATURE', '36.5', '2013-06-03 10:14:56', 28),
(476, 'GSR', '2700', '2013-06-03 10:14:56', 28),
(475, 'TEMPERATURE', '37', '2013-06-02 06:28:16', 28),
(474, 'GSR', '2900', '2013-06-02 06:28:16', 28),
(473, 'TEMPERATURE', '34.5', '2013-06-01 16:34:56', 28),
(472, 'GSR', '2200', '2013-06-01 16:34:56', 28),
(471, 'TEMPERATURE', '35.5', '2013-06-01 13:48:16', 28),
(470, 'GSR', '2200', '2013-06-01 13:48:16', 28),
(469, 'TEMPERATURE', '37.5', '2013-06-01 11:01:36', 28),
(468, 'GSR', '2300', '2013-06-01 11:01:36', 28),
(467, 'TEMPERATURE', '38', '2013-06-01 08:14:56', 28),
(466, 'GSR', '2000', '2013-06-01 08:14:56', 28),
(465, 'TEMPERATURE', '36.5', '2013-06-01 05:44:56', 28),
(464, 'GSR', '2200', '2013-06-01 05:28:16', 28),
(463, 'TEMPERATURE', '37', '2013-06-01 02:58:16', 28),
(462, 'GSR', '2100', '2013-06-01 02:58:16', 28),
(461, 'TEMPERATURE', '36.5', '2013-06-01 02:41:36', 28),
(460, 'GSR', '2200', '2013-06-01 02:41:36', 28),
(459, 'GSR', '0', '2013-06-16 04:42:23', 33),
(458, 'TEMPERATURE', '39.100002', '2013-06-16 04:42:23', 33),
(457, 'GSR', '0', '2013-06-16 04:37:23', 33),
(456, 'TEMPERATURE', '38.600002', '2013-06-16 04:37:23', 33),
(455, 'GSR', '0', '2013-06-16 04:32:23', 33),
(454, 'TEMPERATURE', '39.100002', '2013-06-16 04:32:23', 33),
(453, 'GSR', '0', '2013-06-16 04:27:23', 33),
(452, 'TEMPERATURE', '35.600002', '2013-06-16 04:27:23', 33),
(450, 'TEMPERATURE', '37.100002', '2013-06-16 04:22:23', 33),
(451, 'GSR', '0', '2013-06-16 04:22:23', 33),
(449, 'GSR', '0', '2013-06-16 04:17:23', 33),
(448, 'TEMPERATURE', '40.5', '2013-06-16 04:17:23', 33),
(447, 'GSR', '0', '2013-06-16 04:06:50', 33),
(446, 'TEMPERATURE', '37.100002', '2013-06-16 04:06:50', 33);

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
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=34 ;

--
-- Dump dei dati per la tabella `users`
--

INSERT INTO `users` (`id`, `email`, `username`, `password`, `typeUser`, `active`) VALUES
(29, 'ale2@ale.it', 'alessandro123', 'alepas1105', 'normal', 1),
(28, 'ale@ale.it', 'alessandro1105', 'alepas1105', 'normal', 1),
(30, 'ale3@ale.it', 'alessandro1234', 'alepas1105', 'normal', 1),
(31, 'test@test.it', 'testuser123', 'test1234', 'normal', 1),
(32, 'test2@test.it', 'testuser1234', 'test1234', 'normal', 1),
(33, 'finale@finale.it', 'testfinale', 'test1234', 'normal', 1);
