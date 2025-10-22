-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 22 Okt 2025 pada 19.12
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `bellonime`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(100) DEFAULT NULL,
  `role` enum('super_admin','admin') DEFAULT 'admin',
  `last_login` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `admins`
--

INSERT INTO `admins` (`id`, `username`, `email`, `password`, `full_name`, `role`, `last_login`, `created_at`, `updated_at`) VALUES
(1, 'admin', 'admin@bellonime.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrator', 'super_admin', '2025-10-22 12:22:44', '2025-10-21 13:17:28', '2025-10-22 12:22:44');

-- --------------------------------------------------------

--
-- Struktur dari tabel `animes`
--

CREATE TABLE `animes` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `synopsis` text DEFAULT NULL,
  `poster` varchar(255) DEFAULT NULL,
  `background` varchar(255) DEFAULT NULL,
  `type` enum('TV','Movie','OVA','ONA','Special') DEFAULT 'TV',
  `status` enum('Ongoing','Complete','Upcoming') DEFAULT 'Ongoing',
  `studio` varchar(100) DEFAULT NULL,
  `total_episodes` int(11) DEFAULT 0,
  `duration` int(11) DEFAULT 0,
  `rating` decimal(3,2) DEFAULT 0.00,
  `year` int(11) DEFAULT NULL,
  `season` enum('Spring','Summer','Fall','Winter') DEFAULT NULL,
  `views` int(11) DEFAULT 0,
  `featured` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `animes`
--

INSERT INTO `animes` (`id`, `title`, `slug`, `synopsis`, `poster`, `background`, `type`, `status`, `studio`, `total_episodes`, `duration`, `rating`, `year`, `season`, `views`, `featured`, `created_at`, `updated_at`) VALUES
(2, 'One Piece', 'one-piece', '', 'https://cdn.myanimelist.net/images/anime/1810/139965.jpg', 'https://cdn.myanimelist.net/images/anime/1810/139965.jpg', 'TV', 'Ongoing', '', 1000000, 2, 9.00, 1999, 'Spring', 2, 1, '2025-10-22 13:27:43', '2025-10-22 15:54:14'),
(3, 'One Punch Man 3', 'one-punch-man-3', '', 'https://cdn.myanimelist.net/images/anime/1885/127108.jpg', 'https://cdn.myanimelist.net/images/anime/1885/127108.jpg', 'TV', 'Ongoing', '', 0, 24, 7.00, 2025, 'Spring', 2, 0, '2025-10-22 13:34:44', '2025-10-22 15:53:29'),
(4, 'Ansatsusha de Aru Ore no Status ga Yuusha yori mo Akiraka ni Tsuyoi no da ga', 'ansatsusha-de-aru-ore-no-status-ga-yuusha-yori-mo-akiraka-ni-tsuyoi-no-da-ga', '', 'https://cdn.myanimelist.net/images/anime/1029/148034.jpg', '', 'TV', 'Ongoing', '', 0, 24, 7.30, 2025, '', 3, 0, '2025-10-22 13:48:59', '2025-10-22 15:52:50'),
(5, 'Chitose-kun wa Ramune Bin no Naka', 'chitose-kun-wa-ramune-bin-no-naka', '', 'https://cdn.myanimelist.net/images/anime/1015/151233.jpg', 'https://cdn.myanimelist.net/images/anime/1925/152152.jpg', 'TV', 'Ongoing', '', 0, 0, 7.30, 2025, '', 3, 0, '2025-10-22 13:57:47', '2025-10-22 16:03:59'),
(6, 'Tondemo Skill de Isekai Hourou Meshi 2', 'tondemo-skill-de-isekai-hourou-meshi-2', '', 'https://cdn.myanimelist.net/images/anime/1778/152192.jpg', 'https://cdn.myanimelist.net/images/anime/1778/152192.jpg', 'TV', 'Ongoing', '', 0, 0, 7.70, 2025, '', 7, 0, '2025-10-22 14:16:39', '2025-10-22 16:03:48'),
(7, 'Overlord Movie 3: Sei Oukoku-hen', 'overlord-movie-3-sei-oukoku-hen', '', 'https://cdn.myanimelist.net/images/anime/1954/144101.jpg', 'https://cdn.myanimelist.net/images/anime/1954/144101.jpg', 'Movie', 'Complete', '', 1, 120, 9.00, 2025, 'Spring', 1, 1, '2025-10-22 14:28:11', '2025-10-22 16:52:22');

-- --------------------------------------------------------

--
-- Struktur dari tabel `anime_genre`
--

CREATE TABLE `anime_genre` (
  `anime_id` int(11) NOT NULL,
  `genre_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `anime_genre`
--

INSERT INTO `anime_genre` (`anime_id`, `genre_id`) VALUES
(2, 1),
(2, 2),
(2, 3),
(2, 4),
(2, 5),
(2, 6),
(2, 7),
(2, 8),
(2, 9),
(2, 10),
(2, 11),
(2, 12),
(2, 13),
(2, 14),
(3, 1),
(5, 3),
(5, 8),
(6, 1),
(6, 2),
(6, 3),
(6, 4),
(6, 5),
(6, 6),
(6, 7),
(6, 8),
(6, 9),
(6, 10),
(6, 11),
(6, 12),
(6, 13),
(6, 14),
(7, 1),
(7, 2),
(7, 3),
(7, 4),
(7, 5),
(7, 6),
(7, 7),
(7, 8),
(7, 9),
(7, 10),
(7, 11),
(7, 12),
(7, 13),
(7, 14);

-- --------------------------------------------------------

--
-- Struktur dari tabel `comments`
--

CREATE TABLE `comments` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `anime_id` int(11) NOT NULL,
  `episode_id` int(11) DEFAULT NULL,
  `content` text NOT NULL,
  `status` enum('approved','pending','rejected') DEFAULT 'approved',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `episodes`
--

CREATE TABLE `episodes` (
  `id` int(11) NOT NULL,
  `anime_id` int(11) NOT NULL,
  `episode_number` int(11) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `slug` varchar(255) NOT NULL,
  `video_url` varchar(500) DEFAULT NULL,
  `video_embed` text DEFAULT NULL,
  `duration` int(11) DEFAULT 0,
  `views` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `episodes`
--

INSERT INTO `episodes` (`id`, `anime_id`, `episode_number`, `title`, `slug`, `video_url`, `video_embed`, `duration`, `views`, `created_at`, `updated_at`) VALUES
(3, 2, 1146, 'One Piece - Episode 1146', 'one-piece-episode-1146-ep-1146', '', 'https://api.wibufile.com/embed/49831995-2a61-4254-8353-7c31339b30a2', 24, 5, '2025-10-22 13:29:26', '2025-10-22 14:30:50'),
(4, 3, 1, 'One Punch Man - 1', 'one-punch-man-1-ep-1', 'https://s0.wibufile.com/video01/OPM-S3-1-FULLHD-SAMEHADAKU.CARE.mp4', '', 24, 3, '2025-10-22 13:41:15', '2025-10-22 13:44:25'),
(5, 3, 2, 'One Punch Man - 2', 'one-punch-man-2-ep-2', 'https://s0.wibufile.com/video01/OPM-S3-2-FULLHD-SAMEHADAKU.CARE.mp4', '', 0, 2, '2025-10-22 13:44:21', '2025-10-22 13:49:55'),
(6, 4, 1, 'Ansatsusha de Aru Ore no Status ga Yuusha yori mo Akiraka ni Tsuyoi no da ga - 1', 'ansatsusha-de-aru-ore-no-status-ga-yuusha-yori-mo-akiraka-ni-tsuyoi-no-da-ga-1-ep-1', 'https://s0.wibufile.com/video01/Sutetsuyo-01-FULLHD-SAMEHADAKU.CARE.mp4', '', 0, 2, '2025-10-22 13:50:32', '2025-10-22 13:55:19'),
(7, 4, 2, 'Ansatsusha de Aru Ore no Status ga Yuusha yori mo Akiraka ni Tsuyoi no da ga - 2', 'ansatsusha-de-aru-ore-no-status-ga-yuusha-yori-mo-akiraka-ni-tsuyoi-no-da-ga-2-ep-2', 'https://s0.wibufile.com/video01/Sutetsuyo-02-FULLHD-SAMEHADAKU.CARE.mp4', '', 0, 4, '2025-10-22 13:53:40', '2025-10-22 13:55:15'),
(8, 5, 1, 'Chitose-kun wa Ramune Bin no Naka - 1', 'chitose-kun-wa-ramune-bin-no-naka-1-ep-1', 'https://s0.wibufile.com/video01/Chiramune-01-FULLHD-SAMEHADAKU.CARE.mp4', '', 0, 3, '2025-10-22 14:10:54', '2025-10-22 16:05:49'),
(9, 5, 2, 'Chitose-kun wa Ramune Bin no Naka - Episode 2', 'chitose-kun-wa-ramune-bin-no-naka-episode-2-ep-2', 'https://s0.wibufile.com/video01/Chiramune-02-FULLHD-SAMEHADAKU.CARE.mp4', '', 0, 1, '2025-10-22 14:11:31', '2025-10-22 16:52:09'),
(10, 5, 3, 'Chitose-kun wa Ramune Bin no Naka - Episode 3', 'chitose-kun-wa-ramune-bin-no-naka-episode-3-ep-3', 'https://s0.wibufile.com/video01/Chiramune-03-FULLHD-SAMEHADAKU.CARE.mp4', '', 0, 2, '2025-10-22 14:13:32', '2025-10-22 15:51:37'),
(11, 6, 1, 'Tondemo Skill de Isekai Hourou Meshi 2 - Episode 1', 'tondemo-skill-de-isekai-hourou-meshi-2-episode-1-ep-1', 'https://s0.wibufile.com/video01/TondemoSkill-S2-01-FULLHD-SAMEHADAKU.CARE.mp4', '', 0, 2, '2025-10-22 14:17:43', '2025-10-22 15:57:32'),
(12, 6, 2, 'Tondemo Skill de Isekai Hourou Meshi 2 - Episode 2', 'tondemo-skill-de-isekai-hourou-meshi-2-ep-2', 'https://s0.wibufile.com/video01/TondemoSkill-S2-02-FULLHD-SAMEHADAKU.CARE.mp4', '', 0, 1, '2025-10-22 14:21:22', '2025-10-22 14:30:48'),
(13, 7, 9, 'Overlord Movie 3: Sei Oukoku-hen - Episode 9', 'overlord-movie-3-sei-oukoku-hen-ep-9', '', 'https://pixeldrain.com/u/zp1ZVVap?embed&style=solarized_dark', 120, 21, '2025-10-22 14:28:35', '2025-10-22 16:52:25');

-- --------------------------------------------------------

--
-- Struktur dari tabel `genres`
--

CREATE TABLE `genres` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `slug` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `genres`
--

INSERT INTO `genres` (`id`, `name`, `slug`, `description`, `created_at`) VALUES
(1, 'Action', 'action', 'Anime dengan adegan pertarungan dan aksi yang intens', '2025-10-21 13:17:28'),
(2, 'Adventure', 'adventure', 'Anime dengan petualangan ke tempat baru', '2025-10-21 13:17:28'),
(3, 'Comedy', 'comedy', 'Anime yang lucu dan menghibur', '2025-10-21 13:17:28'),
(4, 'Drama', 'drama', 'Anime dengan cerita yang emosional', '2025-10-21 13:17:28'),
(5, 'Fantasy', 'fantasy', 'Anime dengan elemen magis dan dunia fantasi', '2025-10-21 13:17:28'),
(6, 'Horror', 'horror', 'Anime yang menakutkan', '2025-10-21 13:17:28'),
(7, 'Mystery', 'mystery', 'Anime dengan teka-teki yang harus dipecahkan', '2025-10-21 13:17:28'),
(8, 'Romance', 'romance', 'Anime dengan cerita cinta', '2025-10-21 13:17:28'),
(9, 'Sci-Fi', 'sci-fi', 'Anime dengan tema sains dan teknologi', '2025-10-21 13:17:28'),
(10, 'Slice of Life', 'slice-of-life', 'Anime dengan kehidupan sehari-hari', '2025-10-21 13:17:28'),
(11, 'Sports', 'sports', 'Anime tentang olahraga', '2025-10-21 13:17:28'),
(12, 'Supernatural', 'supernatural', 'Anime dengan kekuatan supernatural', '2025-10-21 13:17:28'),
(13, 'Thriller', 'thriller', 'Anime yang tegang dan mendebarkan', '2025-10-21 13:17:28'),
(14, 'Isekai', 'isekai', 'Anime dengan tema dunia lain', '2025-10-21 13:17:28');

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(100) DEFAULT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  `status` enum('active','inactive','banned') DEFAULT 'active',
  `email_verified` tinyint(1) DEFAULT 0,
  `last_login` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `watchlist`
--

CREATE TABLE `watchlist` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `anime_id` int(11) NOT NULL,
  `status` enum('watching','completed','on_hold','dropped','plan_to_watch') DEFAULT 'plan_to_watch',
  `episodes_watched` int(11) DEFAULT 0,
  `rating` decimal(3,2) DEFAULT 0.00,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indeks untuk tabel `animes`
--
ALTER TABLE `animes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `idx_animes_title` (`title`),
  ADD KEY `idx_animes_slug` (`slug`),
  ADD KEY `idx_animes_status` (`status`),
  ADD KEY `idx_animes_featured` (`featured`);

--
-- Indeks untuk tabel `anime_genre`
--
ALTER TABLE `anime_genre`
  ADD PRIMARY KEY (`anime_id`,`genre_id`),
  ADD KEY `genre_id` (`genre_id`);

--
-- Indeks untuk tabel `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `idx_comments_anime_id` (`anime_id`),
  ADD KEY `idx_comments_episode_id` (`episode_id`);

--
-- Indeks untuk tabel `episodes`
--
ALTER TABLE `episodes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_episode` (`anime_id`,`episode_number`),
  ADD KEY `idx_episodes_anime_id` (`anime_id`),
  ADD KEY `idx_episodes_slug` (`slug`);

--
-- Indeks untuk tabel `genres`
--
ALTER TABLE `genres`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_users_username` (`username`),
  ADD KEY `idx_users_email` (`email`);

--
-- Indeks untuk tabel `watchlist`
--
ALTER TABLE `watchlist`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_watchlist` (`user_id`,`anime_id`),
  ADD KEY `anime_id` (`anime_id`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `animes`
--
ALTER TABLE `animes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT untuk tabel `comments`
--
ALTER TABLE `comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `episodes`
--
ALTER TABLE `episodes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT untuk tabel `genres`
--
ALTER TABLE `genres`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `watchlist`
--
ALTER TABLE `watchlist`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `anime_genre`
--
ALTER TABLE `anime_genre`
  ADD CONSTRAINT `anime_genre_ibfk_1` FOREIGN KEY (`anime_id`) REFERENCES `animes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `anime_genre_ibfk_2` FOREIGN KEY (`genre_id`) REFERENCES `genres` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `comments_ibfk_2` FOREIGN KEY (`anime_id`) REFERENCES `animes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `comments_ibfk_3` FOREIGN KEY (`episode_id`) REFERENCES `episodes` (`id`) ON DELETE SET NULL;

--
-- Ketidakleluasaan untuk tabel `episodes`
--
ALTER TABLE `episodes`
  ADD CONSTRAINT `episodes_ibfk_1` FOREIGN KEY (`anime_id`) REFERENCES `animes` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `watchlist`
--
ALTER TABLE `watchlist`
  ADD CONSTRAINT `watchlist_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `watchlist_ibfk_2` FOREIGN KEY (`anime_id`) REFERENCES `animes` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
