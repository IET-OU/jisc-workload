--
-- DATABASE - copy, then search and replace the text `EDIT ME`
--
-- Jisc / OU Student Workload Tool.
--
-- @license   http://gnu.org/licenses/gpl.html GPL-3.0+
-- @author    Jitse van Ameijde <djitsz@yahoo.com>
-- @copyright 2015 The Open University.
--

CREATE TABLE IF NOT EXISTS `institutions` (
  `institutionId` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `lastUpdated` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `deleted` datetime DEFAULT NULL,
  PRIMARY KEY (`institutionId`)
) COLLATE utf8_unicode_ci ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `users` (
  `userId` int(11) NOT NULL AUTO_INCREMENT,
  `institutionId` int(11) NOT NULL,
  `firstName` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `lastName` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `login` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `password` char(40) COLLATE utf8_unicode_ci NOT NULL,
  `resetToken` char(40) COLLATE utf8_unicode_ci DEFAULT NULL,
  `accessLevel` int(11) NOT NULL,
  `status` tinyint(3) NOT NULL DEFAULT 1,
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `lastUpdatedBy` int(11) NOT NULL,
  `lastUpdated` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `lastLogin` datetime DEFAULT NULL,
  `deletedBy` int(11) DEFAULT NULL,
  `deleted` datetime DEFAULT NULL,
  PRIMARY KEY (`userId`),
  FOREIGN KEY (`institutionId`) REFERENCES `institutions` (`institutionId`) ON DELETE CASCADE ON UPDATE CASCADE,
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `login` (`login`)
) COLLATE utf8_unicode_ci ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `visitors` (
  `visitorId` int(11) NOT NULL AUTO_INCREMENT,
  `acceptedCookies` tinyint(1) NOT NULL DEFAULT 0,
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `lastUpdated` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`visitorId`)
) COLLATE utf8_unicode_ci ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `sessions` (
  `sessionId` int(11) NOT NULL AUTO_INCREMENT,
  `userId` int(11) DEFAULT NULL,
  `visitorId` int(11) NOT NULL,
  `ip` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `referrer` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `lastUpdated` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`sessionId`),
  FOREIGN KEY (`userId`) REFERENCES `users` (`userId`) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY (`visitorId`) REFERENCES `visitors` (`visitorId`) ON DELETE CASCADE ON UPDATE CASCADE
) COLLATE utf8_unicode_ci ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `page_hits` (
  `pageHitId` int(11) NOT NULL AUTO_INCREMENT,
  `sessionId` int(11) NOT NULL,
  `uri` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `errors` int(11) NOT NULL,
  `responseTime` int(11) NOT NULL,
  `dbHits` int(11) DEFAULT NULL,
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`pageHitId`),
  FOREIGN KEY (`sessionId`) REFERENCES `sessions` (`sessionId`) ON DELETE CASCADE ON UPDATE CASCADE,
  INDEX `created` (`created`)
) COLLATE utf8_unicode_ci ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `faculties` (
  `facultyId` int(11) NOT NULL AUTO_INCREMENT,
  `institutionId` int(11) NOT NULL,
  `name` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `createdBy` int(11) NOT NULL,
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `lastUpdatedBy` int(11) NOT NULL,
  `lastUpdated` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `deletedBy` int(11) DEFAULT NULL,
  `deleted` datetime DEFAULT NULL,
  PRIMARY KEY (`facultyId`),
  FOREIGN KEY (`institutionId`) REFERENCES `institutions` (`institutionId`) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY (`createdBy`) REFERENCES `users` (`userId`) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY (`lastUpdatedBy`) REFERENCES `users` (`userId`) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY (`deletedBy`) REFERENCES `users` (`userId`) ON DELETE CASCADE ON UPDATE CASCADE
) COLLATE utf8_unicode_ci ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `courses` (
  `courseId` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `title` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `presentation` varchar(16) COLLATE utf8_unicode_ci NOT NULL,
  `status` tinyint(3) NOT NULL DEFAULT 1,
  `units` tinyint(3) NOT NULL DEFAULT 0,
  `facultyId` int(11) NOT NULL,
  `level` int(11) NOT NULL DEFAULT 0,
  `credits` int(11) NOT NULL DEFAULT 0,
  `defaultWpm` tinyint(3) NOT NULL DEFAULT 1,
  `wpmLow` int(11) NOT NULL,
  `wpmMed` int(11) NOT NULL,
  `wpmHi` int(11) NOT NULL,
  `createdBy` int(11) NOT NULL,
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `lastUpdatedBy` int(11) NOT NULL,
  `lastUpdated` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `deletedBy` int(11) DEFAULT NULL,
  `deleted` datetime DEFAULT NULL,
  PRIMARY KEY (`courseId`),
  FOREIGN KEY (`facultyId`) REFERENCES `faculties` (`facultyId`) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY (`createdBy`) REFERENCES `users` (`userId`) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY (`lastUpdatedBy`) REFERENCES `users` (`userId`) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY (`deletedBy`) REFERENCES `users` (`userId`) ON DELETE CASCADE ON UPDATE CASCADE
) COLLATE utf8_unicode_ci ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `collaborators` (
  `userId` int(11) NOT NULL,
  `courseId` int(11) NOT NULL,
  PRIMARY KEY (`courseId`,`userId`)
) COLLATE utf8_unicode_ci ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `items` (
  `itemId` int(11) NOT NULL AUTO_INCREMENT,
  `courseId` int(11) NOT NULL,
  `order` int(11) NOT NULL,
  `unit` int(11) NOT NULL,
  `title` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `wordcount` int(11) DEFAULT NULL,
  `wpm` tinyint(3) NOT NULL DEFAULT 1,
  `av` int(11) DEFAULT NULL,
  `other` int(11) DEFAULT NULL,
  `FHI` int(11) DEFAULT NULL,
  `communication` int(11) DEFAULT NULL,
  `productive` int(11) DEFAULT NULL,
  `experiential` int(11) DEFAULT NULL,
  `interactive` int(11) DEFAULT NULL,
  `assessment` int(11) DEFAULT NULL,
  `tuition` int(11) DEFAULT NULL,
  `createdBy` int(11) NOT NULL,
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `lastUpdatedBy` int(11) NOT NULL,
  `lastUpdated` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `deletedBy` int(11) DEFAULT NULL,
  `deleted` datetime DEFAULT NULL,
  PRIMARY KEY (`itemId`),
  FOREIGN KEY (`courseId`) REFERENCES `courses` (`courseId`) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY (`createdBy`) REFERENCES `users` (`userId`) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY (`lastUpdatedBy`) REFERENCES `users` (`userId`) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY (`deletedBy`) REFERENCES `users` (`userId`) ON DELETE CASCADE ON UPDATE CASCADE
) COLLATE utf8_unicode_ci ENGINE=InnoDB;

--
-- EDIT ME.
--

INSERT INTO `institutions` (`institutionId`, `name`) VALUES
(1, '[Name of your institution - EDIT ME]');

INSERT INTO `users` (`userId`, `institutionId`, `firstName`, `lastName`, `email`, `login`, `password`, `resetToken`, `accessLevel`, `status`, `created`, `lastUpdatedBy`, `lastUpdated`, `lastLogin`, `deletedBy`, `deleted`) VALUES
(1, 1, '[First name - EDIT ME]', '[Last name - EDIT ME]', '[Email - EDIT ME]', '[Login - EDIT ME]', SHA1('[Password - EDIT ME]'), NULL, 0, 1, '2014-01-01 12:00:00', 1, '2014-01-01 12:00:00', '2014-01-01 12:00:00', NULL, NULL);


-- End.
