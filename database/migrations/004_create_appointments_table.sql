-- Appointments table
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
