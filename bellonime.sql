-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- Host: sql103.infinityfree.com
-- Waktu pembuatan: 26 Okt 2025 pada 07.23
-- Versi server: 11.4.7-MariaDB
-- Versi PHP: 7.2.22

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `if0_39451613_bellonime`
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
(1, 'admin', 'admin@bellonime.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrator', 'super_admin', '2025-10-26 07:37:42', '2025-10-21 13:17:28', '2025-10-26 07:37:42'),
(2, 'admin1', 'admin1@bellonime.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrator', 'admin', '2025-10-26 09:40:16', '2025-10-21 13:17:28', '2025-10-26 09:40:16');

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
(2, 'One Piece', 'one-piece', '', 'https://cdn.myanimelist.net/images/anime/1810/139965.jpg', 'https://cdn.myanimelist.net/images/anime/1810/139965.jpg', 'TV', 'Ongoing', '', 1000000, 2, '9.00', 1999, 'Spring', 3, 1, '2025-10-22 13:27:43', '2025-10-26 07:37:03'),
(3, 'One Punch Man 3', 'one-punch-man-3', '', 'https://cdn.myanimelist.net/images/anime/1885/127108.jpg', 'https://cdn.myanimelist.net/images/anime/1885/127108.jpg', 'TV', 'Ongoing', '', 0, 24, '7.00', 2025, 'Spring', 3, 0, '2025-10-22 13:34:44', '2025-10-26 09:27:47'),
(4, 'Ansatsusha de Aru Ore no Status ga Yuusha yori mo Akiraka ni Tsuyoi no da ga', 'ansatsusha-de-aru-ore-no-status-ga-yuusha-yori-mo-akiraka-ni-tsuyoi-no-da-ga', '', 'https://cdn.myanimelist.net/images/anime/1029/148034.jpg', '', 'TV', 'Ongoing', '', 0, 24, '7.30', 2025, '', 3, 0, '2025-10-22 13:48:59', '2025-10-22 15:52:50'),
(6, 'Tondemo Skill de Isekai Hourou Meshi 2', 'tondemo-skill-de-isekai-hourou-meshi-2', '', 'https://cdn.myanimelist.net/images/anime/1778/152192.jpg', 'https://cdn.myanimelist.net/images/anime/1778/152192.jpg', 'TV', 'Ongoing', '', 0, 0, '7.70', 2025, '', 9, 0, '2025-10-22 14:16:39', '2025-10-26 06:38:07'),
(7, 'Overlord Movie 3: Sei Oukoku-hen', 'overlord-movie-3-sei-oukoku-hen', '', 'https://cdn.myanimelist.net/images/anime/1954/144101.jpg', 'https://cdn.myanimelist.net/images/anime/1954/144101.jpg', 'Movie', 'Complete', '', 1, 120, '9.00', 2025, 'Spring', 2, 1, '2025-10-22 14:28:11', '2025-10-26 09:27:40'),
(8, 'Mugen Gacha', 'mugen-gacha', 'Ketika Light diusir dari Persatuan Suku, mantan rekan-rekannya langsung menyerangnya. Light lolos dari pengkhianatan keji ini dengan susah payah…hanya untuk mendapati dirinya berada di bagian terdalam Naraku, Dungeon paling berbahaya di dunia ini! Agar tidak dimakan monster karnivora, ia menggunakan Gacha Tak Terbatas, satu-satunya keahlian sihirnya. Namun, yang sebelumnya hanya menghasilkan barang-barang rongsokan, kali ini Mei—seorang petarung cantik Level 9999 dalam balutan pakaian pelayan—muncul! Tiga tahun kemudian, Light telah membangun kerajaannya sendiri di ruang bawah tanah terpencil ini, memanggil lebih banyak prajurit cantik Level 9999 yang mengabdi padanya. Kini, sebagai Penguasa Tertinggi Level 9999 yang kuat, Light berencana untuk naik ke permukaan dan membalas dendam pada para pengkhianatnya satu per satu!', 'https://cdn.myanimelist.net/images/anime/1163/151246.jpg', '', 'TV', 'Ongoing', '', 0, 24, '6.70', 2025, 'Fall', 0, 1, '2025-10-26 05:24:17', '2025-10-26 05:24:17'),
(9, 'Chitose-kun wa Ramune Bin no Naka', 'chitose-kun-wa-ramune-bin-no-naka', 'Hinaan yang ditujukan pada Chitose Saku tak terhitung banyaknya. Dijuluki “si playboy brengsek kelas 5,” ia terus-menerus dihina secara online oleh mereka yang iri pada popularitasnya. Beruntung, hal itu hampir tidak memengaruhi kepercayaan diri Saku. Di luar forum internet sempit, ia adalah figur yang dihormati oleh teman-teman sekelasnya dan siswa teladan yang dipercaya oleh guru-gurunya.Saat tahun kedua SMA-nya dimulai, Saku bertemu kembali dengan teman-teman lamanya dan berteman dengan yang baru, semua berasal dari kalangan elit sosial sekolah. Tapi, kesenangan itu terhenti saat guru kelasnya menyuruh Saku meyakinkan teman sekelasnya yang tertutup, Yamazaki Kenta, untuk kembali ke sekolah. Ingin menyelesaikan masalah dengan cepat, Saku mengunjungi rumah Kenta keesokan harinya. Tapi tugas itu ternyata lebih sulit dari yang ia duga ketika ia menemukan bahwa Kenta benar-benar membencinya. Kalau ia ingin menjaga citra sempurna, Saku harus menemukan cara untuk memenangkan hati Kenta dan membawanya kembali ke sekolah.', 'https://v1.samehadaku.how/wp-content/uploads/2025/10/Chitose-kun-wa-Ramune-Bin-no-Naka.jpg', 'https://v1.samehadaku.how/wp-content/uploads/2025/10/Chitose-kun-wa-Ramune-Bin-no-Naka.jpg', 'TV', 'Ongoing', '', 0, 24, '7.30', 2025, 'Fall', 0, 1, '2025-10-26 05:29:40', '2025-10-26 06:37:08'),
(10, 'Mikata Ga Yowasugite Hojo Mahou', 'mikata-ga-yowasugite-hojo-mahou', '“Tim ini tidak membutuhkan penyihir tidak kompeten yang hanya bisa menggunakan sihir pendukung. Kau dipecat, Alec Ygret.”Tiba-tiba, Alec — seorang penyihir istana yang bergabung dengan kelompok pangeran mahkota untuk membantunya menaklukkan Dungeon — diusir dari tim tersebut. Bukan hanya dari tim, namun karena penindasan dari sang pangeran mahkota, Alec juga diusir dari istana kerajaan. Saat ia berada di ujung keputusasaan, seorang teman dari Akademi Sihir menghampirinya.“Hai, Alec. Apakah kau ingin mencoba menaklukkan Dungeon bersama kami lagi?”Dengan demikian, bersama teman-teman lamanya yang dulu pernah berpetualang bersamanya, Alec memulai perjalanan keduanya dalam hidup. Inilah kisah petualangan seorang mantan Penyihir Istana yang telah ditinggalkan.Empat tahun lalu, Lasting Period — sebuah kelompok beranggotakan empat orang yang dulu disebut sebagai “legendaris” — perlahan mulai menyebarkan namanya ke seluruh dunia.', 'https://v1.samehadaku.how/wp-content/uploads/2025/10/Mikata-ga-Yowasugite-Hojo-Mahou.jpg', 'https://v1.samehadaku.how/wp-content/uploads/2025/10/Mikata-ga-Yowasugite-Hojo-Mahou.jpg', 'TV', 'Ongoing', '', 0, 24, '6.30', 2025, 'Fall', 0, 0, '2025-10-26 08:56:32', '2025-10-26 08:56:32');

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
(3, 1),
(6, 1),
(7, 1),
(8, 1),
(10, 1),
(2, 2),
(6, 2),
(7, 2),
(10, 2),
(2, 3),
(6, 3),
(7, 3),
(9, 3),
(2, 4),
(6, 4),
(7, 4),
(2, 5),
(6, 5),
(7, 5),
(8, 5),
(10, 5),
(2, 6),
(6, 6),
(7, 6),
(2, 7),
(6, 7),
(7, 7),
(2, 8),
(6, 8),
(7, 8),
(9, 8),
(2, 9),
(6, 9),
(7, 9),
(2, 10),
(6, 10),
(7, 10),
(2, 11),
(6, 11),
(7, 11),
(2, 12),
(6, 12),
(7, 12),
(2, 13),
(6, 13),
(7, 13),
(2, 14),
(6, 14),
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

--
-- Dumping data untuk tabel `comments`
--

INSERT INTO `comments` (`id`, `user_id`, `anime_id`, `episode_id`, `content`, `status`, `created_at`, `updated_at`) VALUES
(1, NULL, 2, 3, 'mantab slurr', 'approved', '2025-10-25 09:43:26', '2025-10-25 09:43:26'),
(2, NULL, 2, 3, 'gege', 'approved', '2025-10-25 09:43:33', '2025-10-25 09:43:33'),
(3, NULL, 3, 21, 'BOTAK KEREN!!', 'approved', '2025-10-26 09:28:25', '2025-10-26 09:28:25');

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
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `video_480_url` varchar(500) DEFAULT NULL,
  `video_720_url` varchar(500) DEFAULT NULL,
  `video_1080_url` varchar(500) DEFAULT NULL,
  `dl_480_url` varchar(500) DEFAULT NULL,
  `dl_720_url` varchar(500) DEFAULT NULL,
  `dl_1080_url` varchar(500) DEFAULT NULL,
  `embed_480_url` varchar(500) DEFAULT NULL,
  `embed_720_url` varchar(500) DEFAULT NULL,
  `embed_1080_url` varchar(500) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `episodes`
--

INSERT INTO `episodes` (`id`, `anime_id`, `episode_number`, `title`, `slug`, `video_url`, `video_embed`, `duration`, `views`, `created_at`, `updated_at`, `video_480_url`, `video_720_url`, `video_1080_url`, `dl_480_url`, `dl_720_url`, `dl_1080_url`, `embed_480_url`, `embed_720_url`, `embed_1080_url`) VALUES
(3, 2, 1146, 'One Piece - Episode 1146', 'one-piece-ep-1146', '', 'https://api.wibufile.com/embed/49831995-2a61-4254-8353-7c31339b30a2', 24, 16, '2025-10-22 13:29:26', '2025-10-26 07:37:07', 'https://xshotcok.com/embed-89hm8yd0kbbg.html', '', '', '', '', '', 'https://xshotcok.com/embed-89hm8yd0kbbg.html', 'https://xshotcok.com/embed-89hm8yd0kbbg.html', 'https://xshotcok.com/embed-89hm8yd0kbbg.html'),
(4, 3, 1, 'One Punch Man 3 - Episode 1', 'one-punch-man-3-ep-1', 'https://s0.wibufile.com/video01/OPM-S3-1-FULLHD-SAMEHADAKU.CARE.mp4', '', 24, 13, '2025-10-22 13:41:15', '2025-10-26 06:13:54', NULL, NULL, NULL, 'https://hxfile.co/hmuecqawe9dl', 'https://hxfile.co/sgg0qxyyk92c', 'https://hxfile.co/b9htfksjvn7z', 'https://xshotcok.com/embed-hmuecqawe9dl.html', 'https://xshotcok.com/embed-sgg0qxyyk92c.html', 'https://xshotcok.com/embed-b9htfksjvn7z.html'),
(5, 3, 2, 'One Punch Man 3 - Episode 2', 'one-punch-man-3-ep-2', 'https://s0.wibufile.com/video01/OPM-S3-2-FULLHD-SAMEHADAKU.CARE.mp4', '', 0, 3, '2025-10-22 13:44:21', '2025-10-26 06:25:27', NULL, NULL, NULL, '', 'https://hxfile.co/gm2x6py3zk4k', 'https://hxfile.co/opc18msd8het', '', 'https://xshotcok.com/embed-gm2x6py3zk4k.html', 'https://xshotcok.com/embed-opc18msd8het.html'),
(6, 4, 1, 'Ansatsusha de Aru Ore no Status ga Yuusha yori mo Akiraka ni Tsuyoi no da ga - Episode 1', 'ansatsusha-de-aru-ore-no-status-ga-yuusha-yori-mo-akiraka-ni-tsuyoi-no-da-ga-ep-1', 'https://s0.wibufile.com/video01/Sutetsuyo-01-FULLHD-SAMEHADAKU.CARE.mp4', '', 0, 2, '2025-10-22 13:50:32', '2025-10-26 06:06:59', NULL, NULL, NULL, '', 'https://hxfile.co/2llomwwazzom', 'https://hxfile.co/4x3c2vqocewj', '', 'https://xshotcok.com/embed-2llomwwazzom.html', 'https://xshotcok.com/embed-4x3c2vqocewj.html'),
(7, 4, 2, 'Ansatsusha de Aru Ore no Status ga Yuusha yori mo Akiraka ni Tsuyoi no da ga - Episode 2', 'ansatsusha-de-aru-ore-no-status-ga-yuusha-yori-mo-akiraka-ni-tsuyoi-no-da-ga-ep-2', 'https://s0.wibufile.com/video01/Sutetsuyo-02-FULLHD-SAMEHADAKU.CARE.mp4', '', 0, 4, '2025-10-22 13:53:40', '2025-10-26 06:13:03', NULL, NULL, NULL, '', 'https://hxfile.co/yme6yrlf570r', 'https://hxfile.co/ov0n980jo2oc', '', 'https://xshotcok.com/embed-yme6yrlf570r.html', 'https://xshotcok.com/embed-ov0n980jo2oc.html'),
(11, 6, 1, 'Tondemo Skill de Isekai Hourou Meshi 2 - Episode 1', 'tondemo-skill-de-isekai-hourou-meshi-2-ep-1', 'https://s0.wibufile.com/video01/TondemoSkill-S2-01-FULLHD-SAMEHADAKU.CARE.mp4', '', 0, 3, '2025-10-22 14:17:43', '2025-10-26 06:48:40', NULL, NULL, NULL, '', 'https://hxfile.co/2m2x056rwqpo', 'https://hxfile.co/ggpmikpatl0y', '', 'https://xshotcok.com/embed-2m2x056rwqpo.html', 'https://xshotcok.com/embed-ggpmikpatl0y.html'),
(12, 6, 2, 'Tondemo Skill de Isekai Hourou Meshi 2 - Episode 2', 'tondemo-skill-de-isekai-hourou-meshi-2-ep-2', 'https://s0.wibufile.com/video01/TondemoSkill-S2-02-FULLHD-SAMEHADAKU.CARE.mp4', '', 0, 3, '2025-10-22 14:21:22', '2025-10-26 06:52:49', NULL, NULL, NULL, '', 'https://hxfile.co/12cjmb48bssh', 'https://hxfile.co/e238lfjx5avg', '', 'https://xshotcok.com/embed-12cjmb48bssh.html', 'https://xshotcok.com/embed-e238lfjx5avg.html'),
(13, 7, 1, 'Overlord Movie 3: Sei Oukoku-hen - Episode 1', 'overlord-movie-3-sei-oukoku-hen-ep-1', '', 'https://pixeldrain.com/u/zp1ZVVap?embed&style=solarized_dark', 120, 26, '2025-10-22 14:28:35', '2025-10-26 05:18:52', NULL, NULL, NULL, '', '', 'https://hxfile.co/97mt41c1o4sx.html', '', '', 'https://xshotcok.com/embed-97mt41c1o4sx.html'),
(14, 8, 1, 'Mugen Gacha - Episode 1', 'mugen-gacha-ep-1', NULL, NULL, 0, 0, '2025-10-26 05:25:13', '2025-10-26 05:25:13', NULL, NULL, NULL, '', '', 'https://hxfile.co/89hm8yd0kbbg', '', '', 'https://xshotcok.com/embed-89hm8yd0kbbg.html'),
(15, 8, 2, 'Mugen Gacha - Episode 2', 'mugen-gacha-ep-2', NULL, NULL, 0, 2, '2025-10-26 05:25:34', '2025-10-26 05:26:27', NULL, NULL, NULL, '', '', 'https://hxfile.co/ba32bytag52i', '', '', 'https://xshotcok.com/embed-ba32bytag52i.html'),
(16, 8, 3, 'Mugen Gacha - Episode 3', 'mugen-gacha-ep-3', NULL, NULL, 0, 0, '2025-10-26 05:27:01', '2025-10-26 05:27:01', NULL, NULL, NULL, '', '', 'https://hxfile.co/xsh3q4mfpaks', '', '', 'https://xshotcok.com/embed-xsh3q4mfpaks.html'),
(17, 8, 4, 'Mugen Gacha - Episode 4', 'mugen-gacha-ep-4', NULL, NULL, 0, 0, '2025-10-26 05:27:32', '2025-10-26 05:27:32', NULL, NULL, NULL, '', '', 'https://hxfile.co/cy7qt7pnxoyq', '', '', 'https://xshotcok.com/embed-cy7qt7pnxoyq.html'),
(18, 9, 1, 'Chitose Is in the Ramune Bottle - Episode 1', 'chitose-is-in-the-ramune-bottle-ep-1', NULL, NULL, 0, 0, '2025-10-26 05:33:27', '2025-10-26 05:33:27', NULL, NULL, NULL, 'https://hxfile.co/zf138aw90yp2', 'https://hxfile.co/81svu3090a3j', 'https://hxfile.co/5eqck0lm9tsd', 'https://xshotcok.com/embed-zf138aw90yp2.html', 'https://xshotcok.com/embed-81svu3090a3j.html', 'https://xshotcok.com/embed-5eqck0lm9tsd.html'),
(19, 9, 2, 'Chitose Is in the Ramune Bottle - Episode 2', 'chitose-is-in-the-ramune-bottle-ep-2', NULL, NULL, 0, 0, '2025-10-26 05:46:58', '2025-10-26 05:46:58', NULL, NULL, NULL, '', 'https://hxfile.co/gn7iyqenfcig', 'https://hxfile.co/6auz76fpk1f7', '', 'https://xshotcok.com/embed-gn7iyqenfcig.html', 'https://xshotcok.com/embed-6auz76fpk1f7.html'),
(20, 9, 3, 'Chitose Is in the Ramune Bottle - Episode 3', 'chitose-is-in-the-ramune-bottle-ep-3', NULL, NULL, 0, 0, '2025-10-26 05:59:20', '2025-10-26 05:59:20', NULL, NULL, NULL, '', 'https://hxfile.co/i4dtwdf6lo7j', 'https://hxfile.co/kfmykan7dusi', '', 'https://xshotcok.com/embed-i4dtwdf6lo7j.html', 'https://xshotcok.com/embed-kfmykan7dusi.html'),
(21, 3, 3, 'One Punch Man 3 - Episode 3', 'one-punch-man-3-ep-3', NULL, NULL, 0, 3, '2025-10-26 06:35:54', '2025-10-26 09:28:35', NULL, NULL, NULL, '', 'https://hxfile.co/0g721q9zoprl', 'https://hxfile.co/olwbey3p4beb', '', 'https://xshotcok.com/embed-0g721q9zoprl.html', 'https://xshotcok.com/embed-olwbey3p4beb.html'),
(22, 6, 3, 'Tondemo Skill de Isekai Hourou Meshi 2 - Episode 3', 'tondemo-skill-de-isekai-hourou-meshi-2-ep-3', NULL, NULL, 0, 0, '2025-10-26 07:00:40', '2025-10-26 07:33:51', NULL, NULL, NULL, '', 'https://hxfile.co/c1ujbmedr7j1', 'https://hxfile.co/l5fax9w9nn8f', '', 'https://xshotcok.com/embed-c1ujbmedr7j1.html', 'https://xshotcok.com/embed-l5fax9w9nn8f.html'),
(23, 10, 3, 'Mikata Ga Yowasugite Hojo Mahou - Episode 3', 'mikata-ga-yowasugite-hojo-mahou-ep-3', NULL, NULL, 0, 0, '2025-10-26 09:00:16', '2025-10-26 09:00:16', NULL, NULL, NULL, '', '', 'https://hxfile.co/zmtlfbl2et3h', '', '', 'https://xshotcok.com/embed-zmtlfbl2et3h.html'),
(24, 10, 4, 'Mikata Ga Yowasugite Hojo Mahou - Episode 4', 'mikata-ga-yowasugite-hojo-mahou-ep-4', NULL, NULL, 0, 0, '2025-10-26 09:09:05', '2025-10-26 09:09:05', NULL, NULL, NULL, '', '', 'https://hxfile.co/u47rfsjx1tja', '', '', 'https://xshotcok.com/embed-u47rfsjx1tja.html');

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `animes`
--
ALTER TABLE `animes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT untuk tabel `comments`
--
ALTER TABLE `comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `episodes`
--
ALTER TABLE `episodes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

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
