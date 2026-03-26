<?php

namespace App\Models;

use Core\Database;
use Core\Model;

class Subscription extends Model
{
    protected static string $table = 'subscriptions';

    public static function getActiveForUser(int $userId): ?array
    {
        $db = Database::getInstance();
        $stmt = $db->prepare(
            'SELECT * FROM `subscriptions` WHERE `user_id` = ? AND `status` IN (?, ?) ORDER BY `created_at` DESC LIMIT 1'
        );
        $stmt->execute([$userId, 'active', 'trialing']);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public static function findByStripeSubscription(string $stripeSubId): ?array
    {
        return static::whereFirst('stripe_subscription_id', $stripeSubId);
    }

    public static function findByStripeCustomer(string $stripeCustomerId): ?array
    {
        return static::whereFirst('stripe_customer_id', $stripeCustomerId);
    }

    public static function createSubscription(int $userId, array $data): int
    {
        return static::create(array_merge(['user_id' => $userId], $data));
    }

    public static function updateByStripeSubscription(string $stripeSubId, array $data): bool
    {
        $db = Database::getInstance();
        $set = implode(', ', array_map(fn($col) => '`' . $col . '` = ?', array_keys($data)));
        $stmt = $db->prepare('UPDATE `subscriptions` SET ' . $set . ' WHERE `stripe_subscription_id` = ?');
        $values = array_values($data);
        $values[] = $stripeSubId;
        return $stmt->execute($values);
    }

    public static function getUserTier(int $userId): string
    {
        $sub = self::getActiveForUser($userId);
        if ($sub && in_array($sub['status'], ['active', 'trialing'])) {
            return $sub['tier'];
        }
        return 'free';
    }

    public static function syncUserTier(int $userId): void
    {
        $tier = self::getUserTier($userId);
        $db = Database::getInstance();
        $stmt = $db->prepare('UPDATE `users` SET `subscription_tier` = ? WHERE `id` = ?');
        $stmt->execute([$tier, $userId]);
    }

    public static function cancelExpired(): int
    {
        $db = Database::getInstance();
        $stmt = $db->prepare(
            'UPDATE `subscriptions` SET `status` = ? WHERE `status` = ? AND `current_period_end` < NOW() AND `cancel_at_period_end` = 1'
        );
        $stmt->execute(['cancelled', 'active']);
        return $stmt->rowCount();
    }
}
