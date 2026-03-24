-- Medications table
CREATE TABLE IF NOT EXISTS `medications` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT UNSIGNED NOT NULL,
    `name` VARCHAR(500) NOT NULL COMMENT 'AES-256 encrypted',
    `dosage` VARCHAR(500) DEFAULT NULL COMMENT 'AES-256 encrypted',
    `frequency` VARCHAR(100) DEFAULT NULL COMMENT 'daily, twice daily, weekly, as needed, etc.',
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

-- Medication change history (HIPAA audit trail)
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

-- Provider connections (patient-provider links)
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
