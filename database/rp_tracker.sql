-- phpMyAdmin SQL Dump
-- version 4.2.12deb2+deb8u1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: May 23, 2016 at 05:15 PM
-- Server version: 5.5.49-0+deb8u1
-- PHP Version: 5.6.20-0+deb8u1

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
  `health` int(11) NOT NULL,
  `armour` int(11) NOT NULL,
  `strength` int(11) NOT NULL,
  `accuracy` int(11) NOT NULL,
  `magicalSkill` int(11) NOT NULL,
  `magicalDefence` int(11) NOT NULL,
  `peronality` longtext NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

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
  `description` longtext NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=latin1;

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
-- Indexes for dumped tables
--

--
-- Indexes for table `characters`
--
ALTER TABLE `characters`
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
-- Indexes for table `users`
--
ALTER TABLE `users`
 ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `characters`
--
ALTER TABLE `characters`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `players`
--
ALTER TABLE `players`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `rolePlays`
--
ALTER TABLE `rolePlays`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=11;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
