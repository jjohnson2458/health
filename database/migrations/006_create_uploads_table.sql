-- File uploads tracking table
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
