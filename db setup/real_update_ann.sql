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
-- Table structure for table `real_update_ann`
--

CREATE TABLE IF NOT EXISTS `real_update_ann` (
  `id` int(4) NOT NULL,
  `title` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `content` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `type` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `author` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `start_datetime` datetime NOT NULL,
  `end_datetime` datetime NOT NULL,
  `slug` varchar(50) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=52 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `real_update_ann`
--

INSERT INTO `real_update_ann` (`id`, `title`, `content`, `type`, `author`, `start_datetime`, `end_datetime`, `slug`) VALUES
(42, 'Announcement Template', 'This is an announcement template', 'important', 'ktsu@ru.ru', '2016-11-28 00:00:00', '2016-11-28 09:00:00', 'announcement-template'),
(48, 'Badminton', 'There is a short meeting next period with Ms. Dempster. Please skip your class and come.', 'meeting', 'mred@ru.ru', '2015-04-16 12:30:00', '2015-04-22 13:45:00', 'badminton'),
(40, 'CANDY', 'CANDY', 'important', 'asim@ru.ru', '2015-03-31 10:30:00', '2016-08-31 18:30:00', 'candy'),
(51, 'Commencement', 'Farewell Class of 2015! Have a great summer and hopefully we''ll see you here back in October for your graduation ceremonies!', 'other', 'vwat2@rciann.com', '2015-06-26 00:00:00', '2015-06-30 23:45:00', 'commencement'),
(33, 'Computer Club', 'Come tomorrow Wednesday at lunch for a weekly meeting.', 'meeting', 'vwat2@rciann.com', '2013-04-02 15:00:00', '2014-03-12 21:00:00', 'computer-club'),
(45, 'Delete this announcement', 'Test settings, and delete this announcement!', 'meeting', 'theirvingngo@gmail.com', '2014-12-18 10:45:00', '2014-12-18 05:45:00', 'delete-this-announcement'),
(35, 'Engineering Team', 'Next Thursday, we''ll be having a yearbook photo taken. Remember to come to room 101 at lunch for the photo!', 'other', 'b_Wayne@wayneenterprises.us', '2015-04-02 11:45:00', '2015-08-31 16:30:00', 'engineering-team'),
(47, 'Engineering Team', 'Next week, we will be having a short meeting on robotics programming.', 'meeting', 'ptra@ru.ru', '2015-04-16 16:45:00', '2015-04-26 16:45:00', 'engineering-team-1'),
(39, 'Exams', 'Remember students! Exams are coming up, so remember to study and be prepared for all your exams!', 'important', 'layton@gov.ca', '1997-10-31 04:00:00', '2015-07-15 07:45:00', 'exams'),
(29, 'GAA & BAA BBQ', 'GAA & BAA is holding another BBQ at lunch today outside the cafe!', 'sports', 'ptra@ru.ru', '2012-06-21 03:15:00', '2012-08-31 23:00:00', 'gaa-baa-bbq'),
(50, 'HAVE SOME CAKE', 'CAKE FOR THE WIN', 'important', 'ecat@ru.ru', '2015-06-09 03:45:00', '2020-12-31 00:45:00', 'have-some-cake'),
(30, 'Leslieville Festival', 'Leslieville will be holding it''s annual festival and they''ll need some others!', 'other', 'mred@ru.ru', '2014-01-01 10:00:00', '2015-12-31 01:00:00', 'leslieville-festival'),
(41, 'Library club', 'come and eat donuts', 'meeting', 'mrtickles@gmail.com', '1996-09-02 06:15:00', '2017-06-28 18:15:00', 'library-club'),
(37, 'Ryerson Uni Visit', 'Ryerson will be in room 212 for a presentation today afterschool.', 'other', 'carpenter.jamie@dep19.en', '2015-01-01 09:00:00', '2015-01-02 18:45:00', 'ryerson-uni-visit'),
(43, 'Scheduling Ahead', 'Well, to make sure this system works well, we''ll allow scheduling ahead!', 'other', 'kent.c@metronews.com', '2016-11-06 23:30:00', '2020-12-31 00:00:00', 'scheduling-ahead'),
(28, 'SO IMPORTANT', 'THIS ANNOUNCEMENT IS SO IMPORTANT IT IS IN CAPS.', 'important', 'ktsu@ru.ru', '2003-08-11 13:00:00', '2003-08-15 02:00:00', 'so-important'),
(32, 'S.W.A.T.', 'Come by tomorrow afterschool in the library to join us for the 3D Priniting workshop that will be held!', 'other', 'ecat@ru.ru', '2014-05-25 18:15:00', '2014-08-31 22:15:00', 'swat'),
(46, 'Testing Symbols', 'Testing random characters:!@#$%^&*()_+-=[];''\\,./{}:"|<>?`~', 'important', 'ktsu@ru.ru', '2004-12-31 01:30:00', '2024-04-23 17:30:00', 'testing-symbols'),
(49, 'This is a test', 'Testing', 'other', 'dglu@ru.ru', '2017-01-01 00:00:00', '2020-12-31 00:00:00', 'this-is-a-test'),
(38, 'TPL Youth Advisory Group', 'The TPL''s YAG is looking for others to help out with community events and all. If interested visit the link http://google.com/', 'other', '007@mi6.en', '2014-08-02 06:15:00', '2014-11-30 22:30:00', 'tpl-youth-advisory-group'),
(34, 'Track & Field', 'Congrats to those who participated in track & field, for boys 100m dash, we were able to get 1st place gold medal!', 'sports', 'harper_roy@hotmail.com', '2008-01-01 16:30:00', '2009-11-06 23:30:00', 'track-field'),
(31, 'University Of Toronto', 'University Of Toronto will be visiting today at lunch in the auditorium, any students interested in their programs should go.', 'other', 'dglu@ru.ru', '2010-05-08 08:45:00', '2012-06-20 07:30:00', 'university-of-toronto'),
(44, 'Verify Announcements', 'Remember to verify your announcements or they will not be displayed!', 'important', 'kevin.mai_730@hotmail.com', '2014-11-28 00:15:00', '2015-07-24 00:15:00', 'verify-announcements'),
(36, 'X-Press Club', 'There is an important meeting today afterschool in room 319.', 'meeting', 'grayson@jla.int', '2003-02-28 12:45:00', '2008-05-14 17:00:00', 'x-press-club');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `real_update_ann`
--
ALTER TABLE `real_update_ann`
  ADD PRIMARY KEY (`slug`),
  ADD KEY `id` (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `real_update_ann`
--
ALTER TABLE `real_update_ann`
  MODIFY `id` int(4) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=52;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
