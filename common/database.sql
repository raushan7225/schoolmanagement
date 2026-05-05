-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: May 05, 2026 at 06:28 PM
-- Server version: 10.11.16-MariaDB-cll-lve
-- PHP Version: 8.4.20

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `xdcdkpfd_education`
--

-- --------------------------------------------------------

--
-- Table structure for table `academic_years`
--

CREATE TABLE `academic_years` (
  `id` int(10) UNSIGNED NOT NULL,
  `year_label` varchar(50) NOT NULL,
  `year_type` enum('Year','Semester') DEFAULT 'Year',
  `status` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `academic_years`
--

INSERT INTO `academic_years` (`id`, `year_label`, `year_type`, `status`) VALUES
(1, '1st Year', 'Year', 1),
(2, '2nd Year', 'Year', 1),
(3, '3rd Year', 'Year', 1),
(4, '4th Year', 'Year', 1),
(5, '5th Year', 'Year', 1),
(6, '6th year', 'Year', 1),
(7, 'Semester-1', 'Semester', 1),
(8, 'Semester-2', 'Semester', 1),
(9, 'Semester-3', 'Semester', 1),
(10, 'Semester-4', 'Semester', 1),
(11, 'Semester-5', 'Semester', 1),
(12, 'Semester-6', 'Semester', 1);

-- --------------------------------------------------------

--
-- Table structure for table `admissions`
--

CREATE TABLE `admissions` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `roll_number` varchar(25) DEFAULT NULL,
  `course_id` int(10) UNSIGNED DEFAULT NULL,
  `center_id` int(10) UNSIGNED DEFAULT NULL,
  `session_name` varchar(20) DEFAULT NULL,
  `session_id` int(10) UNSIGNED DEFAULT NULL,
  `admission_date` date DEFAULT NULL,
  `qualification` varchar(100) DEFAULT NULL,
  `caste` varchar(50) DEFAULT NULL,
  `blood_group` varchar(10) DEFAULT NULL,
  `full_name` varchar(100) DEFAULT NULL,
  `father_name` varchar(100) DEFAULT NULL,
  `mother_name` varchar(100) DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `gender` enum('male','female','other') DEFAULT NULL,
  `religion` varchar(50) DEFAULT NULL,
  `mobile` varchar(15) DEFAULT NULL,
  `guardian_phone` varchar(15) DEFAULT NULL,
  `guardian_doc` varchar(255) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `country_id` int(10) UNSIGNED DEFAULT NULL,
  `state_id` int(10) UNSIGNED DEFAULT NULL,
  `district_id` int(10) UNSIGNED DEFAULT NULL,
  `city_id` int(10) UNSIGNED DEFAULT NULL,
  `guardian_address` text DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `pincode` varchar(10) DEFAULT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `signature` varchar(255) DEFAULT NULL,
  `id_proof` varchar(255) DEFAULT NULL,
  `aadhar_front` varchar(255) DEFAULT NULL,
  `aadhar_back` varchar(255) DEFAULT NULL,
  `marksheet_10th` varchar(255) DEFAULT NULL,
  `marksheet_12th` varchar(255) DEFAULT NULL,
  `parent_aadhar_front` varchar(255) DEFAULT NULL,
  `parent_aadhar_back` varchar(255) DEFAULT NULL,
  `approval_status` enum('pending','approved','rejected','completed') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `source` enum('manual','online') DEFAULT 'manual'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admissions`
--

INSERT INTO `admissions` (`id`, `user_id`, `roll_number`, `course_id`, `center_id`, `session_name`, `session_id`, `admission_date`, `qualification`, `caste`, `blood_group`, `full_name`, `father_name`, `mother_name`, `dob`, `gender`, `religion`, `mobile`, `guardian_phone`, `guardian_doc`, `email`, `address`, `country_id`, `state_id`, `district_id`, `city_id`, `guardian_address`, `city`, `pincode`, `photo`, `signature`, `id_proof`, `aadhar_front`, `aadhar_back`, `marksheet_10th`, `marksheet_12th`, `parent_aadhar_front`, `parent_aadhar_back`, `approval_status`, `created_at`, `updated_at`, `status`, `source`) VALUES
(5, 9, '2026000005', 1, 1, '2026', 1, '2026-04-28', 'Graduate', 'General', 'B-', 'STUDENT ADMIN', 'FATHER ADMIN', 'MOTHER ADMIN', '2006-10-17', 'male', 'Hindu', '09988776655', '09988776655', 'GDOC_1777418635_790.pdf', 'student@admin.com', '123 Admin Street', 1, 2, 2, 2, NULL, NULL, '110001', 'IMG_1777418633_973.png', 'SIG_1777418633_483.jpg', 'DOC_1777418633_563.png', NULL, NULL, NULL, NULL, NULL, NULL, 'approved', '2026-04-28 23:23:56', '2026-05-01 13:32:39', 0, 'manual'),
(6, 1, '2026000006', 1, 2, '2026', 1, '2026-04-29', '12th', 'OBC', 'AB+', 'SHUBHAM', 'NSME', 'NAME', '2026-04-29', 'male', 'Hindu', '07878787878', '6969695808', 'GDOC_1777429586_423.jpg', 'shubham@gmail.com', 'Prayagraj Uttar Pradesh', 1, 2, 2, 2, NULL, NULL, '211001', 'IMG_1777429586_417.jpg', 'SIG_1777429586_157.jpg', 'DOC_1777429586_767.jpg', NULL, NULL, NULL, NULL, NULL, NULL, 'approved', '2026-04-29 02:26:26', '2026-05-01 13:32:35', 0, 'manual'),
(7, 15, NULL, 1, 2, NULL, 3, '2026-05-04', 'Graduation', 'General', 'A-', 'RAUSHAN RAAJ', 'XYZ', 'XYZ', '1987-03-11', 'male', 'Hindu', '7589658741', '7596325478', 'GDOC_1777846693_173.avif', 'raushan@gmail.com', 'meerut', 1, 39, 694, 20, NULL, NULL, '102356', 'IMG_1777846693_802.png', 'SIG_1777846693_964.jpg', 'DOC_1777846693_333.avif', 'AF_1777846693_560.avif', 'AB_1777846693_668.avif', 'M10_1777846693_202.jpg', 'M12_1777846693_911.jpg', 'PAF_1777846693_363.avif', 'PAB_1777846693_179.avif', 'pending', '2026-05-03 22:18:13', '2026-05-03 22:18:13', 1, 'online');

-- --------------------------------------------------------

--
-- Table structure for table `admission_sessions`
--

CREATE TABLE `admission_sessions` (
  `id` int(10) UNSIGNED NOT NULL,
  `session_label` varchar(100) NOT NULL,
  `status` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admission_sessions`
--

INSERT INTO `admission_sessions` (`id`, `session_label`, `status`) VALUES
(3, 'JUN2023-JUN2024', 1),
(4, 'JUN2024-JUN2025', 1),
(5, 'JUN2025-JUN2026', 1),
(6, 'JUN2026-JUN2027', 1),
(7, 'JUN2023-JUN2025', 1),
(8, 'JUN2024-JUN2026', 1),
(9, 'JUN2025-JUN2027', 1),
(10, 'JUN2026-JUN2028', 1);

-- --------------------------------------------------------

--
-- Table structure for table `admit_download_settings`
--

CREATE TABLE `admit_download_settings` (
  `id` int(11) NOT NULL,
  `exam_id` int(11) DEFAULT NULL,
  `start_date` datetime DEFAULT NULL,
  `end_date` datetime DEFAULT NULL,
  `status` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `centers`
--

CREATE TABLE `centers` (
  `id` int(10) UNSIGNED NOT NULL,
  `code` varchar(20) NOT NULL,
  `name` varchar(150) NOT NULL,
  `director_name` varchar(100) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `mobile` varchar(15) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `state` varchar(100) DEFAULT NULL,
  `district` varchar(100) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `centers`
--

INSERT INTO `centers` (`id`, `code`, `name`, `director_name`, `image`, `mobile`, `email`, `address`, `state`, `district`, `status`, `created_at`) VALUES
(1, 'C-001', 'NEBOOSASE Main Center', 'Raushan Raaj', NULL, NULL, NULL, NULL, 'Delhi', 'New Delhi', 1, '2026-04-24 02:00:12'),
(2, 'C-002', 'Assam Regional Center', 'B. K. Sarma', NULL, NULL, NULL, NULL, 'Assam', 'Guwahati', 1, '2026-04-24 23:45:07');

-- --------------------------------------------------------

--
-- Table structure for table `cities`
--

CREATE TABLE `cities` (
  `id` int(10) UNSIGNED NOT NULL,
  `district_id` int(10) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cities`
--

INSERT INTO `cities` (`id`, `district_id`, `name`, `status`) VALUES
(7, 653, 'Adilabad', 1),
(8, 653, 'Dasnapur', 1),
(10, 355, 'Agar', 1),
(11, 355, 'Badod', 1),
(12, 355, 'Nalkheda', 1),
(13, 355, 'Susner', 1),
(14, 355, 'Soyat Kalan', 1),
(15, 694, 'Agra', 1),
(16, 694, 'Bah', 1),
(17, 694, 'Fatehpur Sikri', 1),
(18, 694, 'Shamsabad', 1),
(19, 694, 'Fatehabad', 1),
(20, 694, 'Achhnera', 1),
(21, 694, 'Pinahat', 1),
(22, 193, 'Ahmedabad', 1),
(23, 193, 'Ahmedabad Cantonment', 1),
(24, 193, 'Bareja', 1),
(25, 193, 'Barwala', 1),
(26, 193, 'Bavla', 1),
(27, 193, 'Bopal', 1),
(28, 193, 'Dhandhuka', 1),
(29, 193, 'Dholka', 1),
(30, 193, 'Nandej', 1),
(31, 193, 'Ranpur', 1),
(32, 193, 'Sanand', 1),
(33, 193, 'Singarva', 1),
(34, 193, 'Viramgam', 1),
(35, 410, 'Ahmednagar', 1),
(36, 410, 'hmednagar Cantonment', 1),
(37, 410, 'Burhanagar', 1),
(38, 410, 'Darewadi', 1),
(39, 410, 'Deolali Pravara', 1),
(40, 410, 'Ghulewadi', 1),
(41, 410, 'Jamkhed', 1),
(42, 410, 'Karjat', 1),
(43, 410, 'Kopargaon', 1),
(44, 410, 'Nagapur', 1),
(45, 410, 'Nagardeole', 1),
(46, 410, 'Pathardi', 1),
(47, 410, 'Rahta Pimplas', 1),
(48, 410, 'Rahuri', 1),
(49, 410, 'Rajur', 1),
(50, 410, 'Sangamner', 1),
(51, 410, 'Shirdi', 1),
(52, 410, 'Shrigonda', 1),
(53, 410, 'Shrirampur', 1),
(54, 478, 'Aizawl', 1),
(55, 478, 'Saitual', 1),
(56, 478, 'Sairang', 1),
(57, 478, 'Darlawn', 1),
(58, 564, 'Ajmer', 1),
(59, 564, 'Beawar', 1),
(60, 564, 'Kishangarh', 1),
(61, 564, 'Nasirabad', 1),
(62, 564, 'Kekri', 1),
(63, 564, 'Vijainagar', 1),
(64, 564, 'Pushkar', 1),
(65, 564, 'Sarwar', 1),
(66, 564, 'Boraj-Kazipura', 1),
(67, 564, 'Baral', 1),
(68, 564, 'Badlya', 1),
(69, 411, 'Akola', 1),
(70, 411, 'Akot', 1),
(71, 411, 'Murtijapur', 1),
(72, 411, 'Balapur', 1),
(73, 411, 'Patur', 1),
(74, 411, 'Telhara', 1),
(75, 411, 'Barshitakli', 1),
(76, 411, 'Umri Pragane Balapur', 1),
(77, 411, 'Shivar', 1),
(78, 411, 'Shivani', 1),
(79, 411, 'Malkapur', 1),
(80, 411, 'Khadki Bk', 1),
(81, 340, 'Alappuzha', 1),
(82, 340, 'Chengannur', 1),
(83, 340, 'Cherthala', 1),
(84, 340, 'Kayamkulam', 1),
(85, 340, 'Mavelikkara', 1),
(86, 340, 'Haripad', 1),
(87, 340, 'Arookutty', 1),
(88, 340, 'Aroor', 1),
(89, 340, 'Bharanikkavu', 1),
(90, 340, 'Bharanikkavu', 1),
(91, 340, 'Chennithala', 1),
(92, 340, 'Cheppad', 1),
(93, 340, 'Chingoli', 1),
(94, 340, 'Ezhupunna', 1),
(95, 340, 'Kandalloor', 1),
(96, 340, 'Kanjikkuzhi', 1),
(97, 340, 'Kannamangalam', 1),
(98, 340, 'Karthikappally', 1),
(99, 340, 'Kattanam', 1),
(100, 340, 'Keerikkad', 1),
(101, 340, 'Kodamthuruth', 1),
(102, 340, 'Kokkothamangalam', 1),
(103, 340, 'Komalapuram', 1),
(104, 340, 'Krishnapuram', 1),
(105, 340, 'Kumarapuram', 1),
(106, 340, 'Kurattissery', 1),
(107, 340, 'Kuthiathode', 1),
(108, 340, 'Mannanchery', 1),
(109, 340, 'Mannar', 1),
(110, 340, 'Muhamma', 1),
(111, 340, 'Muthukulam', 1),
(112, 340, 'Pallippuram', 1),
(113, 340, 'Pathirappally', 1),
(114, 340, 'Pathiyoor', 1),
(115, 340, 'Puthuppally', 1),
(116, 340, 'Thaikattussery', 1),
(117, 340, 'Thanneermukkam', 1),
(118, 340, 'Thazhakara', 1),
(119, 340, 'Vayalar', 1),
(120, 695, 'Aligarh', 1),
(121, 695, 'Aligarh', 1),
(122, 695, 'Atrauli', 1),
(123, 695, 'Jalali', 1),
(124, 695, 'Pilkhana', 1),
(125, 564, 'Iglas', 1),
(126, 564, 'Beswan', 1),
(127, 564, 'Vijaygarh', 1),
(128, 564, 'Kauriaganj', 1),
(129, 564, 'Harduaganj', 1),
(130, 784, 'Alipurduar', 1),
(131, 784, 'Falakata', 1),
(132, 784, 'Alipurduar Railway Junction', 1),
(133, 784, 'Bholar Dabri', 1),
(134, 784, 'Birpara', 1),
(135, 784, 'Chechakhata', 1),
(136, 784, 'Dakshin Rampur', 1),
(137, 784, 'Jagijhora Barabak', 1),
(138, 784, 'Jaigaon', 1),
(139, 784, 'Jateswar', 1),
(140, 784, 'Laskarpara', 1),
(141, 784, 'Mechiabasti', 1),
(142, 784, 'Parangarpar', 1),
(143, 784, 'Paschim Jitpur', 1),
(144, 784, 'Samuktola', 1),
(145, 784, 'Sisha Jumrha', 1),
(146, 784, 'Sobhaganj', 1),
(147, 784, 'Uttar Kamakhyaguri', 1),
(148, 784, 'Uttar Latabari', 1),
(149, 784, 'Uttar Madarihat', 1),
(150, 784, 'Uttar Satali', 1),
(151, 784, 'Jaygaon', 1),
(152, 356, 'Alirajpur', 1),
(153, 356, 'Jobat', 1),
(154, 356, 'Bhavra', 1),
(155, 356, 'Nanpur', 1),
(156, 11, 'Araku Valley', 1),
(157, 11, 'Paderu', 1),
(158, 771, 'Almora', 1),
(159, 771, 'Ranikhet', 1),
(160, 771, 'Dwarahat', 1),
(161, 565, 'Alwar', 1),
(162, 565, 'Bhiwadi', 1),
(163, 565, 'Khairthal', 1),
(164, 565, 'Behror', 1),
(165, 565, 'Rajgarh', 1),
(166, 565, 'Tijara', 1),
(167, 565, 'Kherli', 1),
(168, 565, 'Govindgarh', 1),
(169, 565, 'Diwakari', 1),
(170, 565, 'Shahjahanpur', 1),
(171, 565, 'Tapookra', 1),
(172, 565, 'Ramgarh', 1),
(173, 565, 'Kishangarh', 1),
(174, 565, 'Bhoogar', 1),
(175, 565, 'Desoola', 1),
(176, 565, 'Neemrana', 1),
(177, 227, 'Ambala', 1),
(178, 227, 'Ambala Sadar', 1),
(179, 227, 'Ambala Cantonment', 1),
(180, 227, 'Naraingarh', 1),
(181, 227, 'Shahzadpur', 1),
(182, 227, 'Barara', 1),
(183, 227, 'Babiyal', 1),
(184, 227, 'Boh', 1),
(185, 697, 'Akbarpur', 1),
(186, 697, 'Tanda', 1),
(187, 697, 'Jalalpur', 1),
(188, 697, 'Ashrafpur Kichhauchha', 1),
(189, 697, 'Iltifatganj Bazar', 1),
(190, 697, 'Bhulepur', 1),
(191, 697, 'Jalalpur Dehat', 1),
(192, 698, 'Amethi', 1),
(193, 698, 'Gauriganj', 1),
(194, 698, 'Jagdishpur', 1),
(195, 412, 'Amravati', 1),
(196, 412, 'Achalpur', 1),
(197, 412, 'Anjangaon', 1),
(198, 412, 'Warud', 1),
(199, 412, 'Morshi', 1),
(200, 412, 'Daryapur (Banosa)', 1),
(201, 412, 'Shendurjana', 1),
(202, 412, 'Dattapur Dhamangaon', 1),
(203, 412, 'Chandur Railway', 1),
(204, 412, 'Chandurbazar', 1),
(205, 412, 'Chikhaldara', 1),
(206, 412, 'Dharni', 1),
(207, 194, 'Amreli', 1),
(208, 194, 'Savarkundla', 1),
(209, 194, 'Rajula', 1),
(210, 194, 'Bagasara', 1),
(211, 194, 'Jafrabad', 1),
(212, 194, 'Babra', 1),
(213, 194, 'Lathi', 1),
(214, 194, 'Chalala', 1),
(215, 194, 'Damnagar', 1),
(216, 194, 'Lilia', 1),
(217, 541, 'Amritsar', 1),
(218, 541, 'Amritsar Cantonment', 1),
(219, 541, 'Ajnala', 1),
(220, 541, 'Baba Bakala', 1),
(221, 541, 'Khilchian', 1),
(222, 699, 'Amroha', 1),
(223, 699, 'Hasanpur', 1),
(224, 699, 'Naugawan Sadat', 1),
(225, 699, 'Dhanaura', 1),
(226, 699, 'Gajraula', 1),
(227, 699, 'Bachhraon', 1),
(228, 699, 'Joya', 1),
(229, 699, 'Ujhari', 1),
(230, 699, 'Sainthal', 1),
(231, 12, 'Anakapalli', 1),
(232, 12, 'Elamanchili', 1),
(233, 12, 'Narsipatnam', 1),
(234, 12, 'Bowluvada', 1),
(235, 12, 'Chodavaram', 1),
(236, 12, 'Kantabamsuguda', 1),
(237, 12, 'Mulakuddu', 1),
(238, 12, 'Nakkapalle', 1),
(239, 12, 'Peda Boddepalle', 1),
(240, 12, 'Payakaraopeta', 1),
(241, 195, 'Anand', 1),
(242, 195, 'Khambhat', 1),
(243, 195, 'Petlad', 1),
(244, 195, 'Borsad', 1),
(245, 195, 'Umreth', 1),
(246, 195, 'Sojitra', 1),
(247, 195, 'Vallabh Vidyanagar', 1),
(248, 195, 'Karamsad', 1),
(249, 195, 'Ode', 1),
(250, 195, 'Anklav', 1),
(251, 195, 'Tarapur', 1),
(252, 195, 'Dharmaj', 1),
(253, 195, 'Boriavi', 1),
(254, 195, 'Vasna Borsad', 1),
(255, 195, 'Gamdi', 1),
(256, 30, 'Anantapur', 1),
(257, 30, 'Guntakal', 1),
(258, 30, 'Tadipatri', 1),
(259, 30, 'Rayadurgam', 1),
(260, 30, 'Gooty', 1),
(261, 30, 'Kalyanadurg', 1),
(262, 30, 'Pamidi', 1),
(263, 274, 'Anantnag', 1),
(264, 274, 'Bijbehara', 1),
(265, 274, 'Mattan', 1),
(266, 274, 'Achabal', 1),
(267, 274, 'Kokernag', 1),
(268, 274, 'Pahalgam', 1),
(269, 274, 'Qazigund', 1),
(270, 274, 'Aishmuqam', 1),
(271, 274, 'Seer Hamdan', 1),
(272, 507, 'Angul', 1),
(273, 62, 'Anjaw', 1),
(274, 31, 'Annamayya', 1),
(275, 566, 'Anupgarh', 1),
(276, 357, 'Anuppur', 1),
(277, 102, 'Araria', 1),
(278, 196, 'Aravalli', 1),
(279, 612, 'Ariyalur', 1),
(280, 103, 'Arwal', 1),
(281, 358, 'Ashoknagar', 1),
(282, 700, 'Auraiya', 1),
(283, 413, 'Aurangabad', 1),
(284, 701, 'Ayodhya (faizabad)', 1),
(285, 702, 'Azamgarh', 1),
(286, 308, 'Bagalkot', 1),
(287, 772, 'Bageshwar', 1),
(288, 703, 'Baghpat', 1),
(289, 704, 'Bahraich', 1),
(290, 68, 'Bajali', 1),
(291, 67, 'Baksa', 1),
(292, 359, 'Balaghat', 1),
(293, 508, 'Balangir', 1),
(294, 509, 'Balasore (Baleswar)', 1),
(295, 309, 'Ballari', 1),
(296, 705, 'Ballia', 1),
(297, 141, 'Balod', 1),
(298, 142, 'Baloda Bazar-Bhatapara', 1),
(299, 567, 'Balotra', 1),
(300, 706, 'Balrampur', 1),
(301, 807, 'Balrampur', 1),
(302, 197, 'Banaskantha', 1),
(303, 707, 'Banda', 1),
(304, 281, 'Bandipora', 1),
(305, 105, 'Banka', 1),
(306, 785, 'Bankura', 1),
(307, 568, 'Banswara', 1),
(308, 18, 'Bapatla', 1),
(309, 708, 'Barabanki', 1),
(310, 282, 'Baramulla', 1),
(311, 569, 'Baran', 1),
(312, 709, 'Bareilly', 1),
(313, 510, 'Bargarh', 1),
(314, 570, 'Barmer', 1),
(315, 542, 'Barnala', 1),
(316, 69, 'Barpeta', 1),
(317, 360, 'Barwani', 1),
(318, 144, 'Bastar', 1),
(319, 710, 'Basti', 1),
(320, 543, 'Bathinda', 1),
(321, 571, 'Beawar', 1),
(322, 414, 'Beed', 1),
(323, 106, 'Begusarai', 1),
(324, 310, 'Belagavi', 1),
(325, 145, 'Bemetara', 1),
(326, 311, 'Bengaluru Rural', 1),
(327, 312, 'Bengaluru Urban', 1),
(328, 361, 'Betul', 1),
(329, 711, 'Bhadohi (Sant Ravidas Nagar)', 1),
(330, 654, 'Bhadradri Kothagudem', 1),
(331, 511, 'Bhadrak', 1),
(332, 107, 'Bhagalpur', 1),
(333, 415, 'Bhandara', 1),
(334, 573, 'Bharatpur', 1),
(335, 572, 'Bharatpur', 1),
(336, 198, 'Bharuch', 1),
(337, 199, 'Bhavnagar', 1),
(338, 574, 'Bhilwara', 1),
(339, 362, 'Bhind', 1),
(340, 362, 'Bhind', 1),
(341, 228, 'Bhiwani', 1),
(342, 108, 'Bhojpur', 1),
(343, 363, 'Bhopal', 1),
(344, 41, 'Bichom', 1),
(345, 313, 'Bidar', 1),
(346, 146, 'Bijapur', 1),
(347, 712, 'Bijnor', 1),
(348, 575, 'Bikaner', 1),
(349, 147, 'Bilaspur', 1),
(350, 251, 'Bilaspur', 1),
(351, 786, 'Birbhum', 1),
(352, 449, 'Bishnupur', 1),
(353, 70, 'Biswanath', 1),
(354, 284, 'Bokaro', 1),
(355, 71, 'Bongaigaon', 1),
(356, 200, 'Botad', 1),
(357, 512, 'Boudh', 1),
(358, 713, 'Budaun', 1),
(359, 278, 'Budgam', 1),
(360, 714, 'Bulandshahr', 1),
(361, 416, 'Buldhana', 1),
(362, 576, 'Bundi', 1),
(363, 364, 'Burhanpur', 1),
(364, 109, 'Buxar', 1),
(365, 72, 'Cachar', 1),
(366, 181, 'Central', 1),
(368, 182, 'Central North', 1),
(369, 314, 'Chamarajanagar', 1),
(370, 252, 'Chamba', 1),
(371, 773, 'Chamoli', 1),
(372, 774, 'Champawat', 1),
(373, 479, 'Champhai', 1),
(374, 715, 'Chandauli', 1),
(375, 450, 'Chandel', 1),
(376, 140, 'Chandigarh', 1),
(377, 417, 'Chandrapur', 1),
(378, 64, 'Changlang', 1),
(379, 73, 'Charaideo', 1),
(380, 229, 'Charkhi Dadri', 1),
(381, 285, 'Chatra', 1),
(382, 613, 'Chengalpattu', 1),
(383, 614, 'Chennai', 1),
(384, 365, 'Chhatarpur', 1),
(385, 366, 'Chhindwara', 1),
(386, 201, 'Chhota Udaipur', 1),
(387, 315, 'Chikkaballapur', 1),
(388, 316, 'Chikkamagaluru', 1),
(389, 74, 'Chirang', 1),
(390, 317, 'Chitradurga', 1),
(391, 716, 'Chitrakoot', 1),
(392, 32, 'Chittoor', 1),
(393, 577, 'Chittorgarh', 1),
(394, 489, 'Chumoukedima', 1),
(395, 452, 'Churachandpur', 1),
(396, 451, 'Churachandpur', 1),
(397, 578, 'Churu', 1),
(398, 615, 'Coimbatore', 1),
(399, 617, 'Coimbatore', 1),
(400, 787, 'Cooch Behar', 1),
(401, 618, 'Cuddalore', 1),
(402, 616, 'Cuddalore', 1),
(403, 513, 'Cuttack', 1),
(404, 174, 'Dadar and Nagar Haveli', 1),
(405, 202, 'Dahod', 1),
(406, 788, 'Dakshin Dinajpur', 1),
(407, 318, 'Dakshina Kannada', 1),
(408, 175, 'Daman', 1),
(409, 367, 'Damoh', 1),
(410, 203, 'Dang', 1),
(411, 148, 'Dantewada', 1),
(412, 110, 'Darbhanga', 1),
(413, 789, 'Darjeeling', 1),
(414, 75, 'Darrang', 1),
(415, 368, 'Datia', 1),
(416, 579, 'Dausa', 1),
(417, 319, 'Davanagere', 1),
(418, 775, 'Dehradun', 1),
(419, 514, 'Deogarh (Debagarh)', 1),
(420, 286, 'Deoghar', 1),
(421, 717, 'Deoria', 1),
(422, 204, 'Devbhoomi Dwarka', 1),
(423, 369, 'Dewas', 1),
(424, 686, 'Dhalai', 1),
(425, 149, 'Dhamtari', 1),
(426, 287, 'Dhanbad', 1),
(427, 370, 'Dhar', 1),
(428, 619, 'Dharmapuri', 1),
(429, 320, 'Dharwad', 1),
(430, 76, 'Dhemaji', 1),
(431, 515, 'Dhenkanal', 1),
(432, 580, 'Dholpur', 1),
(433, 77, 'Dhubri', 1),
(434, 418, 'Dhule', 1),
(435, 59, 'Dibang Valley', 1),
(436, 78, 'Dibrugarh', 1),
(437, 581, 'Didwana-Kuchaman', 1),
(438, 79, 'Dima Hasao', 1),
(439, 490, 'Dimapur', 1),
(440, 620, 'Dindigul', 1),
(441, 371, 'Dindori', 1),
(442, 176, 'Diu', 1),
(443, 271, 'Doda', 1),
(444, 19, 'Dr. B.R. Ambedkar Konaseema / Konaseema', 1),
(445, 582, 'Dudu', 1),
(446, 288, 'Dumka', 1),
(447, 583, 'Dungarpur', 1),
(448, 150, 'Durg', 1),
(449, 187, 'East', 1),
(450, 111, 'East Champaran', 1),
(451, 466, 'East Garo Hills', 1),
(452, 20, 'East Godavari', 1),
(453, 467, 'East Jaintia Hills', 1),
(454, 42, 'East Kameng', 1),
(455, 468, 'East Khasi Hills', 1),
(456, 58, 'East Siang', 1),
(457, 289, 'East Singhbhum', 1),
(458, 469, 'Eastern West Khasi Hills', 1),
(459, 21, 'Eluru', 1),
(460, 341, 'Ernakulam', 1),
(461, 621, 'Erode', 1),
(462, 718, 'Etah', 1),
(463, 719, 'Etawah', 1),
(464, 230, 'Faridabad', 1),
(465, 544, 'Faridkot', 1),
(466, 720, 'Farrukhabad', 1),
(467, 232, 'Fatehabad', 1),
(468, 545, 'Fatehgarh Sahib', 1),
(469, 721, 'Fatehpur', 1),
(470, 546, 'Fazilka', 1),
(471, 722, 'Firozabad', 1),
(472, 547, 'Firozpur', 1),
(473, 321, 'Gadag', 1),
(474, 419, 'Gadchiroli', 1),
(475, 516, 'Gajapati', 1),
(476, 280, 'Ganderbal', 1),
(477, 205, 'Gandhinagar', 1),
(478, 584, 'Ganganagar (Sri Ganganagar)', 1),
(479, 606, 'Gangtok', 1),
(480, 517, 'Ganjam', 1),
(481, 290, 'Garhwa', 1),
(482, 151, 'Gariaband', 1),
(483, 152, 'Gaurella-Pendra-Marwahi', 1),
(484, 723, 'Gautam Buddha Nagar (Noida)', 1),
(485, 112, 'Gaya', 1),
(486, 724, 'Ghaziabad', 1),
(487, 725, 'Ghazipur', 1),
(488, 206, 'Gir Somnath', 1),
(489, 291, 'Giridih', 1),
(490, 80, 'Goalpara', 1),
(491, 292, 'Godda', 1),
(492, 81, 'Golaghat', 1),
(493, 687, 'Gomati', 1),
(494, 726, 'Gonda', 1),
(495, 420, 'Gondia', 1),
(496, 113, 'Gopalganj', 1),
(497, 727, 'Gorakhpur', 1),
(498, 293, 'Gumla', 1),
(499, 372, 'Guna', 1),
(500, 22, 'Guntur', 1),
(501, 548, 'Gurdaspur', 1),
(502, 234, 'Gurugram', 1),
(503, 373, 'Gwalior', 1),
(504, 607, 'Gyalshing', 1),
(505, 82, 'Hailakandi', 1),
(506, 254, 'Hamirpur', 1),
(507, 728, 'Hamirpur', 1),
(508, 655, 'Hanumakonda (Warangal Urban)', 1),
(509, 585, 'Hanumangarh', 1),
(510, 729, 'Hapur (Panchsheel Nagar)', 1),
(511, 374, 'Harda', 1),
(512, 730, 'Hardoi', 1),
(513, 776, 'Haridwar', 1),
(514, 322, 'Hassan', 1),
(515, 731, 'Hathras (Mahamaya Nagar)', 1),
(516, 323, 'Haveri', 1),
(517, 294, 'Hazaribagh', 1),
(518, 421, 'Hingoli', 1),
(519, 235, 'Hisar', 1),
(520, 480, 'Hnahthial', 1),
(521, 83, 'Hojai', 1),
(522, 790, 'Hooghly', 1),
(523, 549, 'Hoshiarpur', 1),
(524, 791, 'Howrah', 1),
(525, 656, 'Hyderabad', 1),
(526, 342, 'Idukki', 1),
(527, 453, 'Imphal East', 1),
(528, 454, 'Imphal West', 1),
(529, 375, 'Indore', 1),
(530, 46, 'Itanagar', 1),
(531, 376, 'Jabalpur', 1),
(532, 518, 'Jagatsinghpur', 1),
(533, 657, 'Jagtial', 1),
(534, 586, 'Jaipur', 1),
(535, 587, 'Jaisalmer', 1),
(536, 519, 'Jajpur', 1),
(537, 550, 'Jalandhar', 1),
(538, 732, 'Jalaun (Orai)', 1),
(539, 422, 'Jalgaon', 1),
(540, 423, 'Jalna', 1),
(541, 588, 'Jalore', 1),
(542, 792, 'Jalpaiguri', 1),
(543, 265, 'Jammu', 1),
(544, 207, 'Jamnagar', 1),
(545, 295, 'Jamtara', 1),
(546, 114, 'Jamui', 1),
(547, 658, 'Jangaon', 1),
(548, 153, 'Janjgir-Champa', 1),
(549, 154, 'Jashpur', 1),
(550, 733, 'Jaunpur', 1),
(551, 659, 'Jayashankar Bhupalpally', 1),
(552, 115, 'Jehanabad', 1),
(553, 377, 'Jhabua', 1),
(554, 236, 'Jhajjar', 1),
(555, 589, 'Jhalawar', 1),
(556, 734, 'Jhansi', 1),
(557, 793, 'Jhargram', 1),
(558, 590, 'Jhunjhunu', 1),
(559, 237, 'Jind', 1),
(560, 455, 'Jiribam', 1),
(561, 591, 'Jodhpur', 1),
(562, 660, 'Jogulamba Gadwal', 1),
(563, 84, 'Jorhat', 1),
(564, 208, 'Junagadh', 1),
(565, 155, 'Kabirdham / Kawardha', 1),
(566, 116, 'Kaimur (Bhabua)', 1),
(567, 238, 'Kaithal', 1),
(568, 456, 'Kakching', 1),
(569, 23, 'Kakinada', 1),
(570, 324, 'Kalaburagi', 1),
(571, 520, 'Kalahandi', 1),
(572, 794, 'Kalimpong', 1),
(573, 622, 'Kallakurichi', 1),
(574, 661, 'Kamareddy', 1),
(576, 457, 'Kamjong', 1),
(577, 49, 'Kamle', 1),
(578, 86, 'Kamrup', 1),
(579, 85, 'Kamrup Metropolitan', 1),
(580, 623, 'Kancheepuram', 1),
(581, 521, 'Kandhamal', 1),
(582, 458, 'Kangpokpi', 1),
(583, 255, 'Kangra', 1),
(584, 156, 'Kanker', 1),
(585, 735, 'Kannauj', 1),
(586, 624, 'Kanniyakumari', 1),
(587, 343, 'Kannur', 1),
(588, 736, 'Kanpur Dehat (Ramabai Nagar)', 1),
(589, 737, 'Kanpur Nagar', 1),
(590, 551, 'Kapurthala', 1),
(591, 536, 'Karaikal', 1),
(592, 592, 'Karauli', 1),
(593, 87, 'Karbi Anglong', 1),
(594, 88, 'Karimganj', 1),
(595, 239, 'Karnal', 1),
(596, 625, 'Karur', 1),
(597, 344, 'Kasaragod', 1),
(598, 738, 'Kasganj (Kanshiram Nagar)', 1),
(599, 264, 'Kathua', 1),
(600, 117, 'Katihar', 1),
(601, 378, 'Katni', 1),
(602, 739, 'Kaushambi', 1),
(603, 593, 'Kekri', 1),
(604, 522, 'Kendrapara', 1),
(605, 523, 'Kendujhar (Keonjhar)', 1),
(606, 50, 'Keyi Panyor', 1),
(607, 118, 'Khagaria', 1),
(608, 157, 'Khairagarh-Chhuikhadan-Gandai', 1),
(609, 594, 'Khairthal -Tijara', 1),
(610, 663, 'Khammam', 1),
(611, 379, 'Khandwa', 1),
(612, 380, 'Khargone', 1),
(613, 481, 'Khawzawl', 1),
(614, 209, 'Kheda', 1),
(615, 524, 'Khordha', 1),
(616, 688, 'Khowai', 1),
(617, 296, 'Khunti', 1),
(618, 256, 'Kinnaur', 1),
(619, 491, 'Kiphire', 1),
(620, 119, 'Kishanganj', 1),
(621, 273, 'Kishtwar', 1),
(622, 325, 'Kodagu', 1),
(623, 297, 'Koderma', 1),
(624, 492, 'Kohima', 1),
(625, 89, 'Kokrajhar', 1),
(626, 326, 'Kolar', 1),
(627, 482, 'Kolasib', 1),
(628, 425, 'Kolhapur', 1),
(629, 795, 'Kolkata', 1),
(630, 345, 'Kollam', 1),
(631, 158, 'Kondagaon', 1),
(632, 327, 'Koppal', 1),
(633, 525, 'Koraput', 1),
(634, 159, 'Korba', 1),
(635, 160, 'Koriya', 1),
(636, 595, 'Kota', 1),
(637, 596, 'Kotputli-Behror', 1),
(638, 346, 'Kottayam', 1),
(639, 347, 'Kozhikode', 1),
(640, 47, 'Kra Daadi', 1),
(641, 24, 'Krishna', 1),
(642, 626, 'Krishnagiri', 1),
(643, 275, 'Kulgam', 1),
(644, 275, 'Kulgam', 1),
(645, 257, 'Kullu', 1),
(646, 664, 'Kumuram Bheem Asifabad', 1),
(647, 283, 'Kupwara', 1),
(648, 34, 'Kurnool', 1),
(649, 240, 'Kurukshetra', 1),
(650, 44, 'Kurung Kumey', 1),
(651, 192, 'Kushavati', 1),
(652, 740, 'Kushinagar (Padrauna)', 1),
(653, 224, 'Kutch', 1),
(654, 258, 'Lahaul and Spiti', 1),
(655, 90, 'Lakhimpur', 1),
(656, 741, 'Lakhimpur Kheri', 1),
(657, 120, 'Lakhisarai', 1),
(658, 354, 'Lakshadweep', 1),
(659, 742, 'Lalitpur', 1),
(660, 298, 'Latehar', 1),
(661, 426, 'Latur', 1),
(662, 483, 'Lawngtlai', 1),
(663, 56, 'Lepa-Rada', 1),
(664, 299, 'Lohardaga', 1),
(665, 61, 'Lohit', 1),
(666, 66, 'Longding', 1),
(667, 493, 'Longleng', 1),
(668, 60, 'Lower Dibang Valley', 1),
(669, 55, 'Lower Siang', 1),
(670, 48, 'Lower Subansiri', 1),
(671, 743, 'Lucknow', 1),
(672, 552, 'Ludhiana', 1),
(673, 484, 'Lunglei', 1),
(674, 121, 'Madhepura', 1),
(675, 122, 'Madhubani', 1),
(676, 627, 'Madurai', 1),
(677, 665, 'Mahabubabad', 1),
(678, 666, 'Mahabubnagar', 1),
(679, 744, 'Maharajganj', 1),
(680, 161, 'Mahasamund', 1),
(681, 538, 'Mahe', 1),
(682, 241, 'Mahendragarh', 1),
(683, 226, 'Mahisagar', 1),
(684, 745, 'Mahoba', 1),
(685, 381, 'Maihar', 1),
(686, 746, 'Mainpuri', 1),
(687, 91, 'Majuli', 1),
(688, 348, 'Malappuram', 1),
(689, 796, 'Malda', 1),
(690, 553, 'Malerkotla', 1),
(691, 526, 'Malkangiri', 1),
(692, 485, 'Mamit', 1),
(693, 667, 'Mancherial', 1),
(694, 259, 'Mandi', 1),
(695, 382, 'Mandla', 1),
(696, 383, 'Mandsaur', 1),
(697, 328, 'Mandya', 1),
(698, 162, 'Manendragarh-Chirmiri-Bharatpur', 1),
(699, 608, 'Mangan', 1),
(700, 554, 'Mansa', 1),
(701, 35, 'Markapuram', 1),
(702, 747, 'Mathura', 1),
(703, 748, 'Mau', 1),
(704, 384, 'Mauganj', 1),
(705, 628, 'Mayiladuthurai', 1),
(706, 527, 'Mayurbhanj', 1),
(707, 668, 'Medak', 1),
(708, 669, 'Medchal-Malkajgiri', 1),
(709, 749, 'Meerut', 1),
(710, 210, 'Mehsana', 1),
(711, 494, 'Meluri', 1),
(712, 750, 'Mirzapur', 1),
(713, 555, 'Moga', 1),
(714, 163, 'Mohla-Manpur-Ambagarh Chowki', 1),
(715, 495, 'Mokokchung', 1),
(716, 496, 'Mon', 1),
(717, 751, 'Moradabad', 1),
(718, 211, 'Morbi', 1),
(719, 385, 'Morena', 1),
(720, 92, 'Morigaon', 1),
(721, 670, 'Mulugu', 1),
(722, 427, 'Mumbai City', 1),
(723, 428, 'Mumbai Suburban', 1),
(724, 164, 'Mungeli', 1),
(725, 123, 'Munger (Monghyr)', 1),
(726, 797, 'Murshidabad', 1),
(727, 752, 'Muzaffarnagar', 1),
(728, 124, 'Muzaffarpur', 1),
(729, 329, 'Mysuru', 1),
(730, 528, 'Nabarangpur', 1),
(731, 798, 'Nadia', 1),
(732, 93, 'Nagaon', 1),
(733, 629, 'Nagapattinam', 1),
(734, 671, 'Nagarkurnool', 1),
(735, 597, 'Nagaur', 1),
(736, 429, 'Nagpur', 1),
(737, 777, 'Nainital', 1),
(738, 125, 'Nalanda', 1),
(739, 94, 'Nalbari', 1),
(740, 672, 'Nalgonda', 1),
(741, 630, 'Namakkal', 1),
(742, 609, 'Namchi', 1),
(743, 63, 'Namsai', 1),
(744, 430, 'Nanded', 1),
(745, 431, 'Nandurbar', 1),
(746, 36, 'Nandyal', 1),
(747, 673, 'Narayanpet', 1),
(748, 165, 'Narayanpur', 1),
(749, 212, 'Narmada', 1),
(750, 386, 'Narmadapuram', 1),
(751, 387, 'Narsinghpur', 1),
(752, 432, 'Nashik', 1),
(753, 213, 'Navsari', 1),
(754, 126, 'Nawada', 1),
(755, 530, 'Nayagarh', 1),
(756, 598, 'Neem Ka Thana', 1),
(757, 388, 'Neemuch', 1),
(758, 180, 'New Delhi', 1),
(759, 5, 'Nicobar', 1),
(760, 674, 'Nirmal', 1),
(761, 497, 'Niuland', 1),
(762, 389, 'Niwari', 1),
(763, 675, 'Nizamabad', 1),
(764, 498, 'Noklak', 1),
(765, 459, 'Noney', 1),
(766, 179, 'North', 1),
(767, 799, 'North 24 Parganas', 1),
(768, 7, 'North and Middle Andaman', 1),
(770, 186, 'North East', 1),
(771, 470, 'North Garo Hills', 1),
(772, 190, 'North Goa', 1),
(773, 689, 'North Tripura', 1),
(774, 185, 'North West', 1),
(775, 25, 'NTR', 1),
(776, 529, 'Nuapada', 1),
(777, 242, 'Nuh', 1),
(778, 178, 'Old Delhi', 1),
(779, 433, 'Osmanabad (Dharashiv)', 1),
(780, 184, 'Outer North', 1),
(781, 43, 'Pakke-Kessang', 1),
(782, 300, 'Pakur', 1),
(783, 610, 'Pakyong', 1),
(784, 349, 'Palakkad', 1),
(785, 301, 'Palamu', 1),
(786, 434, 'Palghar', 1),
(787, 599, 'Pali', 1),
(788, 26, 'Palnadu', 1),
(789, 243, 'Palwal', 1),
(790, 244, 'Panchkula', 1),
(791, 214, 'Panchmahal', 1),
(792, 390, 'Pandhurna', 1),
(793, 245, 'Panipat', 1),
(794, 391, 'Panna', 1),
(795, 45, 'Papum Pare', 1),
(796, 435, 'Parbhani', 1),
(797, 13, 'Parvathipuram Manyam', 1),
(798, 800, 'Paschim Bardhaman', 1),
(799, 801, 'Paschim Medinipur', 1),
(800, 215, 'Patan', 1),
(801, 350, 'Pathanamthitta', 1),
(802, 556, 'Pathankot', 1),
(803, 557, 'Patiala', 1),
(804, 127, 'Patna', 1),
(805, 778, 'Pauri Garhwal', 1),
(806, 676, 'Peddapalli', 1),
(807, 631, 'Perambalur', 1),
(808, 500, 'Peren', 1),
(809, 600, 'Phalodi', 1),
(810, 501, 'Phek', 1),
(811, 460, 'Pherzawl', 1),
(812, 753, 'Pilibhit', 1),
(813, 779, 'Pithoragarh', 1),
(814, 14, 'Polavaram', 1),
(815, 270, 'Poonch', 1),
(816, 216, 'Porbandar', 1),
(817, 27, 'Prakasam', 1),
(818, 601, 'Pratapgarh', 1),
(819, 754, 'Pratapgarh', 1),
(820, 696, 'Prayagraj (Allahabad)', 1),
(821, 539, 'Puducherry', 1),
(822, 632, 'Pudukkottai', 1),
(823, 276, 'Pulwama', 1),
(824, 436, 'Pune', 1),
(825, 802, 'Purba Bardhaman', 1),
(826, 803, 'Purba Medinipur', 1),
(827, 531, 'Puri', 1),
(828, 128, 'Purnia (Purnea)', 1),
(829, 804, 'Purulia', 1),
(830, 755, 'Rae Bareli', 1),
(831, 330, 'Raichur', 1),
(832, 437, 'Raigad', 1),
(833, 166, 'Raigarh', 1),
(834, 167, 'Raipur', 1),
(835, 392, 'Raisen', 1),
(836, 677, 'Rajanna Sircilla', 1),
(837, 393, 'Rajgarh', 1),
(838, 217, 'Rajkot', 1),
(839, 168, 'Rajnandgaon', 1),
(840, 269, 'Rajouri', 1),
(841, 602, 'Rajsamand', 1),
(842, 331, 'Ramanagara', 1),
(843, 633, 'Ramanathapuram', 1),
(844, 272, 'Ramban', 1),
(845, 302, 'Ramgarh', 1),
(846, 756, 'Rampur', 1),
(847, 303, 'Ranchi', 1),
(849, 678, 'Rangareddy', 1),
(850, 634, 'Ranipet', 1),
(851, 394, 'Ratlam', 1),
(852, 438, 'Ratnagiri', 1),
(853, 532, 'Rayagada', 1),
(854, 268, 'Reasi', 1),
(855, 395, 'Rewa', 1),
(856, 246, 'Rewari', 1),
(857, 471, 'Ri-Bhoi', 1),
(858, 247, 'Rohtak', 1),
(859, 129, 'Rohtas', 1),
(860, 780, 'Rudraprayag', 1),
(861, 558, 'Rupnagar', 1),
(862, 218, 'Sabarkantha', 1),
(863, 396, 'Sagar', 1),
(864, 757, 'Saharanpur', 1),
(865, 130, 'Saharsa', 1),
(866, 304, 'Sahibganj', 1),
(867, 559, 'Sahibzada Ajit Singh Nagar', 1),
(868, 488, 'Saiha', 1),
(869, 486, 'Saitual', 1),
(870, 169, 'Sakti', 1),
(871, 635, 'Salem', 1),
(872, 603, 'Salumbar', 1),
(873, 131, 'Samastipur', 1),
(874, 266, 'Samba', 1),
(875, 533, 'Sambalpur', 1),
(876, 759, 'Sambhal (Bhimnagar)', 1),
(877, 604, 'Sanchore', 1),
(878, 679, 'Sangareddy', 1),
(879, 439, 'Sangli', 1),
(880, 560, 'Sangrur', 1),
(881, 760, 'Sant Kabir Nagar', 1),
(882, 305, 'Saraikela Kharsawan', 1),
(883, 132, 'Saran', 1),
(884, 170, 'Sarangarh-Bilaigarh', 1),
(885, 441, 'Satara', 1),
(886, 440, 'Satara', 1),
(887, 397, 'Satna', 1),
(888, 605, 'Sawai Madhopur', 1),
(889, 461, 'Senapati', 1),
(890, 399, 'Seoni', 1),
(891, 690, 'Sepahijala', 1),
(892, 487, 'Serchhip', 1),
(893, 400, 'Shahdol', 1),
(894, 561, 'shadheed bhagat singh nagar', 1),
(895, 761, 'Shahjahanpur', 1),
(896, 401, 'Shajapur', 1),
(897, 502, 'Shamator', 1),
(898, 762, 'Shamli (Prabuddhanagar)', 1),
(899, 134, 'Sheohar', 1),
(900, 402, 'Sheopur', 1),
(901, 260, 'Shimla', 1),
(902, 52, 'Shi-Yomi', 1),
(903, 332, 'Shivamogga', 1),
(904, 403, 'Shivpuri', 1),
(905, 277, 'Shopian', 1),
(906, 764, 'Shravasti', 1),
(907, 54, 'Siang', 1),
(908, 765, 'Siddharthnagar', 1),
(909, 680, 'Siddipet', 1),
(910, 404, 'Sidhi', 1),
(911, 306, 'Simdega', 1),
(912, 443, 'Sindhudurg', 1),
(913, 405, 'Singrauli', 1),
(914, 261, 'Sirmaur', 1),
(915, 248, 'Sirsa', 1),
(916, 135, 'Sitamarhi', 1),
(917, 766, 'Sitapur', 1),
(918, 636, 'Sivaganga', 1),
(919, 95, 'Sivasagar', 1),
(920, 136, 'Siwan', 1),
(921, 262, 'Solan', 1),
(922, 444, 'Solapur', 1),
(923, 767, 'Sonbhadra (Robertsganj)', 1),
(924, 96, 'Sonipat', 1),
(925, 96, 'Sonitpur', 1),
(926, 611, 'Soreng', 1),
(927, 188, 'South', 1),
(928, 805, 'South 24 Parganas', 1),
(929, 177, 'South East', 1),
(930, 472, 'South Garo Hills', 1),
(931, 191, 'South Goa', 1),
(932, 97, 'South Salmara-Mankachar', 1),
(933, 183, 'South West', 1),
(934, 691, 'South Tripura', 1),
(936, 473, 'South West Garo Hills', 1),
(937, 474, 'South West Khasi Hills', 1),
(938, 562, 'Sri Muktsar Sahib', 1),
(939, 28, 'Sri Potti Sriramulu Nellore / Nellore', 1),
(940, 37, 'Sri Sathya Sai', 1),
(941, 15, 'Srikakulam', 1),
(942, 279, 'Srinagar', 1),
(943, 534, 'Subarnapur (Sonepur)', 1),
(944, 171, 'Sukma', 1),
(945, 768, 'Sultanpur', 1),
(946, 535, 'Sundargarh', 1),
(947, 137, 'Supaul', 1),
(948, 172, 'Surajpur', 1),
(949, 219, 'Surat', 1),
(950, 220, 'Surendranagar', 1),
(951, 681, 'Suryapet', 1),
(953, 462, 'Tamenglong', 1),
(954, 98, 'Tamulpur', 1),
(955, 221, 'Tapi', 1),
(956, 563, 'Tarn Taran', 1),
(957, 39, 'Tawang', 1),
(958, 781, 'Tehri Garhwal', 1),
(959, 463, 'Tengnoupal', 1),
(960, 637, 'Tenkasi', 1),
(961, 445, 'Thane', 1),
(962, 638, 'Thanjavur', 1),
(963, 639, 'The Nilgiris', 1),
(964, 640, 'Theni', 1),
(965, 641, 'Thiruvallur', 1),
(966, 351, 'Thiruvananthapuram', 1),
(967, 642, 'Thiruvarur', 1),
(968, 643, 'Thoothukudi (Tuticorin)', 1),
(969, 464, 'Thoubal', 1),
(970, 352, 'Thrissur', 1),
(971, 406, 'Tikamgarh', 1),
(972, 99, 'Tinsukia', 1),
(973, 65, 'Tirap', 1),
(974, 645, 'Tiruchirappalli (Trichy)', 1),
(975, 646, 'Tirunelveli', 1),
(976, 647, 'Tirupathur', 1),
(977, 38, 'Tirupati', 1),
(978, 648, 'Tiruppur', 1),
(979, 649, 'Tiruvannamalai', 1),
(980, 503, 'Tseminyü', 1),
(981, 504, 'Tuensang', 1),
(982, 333, 'Tumakuru', 1),
(983, 100, 'Udalguri', 1),
(984, 782, 'Udham Singh Nagar', 1),
(985, 267, 'Udhampur', 1),
(986, 335, 'Udupi', 1),
(987, 407, 'Ujjain', 1),
(988, 465, 'Ukhrul', 1),
(989, 408, 'Umaria', 1),
(990, 263, 'Una 14e9', 1),
(991, 692, 'Unakoti', 1),
(992, 769, 'Unnao', 1),
(993, 57, 'Upper Siang', 1),
(994, 51, 'Upper Subansiri', 1),
(995, 806, 'Uttar Dinajpur', 1),
(996, 336, 'Uttara Kannada', 1),
(997, 783, 'Uttarkashi', 1),
(998, 222, 'Vadodara', 1),
(999, 138, 'Vaishali', 1),
(1000, 223, 'Valsad', 1),
(1001, 770, 'Varanasi', 1),
(1002, 225, 'Vav-Tharad', 1),
(1003, 650, 'Vellore', 1),
(1004, 409, 'Vidisha', 1),
(1005, 337, 'Vijayanagara', 1),
(1006, 338, 'Vijayapura', 1),
(1008, 682, 'Vikarabad', 1),
(1009, 651, 'Viluppuram', 1),
(1010, 652, 'Virudhunagar', 1),
(1011, 16, 'Visakhapatnam', 1),
(1012, 17, 'Vizianagaram', 1),
(1013, 683, 'Wanaparthy', 1),
(1014, 684, 'Warangal (Rural)', 1),
(1015, 446, 'Wardha', 1),
(1016, 447, 'Washim', 1),
(1017, 353, 'Wayanad', 1),
(1018, 139, 'West Champaran', 1),
(1019, 189, 'West dcb4', 1),
(1020, 475, 'West Garo Hills', 1),
(1021, 29, 'West Godavari', 1),
(1022, 476, 'West Jaintia Hills', 1),
(1023, 40, 'West Kameng', 1),
(1024, 101, 'West Karbi Anglong', 1),
(1025, 477, 'west khasi Hills', 1),
(1026, 53, 'West Siang', 1),
(1027, 307, 'West Singhbhum', 1),
(1028, 693, 'West Tripura', 1),
(1029, 505, 'Wokha', 1),
(1030, 685, 'Yadadri Bhuvanagiri', 1),
(1031, 339, 'Yadgir', 1),
(1032, 250, 'Yamunanagar', 1),
(1033, 540, 'Yanam', 1),
(1034, 448, 'Yavatmal', 1),
(1035, 33, 'YSR Kadapa', 1),
(1036, 506, 'Zünheboto', 1);

-- --------------------------------------------------------

--
-- Table structure for table `countries`
--

CREATE TABLE `countries` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `countries`
--

INSERT INTO `countries` (`id`, `name`, `status`) VALUES
(1, 'India', 1);

-- --------------------------------------------------------

--
-- Table structure for table `courses`
--

CREATE TABLE `courses` (
  `id` int(10) UNSIGNED NOT NULL,
  `code` varchar(20) NOT NULL,
  `duration_years` int(11) DEFAULT 0,
  `name` varchar(150) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `category` enum('diploma','certificate','vocational') NOT NULL,
  `duration_months` tinyint(3) UNSIGNED DEFAULT NULL,
  `duration_days` int(11) DEFAULT 0,
  `duration_semesters` int(11) DEFAULT 0,
  `course_fee` decimal(10,2) DEFAULT 0.00,
  `status` tinyint(1) DEFAULT 1,
  `registration_fee` decimal(10,2) DEFAULT 0.00,
  `franchise_fee` decimal(10,2) DEFAULT 0.00,
  `partner_commission` decimal(10,2) DEFAULT 0.00,
  `exam_fee` decimal(10,2) DEFAULT 0.00,
  `eligibility` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `thumbnail` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `courses`
--

INSERT INTO `courses` (`id`, `code`, `duration_years`, `name`, `category_id`, `category`, `duration_months`, `duration_days`, `duration_semesters`, `course_fee`, `status`, `registration_fee`, `franchise_fee`, `partner_commission`, `exam_fee`, `eligibility`, `description`, `thumbnail`) VALUES
(1, 'DCA', 1, 'DIPLOMA IN COMPUTER APPLICATION', 29, 'diploma', 0, 0, 0, 5000.00, 1, 1000.00, 1000.00, 1500.00, 1500.00, '10th', '*DCA – Diploma in Computer Applications*\r\n\r\nDCA is a 1-year diploma program designed to provide foundational knowledge and practical skills in computer applications and IT tools used in offices and businesses.\r\n\r\nStudents learn computer fundamentals, operating systems, MS Office suite including Word, Excel, PowerPoint, internet and email handling, database basics with MS Access, fundamentals of programming, Tally with GST, and basic concepts of web design and computer networking.\r\n\r\nThe course emphasizes hands-on training to make students job-ready for roles like Computer Operator, Data Entry Operator, Office Assistant, and Accounts Assistant in both private and government sectors.\r\n\r\n*Eligibility*: 10th/12th pass from any stream  \r\n*Duration*: 1 Year', 'COURSE_1777948375_519.jpeg'),
(4, 'CMD', 1, 'CERTIFICATE IN MEDICAL DRESSER', 30, 'diploma', 0, 0, 0, 25000.00, 1, 1000.00, 2500.00, 1000.00, 1500.00, '10TH', '*Certificate in Medical Dresser*\r\n\r\nCertificate in Medical Dresser is a 6-month to 1-year paramedical skill program that trains students to assist doctors and nurses in wound care, bandaging, and basic patient support in hospitals and clinics.\r\n\r\nStudents learn first aid techniques, dressing and bandaging of wounds, wound cleaning and infection control, application of plasters and splints, handling of surgical instruments, sterilization procedures, patient hygiene, bed-making, and basic nursing assistance.\r\n\r\nThe course focuses on practical, hands-on training to prepare candidates for immediate employment in hospitals, nursing homes, primary health centers, dispensaries, and emergency care units.\r\n\r\n*Job Roles*: Medical Dresser, Ward Assistant, First Aid Technician, OPD Assistant  \r\n*Eligibility*: 10th pass  \r\n*Duration*: 6 Months – 1 Year', 'COURSE_1777949660_209.jpeg'),
(5, 'DMLT', 2, 'DIPLOMA IN MEDICAL LABORATORY TECHNOLOGY', 30, 'diploma', 0, 0, 0, 35000.00, 1, 1000.00, 4000.00, 1500.00, 3000.00, '12th', '*DMLT – Diploma in Medical Laboratory Technology*\r\n\r\nDMLT is a 2-year paramedical diploma program that trains students to become skilled Medical Lab Technicians. The course focuses on teaching clinical laboratory techniques used to diagnose, treat, and prevent diseases.\r\n\r\nStudents learn to perform and analyze medical tests on blood, urine, tissue, and other body fluids using advanced lab equipment. Key subjects include Hematology, Clinical Pathology, Microbiology, Biochemistry, Blood Banking, and Histopathology.\r\n\r\nThe program combines classroom learning with extensive hands-on training in modern labs and hospital internships. It prepares graduates for job roles as Lab Technician, Phlebotomist, and Lab Assistant in hospitals, diagnostic centers, pathology labs, and blood banks.\r\n\r\n*Eligibility*: 10+2 with Science  \r\n*Duration*: 2 Years', 'COURSE_1777949696_137.jpeg');

-- --------------------------------------------------------

--
-- Table structure for table `course_categories`
--

CREATE TABLE `course_categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `franchise_fee` decimal(10,2) DEFAULT 0.00,
  `status` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `course_categories`
--

INSERT INTO `course_categories` (`id`, `name`, `franchise_fee`, `status`, `created_at`) VALUES
(27, 'SECONDARY LEVEL', 25000.00, 1, '2026-05-01 11:06:38'),
(28, 'SR.SECONDARY LEVEL', 30000.00, 1, '2026-05-01 11:07:16'),
(29, 'SKILL AND VOCATIONAL PROGRAMES', 51000.00, 1, '2026-05-01 11:10:04'),
(30, 'PARAMEDICAL AND HEALTH SCIENCE', 35000.00, 1, '2026-05-01 11:10:55');

-- --------------------------------------------------------

--
-- Table structure for table `course_durations`
--

CREATE TABLE `course_durations` (
  `id` int(10) UNSIGNED NOT NULL,
  `duration_label` varchar(50) NOT NULL,
  `years` int(11) DEFAULT 0,
  `months` int(11) DEFAULT NULL,
  `days` int(11) DEFAULT 0,
  `status` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `course_durations`
--

INSERT INTO `course_durations` (`id`, `duration_label`, `years`, `months`, `days`, `status`) VALUES
(1, '6 Month', 0, 6, 0, 1),
(2, '2 Year', 0, 24, 0, 1),
(3, '1 Year', 1, 0, 0, 1),
(4, '3 Years', 3, 0, 0, 1);

-- --------------------------------------------------------

--
-- Table structure for table `districts`
--

CREATE TABLE `districts` (
  `id` int(10) UNSIGNED NOT NULL,
  `state_id` int(10) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `districts`
--

INSERT INTO `districts` (`id`, `state_id`, `name`, `status`) VALUES
(5, 6, 'Nicobar', 1),
(7, 6, 'North and Middle Andaman', 1),
(11, 7, 'Alluri Sitharama Raju', 1),
(12, 7, 'Anakapalli', 1),
(13, 7, 'Parvathipuram Manyam', 1),
(14, 7, 'Polavaram', 1),
(15, 7, 'Srikakulam', 1),
(16, 7, 'Visakhapatnam', 1),
(17, 7, 'Vizianagaram', 1),
(18, 7, 'Bapatla', 1),
(19, 7, 'Dr. B.R. Ambedkar Konaseema / Konaseema', 1),
(20, 7, 'East Godavari', 1),
(21, 7, 'Eluru', 1),
(22, 7, 'Guntur', 1),
(23, 7, 'Kakinada', 1),
(24, 7, 'Krishna', 1),
(25, 7, 'NTR', 1),
(26, 7, 'Palnadu', 1),
(27, 7, 'Prakasam', 1),
(28, 7, 'Sri Potti Sriramulu Nellore / Nellore', 1),
(29, 7, 'West Godavari', 1),
(30, 7, 'Ananthapuramu', 1),
(31, 7, 'Annamayya', 1),
(32, 7, 'Chittoor', 1),
(33, 7, 'YSR Kadapa', 1),
(34, 7, 'Kurnool', 1),
(35, 7, 'Markapuram', 1),
(36, 7, 'Nandyal', 1),
(37, 7, 'Sri Sathya Sai', 1),
(38, 7, 'Tirupati', 1),
(39, 8, 'Tawang', 1),
(40, 8, 'West Kameng', 1),
(41, 8, 'Bichom', 1),
(42, 8, 'East Kameng', 1),
(43, 8, 'Pakke-Kessang', 1),
(44, 8, 'Kurung Kumey', 1),
(45, 8, 'Papum Pare', 1),
(46, 8, 'Itanagar', 1),
(47, 8, 'Kra Daadi', 1),
(48, 8, 'Lower Subansiri', 1),
(49, 8, 'Kamle', 1),
(50, 8, 'Keyi Panyor', 1),
(51, 8, 'Upper Subansiri', 1),
(52, 8, 'Shi-Yomi', 1),
(53, 8, 'West Siang', 1),
(54, 8, 'Siang', 1),
(55, 8, 'Lower Siang', 1),
(56, 8, 'Lepa-Rada', 1),
(57, 8, 'Upper Siang', 1),
(58, 8, 'East Siang', 1),
(59, 8, 'Dibang Valley', 1),
(60, 8, 'Lower Dibang Valley', 1),
(61, 8, 'Lohit', 1),
(62, 8, 'Anjaw', 1),
(63, 8, 'Namsai', 1),
(64, 8, 'Changlang', 1),
(65, 8, 'Tirap', 1),
(66, 8, 'Longding', 1),
(67, 9, 'Baksa', 1),
(68, 9, 'Bajali', 1),
(69, 9, 'Barpeta', 1),
(70, 9, 'Biswanath', 1),
(71, 9, 'Bongaigaon', 1),
(72, 9, 'Cachar', 1),
(73, 9, 'Charaideo', 1),
(74, 9, 'Chirang', 1),
(75, 9, 'Darrang', 1),
(76, 9, 'Dhemaji', 1),
(77, 9, 'Dhubri', 1),
(78, 9, 'Dibrugarh', 1),
(79, 9, 'Dima Hasao', 1),
(80, 9, 'Goalpara', 1),
(81, 9, 'Golaghat', 1),
(82, 9, 'Hailakandi', 1),
(83, 9, 'Hojai', 1),
(84, 9, 'Jorhat', 1),
(85, 9, 'Kamrup Metropolitan', 1),
(86, 9, 'Kamrup', 1),
(87, 8, 'Karbi Anglong', 1),
(88, 9, 'Karimganj', 1),
(89, 9, 'Kokrajhar', 1),
(90, 9, 'Lakhimpur', 1),
(91, 9, 'Majuli', 1),
(92, 9, 'Morigaon', 1),
(93, 9, 'Nagaon', 1),
(94, 9, 'Nalbari', 1),
(95, 9, 'Sivasagar', 1),
(96, 9, 'Sonitpur', 1),
(97, 9, 'South Salmara-Mankachar', 1),
(98, 9, 'Tamulpur', 1),
(99, 9, 'Tinsukia', 1),
(100, 9, 'Udalguri', 1),
(101, 9, 'West Karbi Anglong', 1),
(102, 10, 'Araria', 1),
(103, 10, 'Arwal', 1),
(105, 10, 'Banka', 1),
(106, 10, 'Begusarai', 1),
(107, 10, 'Bhagalpur', 1),
(108, 10, 'Bhojpur', 1),
(109, 10, 'Buxar', 1),
(110, 10, 'Darbhanga', 1),
(111, 10, 'East Champaran', 1),
(112, 10, 'Gaya', 1),
(113, 10, 'Gopalganj', 1),
(114, 10, 'Jamui', 1),
(115, 10, 'Jehanabad', 1),
(116, 10, 'Kaimur (Bhabua)', 1),
(117, 10, 'Katihar', 1),
(118, 10, 'Khagaria', 1),
(119, 10, 'Kishanganj', 1),
(120, 10, 'Lakhisarai', 1),
(121, 10, 'Madhepura', 1),
(122, 10, 'Madhubani', 1),
(123, 10, 'Munger (Monghyr)', 1),
(124, 10, 'Muzaffarpur', 1),
(125, 10, 'Nalanda', 1),
(126, 10, 'Nawada', 1),
(127, 10, 'Patna', 1),
(128, 10, 'Purnia (Purnea)', 1),
(129, 10, 'Rohtas', 1),
(130, 10, 'Saharsa', 1),
(131, 10, 'Samastipur', 1),
(132, 10, 'Saran', 1),
(133, 10, 'Sheikhpura', 1),
(134, 10, 'Sheohar', 1),
(135, 10, 'Sitamarhi', 1),
(136, 10, 'Siwan', 1),
(137, 10, 'Supaul', 1),
(138, 10, 'Vaishali', 1),
(139, 10, 'West Champaran', 1),
(140, 11, 'Chandigarh', 1),
(141, 12, 'Balod', 1),
(142, 12, 'Baloda Bazar-Bhatapara', 1),
(144, 12, 'Bastar', 1),
(145, 12, 'Bemetara', 1),
(146, 12, 'Bijapur', 1),
(147, 12, 'Bilaspur', 1),
(148, 12, 'Dantewada', 1),
(149, 12, 'Dhamtari', 1),
(150, 12, 'Durg', 1),
(151, 12, 'Gariaband', 1),
(152, 12, 'Gaurella-Pendra-Marwahi', 1),
(153, 12, 'Janjgir-Champa', 1),
(154, 12, 'Jashpur', 1),
(155, 12, 'Kabirdham / Kawardha', 1),
(156, 12, 'Kanker', 1),
(157, 12, 'Khairagarh-Chhuikhadan-Gandai', 1),
(158, 12, 'Kondagaon', 1),
(159, 12, 'Korba', 1),
(160, 12, 'Koriya', 1),
(161, 12, 'Mahasamund', 1),
(162, 12, 'Manendragarh-Chirmiri-Bharatpur', 1),
(163, 12, 'Mohla-Manpur-Ambagarh Chowki', 1),
(164, 12, 'Mungeli', 1),
(165, 12, 'Narayanpur', 1),
(166, 12, 'Raigarh', 1),
(167, 12, 'Raipur', 1),
(168, 12, 'Rajnandgaon', 1),
(169, 12, 'Sakti', 1),
(170, 12, 'Sarangarh-Bilaigarh', 1),
(171, 12, 'Sukma', 1),
(172, 12, 'Surajpur', 1),
(173, 12, 'Surguja', 1),
(174, 13, 'Dadar and Nagar Haveli', 1),
(175, 14, 'Daman', 1),
(176, 14, 'Diu', 1),
(177, 15, 'South East', 1),
(178, 15, 'Old Delhi', 1),
(179, 15, 'North', 1),
(180, 15, 'New Delhi', 1),
(181, 15, 'Central', 1),
(182, 15, 'Central North', 1),
(183, 15, 'South West', 1),
(184, 15, 'Outer North', 1),
(185, 15, 'North West', 1),
(186, 15, 'North East', 1),
(187, 15, 'East', 1),
(188, 15, 'South', 1),
(189, 15, 'West dcb4', 1),
(190, 16, 'North Goa', 1),
(191, 16, 'South Goa', 1),
(192, 16, 'Kushavati', 1),
(193, 17, 'Ahmedabad', 1),
(194, 17, 'Amreli', 1),
(195, 17, 'Anand', 1),
(196, 17, 'Aravalli', 1),
(197, 17, 'Banaskantha', 1),
(198, 17, 'Bharuch', 1),
(199, 17, 'Bhavnagar', 1),
(200, 17, 'Botad', 1),
(201, 17, 'Chhota Udaipur', 1),
(202, 17, 'Dahod', 1),
(203, 17, 'Dang', 1),
(204, 17, 'Devbhoomi Dwarka', 1),
(205, 17, 'Gandhinagar', 1),
(206, 17, 'Gir Somnath', 1),
(207, 17, 'Jamnagar', 1),
(208, 17, 'Junagadh', 1),
(209, 17, 'Kheda', 1),
(210, 17, 'Mehsana', 1),
(211, 17, 'Morbi', 1),
(212, 17, 'Narmada', 1),
(213, 17, 'Navsari', 1),
(214, 17, 'Panchmahal', 1),
(215, 17, 'Patan', 1),
(216, 17, 'Porbandar', 1),
(217, 17, 'Rajkot', 1),
(218, 17, 'Sabarkantha', 1),
(219, 17, 'Surat', 1),
(220, 17, 'Surendranagar', 1),
(221, 17, 'Tapi', 1),
(222, 17, 'Vadodara', 1),
(223, 17, 'Valsad', 1),
(224, 17, 'Kutch', 1),
(225, 17, 'Vav-Tharad', 1),
(226, 17, 'Mahisagar', 1),
(227, 18, 'Ambala', 1),
(228, 18, 'Bhiwani', 1),
(229, 18, 'Charkhi Dadri', 1),
(230, 18, 'Faridabad', 1),
(232, 18, 'Fatehabad', 1),
(234, 18, 'Gurugram', 1),
(235, 18, 'Hisar', 1),
(236, 18, 'Jhajjar', 1),
(237, 18, 'Jind', 1),
(238, 17, 'Kaithal', 1),
(239, 18, 'Karnal', 1),
(240, 18, 'Kurukshetra', 1),
(241, 18, 'Mahendragarh', 1),
(242, 18, 'Nuh', 1),
(243, 18, 'Palwal', 1),
(244, 18, 'Panchkula', 1),
(245, 18, 'Panipat', 1),
(246, 18, 'Rewari', 1),
(247, 18, 'Rohtak', 1),
(248, 18, 'Sirsa', 1),
(249, 18, 'Sonipat', 1),
(250, 18, 'Yamunanagar', 1),
(251, 19, 'Bilaspur', 1),
(252, 19, 'Chamba', 1),
(254, 19, 'Hamirpur', 1),
(255, 19, 'Kangra', 1),
(256, 19, 'Kinnaur', 1),
(257, 19, 'Kullu', 1),
(258, 19, 'Lahaul and Spiti', 1),
(259, 19, 'Mandi', 1),
(260, 19, 'Shimla', 1),
(261, 19, 'Sirmaur', 1),
(262, 19, 'Solan', 1),
(263, 19, 'Una 14e9', 1),
(264, 20, 'Kathua', 1),
(265, 20, 'Jammu', 1),
(266, 20, 'Samba', 1),
(267, 20, 'Udhampur', 1),
(268, 20, 'Reasi', 1),
(269, 20, 'Rajouri', 1),
(270, 20, 'Poonch', 1),
(271, 20, 'Doda', 1),
(272, 20, 'Ramban', 1),
(273, 20, 'Kishtwar', 1),
(274, 20, 'Anantnag', 1),
(275, 20, 'Kulgam', 1),
(276, 20, 'Pulwama', 1),
(277, 20, 'Shopian', 1),
(278, 20, 'Budgam', 1),
(279, 20, 'Srinagar', 1),
(280, 20, 'Ganderbal', 1),
(281, 20, 'Bandipora', 1),
(282, 20, 'Baramulla', 1),
(283, 20, 'Kupwara', 1),
(284, 21, 'Bokaro', 1),
(285, 21, 'Chatra', 1),
(286, 21, 'Deoghar', 1),
(287, 21, 'Dhanbad', 1),
(288, 21, 'Dumka', 1),
(289, 21, 'East Singhbhum', 1),
(290, 21, 'Garhwa', 1),
(291, 21, 'Giridih', 1),
(292, 21, 'Godda', 1),
(293, 21, 'Gumla', 1),
(294, 21, 'Hazaribagh', 1),
(295, 21, 'Jamtara', 1),
(296, 21, 'Khunti', 1),
(297, 21, 'Koderma', 1),
(298, 21, 'Latehar', 1),
(299, 21, 'Lohardaga', 1),
(300, 21, 'Pakur', 1),
(301, 21, 'Palamu', 1),
(302, 21, 'Ramgarh', 1),
(303, 21, 'Ranchi', 1),
(304, 21, 'Sahibganj', 1),
(305, 21, 'Saraikela Kharsawan', 1),
(306, 21, 'Simdega', 1),
(307, 21, 'West Singhbhum', 1),
(308, 22, 'Bagalkot', 1),
(309, 22, 'Ballari', 1),
(310, 22, 'Belagavi', 1),
(311, 22, 'Bengaluru Rural', 1),
(312, 22, 'Bengaluru Urban', 1),
(313, 22, 'Bidar', 1),
(314, 22, 'Chamarajanagar', 1),
(315, 22, 'Chikkaballapur', 1),
(316, 22, 'Chikkamagaluru', 1),
(317, 22, 'Chitradurga', 1),
(318, 22, 'Dakshina Kannada', 1),
(319, 22, 'Davanagere', 1),
(320, 22, 'Dharwad', 1),
(321, 22, 'Gadag', 1),
(322, 22, 'Hassan', 1),
(323, 22, 'Haveri', 1),
(324, 22, 'Kalaburagi', 1),
(325, 22, 'Kodagu', 1),
(326, 22, 'Kolar', 1),
(327, 22, 'Koppal', 1),
(328, 22, 'Mandya', 1),
(329, 22, 'Mysuru', 1),
(330, 22, 'Raichur', 1),
(331, 22, 'Ramanagara', 1),
(332, 22, 'Shivamogga', 1),
(333, 22, 'Tumakuru', 1),
(335, 22, 'Udupi', 1),
(336, 22, 'Uttara Kannada', 1),
(337, 22, 'Vijayanagara', 1),
(338, 22, 'Vijayapura', 1),
(339, 22, 'Yadgir', 1),
(340, 23, 'Alappuzha', 1),
(341, 23, 'Ernakulam', 1),
(342, 23, 'Idukki', 1),
(343, 23, 'Kannur', 1),
(344, 23, 'Kasaragod', 1),
(345, 23, 'Kollam', 1),
(346, 23, 'Kottayam', 1),
(347, 23, 'Kozhikode', 1),
(348, 23, 'Malappuram', 1),
(349, 23, 'Palakkad', 1),
(350, 23, 'Pathanamthitta', 1),
(351, 23, 'Thiruvananthapuram', 1),
(352, 23, 'Thrissur', 1),
(353, 23, 'Wayanad', 1),
(354, 24, 'Lakshadweep', 1),
(355, 25, 'Agar Malwa', 1),
(356, 25, 'Alirajpur', 1),
(357, 25, 'Anuppur', 1),
(358, 25, 'Ashoknagar', 1),
(359, 25, 'Balaghat', 1),
(360, 25, 'Barwani', 1),
(361, 25, 'Betul', 1),
(362, 25, 'Bhind', 1),
(363, 25, 'Bhopal', 1),
(364, 25, 'Burhanpur', 1),
(365, 25, 'Chhatarpur', 1),
(366, 25, 'Chhindwara', 1),
(367, 25, 'Damoh', 1),
(368, 25, 'Datia', 1),
(369, 25, 'Dewas', 1),
(370, 25, 'Dhar', 1),
(371, 25, 'Dindori', 1),
(372, 25, 'Guna', 1),
(373, 25, 'Gwalior', 1),
(374, 25, 'Harda', 1),
(375, 25, 'Indore', 1),
(376, 25, 'Jabalpur', 1),
(377, 25, 'Jhabua', 1),
(378, 25, 'Katni', 1),
(379, 25, 'Khandwa', 1),
(380, 25, 'Khargone', 1),
(381, 25, 'Maihar', 1),
(382, 25, 'Mandla', 1),
(383, 25, 'Mandsaur', 1),
(384, 25, 'Mauganj', 1),
(385, 25, 'Morena', 1),
(386, 25, 'Narmadapuram', 1),
(387, 25, 'Narsinghpur', 1),
(388, 25, 'Neemuch', 1),
(389, 25, 'Niwari', 1),
(390, 25, 'Pandhurna', 1),
(391, 25, 'Panna', 1),
(392, 25, 'Raisen', 1),
(393, 25, 'Rajgarh', 1),
(394, 25, 'Ratlam', 1),
(395, 25, 'Rewa', 1),
(396, 25, 'Sagar', 1),
(397, 25, 'Satna', 1),
(398, 25, 'Sehore', 1),
(399, 25, 'Seoni', 1),
(400, 25, 'Shahdol', 1),
(401, 25, 'Shajapur', 1),
(402, 25, 'Sheopur', 1),
(403, 25, 'Shivpuri', 1),
(404, 25, 'Sidhi', 1),
(405, 25, 'Singrauli', 1),
(406, 25, 'Tikamgarh', 1),
(407, 25, 'Ujjain', 1),
(408, 25, 'Umaria', 1),
(409, 25, 'Vidisha', 1),
(410, 26, 'Ahmednagar', 1),
(411, 26, 'Akola', 1),
(412, 26, 'Amravati', 1),
(413, 26, 'Aurangabad', 1),
(414, 26, 'Beed', 1),
(415, 26, 'Bhandara', 1),
(416, 26, 'Buldhana', 1),
(417, 26, 'Chandrapur', 1),
(418, 26, 'Dhule', 1),
(419, 26, 'Gadchiroli', 1),
(420, 26, 'Gondia', 1),
(421, 26, 'Hingoli', 1),
(422, 26, 'Jalgaon', 1),
(423, 26, 'Jalna', 1),
(425, 26, 'Kolhapur', 1),
(426, 26, 'Latur', 1),
(427, 26, 'Mumbai City', 1),
(428, 26, 'Mumbai Suburban', 1),
(429, 26, 'Nagpur', 1),
(430, 26, 'Nanded', 1),
(431, 26, 'Nandurbar', 1),
(432, 26, 'Nashik', 1),
(433, 26, 'Osmanabad (Dharashiv)', 1),
(434, 26, 'Palghar', 1),
(435, 26, 'Parbhani', 1),
(436, 26, 'Pune', 1),
(437, 26, 'Raigad', 1),
(438, 26, 'Ratnagiri', 1),
(439, 26, 'Sangli', 1),
(441, 26, 'Satara', 1),
(443, 26, 'Sindhudurg', 1),
(444, 26, 'Solapur', 1),
(445, 26, 'Thane', 1),
(446, 26, 'Wardha', 1),
(447, 26, 'Washim', 1),
(448, 26, 'Yavatmal', 1),
(449, 27, 'Bishnupur', 1),
(450, 27, 'Chandel', 1),
(451, 27, 'Churachandpur', 1),
(452, 27, 'Churachandpur', 1),
(453, 27, 'Imphal East', 1),
(454, 27, 'Imphal West', 1),
(455, 27, 'Jiribam', 1),
(456, 27, 'Kakching', 1),
(457, 27, 'Kamjong', 1),
(458, 27, 'Kangpokpi', 1),
(459, 27, 'Noney', 1),
(460, 27, 'Pherzawl', 1),
(461, 27, 'Senapati', 1),
(462, 27, 'Tamenglong', 1),
(463, 27, 'Tengnoupal', 1),
(464, 27, 'Thoubal', 1),
(465, 27, 'Ukhrul', 1),
(466, 28, 'East Garo Hills', 1),
(467, 28, 'East Jaintia Hills', 1),
(468, 28, 'East Khasi Hills', 1),
(469, 28, 'Eastern West Khasi Hills', 1),
(470, 28, 'North Garo Hills', 1),
(471, 28, 'Ri-Bhoi', 1),
(472, 28, 'South Garo Hills', 1),
(473, 28, 'South West Garo Hills', 1),
(474, 28, 'South West Khasi Hills', 1),
(475, 28, 'West Garo Hills', 1),
(476, 28, 'West Jaintia Hills', 1),
(477, 28, 'West Khasi Hills', 1),
(478, 29, 'Aizawl', 1),
(479, 29, 'Champhai', 1),
(480, 29, 'Hnahthial', 1),
(481, 29, 'Khawzawl', 1),
(482, 29, 'Kolasib', 1),
(483, 29, 'Lawngtlai', 1),
(484, 29, 'Lunglei', 1),
(485, 29, 'Mamit', 1),
(486, 29, 'Saitual', 1),
(487, 29, 'Serchhip', 1),
(488, 29, 'Saiha', 1),
(489, 30, 'Chümoukedima', 1),
(490, 30, 'Dimapur', 1),
(491, 30, 'Kiphire', 1),
(492, 30, 'Kohima', 1),
(493, 30, 'Longleng', 1),
(494, 30, 'Meluri', 1),
(495, 30, 'Mokokchung', 1),
(496, 30, 'Mon', 1),
(497, 30, 'Niuland', 1),
(498, 30, 'Noklak', 1),
(500, 30, 'Peren', 1),
(501, 30, 'Phek', 1),
(502, 30, 'Shamator', 1),
(503, 30, 'Tseminyü', 1),
(504, 30, 'Tuensang', 1),
(505, 30, 'Wokha', 1),
(506, 30, 'Zünheboto', 1),
(507, 31, 'Angul', 1),
(508, 31, 'Balangir', 1),
(509, 31, 'Balasore (Baleswar)', 1),
(510, 31, 'Bargarh', 1),
(511, 31, 'Bhadrak', 1),
(512, 31, 'Boudh', 1),
(513, 31, 'Cuttack', 1),
(514, 31, 'Deogarh (Debagarh)', 1),
(515, 31, 'Dhenkanal', 1),
(516, 31, 'Gajapati', 1),
(517, 31, 'Ganjam', 1),
(518, 31, 'Jagatsinghpur', 1),
(519, 31, 'Jajpur', 1),
(520, 31, 'Kalahandi', 1),
(521, 31, 'Kandhamal', 1),
(522, 31, 'Kendrapara', 1),
(523, 31, 'Kendujhar (Keonjhar)', 1),
(524, 31, 'Khordha', 1),
(525, 31, 'Koraput', 1),
(526, 31, 'Malkangiri', 1),
(527, 31, 'Mayurbhanj', 1),
(528, 31, 'Nabarangpur', 1),
(529, 31, 'Nuapada', 1),
(530, 31, 'Nayagarh', 1),
(531, 31, 'Puri', 1),
(532, 31, 'Rayagada', 1),
(533, 31, 'Sambalpur', 1),
(534, 31, 'Subarnapur (Sonepur)', 1),
(535, 31, 'Sundargarh', 1),
(536, 32, 'Karaikal', 1),
(538, 32, 'Mahe', 1),
(539, 32, 'Puducherry', 1),
(540, 32, 'Yanam', 1),
(541, 33, 'Amritsar', 1),
(542, 33, 'Barnala', 1),
(543, 33, 'Bathinda', 1),
(544, 33, 'Faridkot', 1),
(545, 33, 'Fatehgarh Sahib', 1),
(546, 33, 'Fazilka', 1),
(547, 33, 'Firozpur', 1),
(548, 33, 'Gurdaspur', 1),
(549, 33, 'Hoshiarpur', 1),
(550, 33, 'Jalandhar', 1),
(551, 33, 'Kapurthala', 1),
(552, 33, 'Ludhiana', 1),
(553, 33, 'Malerkotla', 1),
(554, 33, 'Mansa', 1),
(555, 33, 'Moga', 1),
(556, 33, 'Pathankot', 1),
(557, 33, 'Patiala', 1),
(558, 33, 'Rupnagar', 1),
(559, 33, 'Sahibzada Ajit Singh Nagar', 1),
(560, 33, 'Sangrur', 1),
(561, 33, 'Shaheed Bhagat Singh Nagar', 1),
(562, 33, 'Sri Muktsar Sahib', 1),
(563, 33, 'Tarn Taran', 1),
(564, 34, 'Ajmer', 1),
(565, 34, 'Alwar', 1),
(566, 34, 'Anupgarh', 1),
(567, 34, 'Balotra', 1),
(568, 34, 'Banswara', 1),
(569, 34, 'Baran', 1),
(570, 34, 'Barmer', 1),
(571, 34, 'Beawar', 1),
(572, 34, 'Bharatpur', 1),
(573, 34, 'Bharatpur', 1),
(574, 34, 'Bhilwara', 1),
(575, 34, 'Bikaner', 1),
(576, 34, 'Bundi', 1),
(577, 34, 'Chittorgarh', 1),
(578, 34, 'Churu', 1),
(579, 34, 'Dausa', 1),
(580, 34, 'Dholpur', 1),
(581, 34, 'Didwana-Kuchaman', 1),
(582, 34, 'Dudu', 1),
(583, 34, 'Dungarpur', 1),
(584, 34, 'Ganganagar (Sri Ganganagar)', 1),
(585, 34, 'Hanumangarh', 1),
(586, 34, 'Jaipur', 1),
(587, 34, 'Jaisalmer', 1),
(588, 34, 'Jalore', 1),
(589, 34, 'Jhalawar', 1),
(590, 34, 'Jhunjhunu', 1),
(591, 34, 'Jodhpur', 1),
(592, 34, 'Karauli', 1),
(593, 34, 'Kekri', 1),
(594, 34, 'Khairthal-Tijara', 1),
(595, 34, 'Kota', 1),
(596, 34, 'Kotputli-Behror', 1),
(597, 34, 'Nagaur', 1),
(598, 34, 'Neem Ka Thana', 1),
(599, 34, 'Pali', 1),
(600, 34, 'Phalodi', 1),
(601, 34, 'Pratapgarh', 1),
(602, 34, 'Rajsamand', 1),
(603, 34, 'Salumbar', 1),
(604, 34, 'Sanchore', 1),
(605, 34, 'Sawai Madhopur', 1),
(606, 35, 'Gangtok', 1),
(607, 35, 'Gyalshing', 1),
(608, 35, 'Mangan', 1),
(609, 35, 'Namchi', 1),
(610, 35, 'Pakyong', 1),
(611, 35, 'Soreng', 1),
(612, 36, 'Ariyalur', 1),
(613, 36, 'Chengalpattu', 1),
(614, 36, 'Chennai', 1),
(615, 36, 'Coimbatore', 1),
(616, 36, 'Cuddalore', 1),
(617, 36, 'Coimbatore', 1),
(618, 36, 'Cuddalore', 1),
(619, 36, 'Dharmapuri', 1),
(620, 36, 'Dindigul', 1),
(621, 36, 'Erode', 1),
(622, 36, 'Kallakurichi', 1),
(623, 36, 'Kancheepuram', 1),
(624, 36, 'Kanniyakumari', 1),
(625, 36, 'Karur', 1),
(626, 36, 'Krishnagiri', 1),
(627, 36, 'Madurai', 1),
(628, 36, 'Mayiladuthurai', 1),
(629, 36, 'Nagapattinam', 1),
(630, 36, 'Namakkal', 1),
(631, 36, 'Perambalur', 1),
(632, 36, 'Pudukkottai', 1),
(633, 36, 'Ramanathapuram', 1),
(634, 36, 'Ranipet', 1),
(635, 36, 'Salem', 1),
(636, 36, 'Sivaganga', 1),
(637, 36, 'Tenkasi', 1),
(638, 36, 'Thanjavur', 1),
(639, 36, 'The Nilgiris', 1),
(640, 36, 'Theni', 1),
(641, 36, 'Thiruvallur', 1),
(642, 36, 'Thiruvarur', 1),
(643, 36, 'Thoothukudi (Tuticorin)', 1),
(645, 36, 'Tiruchirappalli (Trichy)', 1),
(646, 36, 'Tirunelveli', 1),
(647, 36, 'Tirupathur', 1),
(648, 36, 'Tiruppur', 1),
(649, 36, 'Tiruvannamalai', 1),
(650, 36, 'Vellore', 1),
(651, 36, 'Viluppuram', 1),
(652, 36, 'Virudhunagar', 1),
(653, 37, 'Adilabad', 1),
(654, 37, 'Bhadradri Kothagudem', 1),
(655, 37, 'Hanumakonda (Warangal Urban)', 1),
(656, 37, 'Hyderabad', 1),
(657, 37, 'Jagtial', 1),
(658, 37, 'Jangaon', 1),
(659, 37, 'Jayashankar Bhupalpally', 1),
(660, 37, 'Jogulamba Gadwal', 1),
(661, 37, 'Kamareddy', 1),
(663, 37, 'Khammam', 1),
(664, 37, 'Kumuram Bheem Asifabad', 1),
(665, 37, 'Mahabubabad', 1),
(666, 37, 'Mahabubnagar', 1),
(667, 37, 'Mancherial', 1),
(668, 37, 'Medak', 1),
(669, 37, 'Medchal-Malkajgiri', 1),
(670, 37, 'Mulugu', 1),
(671, 37, 'Nagarkurnool', 1),
(672, 37, 'Nalgonda', 1),
(673, 37, 'Narayanpet', 1),
(674, 37, 'Nirmal', 1),
(675, 37, 'Nizamabad', 1),
(676, 37, 'Peddapalli', 1),
(677, 37, 'Rajanna Sircilla', 1),
(678, 37, 'Rangareddy', 1),
(679, 37, 'Sangareddy', 1),
(680, 37, 'Siddipet', 1),
(681, 37, 'Suryapet', 1),
(682, 37, 'Vikarabad', 1),
(683, 37, 'Wanaparthy', 1),
(684, 37, 'Warangal (Rural)', 1),
(685, 37, 'Yadadri Bhuvanagiri', 1),
(686, 38, 'Dhalai', 1),
(687, 38, 'Gomati', 1),
(688, 38, 'Khowai', 1),
(689, 38, 'North Tripura', 1),
(690, 38, 'Sepahijala', 1),
(691, 38, 'South Tripura', 1),
(692, 38, 'Unakoti', 1),
(693, 38, 'West Tripura', 1),
(694, 39, 'Agra', 1),
(695, 39, 'Aligarh', 1),
(696, 39, 'Prayagraj (Allahabad)', 1),
(697, 39, 'Ambedkar Nagar', 1),
(698, 39, 'Amethi', 1),
(699, 39, 'Amroha (JP Nagar)', 1),
(700, 39, 'Auraiya', 1),
(701, 39, 'Ayodhya (Faizabad)', 1),
(702, 39, 'Azamgarh', 1),
(703, 39, 'Baghpat', 1),
(704, 39, 'Bahraich', 1),
(705, 39, 'Ballia', 1),
(706, 39, 'Balrampur', 1),
(707, 39, 'Banda', 1),
(708, 39, 'Barabanki', 1),
(709, 39, 'Bareilly', 1),
(710, 39, 'Basti', 1),
(711, 39, 'Bhadohi (Sant Ravidas Nagar)', 1),
(712, 39, 'Bijnor', 1),
(713, 39, 'Budaun', 1),
(714, 39, 'Bulandshahr', 1),
(715, 39, 'Chandauli', 1),
(716, 39, 'Chitrakoot', 1),
(717, 39, 'Deoria', 1),
(718, 39, 'Etah', 1),
(719, 39, 'Etawah', 1),
(720, 39, 'Farrukhabad', 1),
(721, 39, 'Fatehpur', 1),
(722, 39, 'Firozabad', 1),
(723, 39, 'Gautam Buddha Nagar (Noida)', 1),
(724, 39, 'Ghaziabad', 1),
(725, 39, 'Ghazipur', 1),
(726, 39, 'Gonda', 1),
(727, 39, 'Gorakhpur', 1),
(728, 39, 'Hamirpur', 1),
(729, 39, 'Hapur (Panchsheel Nagar)', 1),
(730, 39, 'Hardoi', 1),
(731, 39, 'Hathras (Mahamaya Nagar)', 1),
(732, 39, 'Jalaun (Orai)', 1),
(733, 39, 'Jaunpur', 1),
(734, 39, 'Jhansi', 1),
(735, 39, 'Kannauj', 1),
(736, 39, 'Kanpur Dehat (Ramabai Nagar)', 1),
(737, 39, 'Kanpur Nagar', 1),
(738, 39, 'Kasganj (Kanshiram Nagar)', 1),
(739, 39, 'Kaushambi', 1),
(740, 39, 'Kushinagar (Padrauna)', 1),
(741, 39, 'Lakhimpur Kheri', 1),
(742, 39, 'Lalitpur', 1),
(743, 39, 'Lucknow', 1),
(744, 39, 'Maharajganj', 1),
(745, 39, 'Mahoba', 1),
(746, 39, 'Mainpuri', 1),
(747, 39, 'Mathura', 1),
(748, 39, 'Mau', 1),
(749, 39, 'Meerut', 1),
(750, 39, 'Mirzapur', 1),
(751, 39, 'Moradabad', 1),
(752, 39, 'Muzaffarnagar', 1),
(753, 39, 'Pilibhit', 1),
(754, 39, 'Pratapgarh', 1),
(755, 39, 'Rae Bareli', 1),
(756, 39, 'Rampur', 1),
(757, 39, 'Saharanpur', 1),
(759, 39, 'Sambhal (Bhimnagar)', 1),
(760, 39, 'Sant Kabir Nagar', 1),
(761, 39, 'Shahjahanpur', 1),
(762, 39, 'Shamli (Prabuddhanagar)', 1),
(764, 39, 'Shravasti', 1),
(765, 39, 'Siddharthnagar', 1),
(766, 39, 'Sitapur', 1),
(767, 39, 'Sonbhadra (Robertsganj)', 1),
(768, 39, 'Sultanpur', 1),
(769, 39, 'Unnao', 1),
(770, 39, 'Varanasi', 1),
(771, 40, 'Almora', 1),
(772, 40, 'Bageshwar', 1),
(773, 40, 'Chamoli', 1),
(774, 40, 'Champawat', 1),
(775, 40, 'Dehradun', 1),
(776, 40, 'Haridwar', 1),
(777, 40, 'Nainital', 1),
(778, 40, 'Pauri Garhwal', 1),
(779, 40, 'Pithoragarh', 1),
(780, 40, 'Rudraprayag', 1),
(781, 40, 'Tehri Garhwal', 1),
(782, 40, 'Udham Singh Nagar', 1),
(783, 40, 'Uttarkashi', 1),
(784, 41, 'Alipurduar', 1),
(785, 41, 'Bankura', 1),
(786, 41, 'Birbhum', 1),
(787, 41, 'Cooch Behar', 1),
(788, 41, 'Dakshin Dinajpur', 1),
(789, 41, 'Darjeeling', 1),
(790, 41, 'Hooghly', 1),
(791, 41, 'Howrah', 1),
(792, 41, 'Jalpaiguri', 1),
(793, 41, 'Jhargram', 1),
(794, 41, 'Kalimpong', 1),
(795, 41, 'Kolkata', 1),
(796, 41, 'Malda', 1),
(797, 41, 'Murshidabad', 1),
(798, 41, 'Nadia', 1),
(799, 41, 'North 24 Parganas', 1),
(800, 41, 'Paschim Bardhaman', 1),
(801, 41, 'Paschim Medinipur', 1),
(802, 41, 'Purba Bardhaman', 1),
(803, 41, 'Purba Medinipur', 1),
(804, 41, 'Purulia', 1),
(805, 41, 'South 24 Parganas', 1),
(806, 41, 'Uttar Dinajpur', 1),
(807, 12, 'Balrampur', 1);

-- --------------------------------------------------------

--
-- Table structure for table `document_templates`
--

CREATE TABLE `document_templates` (
  `id` int(10) UNSIGNED NOT NULL,
  `type` enum('id_card','admit_card','marksheet','certificate') NOT NULL,
  `name` varchar(150) NOT NULL,
  `content_config` longtext DEFAULT NULL,
  `background_image` varchar(255) DEFAULT NULL,
  `status` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `enquiries`
--

CREATE TABLE `enquiries` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED DEFAULT NULL,
  `state` varchar(100) DEFAULT NULL,
  `district` varchar(100) DEFAULT NULL,
  `center_id` int(10) UNSIGNED DEFAULT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `mobile` varchar(15) DEFAULT NULL,
  `course_id` int(10) UNSIGNED DEFAULT NULL,
  `session_id` int(10) UNSIGNED DEFAULT NULL,
  `course_category` varchar(100) DEFAULT NULL,
  `gender` enum('male','female','other') DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `qualification` varchar(100) DEFAULT NULL,
  `prob_admission_date` date DEFAULT NULL,
  `address` text DEFAULT NULL,
  `country_id` int(10) UNSIGNED DEFAULT NULL,
  `state_id` int(10) UNSIGNED DEFAULT NULL,
  `district_id` int(10) UNSIGNED DEFAULT NULL,
  `city_id` int(10) UNSIGNED DEFAULT NULL,
  `pincode` varchar(10) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `approval_status` enum('new','contacted','closed') DEFAULT 'new',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `source` enum('manual','online') DEFAULT 'manual'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `enquiries`
--

INSERT INTO `enquiries` (`id`, `user_id`, `state`, `district`, `center_id`, `full_name`, `email`, `mobile`, `course_id`, `session_id`, `course_category`, `gender`, `dob`, `qualification`, `prob_admission_date`, `address`, `country_id`, `state_id`, `district_id`, `city_id`, `pincode`, `message`, `approval_status`, `created_at`, `status`, `source`) VALUES
(12, NULL, 'Delhi', 'New Delhi', 1, 'RAUSHAN RAAJ', 'raushan@gmail.com', '7548596587', 1, NULL, 'diploma', 'male', '1995-01-01', '12th', '2026-04-23', 'Patna, Bihar', 1, 1, 1, 1, NULL, 'just for testing', 'closed', '2026-04-28 22:06:53', 1, 'manual'),
(13, NULL, 'Delhi', 'New Delhi', 1, 'SHUBHAM', 'shubham@gmail.com', '7878787878', 1, 1, 'diploma', 'male', '2026-04-29', '12th', '2026-04-30', 'Prayagraj Uttar Pradesh', 1, 2, 2, 2, '000875', 'hello', 'new', '2026-04-30 14:48:24', 1, 'manual'),
(14, NULL, NULL, NULL, 1, 'John Student', 'john@auto.com', '9876543210', 1, 1, 'diploma', 'male', '2000-01-01', '12th', '2026-06-01', 'Test Address', 1, 1, 1, 1, '110001', 'Automated Test Message', 'new', '2026-04-30 21:00:28', 1, 'online'),
(16, NULL, NULL, NULL, NULL, 'RAUSHAN RAAJ', 'raushan@example.com', '7548598587', 1, 1, 'DCA', 'male', '1998-05-15', 'Post Graduation', NULL, 'Sector 5, Noida', 1, 1, 1, NULL, '201301', 'Test online enquiry', 'new', '2026-04-30 23:41:48', 1, 'online'),
(17, NULL, NULL, NULL, NULL, 'ANITA KUMARI', 'anita@example.com', '9898989898', 1, 1, 'DCA', 'female', '2002-08-20', '12th', NULL, 'Malviya Nagar, Jaipur', 1, 1, 1, NULL, '302017', 'Interested in course', 'contacted', '2026-04-30 23:41:48', 1, 'online'),
(18, NULL, 'Uttar Pradesh', 'Agra', 2, 'VIKAS YADAV', 'vikas@example.com', '8888888888', 1, 10, 'diploma', 'male', '1995-12-10', 'Diploma', NULL, 'Lucknow, UP', 1, 39, 694, 15, '226001', 'Need details about fees', 'new', '2026-04-30 23:41:48', 1, 'manual');

-- --------------------------------------------------------

--
-- Table structure for table `exams`
--

CREATE TABLE `exams` (
  `id` int(11) NOT NULL,
  `session_year_id` int(11) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `exam_name` varchar(150) NOT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `description` text DEFAULT NULL,
  `status` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `exams`
--

INSERT INTO `exams` (`id`, `session_year_id`, `category_id`, `exam_name`, `start_date`, `end_date`, `description`, `status`, `created_at`) VALUES
(1, 10, 31, 'Test Paper', '2026-05-05', '2026-05-06', 'just test Paper', 1, '2026-05-04 22:43:31');

-- --------------------------------------------------------

--
-- Table structure for table `fee_allocations`
--

CREATE TABLE `fee_allocations` (
  `id` int(11) NOT NULL,
  `admission_id` int(11) NOT NULL,
  `fee_group_id` int(11) NOT NULL,
  `allocated_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `fee_collections`
--

CREATE TABLE `fee_collections` (
  `id` int(11) NOT NULL,
  `admission_id` int(11) NOT NULL,
  `fee_allocation_id` int(11) DEFAULT NULL,
  `amount_paid` decimal(10,2) NOT NULL,
  `payment_mode` enum('cash','online','cheque','other') NOT NULL DEFAULT 'cash',
  `transaction_id` varchar(100) DEFAULT NULL,
  `payment_date` date NOT NULL,
  `remarks` text DEFAULT NULL,
  `collected_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `fee_groups`
--

CREATE TABLE `fee_groups` (
  `id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `description` text DEFAULT NULL,
  `status` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `fee_group_items`
--

CREATE TABLE `fee_group_items` (
  `id` int(11) NOT NULL,
  `fee_group_id` int(11) NOT NULL,
  `fee_type_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `fee_types`
--

CREATE TABLE `fee_types` (
  `id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `fee_code` varchar(50) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `status` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `fee_types`
--

INSERT INTO `fee_types` (`id`, `name`, `fee_code`, `description`, `status`, `created_at`) VALUES
(1, 'Admission Fee', 'ADMIN_FEES', 'This fees type for admission.', 1, '2026-05-04 20:05:25');

-- --------------------------------------------------------

--
-- Table structure for table `franchises`
--

CREATE TABLE `franchises` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `partner_id` int(11) DEFAULT NULL,
  `center_code` varchar(50) DEFAULT NULL,
  `center_name` varchar(150) DEFAULT NULL,
  `director_name` varchar(100) DEFAULT NULL,
  `director_mobile` varchar(15) DEFAULT NULL,
  `aadhar_no` varchar(20) DEFAULT NULL,
  `id_proof` varchar(255) DEFAULT NULL,
  `aadhar_front` varchar(255) DEFAULT NULL,
  `aadhar_back` varchar(255) DEFAULT NULL,
  `approval_doc` varchar(255) DEFAULT NULL,
  `director_photo` varchar(255) DEFAULT NULL,
  `signature` varchar(255) DEFAULT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `phone_alt` varchar(15) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `qualification` varchar(100) DEFAULT NULL,
  `estd_date` date DEFAULT NULL,
  `address` text DEFAULT NULL,
  `state_id` int(11) DEFAULT NULL,
  `district_id` int(11) DEFAULT NULL,
  `city_id` int(11) DEFAULT NULL,
  `pincode` varchar(10) DEFAULT NULL,
  `computers` int(11) DEFAULT 0,
  `teachers` int(11) DEFAULT 0,
  `rooms` int(11) DEFAULT 0,
  `area_sqft` int(11) DEFAULT 0,
  `internet_type` varchar(50) DEFAULT NULL,
  `photo_front` varchar(255) DEFAULT NULL,
  `photo_lab` varchar(255) DEFAULT NULL,
  `photo_office` varchar(255) DEFAULT NULL,
  `wallet_balance` decimal(15,2) DEFAULT 0.00,
  `status` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `franchises`
--

INSERT INTO `franchises` (`id`, `user_id`, `partner_id`, `center_code`, `center_name`, `director_name`, `director_mobile`, `aadhar_no`, `id_proof`, `aadhar_front`, `aadhar_back`, `approval_doc`, `director_photo`, `signature`, `phone`, `phone_alt`, `email`, `qualification`, `estd_date`, `address`, `state_id`, `district_id`, `city_id`, `pincode`, `computers`, `teachers`, `rooms`, `area_sqft`, `internet_type`, `photo_front`, `photo_lab`, `photo_office`, `wallet_balance`, `status`, `created_at`) VALUES
(1, 11, 1, 'ICSTIR924', 'Anne Hendrix', 'Nichole Contreras', '9898989898', NULL, NULL, NULL, NULL, NULL, 'DIRECTOR_PHOTO_1777559394_602.jpeg', NULL, '6392111086', NULL, 'rexuxarug@mailinator.com', NULL, NULL, 'fhgfhg', NULL, NULL, NULL, '222129', 65, 0, 1, 98, 'Leased Line', '', '', '', 0.00, 0, '2026-04-30 14:29:54'),
(2, 12, 1, 'ICSTIR953', 'CCS University Meerut', 'Chaudhari Charan Singh', '9876548569', '758965874586', '', '', '', '', 'DIRECTOR_PHOTO_1777575660_622.jpeg', '', '7546958754', '7548596571', 'ccsu@gmail.com', 'MBA', '2026-05-01', 'CCS university', 39, 694, 15, '754856', 50, 0, 5, 500, 'Broadband / Fiber', 'PHOTO_FRONT_1777575660_496.jpg', 'PHOTO_LAB_1777575660_867.jpg', 'PHOTO_OFFICE_1777575660_507.jpeg', 0.00, 1, '2026-04-30 19:01:00'),
(3, 13, 1, 'ICSTIR7928', 'Raina Sports Academy', 'Suresh Raina', '8456325748', '845796587412', 'ID_PROOF_1777845688_784.avif', 'AADHAR_FRONT_1777845688_889.avif', '', 'APPROVAL_DOC_1777845688_436.pdf', 'DIRECTOR_PHOTO_1777845688_976.jpeg', 'SIGNATURE_1777845688_124.jpg', '7546958783', '8456325768', 'raina@gmail.com', 'B.Sc', '2026-05-03', 'Aagra', 39, 694, 15, '208001', 5, 2, 1, 500, '', 'PHOTO_FRONT_1777845688_244.jpg', 'PHOTO_LAB_1777845688_220.jpg', 'PHOTO_OFFICE_1777845688_457.jpeg', 100000.00, 1, '2026-05-03 22:01:28'),
(4, 17, 2, 'ICSTIR2346', 'NARAYANA PARAMEDICAL', 'HEMCHANDRA', '9304468476', '582678054578', 'ID_PROOF_1777972973_293.jpeg', 'AADHAR_FRONT_1777972973_424.jpeg', 'AADHAR_BACK_1777972973_326.jpeg', 'APPROVAL_DOC_1777972973_406.jpeg', 'DIRECTOR_PHOTO_1777972973_920.jpeg', 'SIGNATURE_1777972973_668.jpeg', '7004070014', '9304468476', 'hemchandrakumar3@gmail.com', 'GRADUATE', '2023-06-01', 'ROJAPAR ARWAL BIHAR', 10, 103, 280, '804401', 10, 15, 5, 2000, '', 'PHOTO_FRONT_1777972973_125.jpeg', 'PHOTO_LAB_1777972973_344.jpeg', 'PHOTO_OFFICE_1777972973_286.jpeg', 19500.00, 1, '2026-05-05 09:22:53');

-- --------------------------------------------------------

--
-- Table structure for table `franchise_documents`
--

CREATE TABLE `franchise_documents` (
  `id` int(11) NOT NULL,
  `franchise_id` int(11) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `type` varchar(100) DEFAULT NULL,
  `file_name` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `franchise_documents`
--

INSERT INTO `franchise_documents` (`id`, `franchise_id`, `title`, `type`, `file_name`, `created_at`) VALUES
(1, 4, 'update', 'Other', 'DOC_1777977715_383.jpeg', '2026-05-05 10:41:55'),
(2, 1, 'update', 'Registration Certificate', 'DOC_1777977792_885.jpeg', '2026-05-05 10:43:12'),
(4, 4, 'latest attach', 'Other', 'DOC_1777978259_112.png', '2026-05-05 10:50:59'),
(5, 2, 'new', 'Agreement Paper', 'DOC_1777978301_551.jpeg', '2026-05-05 10:51:41'),
(6, 4, 'past', 'Agreement Paper', 'DOC_1777978344_944.png', '2026-05-05 10:52:24');

-- --------------------------------------------------------

--
-- Table structure for table `franchise_enquiries`
--

CREATE TABLE `franchise_enquiries` (
  `id` int(11) NOT NULL,
  `director_name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `center_name` varchar(150) DEFAULT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `phone_alt` varchar(15) DEFAULT NULL,
  `qualification` varchar(255) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `state_id` int(11) DEFAULT NULL,
  `district_id` int(11) DEFAULT NULL,
  `city_id` int(11) DEFAULT NULL,
  `pincode` varchar(10) DEFAULT NULL,
  `computers` int(11) DEFAULT 0,
  `teachers` int(11) DEFAULT 0,
  `rooms` int(11) DEFAULT 0,
  `area_sqft` int(11) DEFAULT 0,
  `followup_date` date DEFAULT NULL,
  `estimate_fees` decimal(10,2) DEFAULT 0.00,
  `present_students` int(11) DEFAULT 0,
  `comments` text DEFAULT NULL,
  `dir_photo` varchar(255) DEFAULT NULL,
  `dir_sig` varchar(255) DEFAULT NULL,
  `dir_id_card` varchar(255) DEFAULT NULL,
  `aadhar_front` varchar(255) DEFAULT NULL,
  `aadhar_back` varchar(255) DEFAULT NULL,
  `labs_photo` varchar(255) DEFAULT NULL,
  `approval_doc` varchar(255) DEFAULT NULL,
  `center_photo` varchar(255) DEFAULT NULL,
  `approval_status` enum('new','interested','converted','closed') DEFAULT 'new',
  `status` tinyint(1) DEFAULT 1,
  `source` enum('manual','online') DEFAULT 'manual',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `franchise_enquiries`
--

INSERT INTO `franchise_enquiries` (`id`, `director_name`, `email`, `center_name`, `phone`, `phone_alt`, `qualification`, `address`, `state_id`, `district_id`, `city_id`, `pincode`, `computers`, `teachers`, `rooms`, `area_sqft`, `followup_date`, `estimate_fees`, `present_students`, `comments`, `dir_photo`, `dir_sig`, `dir_id_card`, `aadhar_front`, `aadhar_back`, `labs_photo`, `approval_doc`, `center_photo`, `approval_status`, `status`, `source`, `created_at`) VALUES
(3, 'John Doe', 'john@center.com', 'Elite Education Center', '9999999999', '', 'MBA', 'Main Street, Delhi', 39, 694, 15, '110002', 50, 10, 20, 500, '2026-05-07', 5000.00, 10, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'new', 1, 'manual', '2026-04-30 23:41:48'),
(4, 'Jane Smith', 'jane@portal.com', 'Creative Learning Hub', '7777777777', NULL, 'MA', 'Park Road, Mumbai', 1, 1, NULL, '400001', 0, 0, 0, 0, NULL, 0.00, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'new', 1, 'online', '2026-04-30 23:41:48'),
(5, 'Amit Shah', 'amit@franchise.in', 'Tech World Institute', '6666666666', NULL, 'B.Tech', 'HSR Layout, Bangalore', 1, 1, NULL, '560102', 0, 0, 0, 0, NULL, 0.00, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'interested', 0, 'online', '2026-04-30 23:41:48'),
(6, 'Suresh Raina', 'suresh@raina.com', 'Raina Sports Academy', '5555555555', '8459632587', 'B.Sc', 'Civil Lines, Kanpur', 39, 694, 20, '208001', 50, 20, 30, 500, '2026-05-14', 25000.00, 10, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'closed', 1, 'manual', '2026-04-30 23:41:48'),
(7, 'MAHINDRA KUMAR', 'hdhdhdh@gmail.com', 'JAI PARAMEDICAL', '8404965685', NULL, 'BA', 'PATNA BIHAR MISSION SCHOOL JEHANABAD 876664', 17, 193, 0, '876568', 10, 10, 5, 1000, NULL, 0.00, 0, 'City: Ahmedabad', 'PHOTO_1777687404_7825.jpg', 'SIG_1777687404_8785.jpg', NULL, 'AF_1777687404_8691.jpg', 'AB_1777687405_5793.jpg', 'LAB_1777687406_6477.jpg', 'APPROV_1777687406_1764.jpg', 'CENTER_1777687406_3089.jpg', 'converted', 1, 'online', '2026-05-02 02:03:26');

-- --------------------------------------------------------

--
-- Table structure for table `franchise_gateways`
--

CREATE TABLE `franchise_gateways` (
  `id` int(11) NOT NULL,
  `franchise_id` int(11) NOT NULL,
  `gateway_provider` varchar(50) DEFAULT NULL,
  `currency` varchar(10) DEFAULT 'INR',
  `key_id` varchar(255) DEFAULT NULL,
  `secret_key` varchar(255) DEFAULT NULL,
  `status` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `franchise_notices`
--

CREATE TABLE `franchise_notices` (
  `id` int(11) NOT NULL,
  `subject` varchar(255) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `attachment` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `franchise_registration_transactions`
--

CREATE TABLE `franchise_registration_transactions` (
  `id` int(11) NOT NULL,
  `franchise_id` int(11) DEFAULT NULL,
  `txn_id` varchar(100) DEFAULT NULL,
  `amount` decimal(15,2) DEFAULT NULL,
  `method` varchar(50) DEFAULT NULL,
  `status` enum('success','pending','failed') DEFAULT 'success',
  `remarks` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `franchise_wallet_ledger`
--

CREATE TABLE `franchise_wallet_ledger` (
  `id` int(11) NOT NULL,
  `franchise_id` int(11) DEFAULT NULL,
  `amount` decimal(15,2) DEFAULT NULL,
  `type` enum('credit','debit') DEFAULT NULL,
  `description` text DEFAULT NULL,
  `status` enum('pending','success','failed') DEFAULT 'success',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `franchise_wallet_ledger`
--

INSERT INTO `franchise_wallet_ledger` (`id`, `franchise_id`, `amount`, `type`, `description`, `status`, `created_at`) VALUES
(1, 4, 20000.00, 'credit', 'added by admin', 'success', '2026-05-05 10:39:33'),
(2, 4, 500.00, 'debit', 'deduct by admin', 'success', '2026-05-05 10:40:08'),
(3, 3, 100000.00, 'credit', 'add by admin', 'success', '2026-05-05 10:50:13');

-- --------------------------------------------------------

--
-- Table structure for table `franchise_wallet_requests`
--

CREATE TABLE `franchise_wallet_requests` (
  `id` int(11) NOT NULL,
  `franchise_id` int(11) DEFAULT NULL,
  `amount` decimal(15,2) DEFAULT NULL,
  `payment_method` varchar(50) DEFAULT NULL,
  `transaction_id` varchar(100) DEFAULT NULL,
  `proof_file` varchar(255) DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `admin_remarks` text DEFAULT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `frontend_achievements`
--

CREATE TABLE `frontend_achievements` (
  `id` int(11) NOT NULL,
  `student_name` varchar(150) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `status` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `frontend_achievements`
--

INSERT INTO `frontend_achievements` (`id`, `student_name`, `title`, `description`, `photo`, `status`, `created_at`) VALUES
(1, 'Rahul Kumar', 'State Level Topper', 'lorem Ip some just testing we are the form.', 'ACHIVE_1777841928_348.png', 1, '2026-05-03 20:58:48');

-- --------------------------------------------------------

--
-- Table structure for table `frontend_affiliations`
--

CREATE TABLE `frontend_affiliations` (
  `id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `logo` varchar(255) NOT NULL,
  `link` varchar(255) DEFAULT NULL,
  `status` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `frontend_affiliations`
--

INSERT INTO `frontend_affiliations` (`id`, `name`, `logo`, `link`, `status`, `created_at`) VALUES
(1, 'certificate logo', 'AFFILIATE_1777841853_125.png', 'https://education.techbyrk.com/', 1, '2026-05-03 20:57:33');

-- --------------------------------------------------------

--
-- Table structure for table `frontend_banners`
--

CREATE TABLE `frontend_banners` (
  `id` int(11) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `subtitle` varchar(255) DEFAULT NULL,
  `image` varchar(255) NOT NULL,
  `laptop_image` varchar(255) DEFAULT NULL,
  `mobile_image` varchar(255) DEFAULT NULL,
  `tablet_image` varchar(255) DEFAULT NULL,
  `link` varchar(255) DEFAULT NULL,
  `status` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `frontend_banners`
--

INSERT INTO `frontend_banners` (`id`, `title`, `subtitle`, `image`, `laptop_image`, `mobile_image`, `tablet_image`, `link`, `status`, `created_at`) VALUES
(3, '', '', 'BANNER_1777926461_513.webp', 'LAP_BANNER_1777926461_662.webp', '', '', '', 1, '2026-05-04 20:27:41');

-- --------------------------------------------------------

--
-- Table structure for table `frontend_certificates`
--

CREATE TABLE `frontend_certificates` (
  `id` int(11) NOT NULL,
  `center_name` varchar(255) DEFAULT NULL,
  `image` varchar(255) NOT NULL,
  `sort_order` int(11) DEFAULT 1,
  `status` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `frontend_certificates`
--

INSERT INTO `frontend_certificates` (`id`, `center_name`, `image`, `sort_order`, `status`, `created_at`) VALUES
(1, 'certificate 1', 'CERT_1777404779_915.png', 1, 1, '2026-04-28 19:32:59'),
(2, 'Certificate 2', 'CERT_1777841802_626.jpg', 1, 1, '2026-05-03 20:56:42');

-- --------------------------------------------------------

--
-- Table structure for table `frontend_events`
--

CREATE TABLE `frontend_events` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `event_date` date NOT NULL,
  `location` varchar(255) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `status` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `frontend_events`
--

INSERT INTO `frontend_events` (`id`, `title`, `event_date`, `location`, `image`, `description`, `status`, `created_at`) VALUES
(1, 'New Batch Student Orientation', '2026-04-12', 'Main Hall, NEB Campus', 'EVENT_1777837061_758.jpg', 'Join us for the 2026 academic session orientation. New students will be guided through our digital portals.', 1, '2026-04-27 02:43:11'),
(2, 'Computer Literacy Workshop', '2026-05-25', 'IT Lab 01', 'EVENT_1777837043_975.jpg', 'A specialized 3-day workshop focused on advanced office automation and digital tools.', 1, '2026-04-27 02:43:11'),
(3, 'Annual Convocation Ceremony', '2026-06-10', 'Public Auditorium', 'EVENT_1777837072_923.webp', 'Certificates and Marksheets will be awarded to our graduating students.', 1, '2026-04-27 02:43:11'),
(4, 'Alumni Meet', '2026-04-30', 'Public Auditorium', 'EVENT_1777841702_649.jpg', 'in this location plan we are meet together.', 1, '2026-05-03 20:55:02');

-- --------------------------------------------------------

--
-- Table structure for table `frontend_gallery`
--

CREATE TABLE `frontend_gallery` (
  `id` int(11) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `media_file` varchar(255) NOT NULL,
  `type` enum('image','video') DEFAULT 'image',
  `status` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `frontend_gallery`
--

INSERT INTO `frontend_gallery` (`id`, `category_id`, `title`, `media_file`, `type`, `status`, `created_at`) VALUES
(7, 1, 'Computer lab', 'GALLERY_1777836903_756.jpg', 'image', 1, '2026-05-03 19:35:03'),
(8, 4, 'Play ground', 'GALLERY_1777836919_583.webp', 'image', 1, '2026-05-03 19:35:19'),
(9, 1, 'Campus', 'GALLERY_1777836953_133.jpg', 'image', 1, '2026-05-03 19:35:53');

-- --------------------------------------------------------

--
-- Table structure for table `frontend_gallery_categories`
--

CREATE TABLE `frontend_gallery_categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `status` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `frontend_gallery_categories`
--

INSERT INTO `frontend_gallery_categories` (`id`, `name`, `status`) VALUES
(1, 'IT & Computer', 1),
(2, 'Agriculture', 1),
(3, 'Paramedical', 1),
(4, 'Culture & Sports', 1);

-- --------------------------------------------------------

--
-- Table structure for table `frontend_menus`
--

CREATE TABLE `frontend_menus` (
  `id` int(11) NOT NULL,
  `parent_id` int(11) DEFAULT 0,
  `title` varchar(100) NOT NULL,
  `link` varchar(255) DEFAULT NULL,
  `sort_order` int(11) DEFAULT 0,
  `status` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `frontend_menus`
--

INSERT INTO `frontend_menus` (`id`, `parent_id`, `title`, `link`, `sort_order`, `status`, `created_at`) VALUES
(1, 0, 'Home', '', 1, 1, '2026-04-27 00:05:20'),
(2, 0, 'About Us', '#', 2, 1, '2026-04-27 00:05:20'),
(3, 0, 'Student', '#', 3, 1, '2026-04-27 00:05:20'),
(4, 0, 'Franchise', '#', 4, 1, '2026-04-27 00:05:20'),
(5, 0, 'Center List', 'center-list.php', 5, 1, '2026-04-27 00:05:20'),
(6, 0, 'Events', 'events.php', 6, 1, '2026-04-27 00:05:20'),
(7, 0, 'Gallery', 'gallery.php', 7, 1, '2026-04-27 00:05:20'),
(8, 0, 'Contact Us', 'contact-us', 8, 1, '2026-04-27 00:05:20'),
(9, 2, 'About Board', 'about-us', 1, 1, '2026-04-27 00:05:20'),
(10, 2, 'Recognition & Approvals', 'recognition', 2, 1, '2026-04-27 00:05:20'),
(11, 3, 'Student Login', 'login.php?role=student', 1, 1, '2026-04-27 00:05:20'),
(12, 3, 'Online Student Enquiry', 'student/online-student-enquiry.php', 2, 1, '2026-04-27 00:05:20'),
(13, 3, 'Online Student Admission', 'student/online-student-admission.php', 3, 1, '2026-04-27 00:05:20'),
(14, 3, 'Registration Verification', 'student/registration-verification.php', 4, 1, '2026-04-27 00:05:20'),
(15, 3, 'Certificate Verification', 'student/certificate-verification.php', 5, 1, '2026-04-27 00:05:20'),
(16, 3, 'Admit Card', 'student/admit-card.php', 6, 1, '2026-04-27 00:05:20'),
(17, 3, 'Marksheet', 'student/marksheet.php', 7, 1, '2026-04-27 00:05:20'),
(18, 4, 'Franchise Login', 'login.php?role=franchise', 1, 1, '2026-04-27 00:05:20'),
(19, 4, 'Franchise Application', 'franchise/franchise-application.php', 2, 1, '2026-04-27 00:05:20'),
(20, 4, 'Franchise Information', 'franchise-info', 3, 1, '2026-04-27 00:05:20');

-- --------------------------------------------------------

--
-- Table structure for table `frontend_notices`
--

CREATE TABLE `frontend_notices` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text DEFAULT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `notice_date` date NOT NULL,
  `status` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `frontend_notices`
--

INSERT INTO `frontend_notices` (`id`, `title`, `content`, `file_path`, `notice_date`, `status`, `created_at`) VALUES
(1, 'test notice form', 'test notice form', '', '2026-05-05', 1, '2026-05-03 21:13:31');

-- --------------------------------------------------------

--
-- Table structure for table `frontend_pages`
--

CREATE TABLE `frontend_pages` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `content` longtext DEFAULT NULL,
  `meta_title` varchar(255) DEFAULT NULL,
  `meta_description` text DEFAULT NULL,
  `featured_image` varchar(255) DEFAULT NULL,
  `status` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `frontend_pages`
--

INSERT INTO `frontend_pages` (`id`, `title`, `slug`, `content`, `meta_title`, `meta_description`, `featured_image`, `status`, `created_at`) VALUES
(1, 'Authorized Center List', 'center-list', '<p><br></p>', '', '', NULL, 1, '2026-04-27 02:40:06'),
(2, 'Institutional Events', 'events', NULL, NULL, NULL, NULL, 1, '2026-04-27 02:40:06'),
(3, 'Event Details', 'event-details', NULL, NULL, NULL, NULL, 1, '2026-04-27 02:40:06'),
(4, 'Photo Gallery', 'gallery', NULL, NULL, NULL, NULL, 1, '2026-04-27 02:40:06'),
(5, 'About Us', 'about-us', '<p><br></p>', 'About Our Institution', 'Learn more about our mission, vision, and values.', NULL, 1, '2026-04-28 18:33:31'),
(10, 'Recognition & Approvals', 'recognition', '<p><br></p>', '', '', NULL, 1, '2026-04-28 19:28:30'),
(11, 'Contact Us', 'contact-us', 'CONTACT_CARDS, CONTACT_MAP, CONTACT_FORM_HEADER', NULL, NULL, NULL, 1, '2026-05-03 21:09:21'),
(12, 'Disclaimer', 'disclaimer', '<h1>Disclaimer</h1><p>The information provided by NEB (\"we\", \"us\", or \"our\") on this website is for general informational purposes only. All information on the site is provided in good faith, however we make no representation or warranty of any kind, express or implied, regarding the accuracy, adequacy, validity, reliability, availability, or completeness of any information on the site.</p>', NULL, NULL, NULL, 1, '2026-05-03 21:11:55'),
(13, 'Privacy Policy', 'privacy-policy', '<h1>Privacy Policy</h1><p>Your privacy is important to us. It is NEB\'s policy to respect your privacy regarding any information we may collect from you across our website, and other sites we own and operate.</p><p>We only ask for personal information when we truly need it to provide a service to you. We collect it by fair and lawful means, with your knowledge and consent. We also let you know why we’re collecting it and how it will be used.</p>', NULL, NULL, NULL, 1, '2026-05-03 21:11:55'),
(14, 'Terms and Conditions', 'terms-and-conditions', '<h1>Terms and Conditions</h1><p>Welcome to NEB. These terms and conditions outline the rules and regulations for the use of our Website.</p><p>By accessing this website we assume you accept these terms and conditions. Do not continue to use NEB if you do not agree to take all of the terms and conditions stated on this page.</p>', NULL, NULL, NULL, 1, '2026-05-03 21:11:55'),
(15, 'Return and Refund Policy', 'return-refund-policy', '<h1>Return and Refund Policy</h1><p>Thanks for choosing NEB.</p><p>If you are not entirely satisfied with your purchase, we\'re here to help.</p><p>Our Return and Refund Policy was generated for NEB. You have 30 calendar days to return an item from the date you received it. To be eligible for a return, your item must be unused and in the same condition that you received it.</p>', NULL, NULL, NULL, 1, '2026-05-03 21:11:55');

-- --------------------------------------------------------

--
-- Table structure for table `frontend_recognitions`
--

CREATE TABLE `frontend_recognitions` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `sort_order` int(11) DEFAULT 0,
  `status` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `frontend_recognitions`
--

INSERT INTO `frontend_recognitions` (`id`, `title`, `file_path`, `sort_order`, `status`, `created_at`) VALUES
(1, 'National Institute of Open Schooling (NIOS)', 'nios_cert.png', 0, 1, '2026-04-27 01:37:40'),
(2, 'Medical Council of India (MCI)', 'mci_cert.png', 0, 1, '2026-04-27 01:37:40'),
(3, 'Dental Council of India (DCI)', 'dci_cert.png', 0, 1, '2026-04-27 01:37:40'),
(4, 'Indian Nursing Council (INC)', 'inc_cert.png', 0, 1, '2026-04-27 01:37:40');

-- --------------------------------------------------------

--
-- Table structure for table `frontend_sections`
--

CREATE TABLE `frontend_sections` (
  `id` int(11) NOT NULL,
  `section_key` varchar(50) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `content` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `page_slug` varchar(50) DEFAULT NULL,
  `status` tinyint(1) DEFAULT 1,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `frontend_sections`
--

INSERT INTO `frontend_sections` (`id`, `section_key`, `title`, `content`, `image`, `page_slug`, `status`, `updated_at`) VALUES
(1, 'HOME_WELCOME', 'Welcome to National Examination Board', 'National examination Board of Open Schooling and skill education creates opportunities for learners to complete their Secondary and Senior Secondary education.', NULL, NULL, 1, '2026-04-27 00:50:55'),
(2, 'PROGRAMS_HEADING', 'Programs and Courses Offered', 'Choose a program that meets your goals from Secondary to Skill & Vocational education.', NULL, NULL, 1, '2026-04-27 00:50:55'),
(3, 'PROG_SECONDARY', 'Secondary Level', 'Equivalent to the 10th standard, this program builds a solid foundation for further education and career pathways.', NULL, NULL, 1, '2026-04-27 00:50:55'),
(4, 'PROG_SR_SECONDARY', 'Sr. Secondary Level', 'Equivalent to the 12th standard, this program opens doors to higher education, professional courses, and diverse career opportunities.', NULL, NULL, 1, '2026-04-27 00:50:55'),
(5, 'PROG_VOCATIONAL', 'Skills & Vocational Education', 'Practical, skill-based programs designed to equip learners with industry-relevant expertise for immediate employment.', NULL, NULL, 1, '2026-04-27 00:50:55'),
(6, 'WHY_CHOOSE_HEADING', 'Why Choose Board?', 'For a learning environment that provides enhanced employment qualifications, accessible equal education support.', NULL, NULL, 1, '2026-04-27 00:50:55'),
(7, 'WHY_CHOOSE_F1', 'Flexible Learning', 'Study online, during your own time schedule with access all study materials online 24/7.', NULL, NULL, 1, '2026-04-27 00:50:55'),
(8, 'WHY_CHOOSE_F2', 'Wide Subject Choice', 'Select from multiple distinct courses to match your requirements and interests.', NULL, NULL, 1, '2026-04-27 00:50:55'),
(9, 'WHY_CHOOSE_F3', 'Nationwide Access', 'Get access to verified centers and examinations across the country.', NULL, NULL, 1, '2026-04-27 00:50:55'),
(10, 'ACCREDITATION_INTRO', 'Accreditations & Approvals', 'National examination Board of Open Schooling and skill education is recognized and approved by various government bodies and institutions.', NULL, NULL, 1, '2026-04-27 00:50:55'),
(11, 'STATS_1', '15+', 'Years of Excellence', NULL, NULL, 1, '2026-04-27 00:52:27'),
(12, 'STATS_2', '50k+', 'Students Enrolled', NULL, NULL, 1, '2026-04-27 00:52:27'),
(13, 'STATS_3', '200+', 'Study Centers', NULL, NULL, 1, '2026-04-27 00:52:27'),
(14, 'STATS_4', '100%', 'Govt. Recognized', NULL, NULL, 1, '2026-04-27 00:52:27'),
(15, 'ABOUT_INTRO', 'India\'s Premier Government Recognized Open Schooling Board', 'Providing Secondary (10th) and Senior Secondary (12th) education alongside skill and vocational courses for learners of every age.', NULL, 'about-us', 1, '2026-05-03 20:21:52'),
(16, 'ABOUT_VISION', 'Our Vision', 'To democratize and universalize higher education with non-formal, continuing methods. We provide education regardless of age, gender, caste, or location.', NULL, 'about-us', 1, '2026-04-27 15:14:21'),
(17, 'ABOUT_MISSION', 'Our Mission', 'To bring higher education to every learner in India’s remotest areas, provide job-oriented professional education, and revolutionize traditional education.', NULL, 'about-us', 1, '2026-04-27 15:14:21'),
(18, 'ABOUT_FEAT_1', 'Flexible Learning', 'No age bar, free subject choice, multiple exam attempts.', NULL, 'about-us', 1, '2026-04-27 15:14:21'),
(19, 'ABOUT_FEAT_2', 'Vocational Integration', 'Academic + Vocational courses for industry-ready skills.', NULL, 'about-us', 1, '2026-04-27 15:14:21'),
(20, 'ABOUT_FEAT_3', 'Credit Transfer', 'Accepts credits from recognized boards saving your time.', NULL, 'about-us', 1, '2026-04-27 15:14:21'),
(21, 'ABOUT_FEAT_4', 'Affordable Fees', 'Structured to support and uplift all socio-economic groups.', NULL, 'about-us', 1, '2026-04-27 15:14:21'),
(22, 'ABOUT_FEAT_5', 'Digital Education', 'Complete online resources, student portals, and e-learning.', NULL, 'about-us', 1, '2026-04-27 15:14:21'),
(23, 'ABOUT_FEAT_6', 'Nationwide Acceptance', 'Certificates valid for higher studies and government jobs.', NULL, 'about-us', 1, '2026-04-27 15:14:21'),
(24, 'franchise_intro_title', 'Why Join NEBOOSASE Network?', 'Partner with India\'s leading Skill Training Institute and help us empower the youth with vocational education.', NULL, 'franchise-info', 1, '2026-04-27 02:24:53'),
(25, 'franchise_benefit_1', 'Government Recognized', 'NEBOOSASE is ISO 9001:2015 certified and aligned with national vocational education standards, ensuring your center has full official credibility.', NULL, 'franchise-info', 1, '2026-04-27 02:24:53'),
(26, 'franchise_benefit_2', 'Low Investment', 'Our franchise model is designed for high returns with minimal setup costs, making it accessible for educational entrepreneurs.', NULL, 'franchise-info', 1, '2026-04-27 02:24:53'),
(27, 'franchise_benefit_3', 'Marketing Support', 'Receive complete branding support, national-level advertisements, lead generation, and local marketing materials.', NULL, 'franchise-info', 1, '2026-04-27 02:24:53'),
(28, 'franchise_benefit_4', 'Advanced LMS', 'Get access to our robust online portal for student registration, attendance tracking, and examination management.', NULL, 'franchise-info', 1, '2026-04-27 02:24:53'),
(29, 'franchise_infra_title', 'Infrastructure Requirements', 'To maintain our high standards, we require specific infrastructure at all our authorized study centers.', NULL, 'franchise-info', 1, '2026-04-27 02:24:53'),
(30, 'franchise_infra_space', 'Space Requirement', '500-800 Sq. Ft. of built-up area in a prominent location with a separate office and lab.', NULL, 'franchise-info', 1, '2026-04-27 02:24:53'),
(31, 'franchise_infra_hardware', 'Hardware Requirement', 'Minimum 5-10 high-configuration computers with high-speed internet and power backup (UPS).', NULL, 'franchise-info', 1, '2026-04-27 02:24:53'),
(32, 'franchise_infra_hr', 'Human Resources', 'Minimum 1 IT Faculty and 1 Administrative Staff with basic computer knowledge.', NULL, 'franchise-info', 1, '2026-04-27 02:24:53'),
(33, 'ABOUT_STAT_YEARS', '15+', 'Years of Excellence', NULL, 'about-us', 1, '2026-04-27 15:14:21'),
(34, 'ABOUT_STAT_STUDENTS', '50k+', 'Students Enrolled', NULL, 'about-us', 1, '2026-04-27 15:14:21'),
(35, 'ABOUT_STAT_CENTERS', '200+', 'Authorized Centers', NULL, 'about-us', 1, '2026-04-27 15:14:21'),
(36, 'ABOUT_FEATURES_TITLE', 'What Makes Us Different', 'Unique features that set us apart from traditional schooling boards.', NULL, 'about-us', 1, '2026-05-03 20:21:52'),
(37, 'ABOUT_FEATURES_SUBTITLE', 'Why Choose Us?', 'Experience excellence in education with our unique advantages.', NULL, 'about-us', 1, '2026-05-03 20:21:52'),
(38, 'NOTICE_BOARD_IMAGE', 'Notice Board Image', NULL, 'admission-image.webp', NULL, 1, '2026-04-28 18:06:19'),
(39, 'ENQUIRY_SIDE_IMAGE', 'Enquiry Side Image', NULL, 'enquiry-image.webp', NULL, 1, '2026-04-28 18:10:31'),
(40, 'ABOUT_SIDE_IMAGE', 'About Us Side Image', NULL, 'admission-image.webp', NULL, 1, '2026-04-28 19:01:49'),
(41, 'ABOUT_SUBTITLE', 'Who We Are', 'Welcome to NEB - India\'s Premier Open Schooling', NULL, NULL, 1, '2026-05-03 20:21:52'),
(42, 'ABOUT_BADGE_TITLE', 'Certified', '', NULL, NULL, 1, '2026-04-28 18:57:41'),
(43, 'ABOUT_BADGE_SUB', '', 'Quality Education Guaranteed', NULL, NULL, 1, '2026-04-28 18:57:41'),
(44, 'ABOUT_BTN_TEXT', 'Get In Touch', '', NULL, NULL, 1, '2026-04-28 18:57:41'),
(45, 'ABOUT_BTN_LINK', '', '/contact-us.php', NULL, NULL, 1, '2026-04-28 18:57:41'),
(56, 'INNER_PAGE_BANNER', 'Inner Pages Top Banner', NULL, 'about-us.png', NULL, 1, '2026-04-28 19:06:07'),
(57, 'RECOGNITION_TITLE', 'Our Accreditations', '', NULL, NULL, 1, '2026-04-28 19:18:09'),
(58, 'RECOGNITION_DESC', '', 'International Council Of Skill Training Institute And Research is recognized by various government and nodal bodies.', NULL, NULL, 1, '2026-04-28 19:18:09'),
(59, 'RECOGNITION_INFO_TITLE', 'Verify Certificates Online', '', NULL, NULL, 1, '2026-04-28 19:18:09'),
(60, 'RECOGNITION_INFO_DESC', '', 'You can verify our registration status by visiting the official portals of the respective departments using our registration numbers.', NULL, NULL, 1, '2026-04-28 19:18:09'),
(61, 'CONTACT_INFO', 'Contact Us Details', 'Main Campus, Educational City, India', NULL, NULL, 1, '2026-05-03 20:11:05'),
(95, 'CONTACT_CARDS', 'Contact Info Cards', '<div class=\"row g-4 text-center\">\n    <!-- Phone Card -->\n    <div class=\"col-md-4\">\n        <div class=\"contact-info-card p-5 rounded-4 shadow-sm h-100 transition-all border-bottom border-4 border-transparent hover-border-secondary\">\n            <div class=\"icon-circle-large mx-auto mb-4 bg-secondary-light text-secondary-theme shadow-sm\">\n                <i class=\"fas fa-phone-alt fs-3\"></i>\n            </div>\n            <h5 class=\"fw-bold mb-3\">Phone Support</h5>\n            <p class=\"text-muted mb-0\">+91 00000 00000</p>\n            <p class=\"text-muted x-small mt-2 opacity-75\">Mon - Sat: 10AM - 6PM</p>\n        </div>\n    </div>\n\n    <!-- Email Card -->\n    <div class=\"col-md-4\">\n        <div class=\"contact-info-card p-5 rounded-4 shadow-sm h-100 transition-all border-bottom border-4 border-transparent hover-border-primary\">\n            <div class=\"icon-circle-large mx-auto mb-4 bg-primary-light text-primary-theme shadow-sm\">\n                <i class=\"fas fa-envelope-open-text fs-3\"></i>\n            </div>\n            <h5 class=\"fw-bold mb-3\">Email Address</h5>\n            <p class=\"text-muted mb-0\">info@example.com</p>\n            <p class=\"text-muted x-small mt-2 opacity-75\">Online Support 24/7</p>\n        </div>\n    </div>\n\n    <!-- Address Card -->\n    <div class=\"col-md-4\">\n        <div class=\"contact-info-card p-5 rounded-4 shadow-sm h-100 transition-all border-bottom border-4 border-transparent hover-border-info\">\n            <div class=\"icon-circle-large mx-auto mb-4 bg-info-light text-info shadow-sm\">\n                <i class=\"fas fa-map-marker-alt fs-3\"></i>\n            </div>\n            <h5 class=\"fw-bold mb-3\">Office Address</h5>\n            <p class=\"text-muted small mb-0\">Main Campus, Educational City, India</p>\n        </div>\n    </div>\n</div>', NULL, NULL, 1, '2026-05-03 21:09:21'),
(96, 'CONTACT_MAP', 'Google Map Iframe', '<iframe src=\"https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3502.123!2d77.209!3d28.613!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zMjgMTAnNTQuMCJOIDc3wrAxMiczMi40IkU!5e0!3m2!1sen!2sin!4v1620000000000!5m2!1sen!2sin\" width=\"100%\" height=\"100%\" style=\"border:0; filter: grayscale(0.2) contrast(1.1);\" allowfullscreen=\"\" loading=\"lazy\"></iframe>', NULL, NULL, 1, '2026-05-03 21:09:21'),
(97, 'CONTACT_FORM_HEADER', 'Inquiry Form Header', '<div class=\"text-center mb-5 animate-up\">\n    <h2 class=\"fw-bold text-dark mb-2\">Custom Inquiry Form</h2>\n    <div class=\"theme-separator mx-auto\"></div>\n</div>', NULL, NULL, 1, '2026-05-03 21:09:21');

-- --------------------------------------------------------

--
-- Table structure for table `frontend_testimonials`
--

CREATE TABLE `frontend_testimonials` (
  `id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `designation` varchar(100) DEFAULT NULL,
  `quote` text NOT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `status` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `grade_ranges`
--

CREATE TABLE `grade_ranges` (
  `id` int(11) NOT NULL,
  `grade_name` varchar(50) NOT NULL,
  `min_percentage` decimal(5,2) NOT NULL,
  `max_percentage` decimal(5,2) NOT NULL,
  `grade_point` decimal(4,1) DEFAULT 0.0,
  `remark` varchar(100) DEFAULT NULL,
  `status` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `grade_ranges`
--

INSERT INTO `grade_ranges` (`id`, `grade_name`, `min_percentage`, `max_percentage`, `grade_point`, `remark`, `status`) VALUES
(1, 'A+', 91.00, 100.00, 10.0, 'Outstanding', 1),
(2, 'A', 81.00, 90.99, 9.0, 'Excellent', 1),
(3, 'B+', 71.00, 80.99, 8.0, 'Very Good', 1),
(4, 'B', 61.00, 70.99, 7.0, 'Good', 1),
(5, 'C', 51.00, 60.99, 6.0, 'Average', 1),
(6, 'D', 41.00, 50.99, 5.0, 'Fair', 1),
(7, 'E', 33.00, 40.99, 4.0, 'Marginal', 1),
(8, 'F', 0.00, 32.99, 0.0, 'Fail', 1);

-- --------------------------------------------------------

--
-- Table structure for table `issued_documents`
--

CREATE TABLE `issued_documents` (
  `id` int(10) UNSIGNED NOT NULL,
  `admission_id` int(10) UNSIGNED NOT NULL,
  `template_id` int(10) UNSIGNED NOT NULL,
  `document_type` enum('id_card','admit_card','marksheet','certificate') NOT NULL,
  `unique_id` varchar(100) DEFAULT NULL,
  `issued_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `expiry_date` date DEFAULT NULL,
  `status` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `marks`
--

CREATE TABLE `marks` (
  `id` int(10) UNSIGNED NOT NULL,
  `admission_id` int(10) UNSIGNED NOT NULL,
  `exam_type` enum('annual','half_yearly','supplementary','other') DEFAULT 'annual',
  `subject_name` varchar(100) NOT NULL,
  `marks_obtained` decimal(5,2) DEFAULT NULL,
  `max_marks` decimal(5,2) DEFAULT NULL,
  `result_date` date DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `office_transactions`
--

CREATE TABLE `office_transactions` (
  `id` int(11) NOT NULL,
  `voucher_head_id` int(11) NOT NULL,
  `type` enum('deposit','expense') NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_mode` varchar(50) DEFAULT 'Cash',
  `transaction_id` varchar(100) DEFAULT NULL,
  `date` date NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `online_classes`
--

CREATE TABLE `online_classes` (
  `id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `class_type` enum('live','recorded') DEFAULT 'recorded',
  `video_url` varchar(255) DEFAULT NULL,
  `live_link` varchar(255) DEFAULT NULL,
  `class_date` date DEFAULT NULL,
  `status` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `online_exams`
--

CREATE TABLE `online_exams` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `course_id` int(11) NOT NULL,
  `duration_mins` int(11) NOT NULL DEFAULT 60,
  `pass_percentage` int(11) NOT NULL DEFAULT 40,
  `start_datetime` datetime DEFAULT NULL,
  `end_datetime` datetime DEFAULT NULL,
  `immediate_result` tinyint(1) DEFAULT 1,
  `status` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `online_exams`
--

INSERT INTO `online_exams` (`id`, `title`, `course_id`, `duration_mins`, `pass_percentage`, `start_datetime`, `end_datetime`, `immediate_result`, `status`, `created_at`) VALUES
(1, 'Test Exam', 1, 60, 40, '2026-04-27 02:34:59', '2026-04-28 02:34:59', 1, 1, '2026-04-26 21:04:59');

-- --------------------------------------------------------

--
-- Table structure for table `online_exam_questions`
--

CREATE TABLE `online_exam_questions` (
  `id` int(11) NOT NULL,
  `exam_id` int(11) NOT NULL,
  `question` text NOT NULL,
  `option_a` varchar(255) NOT NULL,
  `option_b` varchar(255) NOT NULL,
  `option_c` varchar(255) NOT NULL,
  `option_d` varchar(255) NOT NULL,
  `correct_answer` enum('A','B','C','D') NOT NULL,
  `marks` int(11) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `online_exam_questions`
--

INSERT INTO `online_exam_questions` (`id`, `exam_id`, `question`, `option_a`, `option_b`, `option_c`, `option_d`, `correct_answer`, `marks`) VALUES
(1, 1, 'eee', 'e', 'ee', 'rr', 'rr', 'A', 1);

-- --------------------------------------------------------

--
-- Table structure for table `online_exam_results`
--

CREATE TABLE `online_exam_results` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `exam_id` int(11) NOT NULL,
  `total_questions` int(11) NOT NULL,
  `correct_answers` int(11) NOT NULL,
  `obtained_marks` int(11) NOT NULL,
  `percentage` decimal(5,2) NOT NULL,
  `result_status` enum('pass','fail') NOT NULL,
  `submitted_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `partners`
--

CREATE TABLE `partners` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `full_name` varchar(100) DEFAULT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `wallet_balance` decimal(15,2) DEFAULT 0.00,
  `profile_image` varchar(255) DEFAULT NULL,
  `status` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `partners`
--

INSERT INTO `partners` (`id`, `user_id`, `full_name`, `phone`, `email`, `wallet_balance`, `profile_image`, `status`, `created_at`) VALUES
(1, 10, 'HEMCHANDRA', '7004070014', 'info@hmsgitr.com', 99500.00, 'partner_1777967163.jpeg', 1, '2026-04-29 07:58:28'),
(2, 16, 'SHIVANI KUMARI', '9022078380', 'nebiosseindia@gmail.com', 500.00, 'partner_1777972195.jpeg', 1, '2026-05-05 09:09:55');

-- --------------------------------------------------------

--
-- Table structure for table `partner_wallet_ledger`
--

CREATE TABLE `partner_wallet_ledger` (
  `id` int(11) NOT NULL,
  `partner_id` int(11) DEFAULT NULL,
  `amount` decimal(15,2) DEFAULT NULL,
  `type` enum('credit','debit') DEFAULT NULL,
  `description` text DEFAULT NULL,
  `status` enum('pending','success','failed') DEFAULT 'success',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `partner_wallet_ledger`
--

INSERT INTO `partner_wallet_ledger` (`id`, `partner_id`, `amount`, `type`, `description`, `status`, `created_at`) VALUES
(1, 1, 100000.00, 'credit', '', 'success', '2026-04-30 14:50:42'),
(2, 1, 500.00, 'debit', '', 'success', '2026-04-30 14:51:11'),
(3, 2, 500.00, 'credit', 'phonepay payment', 'success', '2026-05-05 09:10:51');

-- --------------------------------------------------------

--
-- Table structure for table `qr_attendance`
--

CREATE TABLE `qr_attendance` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `franchise_id` int(11) NOT NULL,
  `check_in_time` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` varchar(20) DEFAULT 'present',
  `location_data` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `question_groups`
--

CREATE TABLE `question_groups` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `role_permissions`
--

CREATE TABLE `role_permissions` (
  `id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL,
  `module` varchar(50) NOT NULL,
  `action` varchar(50) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `role_permissions`
--

INSERT INTO `role_permissions` (`id`, `role_id`, `module`, `action`, `status`) VALUES
(42, 1, 'Enquiry Management', 'view', 1),
(43, 1, 'Enquiry Management', 'add', 1),
(44, 1, 'Enquiry Management', 'edit', 1),
(45, 1, 'Enquiry Management', 'delete', 1),
(46, 1, 'Partner Management', 'view', 1),
(47, 1, 'Partner Management', 'add', 1),
(48, 1, 'Partner Management', 'edit', 1),
(49, 1, 'Partner Management', 'delete', 1),
(50, 1, 'Franchise Management', 'view', 1),
(51, 1, 'Franchise Management', 'add', 1),
(52, 1, 'Franchise Management', 'edit', 1),
(53, 1, 'Franchise Management', 'delete', 1),
(54, 1, 'Student Management', 'view', 1),
(55, 1, 'Student Management', 'add', 1),
(56, 1, 'Student Management', 'edit', 1),
(57, 1, 'Student Management', 'delete', 1),
(58, 1, 'Course Management', 'view', 1),
(59, 1, 'Course Management', 'add', 1),
(60, 1, 'Course Management', 'edit', 1),
(61, 1, 'Course Management', 'delete', 1),
(62, 1, 'Card Management', 'view', 1),
(63, 1, 'Card Management', 'add', 1),
(64, 1, 'Card Management', 'edit', 1),
(65, 1, 'Card Management', 'delete', 1),
(66, 1, 'Accounting', 'view', 1),
(67, 1, 'Accounting', 'add', 1),
(68, 1, 'Accounting', 'edit', 1),
(69, 1, 'Accounting', 'delete', 1),
(70, 1, 'Exam Management', 'view', 1),
(71, 1, 'Exam Management', 'add', 1),
(72, 1, 'Exam Management', 'edit', 1),
(73, 1, 'Exam Management', 'delete', 1),
(74, 1, 'Frontend Management', 'view', 1),
(75, 1, 'Frontend Management', 'add', 1),
(76, 1, 'Frontend Management', 'edit', 1),
(77, 1, 'Frontend Management', 'delete', 1),
(78, 1, 'Report Management', 'view', 1),
(79, 1, 'Locations', 'view', 1),
(80, 1, 'Locations', 'add', 1),
(81, 1, 'Locations', 'edit', 1),
(82, 1, 'Locations', 'delete', 1),
(83, 1, 'Settings', 'view', 1),
(84, 1, 'Settings', 'edit', 1),
(85, 2, 'Student Management', 'view', 1),
(86, 2, 'Exam Management', 'view', 1),
(87, 2, 'Accounting', 'view', 1),
(88, 2, 'Frontend Management', 'view', 1),
(89, 3, 'Enquiry Management', 'view', 1),
(90, 3, 'Enquiry Management', 'add', 1),
(91, 3, 'Enquiry Management', 'edit', 1),
(92, 3, 'Student Management', 'view', 1),
(93, 3, 'Student Management', 'add', 1),
(94, 3, 'Student Management', 'edit', 1),
(95, 3, 'Course Management', 'view', 1),
(96, 3, 'Card Management', 'view', 1),
(97, 3, 'Accounting', 'view', 1),
(98, 3, 'Accounting', 'add', 1),
(99, 3, 'Exam Management', 'view', 1),
(100, 3, 'Report Management', 'view', 1),
(101, 3, 'Frontend Management', 'view', 1),
(102, 4, 'Franchise Management', 'view', 1),
(103, 4, 'Franchise Management', 'add', 1),
(104, 4, 'Franchise Management', 'edit', 1),
(105, 4, 'Partner Management', 'view', 1),
(106, 4, 'Partner Management', 'edit', 1),
(107, 4, 'Student Management', 'view', 1),
(108, 4, 'Report Management', 'view', 1),
(109, 4, 'Frontend Management', 'view', 1);

-- --------------------------------------------------------

--
-- Table structure for table `site_settings`
--

CREATE TABLE `site_settings` (
  `id` int(11) NOT NULL,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text NOT NULL,
  `setting_group` varchar(50) DEFAULT 'general',
  `status` tinyint(1) DEFAULT 1,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `site_settings`
--

INSERT INTO `site_settings` (`id`, `setting_key`, `setting_value`, `setting_group`, `status`, `updated_at`) VALUES
(1, 'theme_primary_color', '#1b1260', 'colors', 1, '2026-04-29 04:33:46'),
(2, 'theme_primary_hover_color', '#0f0a3d', 'colors', 1, '2026-05-05 07:36:21'),
(3, 'theme_secondary_color', '#ff6a1a', 'colors', 1, '2026-04-25 01:29:06'),
(4, 'theme_success_color', '#0b933a', 'colors', 1, '2026-04-25 01:29:06'),
(5, 'theme_body_bg_color', '#f7f9fc', 'colors', 1, '2026-04-25 01:29:06'),
(6, 'theme_heading_text_color', '#061c3a', 'colors', 1, '2026-04-25 01:29:06'),
(7, 'theme_body_text_color', '#444444', 'colors', 1, '2026-04-25 01:29:06'),
(8, 'site_title', 'NEB School Management', 'general', 1, '2026-04-25 01:29:06'),
(9, 'site_phone', '+91 94302 04280', 'general', 1, '2026-04-25 01:29:06'),
(10, 'site_email', 'info@icstirindia.com', 'general', 1, '2026-04-25 01:29:06'),
(11, 'site_address', 'Patna, Bihar, India', 'general', 1, '2026-04-25 01:29:06'),
(12, 'smtp_host', '', 'email', 1, '2026-04-25 01:29:06'),
(13, 'smtp_user', '', 'email', 1, '2026-04-25 01:29:06'),
(14, 'smtp_pass', '', 'email', 1, '2026-04-25 01:29:06'),
(15, 'smtp_port', '587', 'email', 1, '2026-04-25 01:29:06'),
(16, 'theme_font_heading', 'Inter', 'typography', 1, '2026-04-26 14:31:48'),
(17, 'theme_font_body', 'Poppins', 'typography', 1, '2026-04-26 14:31:38'),
(18, 'theme_font_size_base', '16px', 'typography', 1, '2026-04-25 01:53:42'),
(19, 'theme_font_size_h1', '2.2rem', 'typography', 1, '2026-04-25 01:53:42'),
(20, 'theme_font_size_h2', '1.8rem', 'typography', 1, '2026-04-25 01:53:42'),
(21, 'theme_font_weight_heading', '700', 'typography', 1, '2026-04-25 01:53:42'),
(22, 'theme_line_height', '1.65', 'typography', 1, '2026-04-25 01:53:42'),
(23, 'theme_letter_spacing', '0.3px', 'typography', 1, '2026-04-25 01:53:42'),
(24, 'theme_border_radius', '8px', 'layout', 1, '2026-04-25 02:22:13'),
(25, 'theme_border_radius_lg', '10px', 'layout', 1, '2026-04-25 02:22:13'),
(26, 'theme_border_radius_pill', '50px', 'layout', 1, '2026-04-25 01:53:42'),
(27, 'theme_card_shadow', '0px 5px 30px rgba(0,0,0,0.08)', 'layout', 1, '2026-04-25 01:53:42'),
(28, 'theme_card_shadow_hover', '0px 15px 40px rgba(0,0,0,0.15)', 'layout', 1, '2026-04-25 01:53:42'),
(29, 'theme_section_padding', '80px', 'layout', 1, '2026-04-25 01:53:42'),
(30, 'theme_transition_speed', '0.3s', 'layout', 1, '2026-04-25 01:53:42'),
(31, 'theme_admin_sidebar_width', '340px', 'layout', 1, '2026-04-25 02:15:58'),
(32, 'theme_admin_header_height', '70px', 'layout', 1, '2026-04-25 01:53:42'),
(33, 'theme_border_color', '#e9ecef', 'colors', 1, '2026-04-25 01:53:42'),
(34, 'theme_input_focus_color', '#1b1260', 'colors', 1, '2026-04-25 01:53:42'),
(35, 'theme_link_color', '#1b1260', 'colors', 1, '2026-04-25 01:53:42'),
(36, 'theme_link_hover_color', '#ff6a1a', 'colors', 1, '2026-04-25 01:53:42'),
(37, 'theme_card_bg', '#ffffff', 'colors', 1, '2026-04-25 01:53:42'),
(38, 'theme_sidebar_bg', '#ffffff', 'colors', 1, '2026-04-25 01:53:42'),
(39, 'theme_header_bg', '#ffffff', 'colors', 1, '2026-04-25 01:53:42'),
(40, 'theme_footer_bg', '#1b1260', 'colors', 1, '2026-04-25 01:53:42'),
(41, 'theme_footer_text', '#ffffff', 'colors', 1, '2026-04-25 01:53:42'),
(137, 'theme_modal_backdrop_blur', '8px', 'layout', 1, '2026-04-25 08:59:22'),
(138, 'theme_modal_header_bg', '#0f0a3d', 'layout', 1, '2026-04-25 09:05:41'),
(139, 'theme_modal_close_icon', 'fas fa-times', 'layout', 1, '2026-04-25 08:59:22'),
(140, 'theme_modal_close_color', '#ffffff', 'layout', 1, '2026-04-25 08:59:23'),
(141, 'theme_modal_close_size', '18px', 'layout', 1, '2026-04-25 08:59:23'),
(190, 'social_facebook', '', 'social', 1, '2026-04-26 19:43:50'),
(191, 'social_twitter', '', 'social', 1, '2026-04-26 19:43:50'),
(192, 'social_instagram', '', 'social', 1, '2026-04-26 19:43:50'),
(193, 'social_linkedin', '', 'social', 1, '2026-04-26 19:43:50'),
(194, 'social_youtube', '', 'social', 1, '2026-04-26 19:43:50'),
(195, 'footer_copyright', '© 2026 School Board. All rights reserved.', 'social', 1, '2026-04-26 19:43:50'),
(196, 'pg_razorpay_status', '0', 'payment_razorpay', 1, '2026-04-26 23:04:35'),
(197, 'pg_razorpay_currency', 'INR', 'payment_razorpay', 1, '2026-04-26 23:04:35'),
(198, 'pg_razorpay_key', 'admin@admin.com', 'payment_razorpay', 1, '2026-04-26 23:04:35'),
(199, 'pg_razorpay_secret', '123456', 'payment_razorpay', 1, '2026-04-26 23:04:35');

-- --------------------------------------------------------

--
-- Table structure for table `states`
--

CREATE TABLE `states` (
  `id` int(10) UNSIGNED NOT NULL,
  `country_id` int(10) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `states`
--

INSERT INTO `states` (`id`, `country_id`, `name`, `status`) VALUES
(6, 1, 'Andaman and Nicobar Islands', 1),
(7, 1, 'Andhra Pradesh', 1),
(8, 1, 'Arunachal Pradesh', 1),
(9, 1, 'Assam', 1),
(10, 1, 'Bihar', 1),
(11, 1, 'Chandigarh', 1),
(12, 1, 'Chhattisgarh', 1),
(13, 1, 'Dadar and Nagar Haveli', 1),
(14, 1, 'Daman and Diu', 1),
(15, 1, 'Delhi', 1),
(16, 1, 'Goa', 1),
(17, 1, 'Gujarat', 1),
(18, 1, 'Haryana', 1),
(19, 1, 'Himachal Pradesh', 1),
(20, 1, 'Jammu and Kashmir', 1),
(21, 1, 'Jharkhand', 1),
(22, 1, 'Karnataka', 1),
(23, 1, 'Kerala', 1),
(24, 1, 'Lakshadweep', 1),
(25, 1, 'Madya Pradesh', 1),
(26, 1, 'Maharashtra', 1),
(27, 1, 'Manipur', 1),
(28, 1, 'Meghalaya', 1),
(29, 1, 'Mizoram', 1),
(30, 1, 'Nagaland', 1),
(31, 1, 'Orissa', 1),
(32, 1, 'Pondicherry', 1),
(33, 1, 'Punjab', 1),
(34, 1, 'Rajasthan', 1),
(35, 1, 'Sikkim', 1),
(36, 1, 'Tamil Nadu', 1),
(37, 1, 'Telangana', 1),
(38, 1, 'Tripura', 1),
(39, 1, 'Uttar Pradesh', 1),
(40, 1, 'Uttaranchal', 1),
(41, 1, 'West Bengal', 1);

-- --------------------------------------------------------

--
-- Table structure for table `student_attendance`
--

CREATE TABLE `student_attendance` (
  `id` int(10) UNSIGNED NOT NULL,
  `admission_id` int(10) UNSIGNED NOT NULL,
  `center_id` int(10) UNSIGNED NOT NULL,
  `attendance_date` date NOT NULL,
  `status` enum('present','absent','leave') DEFAULT 'present',
  `remarks` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student_attendance`
--

INSERT INTO `student_attendance` (`id`, `admission_id`, `center_id`, `attendance_date`, `status`, `remarks`, `created_at`, `updated_at`) VALUES
(1, 1, 1, '2026-04-26', 'present', '', '2026-04-26 21:32:53', '2026-04-26 21:33:14'),
(2, 1, 1, '2026-04-28', 'present', '', '2026-04-28 20:01:41', '2026-04-28 20:01:41'),
(3, 4, 1, '2026-04-29', 'present', '', '2026-04-28 22:44:17', '2026-04-28 22:44:31'),
(4, 4, 1, '2026-04-28', 'present', '', '2026-04-28 23:25:07', '2026-04-28 23:25:07'),
(5, 5, 1, '2026-04-28', 'present', '', '2026-04-28 23:25:07', '2026-04-28 23:25:07'),
(6, 6, 2, '2026-04-30', 'present', '', '2026-04-30 20:40:24', '2026-04-30 20:40:24'),
(7, 5, 1, '2026-04-30', 'present', '', '2026-04-30 20:40:24', '2026-04-30 20:40:24');

-- --------------------------------------------------------

--
-- Table structure for table `student_marks`
--

CREATE TABLE `student_marks` (
  `id` int(11) NOT NULL,
  `exam_id` int(11) NOT NULL,
  `admission_id` int(11) NOT NULL,
  `subject_id` int(11) NOT NULL,
  `marks_obtained` decimal(5,2) NOT NULL DEFAULT 0.00,
  `max_marks` decimal(5,2) NOT NULL DEFAULT 100.00,
  `passing_marks` decimal(5,2) NOT NULL DEFAULT 33.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `student_registration_transactions`
--

CREATE TABLE `student_registration_transactions` (
  `id` int(10) UNSIGNED NOT NULL,
  `admission_id` int(10) UNSIGNED NOT NULL,
  `center_id` int(10) UNSIGNED NOT NULL,
  `txn_id` varchar(100) DEFAULT NULL,
  `amount` decimal(15,2) NOT NULL,
  `payment_method` enum('razorpay','upi','offline','wallet') DEFAULT 'offline',
  `payment_status` enum('success','pending','failed') DEFAULT 'pending',
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `study_materials`
--

CREATE TABLE `study_materials` (
  `id` int(10) UNSIGNED NOT NULL,
  `course_id` int(10) UNSIGNED NOT NULL,
  `subject_id` int(10) UNSIGNED DEFAULT NULL,
  `title` varchar(200) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `status` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `study_materials`
--

INSERT INTO `study_materials` (`id`, `course_id`, `subject_id`, `title`, `file_path`, `description`, `status`, `created_at`) VALUES
(1, 1, 2, 'Notes of C++', 'MAT_1777416170_919.pdf', 'Complete note of C++', 1, '2026-04-28 22:42:50');

-- --------------------------------------------------------

--
-- Table structure for table `subjects`
--

CREATE TABLE `subjects` (
  `id` int(10) UNSIGNED NOT NULL,
  `course_id` int(10) UNSIGNED NOT NULL,
  `year_sem` varchar(50) DEFAULT NULL,
  `subject_name` varchar(150) NOT NULL,
  `subject_code` varchar(50) DEFAULT NULL,
  `subject_type` enum('Theory','Practical','Both') DEFAULT 'Theory',
  `total_lessons` int(11) DEFAULT 0,
  `status` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `subjects`
--

INSERT INTO `subjects` (`id`, `course_id`, `year_sem`, `subject_name`, `subject_code`, `subject_type`, `total_lessons`, `status`, `created_at`) VALUES
(1, 1, 'Sem-1', 'Computer Fundamental', 'DCA-1', 'Theory', 15, 1, '2026-04-28 20:33:39'),
(2, 1, 'Sem-1', 'C++', 'DCA-2', 'Practical', 12, 1, '2026-04-28 20:34:14'),
(3, 1, 'Sem-1', 'HTML', 'DCA-3', 'Both', 12, 1, '2026-04-28 20:35:14'),
(4, 1, 'Sem-1', 'CSS / CSS3', 'DCA-4', 'Both', 15, 1, '2026-04-28 20:35:37'),
(5, 1, 'Sem-1', 'JavaScript', 'DCA-5', 'Both', 12, 1, '2026-04-28 20:36:17');

-- --------------------------------------------------------

--
-- Table structure for table `support_tickets`
--

CREATE TABLE `support_tickets` (
  `id` int(11) NOT NULL,
  `franchise_id` int(11) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `department` varchar(100) DEFAULT NULL,
  `message` text NOT NULL,
  `status` enum('open','in-progress','resolved','closed') DEFAULT 'open',
  `priority` enum('low','medium','high') DEFAULT 'medium',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','student','franchise','partner') NOT NULL,
  `role_id` int(11) DEFAULT NULL,
  `status` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `role`, `role_id`, `status`, `created_at`, `updated_at`) VALUES
(1, 'NEBOSSE', 'admin@admin.com', '$2y$10$4mEXqAwvjh3i9QVs/vOY/ejpkAEjpyJ3jHwyw4g8VLSvjPv1belBq', 'admin', NULL, 0, '2026-04-24 02:00:11', '2026-05-02 03:00:21'),
(3, 'franchise_test', 'franchise@admin.com', '$2y$10$wc/T4QglOJxulYhG52JeNuUyfWpLjF5cs90Ai8C9xUvvA9dhSoMbO', 'franchise', NULL, 1, '2026-04-24 02:00:12', '2026-04-26 14:43:43'),
(9, 'student@admin.com', 'student@admin.com', '$2y$12$6Itm0tA1S7l9PaqdUWR4EuuKmnz01fozRLZiKmk97EXNSkexoATU2', 'student', NULL, 0, '2026-04-28 23:23:53', '2026-05-01 13:32:39'),
(10, 'HEMCHANDRA', 'info@hmsgitr.com', '$2y$12$OC5k.pCbN4KYyc2Y6rcdBO8J16znL2DUsWWxygLgmMVTsFuMf5ueu', 'partner', NULL, 1, '2026-04-29 07:58:28', '2026-05-05 09:11:33'),
(11, 'rexuxarug@mailinator.com', 'rexuxarug@mailinator.com', '$2y$12$W8kuJtANOfcvtR1yzRsiIeBY4CIcgXfWqo5r8kjcrYSTh68ORRmlm', 'franchise', NULL, 0, '2026-04-30 14:29:54', '2026-04-30 14:29:54'),
(12, 'ccsu@gmail.com', 'ccsu@gmail.com', '$2y$10$rnBou2xLWr0Qyvsqeme9RuhAmoNbRJqU.hhxP3QmEJOQ8hGch97Ci', 'franchise', NULL, 1, '2026-04-30 19:01:00', '2026-04-30 19:01:00'),
(13, 'raina@gmail.com', 'raina@gmail.com', '$2y$10$Thu2fB98oHRK2e4nZDFOwutqoo5jIwu6jup2HoOSHRAtpaldSYez.', 'franchise', NULL, 1, '2026-05-03 22:01:28', '2026-05-03 22:01:28'),
(15, 'raushan@gmail.com', 'raushan@gmail.com', '$2y$10$6Bw3bdlPKthsqAznDoi8OuDp6vhBgF.8RWebbyEtCT7mEv/lCp3bm', 'student', NULL, 1, '2026-05-03 22:18:13', '2026-05-03 22:18:13'),
(16, 'SHIVANI KUMARI', 'nebiosseindia@gmail.com', '$2y$12$T8kVgOF9g63C17P8aDHxPOmwlO2YNaB7K1o/4JcyiSxvD.INwiUMe', 'partner', NULL, 1, '2026-05-05 09:09:55', '2026-05-05 09:09:55'),
(17, 'hemchandrakumar3@gmail.com', 'hemchandrakumar3@gmail.com', '$2y$12$5HYKFvtOimiZzj50C77dReeQop3rN4a1pwmrLLovWD2/5gdSYLZHW', 'franchise', NULL, 1, '2026-05-05 09:22:53', '2026-05-05 09:22:53');

-- --------------------------------------------------------

--
-- Table structure for table `user_roles`
--

CREATE TABLE `user_roles` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `level` int(11) NOT NULL DEFAULT 50,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_roles`
--

INSERT INTO `user_roles` (`id`, `name`, `description`, `level`, `created_at`, `status`) VALUES
(1, 'Admin', 'Admin can do everything', 100, '2026-04-26 22:36:31', 1),
(2, 'Student', 'Standard student account', 10, '2026-04-26 22:44:55', 1),
(3, 'Franchise', 'Center Manager account', 40, '2026-04-26 22:44:55', 1),
(4, 'Partner', 'Regional Partner account', 60, '2026-04-26 22:44:55', 1);

-- --------------------------------------------------------

--
-- Table structure for table `voucher_heads`
--

CREATE TABLE `voucher_heads` (
  `id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `type` enum('deposit','expense') NOT NULL,
  `description` text DEFAULT NULL,
  `status` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `academic_years`
--
ALTER TABLE `academic_years`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `admissions`
--
ALTER TABLE `admissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`),
  ADD UNIQUE KEY `roll_number` (`roll_number`),
  ADD KEY `course_id` (`course_id`),
  ADD KEY `center_id` (`center_id`),
  ADD KEY `idx_roll` (`roll_number`),
  ADD KEY `idx_mobile` (`mobile`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_status` (`approval_status`);

--
-- Indexes for table `admission_sessions`
--
ALTER TABLE `admission_sessions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `admit_download_settings`
--
ALTER TABLE `admit_download_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `centers`
--
ALTER TABLE `centers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_code` (`code`),
  ADD KEY `idx_mobile` (`mobile`);

--
-- Indexes for table `cities`
--
ALTER TABLE `cities`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `countries`
--
ALTER TABLE `countries`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `courses`
--
ALTER TABLE `courses`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`),
  ADD KEY `idx_category` (`category`);

--
-- Indexes for table `course_categories`
--
ALTER TABLE `course_categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `course_durations`
--
ALTER TABLE `course_durations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `districts`
--
ALTER TABLE `districts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `document_templates`
--
ALTER TABLE `document_templates`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `enquiries`
--
ALTER TABLE `enquiries`
  ADD PRIMARY KEY (`id`),
  ADD KEY `course_id` (`course_id`),
  ADD KEY `idx_mobile` (`mobile`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `exams`
--
ALTER TABLE `exams`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `fee_allocations`
--
ALTER TABLE `fee_allocations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `admission_id` (`admission_id`),
  ADD KEY `fee_group_id` (`fee_group_id`);

--
-- Indexes for table `fee_collections`
--
ALTER TABLE `fee_collections`
  ADD PRIMARY KEY (`id`),
  ADD KEY `admission_id` (`admission_id`);

--
-- Indexes for table `fee_groups`
--
ALTER TABLE `fee_groups`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `fee_group_items`
--
ALTER TABLE `fee_group_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fee_group_id` (`fee_group_id`),
  ADD KEY `fee_type_id` (`fee_type_id`);

--
-- Indexes for table `fee_types`
--
ALTER TABLE `fee_types`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `franchises`
--
ALTER TABLE `franchises`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `center_code` (`center_code`);

--
-- Indexes for table `franchise_documents`
--
ALTER TABLE `franchise_documents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `franchise_id` (`franchise_id`);

--
-- Indexes for table `franchise_enquiries`
--
ALTER TABLE `franchise_enquiries`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `franchise_gateways`
--
ALTER TABLE `franchise_gateways`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `franchise_notices`
--
ALTER TABLE `franchise_notices`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `franchise_registration_transactions`
--
ALTER TABLE `franchise_registration_transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `franchise_id` (`franchise_id`),
  ADD KEY `txn_id` (`txn_id`);

--
-- Indexes for table `franchise_wallet_ledger`
--
ALTER TABLE `franchise_wallet_ledger`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `franchise_wallet_requests`
--
ALTER TABLE `franchise_wallet_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `franchise_id` (`franchise_id`),
  ADD KEY `status` (`status`);

--
-- Indexes for table `frontend_achievements`
--
ALTER TABLE `frontend_achievements`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `frontend_affiliations`
--
ALTER TABLE `frontend_affiliations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `frontend_banners`
--
ALTER TABLE `frontend_banners`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `frontend_certificates`
--
ALTER TABLE `frontend_certificates`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `frontend_events`
--
ALTER TABLE `frontend_events`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `frontend_gallery`
--
ALTER TABLE `frontend_gallery`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `frontend_gallery_categories`
--
ALTER TABLE `frontend_gallery_categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `frontend_menus`
--
ALTER TABLE `frontend_menus`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `frontend_notices`
--
ALTER TABLE `frontend_notices`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `frontend_pages`
--
ALTER TABLE `frontend_pages`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indexes for table `frontend_recognitions`
--
ALTER TABLE `frontend_recognitions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `frontend_sections`
--
ALTER TABLE `frontend_sections`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `section_key` (`section_key`);

--
-- Indexes for table `frontend_testimonials`
--
ALTER TABLE `frontend_testimonials`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `grade_ranges`
--
ALTER TABLE `grade_ranges`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `issued_documents`
--
ALTER TABLE `issued_documents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `admission_id` (`admission_id`),
  ADD KEY `template_id` (`template_id`);

--
-- Indexes for table `marks`
--
ALTER TABLE `marks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_admission` (`admission_id`);

--
-- Indexes for table `office_transactions`
--
ALTER TABLE `office_transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `voucher_head_id` (`voucher_head_id`);

--
-- Indexes for table `online_classes`
--
ALTER TABLE `online_classes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `online_exams`
--
ALTER TABLE `online_exams`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `online_exam_questions`
--
ALTER TABLE `online_exam_questions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `exam_id` (`exam_id`);

--
-- Indexes for table `online_exam_results`
--
ALTER TABLE `online_exam_results`
  ADD PRIMARY KEY (`id`),
  ADD KEY `exam_id` (`exam_id`);

--
-- Indexes for table `partners`
--
ALTER TABLE `partners`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `partner_wallet_ledger`
--
ALTER TABLE `partner_wallet_ledger`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `qr_attendance`
--
ALTER TABLE `qr_attendance`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `question_groups`
--
ALTER TABLE `question_groups`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `role_permissions`
--
ALTER TABLE `role_permissions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `site_settings`
--
ALTER TABLE `site_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `setting_key` (`setting_key`);

--
-- Indexes for table `states`
--
ALTER TABLE `states`
  ADD PRIMARY KEY (`id`),
  ADD KEY `country_id` (`country_id`);

--
-- Indexes for table `student_attendance`
--
ALTER TABLE `student_attendance`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_att` (`admission_id`,`attendance_date`);

--
-- Indexes for table `student_marks`
--
ALTER TABLE `student_marks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `exam_id` (`exam_id`),
  ADD KEY `admission_id` (`admission_id`),
  ADD KEY `subject_id` (`subject_id`);

--
-- Indexes for table `student_registration_transactions`
--
ALTER TABLE `student_registration_transactions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `txn_id` (`txn_id`);

--
-- Indexes for table `study_materials`
--
ALTER TABLE `study_materials`
  ADD PRIMARY KEY (`id`),
  ADD KEY `course_id` (`course_id`),
  ADD KEY `subject_id` (`subject_id`);

--
-- Indexes for table `subjects`
--
ALTER TABLE `subjects`
  ADD PRIMARY KEY (`id`),
  ADD KEY `course_id` (`course_id`);

--
-- Indexes for table `support_tickets`
--
ALTER TABLE `support_tickets`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_role` (`role`),
  ADD KEY `idx_status` (`status`);

--
-- Indexes for table `user_roles`
--
ALTER TABLE `user_roles`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `voucher_heads`
--
ALTER TABLE `voucher_heads`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `academic_years`
--
ALTER TABLE `academic_years`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `admissions`
--
ALTER TABLE `admissions`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `admission_sessions`
--
ALTER TABLE `admission_sessions`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `admit_download_settings`
--
ALTER TABLE `admit_download_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `centers`
--
ALTER TABLE `centers`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `cities`
--
ALTER TABLE `cities`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1037;

--
-- AUTO_INCREMENT for table `countries`
--
ALTER TABLE `countries`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `courses`
--
ALTER TABLE `courses`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `course_categories`
--
ALTER TABLE `course_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `course_durations`
--
ALTER TABLE `course_durations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `districts`
--
ALTER TABLE `districts`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=808;

--
-- AUTO_INCREMENT for table `document_templates`
--
ALTER TABLE `document_templates`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `enquiries`
--
ALTER TABLE `enquiries`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `exams`
--
ALTER TABLE `exams`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `fee_allocations`
--
ALTER TABLE `fee_allocations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fee_collections`
--
ALTER TABLE `fee_collections`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fee_groups`
--
ALTER TABLE `fee_groups`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fee_group_items`
--
ALTER TABLE `fee_group_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fee_types`
--
ALTER TABLE `fee_types`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `franchises`
--
ALTER TABLE `franchises`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `franchise_documents`
--
ALTER TABLE `franchise_documents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `franchise_enquiries`
--
ALTER TABLE `franchise_enquiries`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `franchise_gateways`
--
ALTER TABLE `franchise_gateways`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `franchise_notices`
--
ALTER TABLE `franchise_notices`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `franchise_registration_transactions`
--
ALTER TABLE `franchise_registration_transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `franchise_wallet_ledger`
--
ALTER TABLE `franchise_wallet_ledger`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `franchise_wallet_requests`
--
ALTER TABLE `franchise_wallet_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `frontend_achievements`
--
ALTER TABLE `frontend_achievements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `frontend_affiliations`
--
ALTER TABLE `frontend_affiliations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `frontend_banners`
--
ALTER TABLE `frontend_banners`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `frontend_certificates`
--
ALTER TABLE `frontend_certificates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `frontend_events`
--
ALTER TABLE `frontend_events`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `frontend_gallery`
--
ALTER TABLE `frontend_gallery`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `frontend_gallery_categories`
--
ALTER TABLE `frontend_gallery_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `frontend_menus`
--
ALTER TABLE `frontend_menus`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `frontend_notices`
--
ALTER TABLE `frontend_notices`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `frontend_pages`
--
ALTER TABLE `frontend_pages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `frontend_recognitions`
--
ALTER TABLE `frontend_recognitions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `frontend_sections`
--
ALTER TABLE `frontend_sections`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=98;

--
-- AUTO_INCREMENT for table `frontend_testimonials`
--
ALTER TABLE `frontend_testimonials`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `grade_ranges`
--
ALTER TABLE `grade_ranges`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `issued_documents`
--
ALTER TABLE `issued_documents`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `marks`
--
ALTER TABLE `marks`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `office_transactions`
--
ALTER TABLE `office_transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `online_classes`
--
ALTER TABLE `online_classes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `online_exams`
--
ALTER TABLE `online_exams`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `online_exam_questions`
--
ALTER TABLE `online_exam_questions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `online_exam_results`
--
ALTER TABLE `online_exam_results`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `partners`
--
ALTER TABLE `partners`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `partner_wallet_ledger`
--
ALTER TABLE `partner_wallet_ledger`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `qr_attendance`
--
ALTER TABLE `qr_attendance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `question_groups`
--
ALTER TABLE `question_groups`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `role_permissions`
--
ALTER TABLE `role_permissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=110;

--
-- AUTO_INCREMENT for table `site_settings`
--
ALTER TABLE `site_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=232;

--
-- AUTO_INCREMENT for table `states`
--
ALTER TABLE `states`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT for table `student_attendance`
--
ALTER TABLE `student_attendance`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `student_marks`
--
ALTER TABLE `student_marks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `student_registration_transactions`
--
ALTER TABLE `student_registration_transactions`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `study_materials`
--
ALTER TABLE `study_materials`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `subjects`
--
ALTER TABLE `subjects`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `support_tickets`
--
ALTER TABLE `support_tickets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `user_roles`
--
ALTER TABLE `user_roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `voucher_heads`
--
ALTER TABLE `voucher_heads`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `admissions`
--
ALTER TABLE `admissions`
  ADD CONSTRAINT `admissions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `admissions_ibfk_2` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `admissions_ibfk_3` FOREIGN KEY (`center_id`) REFERENCES `centers` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `enquiries`
--
ALTER TABLE `enquiries`
  ADD CONSTRAINT `enquiries_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `enquiries_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `frontend_gallery`
--
ALTER TABLE `frontend_gallery`
  ADD CONSTRAINT `frontend_gallery_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `frontend_gallery_categories` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `marks`
--
ALTER TABLE `marks`
  ADD CONSTRAINT `marks_ibfk_1` FOREIGN KEY (`admission_id`) REFERENCES `admissions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `online_exam_questions`
--
ALTER TABLE `online_exam_questions`
  ADD CONSTRAINT `online_exam_questions_ibfk_1` FOREIGN KEY (`exam_id`) REFERENCES `online_exams` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `online_exam_results`
--
ALTER TABLE `online_exam_results`
  ADD CONSTRAINT `online_exam_results_ibfk_1` FOREIGN KEY (`exam_id`) REFERENCES `online_exams` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `states`
--
ALTER TABLE `states`
  ADD CONSTRAINT `states_ibfk_1` FOREIGN KEY (`country_id`) REFERENCES `countries` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
