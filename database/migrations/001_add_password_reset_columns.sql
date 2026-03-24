-- Add password reset columns to users table
ALTER TABLE `users`
    ADD COLUMN `password_reset_token` VARCHAR(64) DEFAULT NULL AFTER `twofa_expires`,
    ADD COLUMN `password_reset_expires` DATETIME DEFAULT NULL AFTER `password_reset_token`,
    ADD COLUMN `phone_number` VARCHAR(500) DEFAULT NULL COMMENT 'AES-256 encrypted' AFTER `password_reset_expires`,
    ADD COLUMN `phone_hash` VARCHAR(64) DEFAULT NULL AFTER `phone_number`,
    ADD COLUMN `sms_opt_in` TINYINT(1) NOT NULL DEFAULT 0 AFTER `phone_hash`,
    ADD INDEX `idx_password_reset_token` (`password_reset_token`),
    ADD INDEX `idx_phone_hash` (`phone_hash`);
