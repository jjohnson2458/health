<?php

namespace App\Controllers;

use Core\Controller;
use Core\Session;
use App\Models\AffiliateClick;

class AffiliateController extends Controller
{
    private array $affiliateLinks = [
        'noom' => 'https://www.noom.com/',
        'myfitnesspal' => 'https://www.myfitnesspal.com/',
        'nutrisense' => 'https://www.nutrisense.io/',
        'amazon-health' => 'https://www.amazon.com/health-personal-care/b?ie=UTF8&node=3760901',
    ];

    public function redirectPartner(string $partner): void
    {
        $url = $this->affiliateLinks[$partner] ?? null;

        if (!$url) {
            $this->redirect('/pricing');
            return;
        }

        $userId = auth() ? (int) Session::get('user_id') : null;
        $campaign = $this->input('utm_campaign');

        AffiliateClick::track($userId, $partner, $campaign);

        header('Location: ' . $url);
        exit;
    }
}
