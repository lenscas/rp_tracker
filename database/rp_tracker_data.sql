-- phpMyAdmin SQL Dump
-- version 4.2.12deb2+deb8u2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jan 08, 2017 at 07:33 PM
-- Server version: 5.5.53-0+deb8u1
-- PHP Version: 5.6.29-0+deb8u1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `rp_tracker`
--

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
(6, 1, 6, 'Magical defense', ''),
(7, 1, 7, 'Magic skill', ''),
(8, 2, 1, 'Health', ''),
(9, 2, 2, 'Agility', ''),
(10, 2, 3, 'Precision', ''),
(11, 2, 4, 'Durability', ''),
(12, 2, 5, 'Power', ''),
(13, 2, 6, 'Firewall', ''),
(14, 2, 7, 'Hack', '');

--
-- Truncate table before insert `tags`
--

TRUNCATE TABLE `tags`;
--
-- Dumping data for table `tags`
--

INSERT INTO `tags` (`id`, `name`, `class`, `specialRoles`) VALUES
(1, 'NPC', 'default', 'Hidden'),
(2, 'accepted', 'success', 'accepted'),
(3, 'minion', 'success', 'minion'),
(4, 'dead', 'danger', 'dead');

--
-- Truncate table before insert `users`
--

TRUNCATE TABLE `users`;
--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `email`, `hasActivated`, `activationCode`) VALUES
('68026ea182128245c9fe6aeb549578853db0a166', 'root', '$2y$10$rRhH8R1Bf2LAkssQKcAYdu3alGFOG79DQ5oXWI5B96xqEn3bqd6nm', '', 1, '');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
