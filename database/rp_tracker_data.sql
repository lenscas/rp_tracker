-- phpMyAdmin SQL Dump
-- version 4.6.6deb4
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: May 13, 2018 at 05:34 PM
-- Server version: 10.1.26-MariaDB-0+deb9u1
-- PHP Version: 7.0.27-0+deb9u1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `rp_tracker`
--

--
-- Truncate table before insert `battleSystems`
--

TRUNCATE TABLE `battleSystems`;
--
-- Dumping data for table `battleSystems`
--

INSERT INTO `battleSystems` (`id`, `name`, `internalName`, `description`, `endFunction`) VALUES
(1, 'd10 fantasy', 'D10FAN', 'A simple d10 system.', '0'),
(2, 'AOD ', 'AOD', 'A custom battle system used by AOD.', 'battle:setTurnTo(\n    battle:getNextCharacter(\n        battle:getCurrentCharacter()\n    )\n)\n');

--
-- Truncate table before insert `defaultActions`
--

TRUNCATE TABLE `defaultActions`;
--
-- Dumping data for table `defaultActions`
--

INSERT INTO `defaultActions` (`id`, `battleSystemId`, `name`, `code`, `description`) VALUES
(1, 2, 'attack', 'battle:insertModifier({character=target,amount=-1,name=\"Damage\",countDown=-1,type=\"HP\"})', 'A basic attack. This checks if an attack will land and calculates how much damage it will do.'),
(2, 2, 'Accuracy check', '', 'This calculates if a given action will land its target. This is used for example when a character attacks to see if his blow will land.');

--
-- Truncate table before insert `defaultStats`
--

TRUNCATE TABLE `defaultStats`;
--
-- Dumping data for table `defaultStats`
--

INSERT INTO `defaultStats` (`id`, `battleSystemId`, `name`, `intName`, `description`) VALUES
(1, 2, 'Health', 'HP', 'How much life points a character has. When it reaches 0 the character dies.'),
(2, 2, 'Accuracy', 'ACC', 'Is used to check if an attack lands.'),
(3, 2, 'Evasion', 'EVA', 'Is used to check if an character dodges an attack.'),
(4, 2, 'Attack', 'ATK', 'Is used to check how much damage an attack does.'),
(5, 2, 'Defense', 'DEF', 'Is used to lower the amount of damage an incomming attack does.'),
(6, 2, 'Speed', 'SPD', 'Is used to move around and for the turn order.'),
(7, 2, 'Size', 'SZE', 'Is used to set the size of your character. Has influence over all stats!'),
(8, 2, 'Range', 'RAN', 'Is how far away the characters actions can travel.');

--
-- Truncate table before insert `helper_functions`
--

TRUNCATE TABLE `helper_functions`;
--
-- Dumping data for table `helper_functions`
--

INSERT INTO `helper_functions` (`id`, `battleSystemId`, `name`, `params`, `code`) VALUES
(1, 2, 'getTotalStats', 'character', '    print(character.code,\"is awesome\")\n    return character');

--
-- Truncate table before insert `stats`
--

TRUNCATE TABLE `stats`;
--
-- Dumping data for table `stats`
--

INSERT INTO `stats` (`id`, `rpId`, `name`, `internalName`, `description`) VALUES
(59, 1, 'Health', 'HP', 'How much life points a character has. When it reaches 0 the character dies.'),
(60, 1, 'Accuracy', 'ACC', 'Is used to check if an attack lands.'),
(61, 1, 'Evasion', 'EVA', 'Is used to check if an character dodges an attack.'),
(62, 1, 'Attack', 'ATK', 'Is used to check how much damage an attack does.'),
(63, 1, 'Defense', 'DEF', 'Is used to lower the amount of damage an incomming attack does.'),
(64, 1, 'Speed', 'SPD', 'Is used to move around and for the turn order.'),
(65, 1, 'Size', 'SZE', 'Is used to set the size of your character. Has influence over all stats!'),
(66, 1, 'Range', 'RAN', 'Is how far away the characters actions can travel.');

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
