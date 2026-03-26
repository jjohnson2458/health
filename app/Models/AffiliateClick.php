<?php

namespace App\Models;

use Core\Model;

class AffiliateClick extends Model
{
    protected static string $table = 'affiliate_clicks';

    public static function track(?int $userId, string $partner, ?string $campaign = null): int
    {
        return static::create([
            'user_id' => $userId,
            'partner' => $partner,
            'campaign' => $campaign,
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0',
        ]);
    }
}
