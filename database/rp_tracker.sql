-- phpMyAdmin SQL Dump
-- version 4.2.12deb2+deb8u1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jul 28, 2016 at 03:58 PM
-- Server version: 5.5.50-0+deb8u1
-- PHP Version: 5.6.23-0+deb8u1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `rp_tracker`
--
CREATE DATABASE IF NOT EXISTS `rp_tracker` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `rp_tracker`;

-- --------------------------------------------------------

--
-- Table structure for table `abilities`
--

DROP TABLE IF EXISTS `abilities`;
CREATE TABLE IF NOT EXISTS `abilities` (
`id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `cooldown` int(11) NOT NULL,
  `description` longtext NOT NULL,
  `charId` int(11) NOT NULL,
  `countDown` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Truncate table before insert `abilities`
--

TRUNCATE TABLE `abilities`;
-- --------------------------------------------------------

--
-- Table structure for table `characters`
--

DROP TABLE IF EXISTS `characters`;
CREATE TABLE IF NOT EXISTS `characters` (
`id` int(11) NOT NULL,
  `playerId` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `age` int(11) NOT NULL,
  `appearancePicture` varchar(255) NOT NULL,
  `appearanceDescription` longtext NOT NULL,
  `backstory` longtext NOT NULL,
  `personality` longtext NOT NULL,
  `code` varchar(7) NOT NULL,
  `isMinion` tinyint(1) NOT NULL,
  `notes` longtext
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Truncate table before insert `characters`
--

TRUNCATE TABLE `characters`;
-- --------------------------------------------------------

--
-- Table structure for table `includedStats`
--

DROP TABLE IF EXISTS `includedStats`;
CREATE TABLE IF NOT EXISTS `includedStats` (
`id` int(11) NOT NULL COMMENT 'This table is deprecated. Might get reworked if deemed nesesary, else will be removed.',
  `statSheetId` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `role` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Truncate table before insert `includedStats`
--

TRUNCATE TABLE `includedStats`;
-- --------------------------------------------------------

--
-- Table structure for table `modifiers`
--

DROP TABLE IF EXISTS `modifiers`;
CREATE TABLE IF NOT EXISTS `modifiers` (
`id` int(11) NOT NULL,
  `charId` int(11) NOT NULL,
  `statId` int(11) NOT NULL,
  `isBase` tinyint(1) NOT NULL DEFAULT '0',
  `name` varchar(255) NOT NULL,
  `value` int(11) NOT NULL,
  `countDown` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Truncate table before insert `modifiers`
--

TRUNCATE TABLE `modifiers`;
-- --------------------------------------------------------

--
-- Table structure for table `players`
--

DROP TABLE IF EXISTS `players`;
CREATE TABLE IF NOT EXISTS `players` (
`id` int(11) NOT NULL,
  `userId` varchar(255) NOT NULL,
  `rpId` int(11) NOT NULL,
  `is_GM` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Truncate table before insert `players`
--

TRUNCATE TABLE `players`;
-- --------------------------------------------------------

--
-- Table structure for table `rolePlays`
--

DROP TABLE IF EXISTS `rolePlays`;
CREATE TABLE IF NOT EXISTS `rolePlays` (
`id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `code` varchar(7) NOT NULL,
  `isPrivate` int(11) NOT NULL,
  `startingStatAmount` int(11) NOT NULL,
  `startingAbilityAmount` int(11) NOT NULL,
  `description` longtext NOT NULL,
  `creator` varchar(255) NOT NULL,
  `statSheetId` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Truncate table before insert `rolePlays`
--

TRUNCATE TABLE `rolePlays`;
-- --------------------------------------------------------

--
-- Table structure for table `statRoles`
--

DROP TABLE IF EXISTS `statRoles`;
CREATE TABLE IF NOT EXISTS `statRoles` (
  `id` int(11) NOT NULL,
  `role` varchar(255) NOT NULL,
  `description` longtext NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Truncate table before insert `statRoles`
--

TRUNCATE TABLE `statRoles`;
--
-- Dumping data for table `statRoles`
--

INSERT INTO `statRoles` (`id`, `role`, `description`) VALUES
(1, 'health', 'This stat is used to determine how much health a character has, a character is dead when it reaches 0. Normally it is not used during rolls.'),
(2, 'evade_defense', 'This stat is used to check if an attack or ability landed. This one is rolled by the defender.'),
(3, 'evade_attack', 'This stat is used to check if an attack or ability landed. This one is rolled by the attacker.'),
(4, 'physical_defense', 'This stat is used to check if an physical attack did damage, and how much. This stat is rolled by the defender.'),
(5, 'physical_attack', 'This stat is used to check if an physical attack did damage, and how much. This stat is rolled by the attacker.'),
(6, 'ability_defense', 'This stat is used to check if an ability worked, and how successful it was. This stat is rolled by the defender.'),
(7, 'ability_attack', 'This stat is used to check if an ability worked , and how well. This stat is rolled by the attacker.'),
(8, 'custom', 'This is a custom stat. This is currently not in use and use of it in statSheets is discouraged due to the application not knowing what it is for. ');

-- --------------------------------------------------------

--
-- Table structure for table `stats`
--

DROP TABLE IF EXISTS `stats`;
CREATE TABLE IF NOT EXISTS `stats` (
`id` int(11) NOT NULL COMMENT 'This table is deprecated. Might get reworked if deemed nesesary, else will be removed.',
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=latin1;

--
-- Truncate table before insert `stats`
--

TRUNCATE TABLE `stats`;
--
-- Dumping data for table `stats`
--

INSERT INTO `stats` (`id`, `name`) VALUES
(1, 'health'),
(2, 'armour'),
(3, 'strength'),
(4, 'accuracy'),
(5, 'magicalSkill'),
(6, 'magicalDefence'),
(7, 'agility');

-- --------------------------------------------------------

--
-- Table structure for table `statSheets`
--

DROP TABLE IF EXISTS `statSheets`;
CREATE TABLE IF NOT EXISTS `statSheets` (
`id` int(11) NOT NULL,
  `code` varchar(7) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` longtext
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

--
-- Truncate table before insert `statSheets`
--

TRUNCATE TABLE `statSheets`;
--
-- Dumping data for table `statSheets`
--

INSERT INTO `statSheets` (`id`, `code`, `name`, `description`) VALUES
(1, 'fantasy', 'Fantasy', 'These stats have names that work well for RP''s where magic is used.'),
(2, 'sci-fy1', 'Sci-FY ', 'These stats have names that work well in a sci-FY setting.');

-- --------------------------------------------------------

--
-- Table structure for table `statsInSheet`
--

DROP TABLE IF EXISTS `statsInSheet`;
CREATE TABLE IF NOT EXISTS `statsInSheet` (
`id` int(11) NOT NULL,
  `statSheetId` int(11) NOT NULL,
  `roleId` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` longtext NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=latin1;

--
-- Truncate table before insert `statsInSheet`
--

TRUNCATE TABLE `statsInSheet`;
--
-- Dumping data for table `statsInSheet`
--

INSERT INTO `statsInSheet` (`id`, `statSheetId`, `roleId`, `name`, `description`) VALUES
(1, 1, 1, 'Health', ''),
(2, 1, 2, 'Agility', ''),
(3, 1, 3, 'Accuracy', ''),
(4, 1, 4, 'Armour ', ''),
(5, 1, 5, 'Strength', ''),
(6, 1, 5, 'Magical defense', ''),
(7, 1, 6, 'Magic skill', ''),
(8, 2, 1, 'Health', ''),
(9, 2, 2, 'Agility', ''),
(10, 2, 3, 'Precision', ''),
(11, 2, 4, 'Durability', ''),
(12, 2, 5, 'Power', ''),
(13, 2, 6, 'Firewall', ''),
(14, 2, 7, 'Hack', '');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` varchar(255) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `hasActivated` tinyint(1) NOT NULL,
  `activationCode` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Truncate table before insert `users`
--

TRUNCATE TABLE `users`;
--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `email`, `hasActivated`, `activationCode`) VALUES
('68026ea182128245c9fe6aeb549578853db0a166', 'root', '$2y$10$rRhH8R1Bf2LAkssQKcAYdu3alGFOG79DQ5oXWI5B96xqEn3bqd6nm', '', 1, '');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `abilities`
--
ALTER TABLE `abilities`
 ADD PRIMARY KEY (`id`);

--
-- Indexes for table `characters`
--
ALTER TABLE `characters`
 ADD PRIMARY KEY (`id`);

--
-- Indexes for table `includedStats`
--
ALTER TABLE `includedStats`
 ADD PRIMARY KEY (`id`);

--
-- Indexes for table `modifiers`
--
ALTER TABLE `modifiers`
 ADD PRIMARY KEY (`id`);

--
-- Indexes for table `players`
--
ALTER TABLE `players`
 ADD PRIMARY KEY (`id`);

--
-- Indexes for table `rolePlays`
--
ALTER TABLE `rolePlays`
 ADD PRIMARY KEY (`id`);

--
-- Indexes for table `statRoles`
--
ALTER TABLE `statRoles`
 ADD PRIMARY KEY (`id`);

--
-- Indexes for table `stats`
--
ALTER TABLE `stats`
 ADD PRIMARY KEY (`id`);

--
-- Indexes for table `statSheets`
--
ALTER TABLE `statSheets`
 ADD PRIMARY KEY (`id`);

--
-- Indexes for table `statsInSheet`
--
ALTER TABLE `statsInSheet`
 ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
 ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `abilities`
--
ALTER TABLE `abilities`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `characters`
--
ALTER TABLE `characters`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `includedStats`
--
ALTER TABLE `includedStats`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'This table is deprecated. Might get reworked if deemed nesesary, else will be removed.';
--
-- AUTO_INCREMENT for table `modifiers`
--
ALTER TABLE `modifiers`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `players`
--
ALTER TABLE `players`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `rolePlays`
--
ALTER TABLE `rolePlays`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `stats`
--
ALTER TABLE `stats`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'This table is deprecated. Might get reworked if deemed nesesary, else will be removed.',AUTO_INCREMENT=8;
--
-- AUTO_INCREMENT for table `statSheets`
--
ALTER TABLE `statSheets`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `statsInSheet`
--
ALTER TABLE `statsInSheet`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=15;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
