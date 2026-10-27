-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 24, 2026 at 04:51 PM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.0.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `mk`
--

-- --------------------------------------------------------

--
-- Table structure for table `abatahira`
--

CREATE TABLE `abatahira` (
  `id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `services` text DEFAULT NULL,
  `bio` text DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `price` decimal(12,2) DEFAULT 0.00,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `abatahira`
--

INSERT INTO `abatahira` (`id`, `name`, `photo`, `services`, `bio`, `phone`, `price`, `status`, `created_at`) VALUES
(1, 'Grace Iradukunda', 'ab1.jpg', 'Traditional dance, introductions, wedding ceremonies', 'Grace leads graceful traditional performances that honor culture.', '+250788222001', 200000.00, 'active', '2026-10-02 09:03:49'),
(2, 'Divine Team', 'ab2.jpg', 'Group dance, cultural shows', 'A vibrant troupe for introductions and cultural celebrations.', '+250788222002', 350000.00, 'active', '2026-10-02 09:03:49');

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `username` varchar(80) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(150) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `username`, `email`, `password`, `full_name`, `created_at`) VALUES
(1, 'MORA', 'hakizmanaericmora@gmail.com', 'mora', 'MK Administrator', '2026-10-02 09:03:49');

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `id` int(11) NOT NULL,
  `client_name` varchar(150) NOT NULL,
  `client_email` varchar(150) NOT NULL,
  `client_phone` varchar(50) NOT NULL,
  `service_type` enum('mc','abatahira','cakes','photographers','catering','decoration','sonalization','itorero_band','smoke_machine','fireworks','photobooth','other') NOT NULL,
  `service_id` int(11) DEFAULT NULL,
  `event_date` date DEFAULT NULL,
  `event_location` varchar(255) DEFAULT NULL,
  `guests` int(11) DEFAULT 0,
  `message` text DEFAULT NULL,
  `status` enum('pending','confirmed','cancelled','completed') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`id`, `client_name`, `client_email`, `client_phone`, `service_type`, `service_id`, `event_date`, `event_location`, `guests`, `message`, `status`, `created_at`) VALUES
(1, 'mora', 'hakizmana@gmail.com', '07888888', 'cakes', 4, '2026-10-09', 'kigaqli', 2, 'hello', 'confirmed', '2026-10-03 16:52:40'),
(2, 'ERIC', 'hakizmana@gmail.com', '0788888888', 'cakes', NULL, '2026-10-12', 'MUSANZE', 2, 'HELLO', 'confirmed', '2026-10-03 19:03:19');

-- --------------------------------------------------------

--
-- Table structure for table `cakes`
--

CREATE TABLE `cakes` (
  `id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `cake_type` varchar(100) DEFAULT NULL,
  `size` varchar(80) DEFAULT NULL,
  `price` decimal(12,2) DEFAULT 0.00,
  `description` text DEFAULT NULL,
  `custom_order` tinyint(1) DEFAULT 1,
  `status` enum('available','unavailable') DEFAULT 'available',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `cakes`
--

INSERT INTO `cakes` (`id`, `name`, `photo`, `cake_type`, `size`, `price`, `description`, `custom_order`, `status`, `created_at`) VALUES
(1, 'Royal Wedding Cake', 'cake1.jpg', 'Wedding', '3 tiers', 180000.00, 'Elegant multi-tier wedding cake with custom toppers.', 1, 'available', '2026-10-02 09:03:49'),
(2, 'Birthday Bloom', 'cake2.jpg', 'Birthday', 'Medium', 45000.00, 'Floral birthday cake with buttercream finish.', 1, 'available', '2026-10-02 09:03:49'),
(3, 'Chocolate Dream', 'cake3.jpg', 'Custom', 'Large', 65000.00, 'Rich chocolate layers with ganache drip.', 1, 'available', '2026-10-02 09:03:49'),
(4, 'Introduction Special', 'cake4.jpg', 'Introduction', '2 tiers', 120000.00, 'Traditional-inspired design for gusaba & introduction.', 1, 'available', '2026-10-02 09:03:49');

-- --------------------------------------------------------

--
-- Table structure for table `catering`
--

CREATE TABLE `catering` (
  `id` int(11) NOT NULL,
  `package_name` varchar(150) NOT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `menu_items` text DEFAULT NULL,
  `people_count` int(11) DEFAULT 50,
  `price` decimal(12,2) DEFAULT 0.00,
  `description` text DEFAULT NULL,
  `status` enum('available','unavailable') DEFAULT 'available',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `catering`
--

INSERT INTO `catering` (`id`, `package_name`, `photo`, `menu_items`, `people_count`, `price`, `description`, `status`, `created_at`) VALUES
(1, 'Classic Buffet', 'cat1.jpg', 'Rice, chicken, vegetables, salad, juice', 50, 400000.00, 'Reliable buffet for weddings and family events.', 'available', '2026-10-02 09:03:49'),
(2, 'Premium Feast', 'cat2.jpg', 'Grill mix, pasta, fish, desserts, soft drinks', 100, 900000.00, 'Premium spread for large celebrations.', 'available', '2026-10-02 09:03:49'),
(3, 'Corporate Lunch', 'cat3.jpg', 'Sandwich platters, salads, coffee, pastries', 30, 250000.00, 'Light professional catering for meetings.', 'available', '2026-10-02 09:03:49'),
(4, 'Unlimited Spins', '6ac1467899cc9_1791051384.jpg', 'yes', 50, 2222.00, 'yes', 'available', '2026-10-03 18:16:24');

-- --------------------------------------------------------

--
-- Table structure for table `contact_messages`
--

CREATE TABLE `contact_messages` (
  `id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `email` varchar(150) NOT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `subject` varchar(200) DEFAULT NULL,
  `message` text NOT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `decorations`
--

CREATE TABLE `decorations` (
  `id` int(11) NOT NULL,
  `title` varchar(150) NOT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `category` enum('wedding','birthday','introduction','corporate','other') DEFAULT 'wedding',
  `package_name` varchar(150) DEFAULT NULL,
  `price` decimal(12,2) DEFAULT 0.00,
  `description` text DEFAULT NULL,
  `status` enum('available','unavailable') DEFAULT 'available',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `decorations`
--

INSERT INTO `decorations` (`id`, `title`, `photo`, `category`, `package_name`, `price`, `description`, `status`, `created_at`) VALUES
(1, 'Garden Wedding Setup', 'dec1.jpg', 'wedding', 'Romantic Garden', 500000.00, 'Floral arches, aisle runners and soft lighting.', 'available', '2026-10-02 09:03:49'),
(2, 'Kids Birthday Magic', 'dec2.jpg', 'birthday', 'Party Pop', 150000.00, 'Colorful balloons, backdrop and dessert table.', 'available', '2026-10-02 09:03:49'),
(3, 'Traditional Introduction', 'dec3.jpg', 'introduction', 'Heritage Hall', 350000.00, 'Cultural motifs, seating and ceremony d?®cor.', 'available', '2026-10-02 09:03:49'),
(4, 'Corporate Stage Look', 'dec4.jpg', 'corporate', 'Brand Stage', 400000.00, 'Stage branding, lighting and seating layout.', 'available', '2026-10-02 09:03:49');

-- --------------------------------------------------------

--
-- Table structure for table `extra_services`
--

CREATE TABLE `extra_services` (
  `id` int(11) NOT NULL,
  `category` enum('smoke_machine','fireworks','photobooth') NOT NULL,
  `name` varchar(150) NOT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `duration` varchar(80) DEFAULT NULL,
  `package_name` varchar(150) DEFAULT NULL,
  `price` decimal(12,2) DEFAULT 0.00,
  `description` text DEFAULT NULL,
  `status` enum('available','unavailable') DEFAULT 'available',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `extra_services`
--

INSERT INTO `extra_services` (`id`, `category`, `name`, `photo`, `duration`, `package_name`, `price`, `description`, `status`, `created_at`) VALUES
(1, 'smoke_machine', 'Low Fog Sweep', 'smoke1.jpg', '2 hours', 'Ceremony Mist', 80000.00, 'Soft ground fog for aisles, first looks and romantic couple entries.', 'available', '2026-10-02 11:11:12'),
(2, 'smoke_machine', 'Dance Floor Cloud', 'smoke2.jpg', 'Evening set', 'Party Atmosphere', 120000.00, 'Thick, safe haze that makes lights and lasers look cinematic on the dance floor.', 'available', '2026-10-02 11:11:12'),
(3, 'smoke_machine', 'Grand Entrance Fog', 'smoke3.jpg', 'Highlight moments', 'VIP Reveal', 150000.00, 'Timed smoke bursts for MC announcements, cake cutting and celebrity-style reveals.', 'available', '2026-10-02 11:11:12'),
(4, 'fireworks', 'Sparkler Send-off', 'fw1.jpg', '10 minutes', 'Guest Sparklers', 45000.00, 'Handheld sparklers for a glowing exit photo moment with your guests.', 'available', '2026-10-02 11:11:12'),
(5, 'fireworks', 'Cold Fountain Show', 'fw2.jpg', '8ÔÇô12 minutes', 'Indoor Safe Fountains', 180000.00, 'Indoor-safe cold fireworks around the stage, cake or coupleÔÇÖs first dance.', 'available', '2026-10-02 11:11:12'),
(6, 'fireworks', 'Night Sky Finale', 'fw3.jpg', 'Finale', 'Outdoor Burst', 450000.00, 'A licensed outdoor fireworks finale that closes the night in gold and color.', 'available', '2026-10-02 11:11:12'),
(7, 'photobooth', 'Classic 360 Spin', 'pb1.jpg', '3 hours', '360 Photobooth', 150000.00, 'Spinning platform, LED ring and instant sharing for unforgettable slow-motion clips.', 'available', '2026-10-02 11:11:12'),
(8, 'photobooth', 'LED Ring Booth', 'pb2.jpg', '4 hours', 'Glow Ring 360', 220000.00, 'RGB lighting, props and custom overlay with your names and event date.', 'available', '2026-10-02 11:11:12'),
(9, 'photobooth', 'VIP Slow-Mo Booth', 'pb3.jpg', 'Full reception', 'Unlimited Spins', 280000.00, 'Unlimited guest spins, attendant, branded templates and same-night video delivery.', 'available', '2026-10-02 11:11:12');

-- --------------------------------------------------------

--
-- Table structure for table `gallery`
--

CREATE TABLE `gallery` (
  `id` int(11) NOT NULL,
  `title` varchar(150) DEFAULT NULL,
  `image` varchar(255) NOT NULL,
  `category` varchar(80) DEFAULT 'events',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `gallery`
--

INSERT INTO `gallery` (`id`, `title`, `image`, `category`, `created_at`) VALUES
(1, 'Golden Wedding Night', 'gal1.jpg', 'wedding', '2026-10-02 09:03:49'),
(2, 'Introduction Ceremony', 'gal2.jpg', 'introduction', '2026-10-02 09:03:49'),
(3, 'Birthday Celebration', 'gal3.jpg', 'birthday', '2026-10-02 09:03:49'),
(4, 'Corporate Gala', 'gal4.jpg', 'corporate', '2026-10-02 09:03:49'),
(5, 'Cake Display', 'gal5.jpg', 'cakes', '2026-10-02 09:03:49'),
(6, 'Stage Dec', 'gal6.jpg', 'decoration', '2026-10-02 09:03:49'),
(7, 'Live Band Night', 'gal7.jpg', 'music', '2026-10-02 09:03:49'),
(8, 'Catering Setup', 'gal8.jpg', 'catering', '2026-10-02 09:03:49'),
(9, 'REAL  MADRID LIVE', '6ac147d14eca0_1791051729.jpg', 'events', '2026-10-03 18:22:09'),
(10, 'IT KP ', '6adb36c7c8cc8_1792751303.jpg', 'events', '2026-10-23 10:28:23');

-- --------------------------------------------------------

--
-- Table structure for table `itorero_bands`
--

CREATE TABLE `itorero_bands` (
  `id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `group_type` enum('itorero','band','music_group') DEFAULT 'band',
  `media_url` varchar(255) DEFAULT NULL,
  `package_name` varchar(150) DEFAULT NULL,
  `price` decimal(12,2) DEFAULT 0.00,
  `bio` text DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `itorero_bands`
--

INSERT INTO `itorero_bands` (`id`, `name`, `photo`, `group_type`, `media_url`, `package_name`, `price`, `bio`, `phone`, `status`, `created_at`) VALUES
(1, 'Intore Legacy', 'it1.jpg', 'itorero', NULL, 'Cultural Hour', 300000.00, 'Traditional Itorero performance with drums and dance.', '+250788444001', 'active', '2026-10-02 09:03:49'),
(2, 'Kigali Live Band', 'it2.jpg', 'band', NULL, 'Evening Set', 450000.00, 'Live covers and original songs for receptions.', '+250788444002', 'active', '2026-10-02 09:03:49'),
(3, 'Harmony Voices', 'it3.jpg', 'music_group', NULL, 'Ceremony Songs', 200000.00, 'Vocal group for church and outdoor ceremonies.', '+250788444003', 'active', '2026-10-02 09:03:49');

-- --------------------------------------------------------

--
-- Table structure for table `mcs`
--

CREATE TABLE `mcs` (
  `id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `experience` varchar(100) DEFAULT NULL,
  `bio` text DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `price` decimal(12,2) DEFAULT 0.00,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `mcs`
--

INSERT INTO `mcs` (`id`, `name`, `photo`, `experience`, `bio`, `phone`, `email`, `price`, `status`, `created_at`) VALUES
(1, 'Jean Claude Mukiza', 'mc1.jpg', '8 years', 'Professional wedding & corporate MC with warm energy and flawless flow.', '+250788111001', 'jc@mkevent.rw', 150000.00, 'active', '2026-10-02 09:03:49'),
(2, 'Aline Uwase MORA', 'mc2.jpg', '1 years', 'Elegant bilingual MC for introductions, birthdays and gala nights.', '+250788111002', 'aline@mkevent.rw', 120000.00, 'active', '2026-10-02 09:03:49'),
(7, 'AMAHORO', NULL, '7', 'yes', '+250788111002', 'aline@mkevent.rw', 34567.00, 'active', '2026-10-03 17:59:15'),
(8, 'MOUNT', '6ac1472b65264_1791051563.jpg', '1 years', 'YES', '+250788111002', 'angefilletteniyigena@gmail.com', 20000.00, 'active', '2026-10-03 18:19:23'),
(9, 'KAZUNGU  ALain', '6ac5fdb2443ae_1791360434.jpg', '2 years', 'yes', '+250788111002', 'kazunguAlain@mkevent.rw', 100000.00, 'active', '2026-10-07 08:07:14'),
(10, 'KWIZERA  Samuel', '6adb33bbad095_1792750523.jpg', '1 years', 'great', '+250788111002', 'aline@mkevent.rw', 123456.00, 'active', '2026-10-23 10:15:23'),
(11, 'Samuel  kw', '6adb364d5d91a_1792751181.jpg', '4years', 'great', '+250 789690136', 'info@mkevent.rw', 100000.00, 'active', '2026-10-23 10:26:21');

-- --------------------------------------------------------

--
-- Table structure for table `photographers`
--

CREATE TABLE `photographers` (
  `id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `portfolio` text DEFAULT NULL,
  `package_name` varchar(150) DEFAULT NULL,
  `price` decimal(12,2) DEFAULT 0.00,
  `bio` text DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `photographers`
--

INSERT INTO `photographers` (`id`, `name`, `photo`, `portfolio`, `package_name`, `price`, `bio`, `phone`, `status`, `created_at`) VALUES
(1, 'Lens by Patrick', 'ph1.jpg', 'Weddings, portraits, events', 'Gold Package', 250000.00, 'Storytelling photography with cinematic edits.', '+250788333001', 'active', '2026-10-02 09:03:49'),
(2, 'Shine Studio', 'ph2.jpg', 'Corporate, parties, fashion', 'Silver Package', 180000.00, 'Clean modern style for brand and social events.', '+250788333002', 'active', '2026-10-02 09:03:49'),
(3, 'Moments RW', 'ph3.jpg', 'Traditional ceremonies', 'Heritage Package', 220000.00, 'Culture-focused coverage for introductions and Itorero.', '+250788333003', 'active', '2026-10-02 09:03:49');

-- --------------------------------------------------------

--
-- Table structure for table `service_protocols`
--

CREATE TABLE `service_protocols` (
  `id` int(11) NOT NULL,
  `title` varchar(150) NOT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(12,2) DEFAULT 0.00,
  `status` enum('available','unavailable') DEFAULT 'available',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `service_protocols`
--

INSERT INTO `service_protocols` (`id`, `title`, `photo`, `description`, `price`, `status`, `created_at`) VALUES
(3, 'boys', '6adb33ee680e2_1792750574.jpg', 'great', 100000.00, 'available', '2026-10-23 10:16:14');

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` int(11) NOT NULL,
  `site_name` varchar(150) DEFAULT 'MK Event Company',
  `tagline` varchar(255) DEFAULT 'We Make Your Events Memorable',
  `phone` varchar(50) DEFAULT '+250 788 000 000',
  `email` varchar(150) DEFAULT 'info@mkevent.rw',
  `address` text DEFAULT NULL,
  `facebook` varchar(255) DEFAULT NULL,
  `instagram` varchar(255) DEFAULT NULL,
  `twitter` varchar(255) DEFAULT NULL,
  `whatsapp` varchar(50) DEFAULT NULL,
  `about_text` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `site_name`, `tagline`, `phone`, `email`, `address`, `facebook`, `instagram`, `twitter`, `whatsapp`, `about_text`) VALUES
(1, 'MK Event Company', 'We Make Your Events Memorable', '+250 780094497', 'info@mkevent.rw', ' Rwanda, Kigali', 'https://facebook.com/mkevent', 'https://www.instagram.com/mk_event_company', 'https://x.com/@MKEventCompany', '+250780094497', 'MK Event Company is a full-service events partner in Rwanda. From MCs and Abatahira to cakes, photography, catering, decoration, sonalization, and live Itorero & bands ÔÇö we craft celebrations that feel unforgettable.');

-- --------------------------------------------------------

--
-- Table structure for table `sonalization`
--

CREATE TABLE `sonalization` (
  `id` int(11) NOT NULL,
  `title` varchar(150) NOT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `product_type` varchar(100) DEFAULT NULL,
  `price` decimal(12,2) DEFAULT 0.00,
  `description` text DEFAULT NULL,
  `status` enum('available','unavailable') DEFAULT 'available',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `sonalization`
--

INSERT INTO `sonalization` (`id`, `title`, `photo`, `product_type`, `price`, `description`, `status`, `created_at`) VALUES
(1, 'Custom Invitations', 'son1.jpg', 'Invitations', 25000.00, 'Printed & digital invitations with your names and theme.', 'available', '2026-10-02 09:03:49'),
(2, 'Name Tags & Place Cards', 'son2.jpg', 'Stationery', 15000.00, 'Elegant seating cards and guest name tags.', 'available', '2026-10-02 09:03:49'),
(3, 'Branded Event Kit', 'son3.jpg', 'Custom products', 80000.00, 'Menus, programs, signage and keepsakes in one kit.', 'available', '2026-10-02 09:03:49');

-- --------------------------------------------------------

--
-- Table structure for table `testimonials`
--

CREATE TABLE `testimonials` (
  `id` int(11) NOT NULL,
  `client_name` varchar(150) NOT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `rating` tinyint(4) DEFAULT 5,
  `message` text NOT NULL,
  `event_type` varchar(100) DEFAULT NULL,
  `status` enum('approved','pending') DEFAULT 'approved',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `testimonials`
--

INSERT INTO `testimonials` (`id`, `client_name`, `photo`, `rating`, `message`, `event_type`, `status`, `created_at`) VALUES
(1, 'Diane & Patrick', NULL, 5, 'MK Event Company made our wedding flawless ÔÇö MC, d?®cor and band were perfect.', 'Wedding', 'approved', '2026-10-02 09:03:49'),
(2, 'Boss Ltd', NULL, 5, 'Professional catering and stage setup for our annual gala. Highly recommend.', 'Corporate', 'approved', '2026-10-02 09:03:49'),
(3, 'Uwase Family', NULL, 5, 'Beautiful introduction d?®cor and amazing Abatahira. Guests still talk about it.', 'Introduction', 'approved', '2026-10-02 09:03:49');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `abatahira`
--
ALTER TABLE `abatahira`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cakes`
--
ALTER TABLE `cakes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `catering`
--
ALTER TABLE `catering`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `contact_messages`
--
ALTER TABLE `contact_messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `decorations`
--
ALTER TABLE `decorations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `extra_services`
--
ALTER TABLE `extra_services`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `gallery`
--
ALTER TABLE `gallery`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `itorero_bands`
--
ALTER TABLE `itorero_bands`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `mcs`
--
ALTER TABLE `mcs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `photographers`
--
ALTER TABLE `photographers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `service_protocols`
--
ALTER TABLE `service_protocols`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sonalization`
--
ALTER TABLE `sonalization`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `testimonials`
--
ALTER TABLE `testimonials`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `abatahira`
--
ALTER TABLE `abatahira`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `cakes`
--
ALTER TABLE `cakes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `catering`
--
ALTER TABLE `catering`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `contact_messages`
--
ALTER TABLE `contact_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `decorations`
--
ALTER TABLE `decorations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `extra_services`
--
ALTER TABLE `extra_services`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `gallery`
--
ALTER TABLE `gallery`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `itorero_bands`
--
ALTER TABLE `itorero_bands`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `mcs`
--
ALTER TABLE `mcs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `photographers`
--
ALTER TABLE `photographers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `service_protocols`
--
ALTER TABLE `service_protocols`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `sonalization`
--
ALTER TABLE `sonalization`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `testimonials`
--
ALTER TABLE `testimonials`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
