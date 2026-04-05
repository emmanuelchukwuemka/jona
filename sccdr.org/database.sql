-- database.sql
-- Run this script in phpMyAdmin or your MySQL client to set up the database

CREATE DATABASE IF NOT EXISTS `sccdr_db` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `sccdr_db`;

-- --------------------------------------------------------
-- Table structure for table `users` (Members)
-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `institution` varchar(255) DEFAULT NULL,
  `membership_category` varchar(100) NOT NULL,
  `role` ENUM('member', 'admin') NOT NULL DEFAULT 'member',
  `password_hash` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Table structure for table `journals`
-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `journals` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(500) NOT NULL,
  `category` varchar(200) NOT NULL DEFAULT 'Uncategorized',
  `abstract` text DEFAULT NULL,
  `file_path` varchar(500) NOT NULL,
  `cover_image` varchar(500) DEFAULT NULL,
  `uploaded_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  FOREIGN KEY (`uploaded_by`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
