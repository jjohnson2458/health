-- CGM readings table
CREATE TABLE IF NOT EXISTS `cgm_readings` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT UNSIGNED NOT NULL,
    `glucose_value` DECIMAL(5,1) NOT NULL COMMENT 'mg/dL or mmol/L based on user preference',
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

-- CGM connections (platform credentials)
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
