-- phpMyAdmin SQL Dump
-- version 4.6.6deb4
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Oct 06, 2017 at 10:14 PM
-- Server version: 10.1.26-MariaDB-0+deb9u1
-- PHP Version: 7.0.19-1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

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
CREATE TABLE `abilities` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `cooldown` int(11) NOT NULL,
  `description` longtext NOT NULL,
  `charId` int(11) NOT NULL,
  `countDown` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `battle`
--

DROP TABLE IF EXISTS `battle`;
CREATE TABLE `battle` (
  `id` int(11) NOT NULL,
  `rpId` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `link` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `characters`
--

DROP TABLE IF EXISTS `characters`;
CREATE TABLE `characters` (
  `id` int(11) NOT NULL,
  `playerId` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `age` int(11) NOT NULL,
  `appearancePicture` varchar(255) DEFAULT NULL,
  `appearanceDescription` longtext,
  `isLocalImage` tinyint(1) NOT NULL DEFAULT '0',
  `backstory` longtext NOT NULL,
  `personality` longtext NOT NULL,
  `code` varchar(7) NOT NULL,
  `notes` longtext,
  `hiddenData` longtext COMMENT 'Data here will normally not be displayed and can be used by third_party clients to store extra information about characters to allow combat systems that are currently not supported and need extra data.'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `charsInBattle`
--

DROP TABLE IF EXISTS `charsInBattle`;
CREATE TABLE `charsInBattle` (
  `id` int(11) NOT NULL,
  `charId` int(11) NOT NULL,
  `battleId` int(11) NOT NULL,
  `turnOrder` int(11) NOT NULL,
  `isTurn` tinyint(1) NOT NULL,
  `isRemoved` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `includedStats`
--

DROP TABLE IF EXISTS `includedStats`;
CREATE TABLE `includedStats` (
  `id` int(11) NOT NULL COMMENT 'This table is deprecated. Might get reworked if deemed nesesary, else will be removed.',
  `statSheetId` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `role` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `modifiers`
--

DROP TABLE IF EXISTS `modifiers`;
CREATE TABLE `modifiers` (
  `id` int(11) NOT NULL,
  `charId` int(11) NOT NULL,
  `statId` int(11) NOT NULL,
  `isBase` tinyint(1) NOT NULL DEFAULT '0',
  `name` varchar(255) NOT NULL,
  `value` int(11) NOT NULL,
  `countDown` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `players`
--

DROP TABLE IF EXISTS `players`;
CREATE TABLE `players` (
  `id` int(11) NOT NULL,
  `userId` varchar(255) NOT NULL,
  `rpId` int(11) NOT NULL,
  `is_GM` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `rolePlays`
--

DROP TABLE IF EXISTS `rolePlays`;
CREATE TABLE `rolePlays` (
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

-- --------------------------------------------------------

--
-- Table structure for table `socketRegisterQueue`
--

DROP TABLE IF EXISTS `socketRegisterQueue`;
CREATE TABLE `socketRegisterQueue` (
  `id` int(11) NOT NULL,
  `code` varchar(255) NOT NULL,
  `userId` varchar(255) NOT NULL,
  `time` int(11) NOT NULL,
  `notUsed` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `statRoles`
--

DROP TABLE IF EXISTS `statRoles`;
CREATE TABLE `statRoles` (
  `id` int(11) NOT NULL,
  `role` varchar(255) NOT NULL,
  `description` longtext NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `statSheets`
--

DROP TABLE IF EXISTS `statSheets`;
CREATE TABLE `statSheets` (
  `id` int(11) NOT NULL,
  `code` varchar(7) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` longtext
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `statsInSheet`
--

DROP TABLE IF EXISTS `statsInSheet`;
CREATE TABLE `statsInSheet` (
  `id` int(11) NOT NULL,
  `statSheetId` int(11) NOT NULL,
  `roleId` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` longtext NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `tags`
--

DROP TABLE IF EXISTS `tags`;
CREATE TABLE `tags` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL COMMENT 'the name of the tag (what get displayed)',
  `class` varchar(255) NOT NULL COMMENT 'the class that gets used for its collor',
  `specialRoles` varchar(255) NOT NULL COMMENT 'tags can change the behavior of how the program reacts to the character. This is used for that'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='this table contain all the possible tags characters can have';

-- --------------------------------------------------------

--
-- Table structure for table `tagsOnCharacters`
--

DROP TABLE IF EXISTS `tagsOnCharacters`;
CREATE TABLE `tagsOnCharacters` (
  `id` int(11) NOT NULL,
  `characterId` int(11) NOT NULL,
  `TagId` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` varchar(255) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `hasActivated` tinyint(1) NOT NULL,
  `activationCode` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `abilities`
--
ALTER TABLE `abilities`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `battle`
--
ALTER TABLE `battle`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `characters`
--
ALTER TABLE `characters`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`);

--
-- Indexes for table `charsInBattle`
--
ALTER TABLE `charsInBattle`
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
-- Indexes for table `socketRegisterQueue`
--
ALTER TABLE `socketRegisterQueue`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `statRoles`
--
ALTER TABLE `statRoles`
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
-- Indexes for table `tags`
--
ALTER TABLE `tags`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tagsOnCharacters`
--
ALTER TABLE `tagsOnCharacters`
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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;
--
-- AUTO_INCREMENT for table `battle`
--
ALTER TABLE `battle`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
--
-- AUTO_INCREMENT for table `characters`
--
ALTER TABLE `characters`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;
--
-- AUTO_INCREMENT for table `charsInBattle`
--
ALTER TABLE `charsInBattle`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;
--
-- AUTO_INCREMENT for table `includedStats`
--
ALTER TABLE `includedStats`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'This table is deprecated. Might get reworked if deemed nesesary, else will be removed.';
--
-- AUTO_INCREMENT for table `modifiers`
--
ALTER TABLE `modifiers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=74;
--
-- AUTO_INCREMENT for table `players`
--
ALTER TABLE `players`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;
--
-- AUTO_INCREMENT for table `rolePlays`
--
ALTER TABLE `rolePlays`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;
--
-- AUTO_INCREMENT for table `socketRegisterQueue`
--
ALTER TABLE `socketRegisterQueue`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=218;
--
-- AUTO_INCREMENT for table `statSheets`
--
ALTER TABLE `statSheets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `statsInSheet`
--
ALTER TABLE `statsInSheet`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;
--
-- AUTO_INCREMENT for table `tags`
--
ALTER TABLE `tags`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT for table `tagsOnCharacters`
--
ALTER TABLE `tagsOnCharacters`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
