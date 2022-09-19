SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

CREATE DATABASE IF NOT EXISTS `testdb` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `testdb`;

DROP TABLE IF EXISTS `log`;
CREATE TABLE IF NOT EXISTS `log` (
  `logId` int(11) NOT NULL AUTO_INCREMENT,
  `extra_userId` int(11) DEFAULT NULL,
  `extra_userName` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `extra_role` tinytext COLLATE utf8mb4_unicode_ci,
  `extra_privilege` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `extra_firstName` tinytext COLLATE utf8mb4_unicode_ci,
  `extra_lastName` tinytext COLLATE utf8mb4_unicode_ci,
  `extra_email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `extra_profileImage` mediumtext COLLATE utf8mb4_unicode_ci,
  `priorityName` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `extra_resourceId` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `extra_action` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `extra_textdomain` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
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
) ENGINE=InnoDB AUTO_INCREMENT=34 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `log` (`logId`, `extra_userId`, `extra_userName`, `extra_role`, `extra_privilege`, `extra_firstName`, `extra_lastName`, `extra_email`, `extra_profileImage`, `priorityName`, `extra_resourceId`, `extra_action`, `extra_textdomain`, `message`, `timeStamp`, `priority`, `extra_referenceId`, `extra_errno`, `extra_file`, `extra_line`, `extra_trace`, `fileId`) VALUES
(32, 1, 'jsmith', 'Administrator', NULL, 'Joey', 'Smith', 'jsmith@webinertia.net', 'profileImage_62a9588780c666_31265011.jpg', 'INFO', NULL, NULL, NULL, 'User Joey Smith successfully logged out.', '09-18-2022 17:49:45', 6, NULL, NULL, NULL, NULL, NULL, 0),
(33, 1, 'jsmith', 'Administrator', NULL, 'Joey', 'Smith', 'jsmith@webinertia.net', 'profileImage_62a9588780c666_31265011.jpg', 'NOTICE', NULL, NULL, NULL, 'log_login_success', '09-18-2022 18:06:55', 5, NULL, NULL, NULL, NULL, NULL, 0);

DROP TABLE IF EXISTS `pages`;
CREATE TABLE IF NOT EXISTS `pages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parentId` int(10) UNSIGNED DEFAULT NULL,
  `ownerId` int(11) UNSIGNED DEFAULT NULL,
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
  `visible` tinyint(1) UNSIGNED NOT NULL DEFAULT '1',
  `route` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `uri` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `action` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `query` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `isGroupPage` tinyint(1) UNSIGNED DEFAULT NULL,
  `allowComments` tinyint(1) UNSIGNED DEFAULT NULL,
  `content` text COLLATE utf8mb4_unicode_ci,
  `isLandingPage` tinyint(1) UNSIGNED DEFAULT NULL,
  `cmsType` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `createdDate` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `updatedDate` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `keywords` text COLLATE utf8mb4_unicode_ci,
  `description` text COLLATE utf8mb4_unicode_ci,
  `showOnLandingPage` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `pages` (`id`, `parentId`, `ownerId`, `label`, `title`, `class`, `iconClass`, `order`, `params`, `rel`, `rev`, `resource`, `privilege`, `visible`, `route`, `uri`, `action`, `query`, `isGroupPage`, `allowComments`, `content`, `isLandingPage`, `cmsType`, `createdDate`, `updatedDate`, `keywords`, `description`, `showOnLandingPage`) VALUES
(1, 0, 1, 'HomeLandingPage', 'homelandingpage', 'nav-link', NULL, 1, '{\"title\": \"homelandingpage\"}', NULL, NULL, 'page', 'view', 1, 'page', NULL, NULL, NULL, NULL, NULL, '<p>Congratulations! You have successfully installed <a href=\"https://github.com/Tyrsson/aurora-2.0/wiki\" target=\"_blank\" rel=\"noopener\">ACMS</a>. testing</p>', 1, NULL, '07-23-2022 5:12:21', '07-23-2022 9:26:01', NULL, NULL, 0),
(6, 1, 1, 'Follow Development', 'follow-development', 'nav-link', NULL, 2, '{\"title\": \"follow-development\"}', NULL, NULL, 'page', 'view', 1, 'page', NULL, 'page', NULL, NULL, NULL, '<p>Follow Development.</p>\r\n<p>Keep up to date on all the changes, or bugs lol... Testing.. Again. and Again...</p>', 0, NULL, '07-23-2022 6:28:31', '07-24-2022 11:59:42', 'Aurora, Php, Custom Development', 'Follow Aurora Development!!', 1),
(9, 0, 1, 'About Us', 'about-us', 'nav-link', NULL, 3, '{\"title\": \"about-us\"}', NULL, NULL, 'page', 'view', 1, 'page', NULL, 'page', NULL, NULL, NULL, '<p>This will be the about us page. Testing Edit. This is some text</p>', 0, NULL, '07-23-2022 10:25:00', '09-17-2022 1:28:40', NULL, NULL, 0),
(10, 0, 1, 'Test', 'test', 'nav-link', NULL, 4, '{\"title\": \"test\"}', NULL, NULL, 'page', 'view', 1, 'page', NULL, NULL, NULL, NULL, NULL, '', 0, NULL, '07-27-2022 8:01:53', NULL, NULL, NULL, 0),
(11, 0, 1, 'New test', 'new-test', 'nav-link', NULL, 5, '{\"title\": \"new-test\", \"action\": \"page\"}', NULL, NULL, 'page', 'view', 1, 'page', NULL, NULL, NULL, NULL, NULL, '<p>This is to test pages creation and url generation.</p>', 0, NULL, '08-6-2022 9:23:28', NULL, NULL, NULL, 0),
(12, 1, 1, 'Daves Page', 'daves-page', 'nav-link', NULL, 6, '{\"title\": \"daves-page\"}', NULL, NULL, 'page', 'view', 1, 'page', NULL, NULL, NULL, NULL, NULL, '<p>This is Daves text</p>', 0, NULL, '09-17-2022 1:32:47', NULL, NULL, NULL, 1);

DROP TABLE IF EXISTS `sessions`;
CREATE TABLE IF NOT EXISTS `sessions` (
  `id` char(32) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` char(32) COLLATE utf8mb4_unicode_ci NOT NULL,
  `modified` int(11) DEFAULT NULL,
  `lifetime` int(11) DEFAULT NULL,
  `data` longtext COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`,`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `sessions` (`id`, `name`, `modified`, `lifetime`, `data`) VALUES
('iskgkf864rkam3ec2eh62g7fee', 'PHPSESSID', 1663542418, 86400, '__Laminas|a:3:{s:20:\"_REQUEST_ACCESS_TIME\";d:1663542417.789155;s:6:\"_VALID\";a:1:{s:28:\"Laminas\\Session\\Validator\\Id\";s:26:\"iskgkf864rkam3ec2eh62g7fee\";}s:14:\"FlashMessenger\";a:1:{s:11:\"EXPIRE_HOPS\";a:2:{s:4:\"hops\";i:0;s:2:\"ts\";d:1663542417.789155;}}}Aurora_Auth|O:26:\"Laminas\\Stdlib\\ArrayObject\":4:{s:7:\"storage\";a:1:{s:7:\"storage\";s:6:\"jsmith\";}s:4:\"flag\";i:2;s:13:\"iteratorClass\";s:13:\"ArrayIterator\";s:19:\"protectedProperties\";a:4:{i:0;s:7:\"storage\";i:1;s:4:\"flag\";i:2;s:13:\"iteratorClass\";i:3;s:19:\"protectedProperties\";}}FlashMessenger|O:26:\"Laminas\\Stdlib\\ArrayObject\":4:{s:7:\"storage\";a:0:{}s:4:\"flag\";i:2;s:13:\"iteratorClass\";s:13:\"ArrayIterator\";s:19:\"protectedProperties\";a:4:{i:0;s:7:\"storage\";i:1;s:4:\"flag\";i:2;s:13:\"iteratorClass\";i:3;s:19:\"protectedProperties\";}}');

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
  `jobTitle` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mobileNumber` varchar(25) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `officeNumber` varchar(25) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `homeNumber` varchar(25) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `street` text COLLATE utf8mb4_unicode_ci,
  `aptNumber` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `city` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `state` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `zip` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `country` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `webUrl` text COLLATE utf8mb4_unicode_ci,
  `github` text COLLATE utf8mb4_unicode_ci,
  `twitter` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `instagram` text COLLATE utf8mb4_unicode_ci,
  `facebook` text COLLATE utf8mb4_unicode_ci,
  `linkedin` text COLLATE utf8mb4_unicode_ci,
  `slack` text COLLATE utf8mb4_unicode_ci,
  `sessionLength` int(11) NOT NULL DEFAULT '86400',
  `regDate` tinytext COLLATE utf8mb4_unicode_ci,
  `active` tinyint(1) NOT NULL DEFAULT '0',
  `prefsTheme` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'default',
  `regHash` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `resetTimeStamp` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `resetHash` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `users` (`id`, `userName`, `email`, `password`, `role`, `firstName`, `lastName`, `profileImage`, `age`, `birthday`, `gender`, `race`, `bio`, `companyName`, `jobTitle`, `mobileNumber`, `officeNumber`, `homeNumber`, `street`, `aptNumber`, `city`, `state`, `zip`, `country`, `webUrl`, `github`, `twitter`, `instagram`, `facebook`, `linkedin`, `slack`, `sessionLength`, `regDate`, `active`, `prefsTheme`, `regHash`, `resetTimeStamp`, `resetHash`) VALUES
(1, 'jsmith', 'jsmith@webinertia.net', '$2y$10$buYOVRO7oURp1Ej3/mNBK.9c.Yo.LH49Iba2Q1l7F3Lmr6dRzAACq', 'Administrator', 'Joey', 'Smith', 'profileImage_62a9588780c666_31265011.jpg', 47, '02-13-1975 12:00:00', 'Male', 'White', 'This is text to test the edit routine and to provide text to use while building the profile page.', 'Webinertia Data Systems', 'Lead Developer', '(205) 555-5555', '(205) 555-5555', NULL, '123 You Wish You Knew St.', NULL, 'Birmingham', 'AL', '35123', NULL, 'https://webinertia.net', 'https://github.com/webinertia', '@webinertia', 'someinstagram', 'https://facebook.com/web', 'https://www.linkedin.com/in/joey-smith-367b9850/', '/webinertia-chat.slack.com', 86400, '02-13-2021 4:20:30', 1, 'default', NULL, NULL, NULL),
(2, 'test', 'test@webinertia.net', '$2y$10$fi1Ibl3JqEB.P/530rb4NOLbieZ6vRS0U0JaWewujWRvbSGxvUEia', 'Member', 'test', 'User', NULL, 99, '08-1-2022 12:00:00', NULL, NULL, 'Testing', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '@test12345', '', 'facebook.com/profile=test;', NULL, '', 86400, '', 0, 'default', NULL, NULL, NULL),
(8, 'asmith', 'asmith@webinertia.net', '$2y$10$/hlG21z1IkvLQ7hCNvTPw.8YxplLKHMoaWiPCI1ghU8Jzvn/KTeSS', 'Super Administrator', 'Aaron', 'Smith', NULL, 24, '03-14-1997 12:00:00', NULL, NULL, 'errt34teferfr4rtr', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 86400, '03-26-2022 4:41:57', 1, 'default', '$2y$10$7PPdVUCmYd4n9JevGthS/.SvZqdGdTs7Za3s7/VZYILJjo69W8tda', NULL, NULL),
(9, 'dsmith', 'dsmith@webinertia.net', '$2y$10$x2ZfQZ3Ob2AmSBb8PH3lC.enEOIja8ZIfHl0v2SPG2w/qFsIwEr06', 'Member', 'Dalton', 'Smith', NULL, 23, '09-29-1999 12:00:00', NULL, NULL, 'Dalton is currently in the U.S. Army.', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 86400, '04-29-2022 10:43:20', 1, 'default', '$2y$10$amkFhTHEtQhJPsJesvt2k.rc4qC5OHPjfJhTZfhE6bvgWl5G7Quta', NULL, NULL);
COMMIT;
