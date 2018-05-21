-- phpMyAdmin SQL Dump
-- version 4.6.6deb4
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: May 21, 2018 at 07:39 PM
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
(1, 'd10 fantasy', 'D10FAN', 'A simple d10 system.', 'local curChar = battle:getCurrentCharacter()\nlocal nextChar = battle:getNextCharacter(curChar)\nbattle:removeTurnFromMods(nextChar)\nbattle:setTurnTo(nextChar)\n\n'),
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
(2, 2, 'Accuracy check', '', 'This calculates if a given action will land its target. This is used for example when a character attacks to see if his blow will land.'),
(3, 1, 'check', '	local userStats = battle:getTotalStatsOnChar(user)\n	local targetStats = battle:getTotalStatsOnChar(target)\n	local targetRoll = rolld10(targetStats.EVA or 0)\n	local userRoll   = rolld10(userStats.ACC or 0)\n	local success = userRoll > targetRoll\n	print(\"The user has an accuracy of\",userStats.ACC or 0)\n	print(\"The target has an evasion of\",targetStats.EVA or 0)\n	print(\"The user rolled\",userRoll)\n	print(\"The target rolled\",targetRol)\n	if success then\n    		print(\"The attack landed\")\n	else\n    		print(\"The attack missed\")\n	end\n	return success, userRoll,targetRoll', 'A simple check to see if an attack lands or misses.'),
(4, 1, 'attack_physcial', 'if not actions.check(user,target) then\n    return\nend\nlocal userStats = battle:getTotalStatsOnChar(user)\nlocal targetStats = battle:getTotalStatsOnChar(target)\nlocal targetRoll = rolld10(targetStats.ARM or 0) or 0\nlocal userRoll   = rolld10(userStats.STR or 0) or 0\nprint(\"The user has an strength of\",userStats.STR or 0)\nprint(\"The target has an armor of\",targetStats.ARM or 0)\nprint(\"The user rolled\",userRoll)\nprint(\"The target rolled\",targetRoll or 0)\nif targetRoll< userRoll then\n    print(\"The attack landed\")\n    local diff = targetRoll - userRoll\n    if diff <= 10 then\n         battle:insertModifier({character=target,amount=-1,name=\"Damage\",countDown=-1,type=\"HP\"})\n    elseif diff <=20 then\n         battle:insertModifier({character=target,amount=-2,name=\"Damage\",countDown=-1,type=\"HP\"})\n    elseif diff <=30 then\n         battle:insertModifier({character=target,amount=-3,name=\"Damage\",countDown=-1,type=\"HP\"})\n    end\nelse\n    print(\"The attack failed to do any damage\")\nend', 'A simple physical attack.'),
(5, 1, 'attack_magical', 'if not actions.check(user,target) then\r\n    return\r\nend\r\nlocal userStats = battle:getTotalStatsOnChar(user)\r\nlocal targetStats = battle:getTotalStatsOnChar(target)\r\nlocal targetRoll = rolld10(targetStats.MDF or 0) or 0\r\nlocal userRoll   = rolld10(userStats.MGC or 0) or 0\r\nprint(\"The user has a magic stat of\",userStats.MGC or 0)\r\nprint(\"The target has an magic defence of\",targetStats.MDF or 0)\r\nprint(\"The user rolled\",userRoll or 0)\r\nprint(\"The target rolled\",targetRoll or 0)\r\nif targetRoll< userRoll then\r\n    print(\"The attack landed\")\r\n    local diff = targetRoll - userRoll\r\n    if diff <= 10 then\r\n         battle:insertModifier({character=target,amount=-1,name=\"Damage\",countDown=-1,type=\"HP\"})\r\n    elseif diff <=20 then\r\n         battle:insertModifier({character=target,amount=-2,name=\"Damage\",countDown=-1,type=\"HP\"})\r\n    elseif diff <=30 then\r\n         battle:insertModifier({character=target,amount=-3,name=\"Damage\",countDown=-1,type=\"HP\"})\r\n    end\r\nelse\r\n    print(\"The attack failed to do any damage\")\r\nend', 'A simple magical attack.');

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
(8, 2, 'Range', 'RAN', 'Is how far away the characters actions can travel.'),
(9, 1, 'Health', 'HP', 'How many life points a character has. When this reaches 0 the character dies.'),
(10, 1, 'Accuracy', 'ACC', 'Is used to check if an attack lands.'),
(13, 1, 'Evasion', 'EVA', 'Is used to check if an character dodges an attack.'),
(14, 1, 'Strength', 'STR', 'Is used to check how much damage physical attacks do.'),
(15, 1, 'Armor', 'ARM', 'Is used to lower the amount of damage an incoming physical attack does.'),
(16, 1, 'Magic', 'MGC', 'Is used to check how much damage magical attacks do.'),
(17, 1, 'Magic defence', 'MDF', 'Is used to lower the amount of damage an incoming magical attack does.');

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
(67, 1, 'Health', 'HP', 'How much life points a character has. When it reaches 0 the character dies.'),
(68, 1, 'Accuracy', 'ACC', 'Is used to check if an attack lands.'),
(69, 1, 'Evasion', 'EVA', 'Is used to check if an character dodges an attack.'),
(70, 1, 'Attack', 'ATK', 'Is used to check how much damage an attack does.'),
(71, 1, 'Defense', 'DEF', 'Is used to lower the amount of damage an incomming attack does.'),
(72, 1, 'Speed', 'SPD', 'Is used to move around and for the turn order.'),
(73, 1, 'Size', 'SZE', 'Is used to set the size of your character. Has influence over all stats!'),
(74, 1, 'Range', 'RAN', 'Is how far away the characters actions can travel.'),
(75, 2, 'Health', 'HP', 'How many life points a character has. When this reaches 0 the character dies.'),
(76, 2, 'Accuracy', 'ACC', 'Is used to check if an attack lands.'),
(77, 2, 'Evasion', 'EVA', 'Is used to check if an character dodges an attack.'),
(78, 2, 'Strength', 'STR', 'Is used to check how much damage physical attacks do.'),
(79, 2, 'Armor', 'ARM', 'Is used to lower the amount of damage an incoming physical attack does.'),
(80, 2, 'Magic', 'MGC', 'Is used to check how much damage magical attacks do.'),
(81, 2, 'Magic defence', 'MDF', 'Is used to lower the amount of damage an incoming magical attack does.');

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
('07129a523ca9bd0892e223fc506203ab34973dae', 'lenscas', '$2y$10$tpC/OyEepDFHoVD2nIydUeMh7kh0JJdEF147f3eqs6dHo.cZ2lp9O', 'lenscas@localhost.com', 1, ''),
('68026ea182128245c9fe6aeb549578853db0a166', 'root', '$2y$10$rRhH8R1Bf2LAkssQKcAYdu3alGFOG79DQ5oXWI5B96xqEn3bqd6nm', '', 1, ''),
('someUserId', 'someUserName', 'somePassword', 'noEmail@localhost.com', 1, '');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
