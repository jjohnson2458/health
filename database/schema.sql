-- Claude Health Database Schema
-- HIPAA-compliant health & weight loss tracking

CREATE DATABASE IF NOT EXISTS `claude_health`
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE `claude_health`;

-- ============================================================
-- Users table
-- PII fields (first_name, last_name, email) stored encrypted
-- email_hash used for lookups (SHA-256)
-- ============================================================
CREATE TABLE IF NOT EXISTS `users` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `first_name` VARCHAR(500) NOT NULL COMMENT 'AES-256 encrypted',
    `last_name` VARCHAR(500) NOT NULL COMMENT 'AES-256 encrypted',
    `email` VARCHAR(500) NOT NULL COMMENT 'AES-256 encrypted',
    `email_hash` VARCHAR(64) NOT NULL UNIQUE COMMENT 'SHA-256 for lookups',
    `password_hash` VARCHAR(255) NOT NULL COMMENT 'bcrypt',
    `email_verified` TINYINT(1) NOT NULL DEFAULT 0,
    `email_token` VARCHAR(64) DEFAULT NULL,
    `email_token_expires` DATETIME DEFAULT NULL,
    `twofa_code` VARCHAR(6) DEFAULT NULL,
    `twofa_expires` DATETIME DEFAULT NULL,
    `language` ENUM('en', 'es') NOT NULL DEFAULT 'en',
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_email_hash` (`email_hash`),
    INDEX `idx_email_token` (`email_token`)
) ENGINE=InnoDB;

-- ============================================================
-- Health entries - one per user per day
-- weight and notes stored encrypted for HIPAA
-- ============================================================
CREATE TABLE IF NOT EXISTS `health_entries` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT UNSIGNED NOT NULL,
    `entry_date` DATE NOT NULL,
    `weight` VARCHAR(500) DEFAULT NULL COMMENT 'AES-256 encrypted, lbs',
    `calories` INT UNSIGNED DEFAULT NULL,
    `protein_g` DECIMAL(6,2) DEFAULT NULL,
    `carbs_g` DECIMAL(6,2) DEFAULT NULL,
    `fat_g` DECIMAL(6,2) DEFAULT NULL,
    `heart_rate` SMALLINT UNSIGNED DEFAULT NULL COMMENT 'bpm',
    `blood_sugar` DECIMAL(5,1) DEFAULT NULL COMMENT 'mg/dL',
    `exercise_minutes` SMALLINT UNSIGNED DEFAULT NULL,
    `exercise_type` VARCHAR(100) DEFAULT NULL,
    `notes` TEXT DEFAULT NULL COMMENT 'AES-256 encrypted',
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY `uq_user_date` (`user_id`, `entry_date`),
    INDEX `idx_user_id` (`user_id`),
    INDEX `idx_entry_date` (`entry_date`),
    CONSTRAINT `fk_entries_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ============================================================
-- Audit log - HIPAA requirement
-- Tracks all data access and modifications
-- ============================================================
CREATE TABLE IF NOT EXISTS `audit_log` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT UNSIGNED DEFAULT NULL,
    `action` VARCHAR(50) NOT NULL COMMENT 'login, view, edit, delete, export',
    `resource` VARCHAR(100) DEFAULT NULL COMMENT 'what was accessed',
    `details` TEXT DEFAULT NULL,
    `ip_address` VARCHAR(45) NOT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_user_id` (`user_id`),
    INDEX `idx_action` (`action`),
    INDEX `idx_created_at` (`created_at`),
    CONSTRAINT `fk_audit_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ============================================================
-- Login attempts - rate limiting
-- ============================================================
CREATE TABLE IF NOT EXISTS `login_attempts` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `email_hash` VARCHAR(64) NOT NULL,
    `ip_address` VARCHAR(45) NOT NULL,
    `attempted_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `success` TINYINT(1) NOT NULL DEFAULT 0,
    INDEX `idx_email_hash` (`email_hash`),
    INDEX `idx_ip_address` (`ip_address`),
    INDEX `idx_attempted_at` (`attempted_at`)
) ENGINE=InnoDB;
