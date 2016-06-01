-- phpMyAdmin SQL Dump
-- version 4.4.14
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Jun 02, 2016 at 01:00 AM
-- Server version: 5.6.26
-- PHP Version: 5.6.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_9ca911_kt`
--

-- --------------------------------------------------------

--
-- Table structure for table `real_update_users`
--

CREATE TABLE IF NOT EXISTS `real_update_users` (
  `first_name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `last_name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `type` varchar(5) COLLATE utf8_unicode_ci NOT NULL,
  `hash` varchar(75) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `real_update_users`
--

INSERT INTO `real_update_users` (`first_name`, `last_name`, `email`, `type`, `hash`) VALUES
('James', 'Bond', '007@mi6.en', 'guest', '$2y$10$wA3m4ykqvu15bQmsW9ST4egn5/sSlls4vJgcOFWdKTqrOb66QGvs6'),
('Admin', '.', 'admin@ru.ru', 'admin', '$2y$10$hs5w78sC2E3fWPG7aFncRuNEPzvySaY.BhmW3NlE47OydYCpOma8a'),
('Andy', 'Simo', 'asim@ru.ru', 'admin', '$2y$10$imsYoWm4JNFFwhmGgQiaLuaolXpWyFJalFGYvEQSzkyOBomNvOlz6'),
('Bruce', 'Wayne', 'b_Wayne@wayneenterprises.us', 'admin', '$2y$10$9s0TyqyE03rT3bBhNi4g9.Um640KSPTt1Z6eYbUHE8jxebvnnk1se'),
('Jamie', 'Carpenter', 'carpenter.jamie@dep19.en', 'guest', '$2y$10$4ipu60NXOs9cmWti6vpQv.dNu9W/e1vhLGPAMoF1G9jF1NQadNAyq'),
('Dari', 'Glucose Fructose', 'dglu@ru.ru', 'admin', '$2y$10$TdywLrMbXLEztXSfI2NSEeLevufPQtpxloC26ueYgDTia1xWxxEAi'),
('Edith', 'Cat-Ball', 'ecat@ru.ru', 'admin', '$2y$10$17jVhlWA4zLs33A7VTVz5uppRDRePfdDd7jjUJH487GSldzLUd.gC'),
('Dick', 'Grayson', 'grayson@jla.int', 'guest', '$2y$10$PN1xtk1kt84tR.C1SwZkoOkSIc9JLlGau8piGAffbj8fPQdUk11sG'),
('Guest', '.', 'guest@ru.ru', 'guest', '$2y$10$FO2pU1dgR.NvuT7B5RkhneDkGr4X4IA2LKnyvtO/IMYwFxXh0mu3G'),
('Roy', 'Harper', 'harper_roy@hotmail.com', 'guest', '$2y$10$Kia7B23/lPm93PYEzOPH1e/Hf5SPkcKI2pEWfCvJNYi9kNl7zsHO2'),
('Clark', 'Kent', 'kent.c@metronews.com', 'guest', '$2y$10$UkPP4AYRWDkxumrAde889OjjMty0KH6IVs.vQR5NsXmtrX5hOB6aO'),
('Kevin', 'Mai', 'kevin.mai_730@hotmail.com', 'admin', '$2y$10$8ittW85R9HCRDeDfTkvqH.bEEaLtKih/rDsaEgAMkkKnVmXtHpgpK'),
('Kenneth', 'Tsunami', 'ktsu@ru.ru', 'admin', '$2y$10$/zHEwGXkhF/hxDQ6nesWQeb.pseOUN9leiGJz26xpaxaS1GJu1zmO'),
('Jack', 'Layton', 'layton@gov.ca', 'guest', '$2y$10$tKZXU/2AE.lgRLCARd3Re.fBoNAxNHY/xK0PQom8XonS9oKrgFk7i'),
('Mingy', 'Stingy', 'mred@ru.ru', 'admin', '$2y$10$MLj/Ozzqk5aFZVFoSZrrMO9pWMCu.AE8diKoWZzj/A5ZVx017TiCW'),
('MrTickles', 'Laughs', 'mrtickles@gmail.com', 'guest', '$2y$10$SR/EqVjyxAZL3MWFIlQYM.WQoNSHlKl2CU310E6mUaAH2LdNmiZb.'),
('Oliver', 'Queen', 'o.queen@queenconsolidated.com', 'guest', '$2y$10$xLbEspPRGGs9.OJ1WYFgaOkgVCWpjojhl8VfQr/9WXDYJKAWQ2CeS'),
('Philly', 'Cheesesteak', 'ptra@ru.ru', 'admin', '$2y$10$Oh76zybNYIYMVPK/lxAOZOiwZJyhjIOGeuY5P0y6sFGs8ufQ/n/zG'),
('Irving', 'Ngo', 'theirvingngo@gmail.com', 'guest', '$2y$10$1R9cjSj4fu6dr9cPEzgLAOlyZ8GJwtrpK.xS.f/WxqctyfQEm1OD2'),
('Vic', 'Water', 'vwat2@rciann.com', 'admin', '$2y$10$4NIbO8Rq6FMZJLW6kkxCHOLvyUk2EDyhqNtyk7mu77Cxpx3G2Ed5C');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `real_update_users`
--
ALTER TABLE `real_update_users`
  ADD PRIMARY KEY (`email`),
  ADD UNIQUE KEY `email` (`email`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
