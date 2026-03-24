-- Error log table for application error tracking
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
