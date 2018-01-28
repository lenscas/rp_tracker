-- phpMyAdmin SQL Dump
-- version 4.6.6deb4
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jan 28, 2018 at 01:26 PM
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

--
-- Truncate table before insert `battleSystems`
--

TRUNCATE TABLE `battleSystems`;
--
-- Dumping data for table `battleSystems`
--

INSERT INTO `battleSystems` (`id`, `name`, `internalName`, `description`) VALUES
(1, 'd10 fantasy', 'D10FAN', 'A simple d10 system.'),
(2, 'AOD ', 'AOD', 'A custom battle system used by AOD.');

--
-- Truncate table before insert `defaultActions`
--

TRUNCATE TABLE `defaultActions`;
--
-- Dumping data for table `defaultActions`
--

INSERT INTO `defaultActions` (`id`, `battleSystemId`, `name`, `code`, `description`) VALUES
(1, 2, 'attack', 'local accUser = getTotalStats(user,\"ACC\")\nlocal evaTarget = getTotalStats(target,\"EVA\")\nlocal hasEvaded = rollAccCheck(accUser,evaTarget)\nif hasEvaded then\n    local atkUser   = getTotalStats(user,\"ATK\")\n    local defTarget = getTotalStats(target,\"DEF\")\n    local damage    = rollDamage(atkUser,defTarget)\n    local damageMod = createDamageMod(damage,target)\n    table.insert(battle.characters[target.code],damageMod)\nend\nreturn battle', 'A basic attack. This checks if an attack will land and calculates how much damage it will do.'),
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
(1, 2, 'calcstats', 'character', '    print(character.code,\"is awesome\")\n    return character');

--
-- Truncate table before insert `stats`
--

TRUNCATE TABLE `stats`;
--
-- Dumping data for table `stats`
--

INSERT INTO `stats` (`id`, `rpId`, `name`, `internalName`, `description`) VALUES
(1, 1, '12', '12', '12'),
(2, 1, '13', '13', '13'),
(3, 4, 'Health', 'HP', 'How much life points a character has. When it reaches 0 the character dies.'),
(4, 4, 'Accuracy', 'ACC', 'Is used to check if an attack lands.'),
(5, 4, 'Evasion', 'EVA', 'Is used to check if an character dodges an attack.'),
(6, 4, 'Attack', 'ATK', 'Is used to check how much damage an attack does.'),
(7, 4, 'Defense', 'DEF', 'Is used to lower the amount of damage an incomming attack does.'),
(8, 4, 'Speed', 'SPD', 'Is used to move around and for the turn order.'),
(9, 4, 'Size', 'SZE', 'Is used to set the size of your character. Has influence over all stats!'),
(10, 4, 'Range', 'RAN', 'Is how far away the characters actions can travel.'),
(11, 5, 'Health', 'HP', 'How much life points a character has. When it reaches 0 the character dies.'),
(12, 5, 'Accuracy', 'ACC', 'Is used to check if an attack lands.'),
(13, 5, 'Evasion', 'EVA', 'Is used to check if an character dodges an attack.'),
(14, 5, 'Attack', 'ATK', 'Is used to check how much damage an attack does.'),
(15, 5, 'Defense', 'DEF', 'Is used to lower the amount of damage an incomming attack does.'),
(16, 5, 'Speed', 'SPD', 'Is used to move around and for the turn order.'),
(17, 5, 'Size', 'SZE', 'Is used to set the size of your character. Has influence over all stats!'),
(18, 5, 'Range', 'RAN', 'Is how far away the characters actions can travel.'),
(19, 6, 'Health', 'HP', 'How much life points a character has. When it reaches 0 the character dies.'),
(20, 6, 'Accuracy', 'ACC', 'Is used to check if an attack lands.'),
(21, 6, 'Evasion', 'EVA', 'Is used to check if an character dodges an attack.'),
(22, 6, 'Attack', 'ATK', 'Is used to check how much damage an attack does.'),
(23, 6, 'Defense', 'DEF', 'Is used to lower the amount of damage an incomming attack does.'),
(24, 6, 'Speed', 'SPD', 'Is used to move around and for the turn order.'),
(25, 6, 'Size', 'SZE', 'Is used to set the size of your character. Has influence over all stats!'),
(26, 6, 'Range', 'RAN', 'Is how far away the characters actions can travel.'),
(51, 11, 'Health', 'HP', 'How much life points a character has. When it reaches 0 the character dies.'),
(52, 11, 'Accuracy', 'ACC', 'Is used to check if an attack lands.'),
(53, 11, 'Evasion', 'EVA', 'Is used to check if an character dodges an attack.'),
(54, 11, 'Attack', 'ATK', 'Is used to check how much damage an attack does.'),
(55, 11, 'Defense', 'DEF', 'Is used to lower the amount of damage an incomming attack does.'),
(56, 11, 'Speed', 'SPD', 'Is used to move around and for the turn order.'),
(57, 11, 'Size', 'SZE', 'Is used to set the size of your character. Has influence over all stats!'),
(58, 11, 'Range', 'RAN', 'Is how far away the characters actions can travel.');

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
('6416334e76ea73290d5f160862454a26dad86189', 'rootvbladwad', '$2y$10$.M3qWXTi0m4HOYEshzeXHeYBmPA2qTJa3WO5bjCrpdKeoZlipNdYK', 'root@root.nl', 1, ''),
('68026ea182128245c9fe6aeb549578853db0a166', 'root', '$2y$10$rRhH8R1Bf2LAkssQKcAYdu3alGFOG79DQ5oXWI5B96xqEn3bqd6nm', '', 1, ''),
('c01359cde591957ef5efe184229ad86c28c2be2a', 'root1', '$2y$10$LpFj9j4bOTYPT6Lf8L5Ap.zg/W8JnDE2k1QSHBSv8bz5iidkBMnWO', 'root@root.com', 1, ''),
('dea40b9a9b160fe63506f9330e8c7b10e86c44ec', 'root2', '$2y$10$X8IgTOXhVtQoSfCLBIn.2ej09cwMgk/ryIl3IrGLpGFdB1C9sy/e6', 'lenscas@localhost.com', 0, 'LnosJTMImCkrpFZvhSHGqVUbzWXQcfeN'),
('ee047673d79c785801cce274dc285cfd026a4c0a', 'lenscas', '$2y$10$MFtER/Mc00K6qqJu8rY5O.NGzHWm9zsVFZwP11mUm.1jQ468RjaJK', 'lenscas@gmail.com', 1, '');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
