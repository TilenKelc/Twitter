-- phpMyAdmin SQL Dump
-- version 4.8.5
-- https://www.phpmyadmin.net/
--
-- Gostitelj: 127.0.0.1
-- Čas nastanka: 01. okt 2019 ob 17.47
-- Različica strežnika: 10.1.39-MariaDB
-- Različica PHP: 7.3.5

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Zbirka podatkov: `twitter`
--

-- --------------------------------------------------------

--
-- Struktura tabele `friends`
--

CREATE TABLE `friends` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `state` varchar(50) COLLATE utf8_bin NOT NULL DEFAULT 'Following',
  `friend_id` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Odloži podatke za tabelo `friends`
--

INSERT INTO `friends` (`id`, `user_id`, `state`, `friend_id`) VALUES
(11, 8, 'Following', 3);

-- --------------------------------------------------------

--
-- Struktura tabele `replies`
--

CREATE TABLE `replies` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `reply` text COLLATE utf8_bin NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `tweet_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Odloži podatke za tabelo `replies`
--

INSERT INTO `replies` (`id`, `user_id`, `reply`, `date`, `tweet_id`) VALUES
(1, 8, 'asd', '2019-09-17 20:07:46', 1),
(2, 8, 'asd', '2019-09-17 20:07:49', 1),
(3, 8, 'Not much', '2019-09-26 10:48:24', 1),
(4, 8, 'N', '2019-09-26 12:30:33', 1),
(5, 8, 'What event', '2019-09-26 13:00:43', 2);

-- --------------------------------------------------------

--
-- Struktura tabele `tweets`
--

CREATE TABLE `tweets` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `picture` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `text` text COLLATE utf8_bin NOT NULL,
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `likes` int(11) NOT NULL DEFAULT '0',
  `like_id` int(11) DEFAULT NULL,
  `reported` int(1) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Odloži podatke za tabelo `tweets`
--

INSERT INTO `tweets` (`id`, `user_id`, `picture`, `text`, `time`, `likes`, `like_id`, `reported`) VALUES
(8, 8, NULL, 'sad', '2019-09-29 13:15:30', 0, NULL, 0),
(9, 8, NULL, 'asd', '2019-09-29 13:15:31', 0, NULL, 0),
(10, 8, NULL, 'asd', '2019-09-29 13:15:33', 0, NULL, 0),
(6, 8, NULL, 'asdasd', '2019-09-29 10:59:50', 0, NULL, 0),
(7, 8, NULL, 'b', '2019-09-29 12:09:14', 0, NULL, 0),
(11, 8, NULL, 'asd', '2019-09-29 13:15:34', 0, NULL, 0),
(12, 8, NULL, 'asd', '2019-09-29 13:15:39', 0, NULL, 0),
(13, 8, NULL, 'asdasda', '2019-09-29 13:15:43', 0, NULL, 0),
(14, 8, NULL, 'qweqwe', '2019-09-29 13:18:38', 0, NULL, 0),
(15, 8, NULL, 'asdasd', '2019-09-29 13:18:40', 0, NULL, 0),
(16, 10, NULL, 'nc', '2019-10-01 15:38:42', 0, NULL, 0);

-- --------------------------------------------------------

--
-- Struktura tabele `types`
--

CREATE TABLE `types` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_type` varchar(50) COLLATE utf8_bin NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Odloži podatke za tabelo `types`
--

INSERT INTO `types` (`id`, `user_type`) VALUES
(1, 'Administrator'),
(2, 'User');

-- --------------------------------------------------------

--
-- Struktura tabele `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `username` varchar(50) COLLATE utf8_bin NOT NULL,
  `joined` date NOT NULL,
  `bio` text COLLATE utf8_bin,
  `location` varchar(100) COLLATE utf8_bin DEFAULT NULL,
  `born` date DEFAULT NULL,
  `avatar` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `password` varchar(100) COLLATE utf8_bin NOT NULL,
  `email` varchar(100) COLLATE utf8_bin NOT NULL,
  `type_id` bigint(20) UNSIGNED NOT NULL DEFAULT '2'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Odloži podatke za tabelo `users`
--

INSERT INTO `users` (`id`, `username`, `joined`, `bio`, `location`, `born`, `avatar`, `password`, `email`, `type_id`) VALUES
(4, 'mts', '2019-09-12', NULL, NULL, NULL, NULL, '$2y$10$T76hOevhSyxy7vj22ANx0Op9NIC27Qp88DRo.m1wtDakHP4ODgy4e', 'tilen@gmail.com', 2),
(3, '123', '2019-09-10', NULL, NULL, NULL, NULL, '$2y$10$zYk4eYILDdGHpgeLojQZmu3bLWIRuLY5YCGZ506MF3G5PTP4QPyta', 'tilenkelc@gmail.com', 2),
(8, 'Tilen Kelc', '2019-09-15', '', '', '0000-00-00', NULL, 'google', 'tilen.kelc@gmail.com', 1),
(10, '123123', '2019-09-27', '', '', '0000-00-00', 'Zajeta slika.PNG', '$2y$10$/GcV0JCLq9aoEJK5pw1Us.CJ0DQ6r13ko7dMthf.koKSaUNmCwjG2', 'kelc@gmail.com', 2);

--
-- Indeksi zavrženih tabel
--

--
-- Indeksi tabele `friends`
--
ALTER TABLE `friends`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indeksi tabele `replies`
--
ALTER TABLE `replies`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`),
  ADD KEY `tweet_id` (`tweet_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indeksi tabele `tweets`
--
ALTER TABLE `tweets`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indeksi tabele `types`
--
ALTER TABLE `types`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`);

--
-- Indeksi tabele `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`),
  ADD KEY `type_id` (`type_id`);

--
-- AUTO_INCREMENT zavrženih tabel
--

--
-- AUTO_INCREMENT tabele `friends`
--
ALTER TABLE `friends`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT tabele `replies`
--
ALTER TABLE `replies`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT tabele `tweets`
--
ALTER TABLE `tweets`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT tabele `types`
--
ALTER TABLE `types`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT tabele `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
