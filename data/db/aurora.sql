-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Apr 17, 2022 at 06:48 AM
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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `log`
--

INSERT INTO `log` (`logId`, `extra_userId`, `extra_userName`, `extra_role`, `extra_firstName`, `extra_lastName`, `priorityName`, `message`, `timeStamp`, `priority`, `extra_referenceId`, `extra_errno`, `extra_file`, `extra_line`, `extra_trace`, `fileId`) VALUES
(1, 1, 'jsmith', NULL, 'Joey', 'Smith', 'INFO', 'User jsmith logged in.', '', 6, NULL, NULL, NULL, NULL, NULL, 0);

-- --------------------------------------------------------

--
-- Table structure for table `pages`
--

DROP TABLE IF EXISTS `pages`;
CREATE TABLE IF NOT EXISTS `pages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parentTitle` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `userId` int(11) NOT NULL,
  `label` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `class` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `iconClass` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `order` int(11) DEFAULT NULL,
  `params` json DEFAULT NULL,
  `rel` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rev` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `resource` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `privilege` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `visible` int(1) NOT NULL DEFAULT '1',
  `route` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `uri` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `controller` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `action` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `query` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `isGroupPage` tinyint(1) NOT NULL DEFAULT '0',
  `allowComments` tinyint(1) NOT NULL DEFAULT '1',
  `content` text COLLATE utf8mb4_unicode_ci,
  `isLandingPage` tinyint(1) NOT NULL DEFAULT '1',
  `cmsType` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `createdDate` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `updatedDate` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `pages`
--

INSERT INTO `pages` (`id`, `parentTitle`, `userId`, `label`, `title`, `class`, `iconClass`, `order`, `params`, `rel`, `rev`, `resource`, `privilege`, `visible`, `route`, `uri`, `controller`, `action`, `query`, `isGroupPage`, `allowComments`, `content`, `isLandingPage`, `cmsType`, `createdDate`, `updatedDate`) VALUES
(10, NULL, 1, 'Test Six', 'test-six', 'nav-link', NULL, NULL, '{\"parentTitle\": \"test-six\"}', NULL, NULL, 'pages', 'view', 1, 'content/category', NULL, NULL, 'page', NULL, 0, 1, '<p>yet another test</p>', 1, NULL, NULL, NULL),
(11, NULL, 1, 'Test Ten', 'test-ten', 'nav-link', NULL, NULL, '{\"parentTitle\": \"test-ten\"}', NULL, NULL, 'pages', 'view', 1, 'content/category', NULL, NULL, 'page', NULL, 0, 1, '<p>yet another test. maybe it will work this time as expected.</p>', 1, NULL, NULL, NULL);

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

-- --------------------------------------------------------

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
  `birthday` tinytext COLLATE utf8mb4_unicode_ci,
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
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `userName`, `email`, `password`, `role`, `firstName`, `lastName`, `profileImage`, `age`, `birthday`, `gender`, `race`, `bio`, `companyName`, `sessionLength`, `regDate`, `active`, `verified`, `regHash`, `resetTimeStamp`, `resetHash`) VALUES
(1, 'jsmith', 'jsmith@webinertia.net', '$2y$10$buYOVRO7oURp1Ej3/mNBK.9c.Yo.LH49Iba2Q1l7F3Lmr6dRzAACq', 'admin', 'Joey', 'Smith', NULL, 47, '02/13/1975', 'male', 'white', 'Just some text for testing.', 'Webinertia Data Systems', 86400, '02-13-2021 4:20:30', 1, 1, NULL, NULL, NULL),
(2, 'test', 'test@webinertia.net', '$2y$10$fi1Ibl3JqEB.P/530rb4NOLbieZ6vRS0U0JaWewujWRvbSGxvUEia', 'staff', 'test', 'User', NULL, 99, '04-4-2022 12:00:00', NULL, NULL, 'gsrtgsrtgsrtg', NULL, 86400, '03-12-2022 4:41:32', 1, 1, NULL, NULL, NULL),
(8, 'asmith', 'asmith@webinertia.net', '$2y$10$/hlG21z1IkvLQ7hCNvTPw.8YxplLKHMoaWiPCI1ghU8Jzvn/KTeSS', 'admin', 'Aaron', 'Smith', NULL, 24, '03-14-1997 12:00:00', NULL, NULL, 'errt34teferfr4rtr', NULL, 86400, '03-26-2022 4:41:57', 0, 0, '$2y$10$7PPdVUCmYd4n9JevGthS/.SvZqdGdTs7Za3s7/VZYILJjo69W8tda', NULL, NULL);

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
(2, 'staff', 'user', 'Staff'),
(3, 'admin', 'staff', 'Administrator');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
