-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Mar 14, 2022 at 02:10 PM
-- Server version: 5.7.31
-- PHP Version: 7.4.9


SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `aurora`
--
CREATE DATABASE IF NOT EXISTS `aurora` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `aurora`;

-- --------------------------------------------------------

--
-- Table structure for table `log`
--

DROP TABLE IF EXISTS `log`;
CREATE TABLE IF NOT EXISTS `log` (
  `logId` int(11) NOT NULL AUTO_INCREMENT,
  `extra_userId` int(11) DEFAULT NULL,
  `extra_userName` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `extra_role` tinytext COLLATE utf8mb4_unicode_ci,
  `extra_firstName` tinytext COLLATE utf8mb4_unicode_ci,
  `extra_lastName` tinytext COLLATE utf8mb4_unicode_ci,
  `priorityName` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `message` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `timeStamp` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `priority` int(1) NOT NULL,
  `extra_referenceId` tinytext COLLATE utf8mb4_unicode_ci,
  `extra_errno` tinytext COLLATE utf8mb4_unicode_ci,
  `extra_file` text COLLATE utf8mb4_unicode_ci,
  `extra_line` text COLLATE utf8mb4_unicode_ci,
  `extra_trace` text COLLATE utf8mb4_unicode_ci,
  `fileId` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`logId`),
  KEY `userId` (`extra_userId`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
CREATE TABLE IF NOT EXISTS `sessions` (
  `id` char(32) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` char(32) COLLATE utf8mb4_unicode_ci NOT NULL,
  `modified` int(11) DEFAULT NULL,
  `lifetime` int(11) DEFAULT NULL,
  `data` text COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`,`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userName` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(320) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'user',
  `firstName` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `lastName` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `profileImage` mediumtext COLLATE utf8mb4_unicode_ci,
  `age` int(11) DEFAULT NULL,
  `birthday` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gender` varchar(6) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `race` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bio` text COLLATE utf8mb4_unicode_ci,
  `companyName` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sessionLength` int(11) NOT NULL DEFAULT '86400',
  `regDate` tinytext COLLATE utf8mb4_unicode_ci,
  `active` tinyint(1) NOT NULL DEFAULT '0',
  `verified` tinyint(1) NOT NULL DEFAULT '0',
  `regHash` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `resetTimeStamp` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `resetHash` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_roles`
--

DROP TABLE IF EXISTS `user_roles`;
CREATE TABLE IF NOT EXISTS `user_roles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `role` tinytext COLLATE utf8mb4_unicode_ci NOT NULL,
  `inheritsFrom` tinytext COLLATE utf8mb4_unicode_ci,
  `label` tinytext COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_roles`
--

INSERT INTO `user_roles` (`id`, `role`, `inheritsFrom`, `label`) VALUES
(1, 'user', 'guest', 'Member'),
(2, 'moderator', 'user', 'Moderator'),
(3, 'admin', 'moderator', 'Administrator');

--
-- Constraints for dumped tables
--

--
-- Constraints for table `log`
--
ALTER TABLE `log`
  ADD CONSTRAINT `log_ibfk_1` FOREIGN KEY (`extra_userId`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
