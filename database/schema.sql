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
    `password_reset_token` VARCHAR(64) DEFAULT NULL,
    `password_reset_expires` DATETIME DEFAULT NULL,
    `phone_number` VARCHAR(500) DEFAULT NULL COMMENT 'AES-256 encrypted',
    `phone_hash` VARCHAR(64) DEFAULT NULL,
    `sms_opt_in` TINYINT(1) NOT NULL DEFAULT 0,
    `role` ENUM('user', 'admin') NOT NULL DEFAULT 'user',
    `language` ENUM('en', 'es') NOT NULL DEFAULT 'en',
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_email_hash` (`email_hash`),
    INDEX `idx_email_token` (`email_token`),
    INDEX `idx_password_reset_token` (`password_reset_token`),
    INDEX `idx_phone_hash` (`phone_hash`)
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

-- ============================================================
-- Error log - application error tracking
-- ============================================================
CREATE TABLE IF NOT EXISTS `error_log` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `error_level` VARCHAR(20) NOT NULL COMMENT 'ERROR, WARNING, NOTICE, EXCEPTION',
    `message` TEXT NOT NULL,
    `file` VARCHAR(500) DEFAULT NULL,
    `line` INT UNSIGNED DEFAULT NULL,
    `stack_trace` TEXT DEFAULT NULL,
    `user_id` INT UNSIGNED DEFAULT NULL,
    `ip_address` VARCHAR(45) NOT NULL DEFAULT '0.0.0.0',
    `url` VARCHAR(500) DEFAULT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_error_level` (`error_level`),
    INDEX `idx_user_id` (`user_id`),
    INDEX `idx_created_at` (`created_at`),
    CONSTRAINT `fk_error_log_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ============================================================
-- Medications - encrypted prescription tracking
-- ============================================================
CREATE TABLE IF NOT EXISTS `medications` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT UNSIGNED NOT NULL,
    `name` VARCHAR(500) NOT NULL COMMENT 'AES-256 encrypted',
    `dosage` VARCHAR(500) DEFAULT NULL COMMENT 'AES-256 encrypted',
    `frequency` VARCHAR(100) DEFAULT NULL,
    `prescriber_name` VARCHAR(500) DEFAULT NULL COMMENT 'AES-256 encrypted',
    `prescriber_npi` VARCHAR(10) DEFAULT NULL,
    `prescribed_date` DATE DEFAULT NULL,
    `status` ENUM('active', 'inactive', 'discontinued') NOT NULL DEFAULT 'active',
    `discontinued_date` DATE DEFAULT NULL,
    `discontinued_reason` VARCHAR(500) DEFAULT NULL COMMENT 'AES-256 encrypted',
    `notes` TEXT DEFAULT NULL COMMENT 'AES-256 encrypted',
    `source` ENUM('manual', 'provider_push', 'csv_import') NOT NULL DEFAULT 'manual',
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_user_id` (`user_id`),
    INDEX `idx_status` (`status`),
    INDEX `idx_user_status` (`user_id`, `status`),
    CONSTRAINT `fk_medications_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ============================================================
-- Medication history - HIPAA audit trail for Rx changes
-- ============================================================
CREATE TABLE IF NOT EXISTS `medication_history` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `medication_id` INT UNSIGNED NOT NULL,
    `user_id` INT UNSIGNED NOT NULL,
    `action` ENUM('added', 'updated', 'discontinued', 'reactivated') NOT NULL,
    `changed_by` ENUM('patient', 'provider') NOT NULL DEFAULT 'patient',
    `details` TEXT DEFAULT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_medication_id` (`medication_id`),
    INDEX `idx_user_id` (`user_id`),
    CONSTRAINT `fk_med_history_med` FOREIGN KEY (`medication_id`) REFERENCES `medications`(`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_med_history_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ============================================================
-- Provider connections - patient-provider links
-- ============================================================
CREATE TABLE IF NOT EXISTS `provider_connections` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT UNSIGNED NOT NULL,
    `provider_name` VARCHAR(500) DEFAULT NULL COMMENT 'AES-256 encrypted',
    `provider_email` VARCHAR(500) DEFAULT NULL COMMENT 'AES-256 encrypted',
    `provider_email_hash` VARCHAR(64) DEFAULT NULL,
    `provider_npi` VARCHAR(10) DEFAULT NULL,
    `access_token` VARCHAR(64) NOT NULL UNIQUE,
    `connection_status` ENUM('pending', 'active', 'revoked') NOT NULL DEFAULT 'pending',
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_user_id` (`user_id`),
    INDEX `idx_access_token` (`access_token`),
    INDEX `idx_status` (`connection_status`),
    CONSTRAINT `fk_provider_conn_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ============================================================
-- Appointments - encrypted scheduling
-- ============================================================
CREATE TABLE IF NOT EXISTS `appointments` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT UNSIGNED NOT NULL,
    `provider_name` VARCHAR(500) DEFAULT NULL COMMENT 'AES-256 encrypted',
    `appointment_date` DATE NOT NULL,
    `appointment_time` TIME DEFAULT NULL,
    `location` VARCHAR(500) DEFAULT NULL COMMENT 'AES-256 encrypted',
    `type` ENUM('checkup', 'lab', 'specialist', 'dental', 'vision', 'therapy', 'other') NOT NULL DEFAULT 'checkup',
    `notes` TEXT DEFAULT NULL COMMENT 'AES-256 encrypted',
    `email_reminder_sent` TINYINT(1) NOT NULL DEFAULT 0,
    `sms_reminder_sent` TINYINT(1) NOT NULL DEFAULT 0,
    `source` ENUM('manual', 'provider_push') NOT NULL DEFAULT 'manual',
    `status` ENUM('scheduled', 'completed', 'cancelled') NOT NULL DEFAULT 'scheduled',
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_user_id` (`user_id`),
    INDEX `idx_appointment_date` (`appointment_date`),
    INDEX `idx_user_status` (`user_id`, `status`),
    INDEX `idx_reminder` (`appointment_date`, `email_reminder_sent`, `sms_reminder_sent`),
    CONSTRAINT `fk_appointments_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ============================================================
-- CGM readings - continuous glucose monitor data
-- ============================================================
CREATE TABLE IF NOT EXISTS `cgm_readings` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT UNSIGNED NOT NULL,
    `glucose_value` DECIMAL(5,1) NOT NULL,
    `unit` ENUM('mg_dl', 'mmol_l') NOT NULL DEFAULT 'mg_dl',
    `trend_arrow` ENUM('rising_fast', 'rising', 'stable', 'falling', 'falling_fast', 'unknown') DEFAULT 'unknown',
    `reading_timestamp` DATETIME NOT NULL,
    `source` ENUM('libre', 'dexcom', 'nightscout', 'manual', 'csv_import') NOT NULL DEFAULT 'manual',
    `device_serial` VARCHAR(100) DEFAULT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_user_id` (`user_id`),
    INDEX `idx_user_timestamp` (`user_id`, `reading_timestamp`),
    INDEX `idx_reading_timestamp` (`reading_timestamp`),
    UNIQUE KEY `uq_user_source_timestamp` (`user_id`, `source`, `reading_timestamp`),
    CONSTRAINT `fk_cgm_readings_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ============================================================
-- CGM connections - platform credentials
-- ============================================================
CREATE TABLE IF NOT EXISTS `cgm_connections` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT UNSIGNED NOT NULL,
    `platform` ENUM('libre', 'dexcom', 'nightscout') NOT NULL,
    `credentials_encrypted` TEXT DEFAULT NULL COMMENT 'AES-256 encrypted JSON',
    `nightscout_url` VARCHAR(500) DEFAULT NULL,
    `last_sync` DATETIME DEFAULT NULL,
    `sync_status` ENUM('active', 'error', 'disabled') NOT NULL DEFAULT 'active',
    `sync_error` VARCHAR(500) DEFAULT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY `uq_user_platform` (`user_id`, `platform`),
    INDEX `idx_user_id` (`user_id`),
    INDEX `idx_sync_status` (`sync_status`),
    CONSTRAINT `fk_cgm_conn_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ============================================================
-- File uploads tracking
-- ============================================================
CREATE TABLE IF NOT EXISTS `uploads` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT UNSIGNED NOT NULL,
    `filename` VARCHAR(255) NOT NULL,
    `original_name` VARCHAR(255) NOT NULL,
    `path` VARCHAR(500) NOT NULL,
    `mime_type` VARCHAR(100) NOT NULL,
    `size` INT UNSIGNED NOT NULL COMMENT 'bytes',
    `storage_type` ENUM('local', 's3') NOT NULL DEFAULT 'local',
    `related_type` VARCHAR(50) DEFAULT NULL COMMENT 'medication, cgm_report, profile, etc.',
    `related_id` INT UNSIGNED DEFAULT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_user_id` (`user_id`),
    INDEX `idx_related` (`related_type`, `related_id`),
    CONSTRAINT `fk_uploads_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ============================================================
-- Migrations tracking
-- ============================================================
CREATE TABLE IF NOT EXISTS `migrations` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `filename` VARCHAR(255) NOT NULL UNIQUE,
    `executed_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;
