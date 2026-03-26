-- Add subscription tier to users and create subscriptions table

ALTER TABLE `users`
    ADD COLUMN `subscription_tier` ENUM('free', 'premium', 'premium_plus') NOT NULL DEFAULT 'free' AFTER `role`;

CREATE TABLE IF NOT EXISTS `subscriptions` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT UNSIGNED NOT NULL,
    `stripe_customer_id` VARCHAR(255) DEFAULT NULL,
    `stripe_subscription_id` VARCHAR(255) DEFAULT NULL UNIQUE,
    `stripe_price_id` VARCHAR(255) DEFAULT NULL,
    `tier` ENUM('free', 'premium', 'premium_plus') NOT NULL DEFAULT 'free',
    `status` ENUM('active', 'past_due', 'cancelled', 'trialing', 'incomplete') NOT NULL DEFAULT 'active',
    `current_period_start` DATETIME DEFAULT NULL,
    `current_period_end` DATETIME DEFAULT NULL,
    `cancel_at_period_end` TINYINT(1) NOT NULL DEFAULT 0,
    `cancelled_at` DATETIME DEFAULT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_user_id` (`user_id`),
    INDEX `idx_stripe_customer` (`stripe_customer_id`),
    INDEX `idx_stripe_subscription` (`stripe_subscription_id`),
    INDEX `idx_status` (`status`),
    CONSTRAINT `fk_subscriptions_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Affiliate tracking table
CREATE TABLE IF NOT EXISTS `affiliate_clicks` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT UNSIGNED DEFAULT NULL,
    `partner` VARCHAR(50) NOT NULL COMMENT 'noom, myfitnesspal, etc.',
    `campaign` VARCHAR(100) DEFAULT NULL,
    `ip_address` VARCHAR(45) NOT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_partner` (`partner`),
    INDEX `idx_user_id` (`user_id`),
    CONSTRAINT `fk_affiliate_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB;
