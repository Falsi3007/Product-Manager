-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 24, 2024 at 08:11 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `management`
--

-- --------------------------------------------------------

--
-- Table structure for table `category`
--

CREATE TABLE `category` (
  `id` int(11) NOT NULL,
  `image` varchar(255) NOT NULL,
  `name` varchar(200) NOT NULL,
  `ordering` int(11) NOT NULL,
  `status` enum('active','inactive') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `category`
--

INSERT INTO `category` (`id`, `image`, `name`, `ordering`, `status`, `created_at`, `updated_at`) VALUES
(309, 'Images/1706676197_download.jpg', 'jewellry', 1, 'active', '2024-01-31 04:43:17', '2024-02-13 10:37:11'),
(376, 'Images/1707650510_merigold (1).jpg', 'fruit', 1, 'inactive', '2024-02-11 11:21:50', '2024-02-19 05:25:58'),
(412, 'Images/1707975570_px1_500.png', 'pigg', 2, 'active', '2024-02-15 05:39:30', '2024-02-15 05:39:30'),
(420, 'Images/1708059354_merigold (1).jpg', 'fridayy', 1, 'active', '2024-02-16 04:55:32', '2024-02-16 04:55:54'),
(430, 'Images/1709012546_creata.jpg', 'bhakti112', 12, 'active', '2024-02-18 11:41:41', '2024-02-27 05:42:26'),
(432, 'Images/1708256647_merigold (1).jpg', 'sundayyy', 1, 'active', '2024-02-18 11:44:07', '2024-02-18 11:44:07'),
(436, 'Images/1708320244_px4.jpg', 'vegetabless', 11, 'active', '2024-02-19 05:24:04', '2024-02-19 05:24:20'),
(437, 'Images/1708322462_apple-f.jpg', 'diwalii', 2, 'active', '2024-02-19 05:31:27', '2024-02-19 06:01:02'),
(438, 'Images/1708320854_px1_500.png', 'waterr', 2, 'active', '2024-02-19 05:34:14', '2024-02-19 05:34:14'),
(440, 'Images/1708322430_px3_500.jpeg', 'mackup', 2, 'active', '2024-02-19 06:00:30', '2024-02-19 06:00:30'),
(446, 'Images/1716529160_download.jpg', 'bhakti', 2, 'active', '2024-05-24 05:39:20', '2024-05-24 05:39:20');

-- --------------------------------------------------------

--
-- Table structure for table `product`
--

CREATE TABLE `product` (
  `id` int(11) NOT NULL,
  `name` varchar(200) NOT NULL,
  `product_code` int(11) NOT NULL,
  `price` float NOT NULL,
  `sale_price` float NOT NULL,
  `quantity` int(11) NOT NULL,
  `ordering` int(11) NOT NULL,
  `status` enum('active','inactive') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product`
--

INSERT INTO `product` (`id`, `name`, `product_code`, `price`, `sale_price`, `quantity`, `ordering`, `status`, `created_at`, `updated_at`) VALUES
(2, 'banana', 5917, 222, 111, 7, 2, 'inactive', '2024-02-27 08:34:16', '2024-02-27 08:37:55'),
(3, 'cat', 4185, 500, 400, 5, 3, 'active', '2024-02-27 08:35:00', '2024-02-27 08:35:00'),
(5, 'bike', 7850, 2200, 2100, 11, 3, 'active', '2024-02-27 08:37:45', '2024-02-27 08:37:45'),
(7, 'fruit', 8991, 99, 77, 2, 3, 'active', '2024-02-27 08:39:14', '2024-02-27 08:39:14'),
(9, 'phones', 7231, 2330, 2220, 2, 1, 'active', '2024-02-27 08:40:15', '2024-02-27 08:40:15'),
(10, 'phonesssssssssss', 2743, 222, 111, 2, 2, 'active', '2024-02-27 08:40:37', '2024-02-27 08:40:37'),
(11, 'city', 6442, 222, 111, 2, 2, 'inactive', '2024-02-27 08:41:07', '2024-02-27 08:41:07'),
(12, 'charey', 5608, 2330, 2220, 11, 3, 'active', '2024-02-27 08:41:45', '2024-02-27 08:41:45'),
(13, 'applee', 4634, 99, 77, 1, 2, 'active', '2024-02-27 08:50:09', '2024-02-27 08:50:09'),
(15, 'zzzz', 2663, 222, 111, 2, 12, 'active', '2024-02-27 09:07:27', '2024-02-27 09:07:27'),
(18, 'aacc', 4478, 2330, 2100, 7, 2, 'active', '2024-02-27 09:20:20', '2024-02-27 09:20:20'),
(20, 'shivuu', 2225, 2200, 2220, 2, 3, 'active', '2024-02-27 09:24:40', '2024-02-27 09:24:40'),
(22, 'ziba', 4661, 222, 2220, 2, 12, 'active', '2024-02-27 09:25:17', '2024-02-27 09:25:17'),
(23, 'Bha', 2749, 222, 2220, 2, 3, 'active', '2024-02-27 09:25:41', '2024-02-27 09:25:41'),
(25, 'mobilee', 2878, 500, 400, 1, 2, 'active', '2024-05-24 05:50:45', '2024-05-24 05:50:45'),
(26, 'aabb', 8705, 300, 200, 2, 3, 'active', '2024-05-24 05:53:50', '2024-05-24 05:53:50');

-- --------------------------------------------------------

--
-- Table structure for table `product_category`
--

CREATE TABLE `product_category` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_category`
--

INSERT INTO `product_category` (`id`, `product_id`, `category_id`) VALUES
(44, 26, 223),
(163, 31, 244),
(178, 25, 244),
(281, 40, 223),
(297, 38, 244),
(300, 42, 216),
(301, 42, 249),
(306, 43, 244),
(312, 24, 216),
(314, 4, 244),
(315, 27, 216),
(316, 27, 233),
(321, 48, 216),
(330, 44, 216),
(334, 50, 216),
(350, 51, 216),
(352, 52, 216),
(369, 55, 223),
(375, 56, 223),
(402, 57, 233),
(403, 57, 244),
(414, 58, 216),
(431, 65, 216),
(432, 66, 216),
(433, 67, 216),
(434, 68, 216),
(437, 71, 216),
(451, 77, 216),
(452, 78, 216),
(457, 82, 216),
(466, 85, 216),
(467, 86, 216),
(470, 89, 244),
(475, 93, 216),
(493, 49, 233),
(499, 95, 223),
(500, 96, 223),
(521, 98, 216),
(522, 99, 223),
(534, 101, 223),
(535, 102, 216),
(550, 104, 216),
(552, 105, 216),
(553, 106, 223),
(555, 107, 223),
(742, 323, 309),
(817, 378, 309),
(1405, 434, 309),
(1407, 436, 309),
(1439, 437, 309),
(1445, 440, 309),
(1465, 423, 309),
(1467, 435, 309),
(1493, 444, 309),
(1500, 446, 309),
(1523, 451, 376),
(1524, 452, 376),
(1529, 455, 412),
(1530, 429, 437),
(1549, 454, 309),
(1552, 457, 376),
(1553, 458, 309),
(1554, 459, 309),
(1633, 456, 376),
(1694, 453, 412),
(1697, 462, 376),
(1698, 462, 440),
(1699, 386, 376),
(1700, 386, 440),
(1703, 463, 376),
(1704, 463, 440),
(1726, 464, 376),
(1727, 415, 420),
(1832, 467, 412),
(1833, 467, 440),
(1852, 465, 412),
(1856, 447, 420),
(1941, 468, 376),
(1942, 468, 438),
(1975, 469, 412),
(2036, 470, 309),
(2037, 470, 438),
(2042, 461, 376),
(2043, 461, 440),
(2060, 471, 376),
(2103, 466, 376),
(2104, 466, 438),
(2108, 472, 376),
(2113, 3, 412),
(2114, 4, 412),
(2115, 5, 412),
(2116, 2, 376),
(2117, 2, 438),
(2122, 7, 376),
(2123, 7, 438),
(2125, 8, 376),
(2126, 9, 309),
(2127, 10, 436),
(2130, 12, 420),
(2135, 1, 376),
(2136, 1, 438),
(2137, 13, 309),
(2138, 13, 376),
(2140, 6, 412),
(2141, 6, 438),
(2149, 14, 376),
(2151, 17, 440),
(2153, 16, 412),
(2154, 16, 430),
(2156, 19, 376),
(2158, 21, 376),
(2159, 22, 309),
(2161, 11, 309),
(2162, 11, 440),
(2164, 23, 438),
(2165, 24, 438),
(2166, 15, 412),
(2168, 20, 420),
(2169, 18, 309),
(2170, 25, 376),
(2171, 26, 412);

-- --------------------------------------------------------

--
-- Table structure for table `product_images`
--

CREATE TABLE `product_images` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `image` varchar(200) NOT NULL,
  `is_main_image` tinyint(5) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_images`
--

INSERT INTO `product_images` (`id`, `product_id`, `image`, `is_main_image`) VALUES
(55, 26, 'download.1705404468.jpg', 1),
(56, 26, 'px4.1705404468.jpg', 0),
(200, 31, 'px4.1705572705.jpg', 1),
(208, 27, 'download.1705573703.jpg', 1),
(212, 25, 'px3_500.1705573743.jpeg', 1),
(213, 25, 'px4.1705573743.jpg', 0),
(230, 36, '123.1705639146.jpg', 1),
(231, 36, 'download.1705639146.jpg', 0),
(232, 36, 'px_6.1705639146.png', 0),
(234, 27, 'px2.1705640880.jpeg', 0),
(235, 27, 'px4.1705641117.jpg', 0),
(239, 38, 'px1_500.1705646137.png', 1),
(240, 38, 'px2.1705646137.jpeg', 0),
(241, 38, 'download.1705646154.jpg', 0),
(250, 40, 'px4.jpg', 1),
(253, 40, 'px4.jpg', 0),
(254, 40, 'px2.jpeg', 0),
(255, 40, '123.jpg', 0),
(262, 42, 'px1_500.1705654751.png', 1),
(264, 42, 'px4.1705654751.jpg', 0),
(265, 42, 'px2.jpeg', 0),
(266, 38, 'download.jpg', 0),
(267, 43, 'download.jpg', 1),
(269, 43, 'px4.jpg', 0),
(270, 44, '123.1705662777.jpg', 1),
(271, 44, 'px_6.1705662777.png', 0),
(272, 44, 'px1_500.1705662777.png', 0),
(280, 48, 'defination_php_project.png', 1),
(282, 49, 'px1_500.png', 1),
(285, 50, 'download.jpg', 1),
(286, 44, 'defination_php_project.png', 0),
(289, 50, 'px2.jpeg', 0),
(290, 50, 'px1_500.png', 0),
(291, 50, 'px4.jpg', 0),
(293, 49, 'download.jpg', 0),
(294, 49, 'defination_php_project.png', 0),
(295, 51, 'px_5.1705902610.png', 1),
(296, 51, 'px2.1705902610.jpeg', 0),
(298, 52, 'defination_php_project.1705904771.png', 1),
(299, 54, 'px2.1705915806.jpeg', 1),
(300, 54, 'px4.1705915806.jpg', 0),
(301, 55, 'px1_500.1705916084.png', 1),
(302, 55, 'px2.1705916084.jpeg', 0),
(303, 56, 'px2.1705916552.jpeg', 1),
(304, 56, 'px3_500.1705916552.jpeg', 0),
(305, 56, 'px1_500.png', 0),
(312, 57, 'download.jpg', 1),
(314, 57, 'px_6.1705919797.png', 0),
(315, 57, 'px2.jpeg', 0),
(316, 58, 'download.jpg', 1),
(317, 58, 'px1_500.png', 0),
(318, 58, 'px2.jpeg', 0),
(319, 59, 'unit_1.1705921449.pdf', 1),
(322, 64, 'px1_500.1705925057.png', 1),
(326, 71, 'px4.1705925818.jpg', 1),
(330, 76, 'unit_1.pdf', 1),
(333, 78, 'px1_5001705928634.png', 1),
(334, 78, 'px21705928634.jpeg', 0),
(335, 78, 'px3_5001705928634.jpeg', 0),
(336, 79, 'gif1.jpg', 1),
(337, 79, 'px41705983719.jpg', 0),
(338, 81, 'gif11705983985.jpg', 1),
(339, 81, 'px_61705983985.png', 0),
(340, 79, 'download.jpg', 0),
(341, 79, 'px1_500.png', 0),
(342, 79, 'px2.jpeg', 0),
(343, 85, 'gif11705984842.jpg', 1),
(344, 87, 'px21705986244.jpeg', 1),
(345, 89, 'px1_5001705986312.png', 1),
(346, 89, 'px21705986312.jpeg', 0),
(347, 91, 'gif1.jpg', 1),
(349, 92, 'px1_5001705988666.png', 1),
(350, 92, 'px21705988666.jpeg', 0),
(351, 93, 'px1_5001705989444.png', 1),
(352, 93, 'px21705989444.jpeg', 0),
(357, 49, 'px_6.png', 0),
(360, 91, 'gif1.jpg', 1),
(361, 95, 'gif11705992405.jpg', 1),
(364, 100, 'px1_500.png', 1),
(365, 100, 'px21705994304.jpeg', 0),
(366, 100, 'download.jpg', 0),
(367, 101, 'px1_5001705999590.png', 1),
(368, 101, 'px21705999590.jpeg', 0),
(369, 101, 'px3_5001705999590.jpeg', 0),
(384, 104, 'px1_5001706012760.png', 1),
(385, 104, 'px21706012760.jpeg', 0),
(386, 104, 'download.jpg', 0),
(387, 104, 'gif1.jpg', 0),
(388, 105, 'download1706502681.jpg', 1),
(389, 105, 'px1_5001706502681.png', 0),
(390, 105, 'px21706502682.jpeg', 0),
(391, 107, 'download.jpg', 1),
(393, 107, 'px3_5001706527334.jpeg', 0),
(394, 108, 'gif11706620182.jpg', 1),
(397, 109, 'px3_500.jpeg', 1),
(399, 111, 'gif11706683505.jpg', 1),
(400, 112, 'gif11706683523.jpg', 1),
(401, 113, 'download.jpg', 1),
(403, 113, 'Sample-jpg-image-50kb1706683590.jpg', 0),
(404, 114, 'gif11706683776.jpg', 1),
(405, 115, 'download.jpg', 1),
(406, 115, 'px21706683792.jpeg', 0),
(414, 116, 'download.jpg', 1),
(415, 116, 'px4_1706693195.jpg', 0),
(416, 116, 'Sample-jpg-image-50kb_1706693195.jpg', 0),
(448, 108, 'download.jpg', 0),
(449, 108, 'px1_500.png', 0),
(450, 117, 'px1_500_1706705379.png', 1),
(455, 117, 'px3_500.jpeg', 0),
(456, 117, 'px4.jpg', 0),
(457, 117, 'Sample-jpg-image-50kb.jpg', 0),
(685, 325, 'download.jpg', 1),
(686, 325, 'px2_1706778729.jpeg', 0),
(687, 326, 'gif1.jpg', 1),
(688, 326, 'px2_1706778760.jpeg', 0),
(689, 327, 'gif1.jpg', 1),
(690, 327, 'px3_500_1706778810.jpeg', 0),
(691, 328, 'download.jpg', 1),
(692, 328, 'px3_500_1706782337.jpeg', 0),
(693, 329, 'download.jpg', 1),
(694, 329, 'px1_500_1706783462.png', 0),
(695, 329, 'Sample-jpg-image-50kb_1706783462.jpg', 0),
(696, 330, 'gif1_1706790051.jpg', 1),
(697, 330, 'px4_1706790051.jpg', 0),
(708, 344, 'gif1_1706852819.jpg', 1),
(740, 368, 'px4_1707066300.jpg', 1),
(752, 377, 'gif1_1707068874.jpg', 1),
(754, 379, 'bike_1707072025.jpg', 1),
(755, 379, 'download_1707072025.jpg', 0),
(756, 379, 'gif1_1707072025.jpg', 0),
(762, 381, 'bike_1707107081.jpg', 1),
(774, 384, 'download_1707109522.jpg', 1),
(778, 385, 'px1_500_1707110149.png', 1),
(841, 399, 'download_1707150382.jpg', 1),
(875, 406, 'px2_1707193182.jpeg', 1),
(876, 406, 'px4 - Copy_1707193182.jpg', 0),
(877, 407, 'download_1707193457.jpg', 1),
(878, 407, 'px2_1707193457.jpeg', 0),
(879, 407, 'Sample-jpg-image-50kb_1707193457.jpg', 0),
(880, 408, 'bike_1707193696.jpg', 1),
(898, 412, 'creata_1707197788.jpg', 1),
(908, 415, 'px1_500_1707198798.png', 1),
(914, 408, 'px_6.png', 0),
(916, 412, 'bike.jpg', 0),
(917, 384, 'px3_500.jpeg', 0),
(918, 384, 'Sample-jpg-image-50kb.jpg', 0),
(919, 381, 'gif1.jpg', 0),
(921, 417, 'download_1707208048.jpg', 1),
(922, 417, 'gif1_1707208048.jpg', 0),
(925, 417, 'bike.jpg', 0),
(938, 419, 'px1_500_1707209095.png', 1),
(939, 419, 'Sample-jpg-image-50kb_1707209095.jpg', 0),
(980, 423, 'px3_500_1707302258.jpeg', 1),
(981, 423, 'px4 - Copy_1707302258.jpg', 0),
(986, 426, 'gif1_1707325453.jpg', 1),
(990, 428, 'cherry_1707364671.jpg', 1),
(1029, 431, 'bike_1707408676.jpg', 1),
(1030, 431, 'download_1707408676.jpg', 0),
(1031, 431, 'gif1_1707408676.jpg', 0),
(1032, 432, 'px1_500_1707408784.png', 1),
(1033, 432, 'px2_1707408784.jpeg', 0),
(1034, 432, 'px3_500_1707408785.jpeg', 0),
(1035, 432, 'px4 - Copy_1707408785.jpg', 0),
(1052, 436, 'merigold (1)_1707904777.jpg', 1),
(1053, 436, 'px3_500_1707904777.jpeg', 0),
(1057, 437, 'bike_1707908650.jpg', 1),
(1058, 437, 'gif1_1707908650.jpg', 0),
(1066, 442, 'px1_500_1707975736.png', 1),
(1067, 442, 'Sample-jpg-image-50kb_1707975736.jpg', 0),
(1068, 442, 'bike.jpg', 0),
(1072, 444, '1708017956_download.jpg', 1),
(1073, 444, '1708017956_px1_500.png', 0),
(1080, 447, '1708066678_bike.jpg', 1),
(1088, 452, '1708331034_gif1.jpg', 1),
(1089, 453, '1708332651_bike.jpg', 1),
(1091, 455, '1708334334_download.jpg', 1),
(1092, 456, '1708336733_creata.jpg', 1),
(1101, 457, '1708405714_download.jpg', 1),
(1102, 457, '1708405714_gif1.jpg', 0),
(1103, 457, 'bike1.jpg', 0),
(1104, 458, '1708412400_px1_500.png', 1),
(1105, 458, '1708412400_Sample-jpg-image-50kb.jpg', 0),
(1106, 459, '1708412453_px1_500.png', 1),
(1107, 459, '1708412453_Sample-jpg-image-50kb.jpg', 0),
(1133, 385, 'download1.jpg', 0),
(1135, 415, 'download1.jpg', 0),
(1147, 447, 'creata.jpg', 0),
(1151, 456, 'px23.jpeg', 0),
(1152, 385, 'px2.jpeg', 0),
(1165, 442, 'px4.jpg', 0),
(1166, 442, 'tameto.jpg', 0),
(1167, 442, '123.jpg', 0),
(1168, 442, 'aaple.webp', 0),
(1169, 442, 'unit_1.pdf', 0),
(1170, 415, 'gif1.jpg', 0),
(1171, 453, 'download.jpg', 0),
(1173, 462, '1708584311_bike.jpg', 1),
(1174, 462, '1708584311_download.jpg', 0),
(1175, 462, 'download.jpg', 0),
(1177, 463, '1708603010_px1_500.png', 1),
(1178, 463, '1708603010_px2.jpeg', 0),
(1208, 385, 'px4.jpg', 0),
(1209, 385, 'sample-jpg-image-50kb.jpg', 0),
(1244, 469, 'px2.jpeg', 1),
(1245, 469, '1709010812_gif1.jpg', 0),
(1246, 469, 'creata.jpg', 0),
(1247, 469, 'merigold (1).jpg', 0),
(1274, 2, '1709022856_px1_500.png', 1),
(1275, 2, '1709022856_px2.jpeg', 0),
(1276, 3, '1709022900_px1_500.png', 1),
(1279, 5, '1709023065_bike.jpg', 1),
(1282, 7, '1709023154_download.jpg', 1),
(1283, 7, '1709023154_gif1.jpg', 0),
(1286, 9, '1709023215_gif1.jpg', 1),
(1287, 10, '1709023237_creata.jpg', 1),
(1288, 11, '1709023267_bike.jpg', 1),
(1289, 11, '1709023267_download.jpg', 0),
(1290, 12, '1709023305_cherry.jpg', 1),
(1291, 13, '1709023809_apple-f.jpg', 1),
(1293, 15, 'Images/apple-f.jpg', 1),
(1297, 18, 'Images/download.jpg', 1),
(1299, 20, 'Images/bike.jpg', 1),
(1301, 22, '1709025917_gif1.jpg', 1),
(1302, 23, '1709025941_cherry.jpg', 1),
(1303, 11, 'apple-f.jpg', 0),
(1304, 23, 'bike.jpg', 0),
(1306, 25, '1716529845_creata.jpg', 1),
(1307, 25, '1716529845_download.jpg', 0),
(1308, 26, '1716530030_creata.jpg', 1);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(255) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`) VALUES
(1, 'bhakti', '1692fcfff3e01e7ba8cffc2baadef5f5');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `category`
--
ALTER TABLE `category`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `product`
--
ALTER TABLE `product`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `product_category`
--
ALTER TABLE `product_category`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_category_ibfk_1` (`product_id`),
  ADD KEY `product_category_ibfk_2` (`category_id`);

--
-- Indexes for table `product_images`
--
ALTER TABLE `product_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_images_ibfk_1` (`product_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `category`
--
ALTER TABLE `category`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=447;

--
-- AUTO_INCREMENT for table `product`
--
ALTER TABLE `product`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `product_category`
--
ALTER TABLE `product_category`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2172;

--
-- AUTO_INCREMENT for table `product_images`
--
ALTER TABLE `product_images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1309;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `product_category`
--
ALTER TABLE `product_category`
  ADD CONSTRAINT `product_category_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `category` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `product_images`
--
ALTER TABLE `product_images`
  ADD CONSTRAINT `product_images_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `product` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
