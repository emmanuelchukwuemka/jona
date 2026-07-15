-- database_complete.sql
-- Society for Community & Communication Development Research (SCCDR)
-- TRULY COMPLETE Database Schema (Total System Reset)

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";
SET FOREIGN_KEY_CHECKS = 0;

-- --------------------------------------------------------
-- 1. CLEAN SLATE: Remove ALL existing tables
-- --------------------------------------------------------

DROP TABLE IF EXISTS `message_replies`;
DROP TABLE IF EXISTS `messages`;
DROP TABLE IF EXISTS `abstracts`;
DROP TABLE IF EXISTS `journals`;
DROP TABLE IF EXISTS `transactions`;
DROP TABLE IF EXISTS `password_resets`;
DROP TABLE IF EXISTS `posts`;
DROP TABLE IF EXISTS `board_members`;
DROP TABLE IF EXISTS `subscribers`;
DROP TABLE IF EXISTS `users`;

-- --------------------------------------------------------
-- 2. TABLE STRUCTURES
-- --------------------------------------------------------

-- Table `users` (Members & Admins)
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `institution` varchar(255) DEFAULT NULL,
  `membership_category` varchar(100) NOT NULL,
  `role` ENUM('member', 'admin') NOT NULL DEFAULT 'member',
  `password_hash` varchar(255) NOT NULL,
  `profile_picture` varchar(500) DEFAULT NULL,
  `status` ENUM('active', 'suspended') NOT NULL DEFAULT 'active',
  `subscription_status` ENUM('inactive', 'active', 'expired') NOT NULL DEFAULT 'inactive',
  `subscription_end` datetime DEFAULT NULL,
  `stripe_customer_id` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table `journals` (Research Publications)
CREATE TABLE `journals` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(500) NOT NULL,
  `category` varchar(200) NOT NULL DEFAULT 'Uncategorized',
  `abstract` text DEFAULT NULL,
  `file_path` varchar(500) NOT NULL,
  `cover_image` varchar(500) DEFAULT NULL,
  `uploaded_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `uploaded_by` (`uploaded_by`),
  CONSTRAINT `fk_journals_user` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table `abstracts` (Conference Submissions)
CREATE TABLE `abstracts` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `user_id` int(11) NOT NULL,
    `title` varchar(500) NOT NULL,
    `authors` varchar(500) NOT NULL,
    `category` varchar(200) NOT NULL DEFAULT 'General',
    `abstract_text` text DEFAULT NULL,
    `file_path` varchar(500) DEFAULT NULL,
    `status` ENUM('submitted','review','accepted','rejected') NOT NULL DEFAULT 'submitted',
    `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
    PRIMARY KEY (`id`),
    KEY `user_id` (`user_id`),
    CONSTRAINT `fk_abstracts_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table `transactions` (Payments)
CREATE TABLE `transactions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `stripe_session_id` varchar(255) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `currency` varchar(10) NOT NULL DEFAULT 'USD',
  `status` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `fk_transactions_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table `posts` (Blog)
CREATE TABLE `posts` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `title` varchar(500) NOT NULL,
    `slug` varchar(520) NOT NULL,
    `category` varchar(200) NOT NULL DEFAULT 'News',
    `excerpt` varchar(500) DEFAULT NULL,
    `content` longtext NOT NULL,
    `featured_image` varchar(500) DEFAULT NULL,
    `status` ENUM('published','draft') NOT NULL DEFAULT 'published',
    `author` varchar(200) NOT NULL DEFAULT 'SCCDR Admin',
    `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
    PRIMARY KEY (`id`),
    UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table `messages` (Contact Inquiries)
CREATE TABLE `messages` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `user_id` int(11) DEFAULT NULL,
    `first_name` varchar(100) NOT NULL,
    `last_name` varchar(100) NOT NULL,
    `email` varchar(255) NOT NULL,
    `phone` varchar(50) DEFAULT NULL,
    `message` text NOT NULL,
    `status` ENUM('open', 'replied', 'closed') NOT NULL DEFAULT 'open',
    `is_read` tinyint(1) NOT NULL DEFAULT 0,
    `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table `message_replies` (Correspondence Thread)
CREATE TABLE `message_replies` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `message_id` int(11) NOT NULL,
    `sender_id` int(11) NOT NULL,
    `sender_type` ENUM('admin', 'member') NOT NULL DEFAULT 'admin',
    `reply_text` text NOT NULL,
    `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
    PRIMARY KEY (`id`),
    KEY `message_id` (`message_id`),
    CONSTRAINT `fk_replies_message` FOREIGN KEY (`message_id`) REFERENCES `messages`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table `board_members` (Team)
CREATE TABLE `board_members` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `name` varchar(300) NOT NULL,
    `role` varchar(200) NOT NULL,
    `bio` text DEFAULT NULL,
    `photo` varchar(500) DEFAULT NULL,
    `sort_order` int(11) NOT NULL DEFAULT 0,
    `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table `subscribers` (Newsletters)
CREATE TABLE `subscribers` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `email` varchar(255) NOT NULL,
    `status` ENUM('active','unsubscribed') NOT NULL DEFAULT 'active',
    `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
    PRIMARY KEY (`id`),
    UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table `password_resets`
CREATE TABLE `password_resets` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `email` varchar(255) NOT NULL,
    `code` varchar(6) NOT NULL,
    `expires_at` datetime NOT NULL,
    `used` tinyint(1) NOT NULL DEFAULT 0,
    `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
    PRIMARY KEY (`id`),
    KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- 3. INITIAL DATA
-- --------------------------------------------------------

-- Initial Admin User
INSERT INTO `users` (`first_name`, `last_name`, `email`, `role`, `password_hash`, `membership_category`, `status`) 
VALUES ('System', 'Administrator', 'admin@sccdr.org.ng', 'admin', '$2y$10$KZ63Y.EZGKkmkaVQRv8prueOGa9iB7L3g5nLIYAYI/38vuV7A4SWW', 'Professional Member', 'active');

SET FOREIGN_KEY_CHECKS = 1;
COMMIT;
