<?php

namespace App\Controllers;

use Core\Controller;
use Core\Session;
use Core\View;
use App\Models\Subscription;
use App\Models\AuditLog;

class SubscriptionController extends Controller
{
    public function pricing(): void
    {
        $currentTier = auth() ? userTier() : 'free';
        $upgradeRequired = Session::getFlash('upgrade_required', false);

        View::render('subscription/pricing', [
            'title' => __('subscription.pricing_title'),
            'currentTier' => $currentTier,
            'upgradeRequired' => $upgradeRequired,
            'stripeKey' => $_ENV['STRIPE_PUBLISHABLE_KEY'] ?? '',
        ]);
    }

    public function checkout(): void
    {
        $userId = (int) Session::get('user_id');
        $tier = $this->input('tier', 'premium');
        $interval = $this->input('interval', 'monthly');

        $priceMap = [
            'premium_monthly' => $_ENV['STRIPE_PRICE_PREMIUM'] ?? '',
            'premium_yearly' => $_ENV['STRIPE_PRICE_PREMIUM_YEARLY'] ?? '',
            'premium_plus_monthly' => $_ENV['STRIPE_PRICE_PREMIUM_PLUS'] ?? '',
            'premium_plus_yearly' => $_ENV['STRIPE_PRICE_PREMIUM_PLUS_YEARLY'] ?? '',
        ];

        $priceKey = $tier . '_' . $interval;
        $priceId = $priceMap[$priceKey] ?? '';

        if (empty($priceId) || empty($_ENV['STRIPE_SECRET_KEY'])) {
            Session::flash('errors', __('subscription.stripe_not_configured'));
            $this->redirect('/pricing');
            return;
        }

        $userData = Session::get('user_data', []);

        // Find or create Stripe customer
        $sub = Subscription::getActiveForUser($userId);
        $customerId = $sub['stripe_customer_id'] ?? null;

        $stripe = new \stdClass();
        try {
            \Stripe\Stripe::setApiKey($_ENV['STRIPE_SECRET_KEY']);

            if (!$customerId) {
                $customer = \Stripe\Customer::create([
                    'email' => $userData['email'] ?? '',
                    'metadata' => ['user_id' => $userId],
                ]);
                $customerId = $customer->id;
            }

            $session = \Stripe\Checkout\Session::create([
                'customer' => $customerId,
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price' => $priceId,
                    'quantity' => 1,
                ]],
                'mode' => 'subscription',
                'success_url' => url('subscription/success?session_id={CHECKOUT_SESSION_ID}'),
                'cancel_url' => url('pricing'),
                'metadata' => [
                    'user_id' => $userId,
                    'tier' => $tier,
                ],
            ]);

            AuditLog::log($userId, 'create', 'subscription', "Started checkout for {$tier} ({$interval})");
            $this->redirect($session->url);
        } catch (\Exception $e) {
            Session::flash('errors', __('subscription.checkout_error'));
            $this->redirect('/pricing');
        }
    }

    public function success(): void
    {
        $userId = (int) Session::get('user_id');
        $sessionId = $this->input('session_id');

        if ($sessionId && !empty($_ENV['STRIPE_SECRET_KEY'])) {
            try {
                \Stripe\Stripe::setApiKey($_ENV['STRIPE_SECRET_KEY']);
                $session = \Stripe\Checkout\Session::retrieve($sessionId);
                $stripeSubId = $session->subscription;
                $stripeSub = \Stripe\Subscription::retrieve($stripeSubId);

                $tier = $session->metadata->tier ?? 'premium';

                Subscription::createSubscription($userId, [
                    'stripe_customer_id' => $session->customer,
                    'stripe_subscription_id' => $stripeSubId,
                    'stripe_price_id' => $stripeSub->items->data[0]->price->id ?? '',
                    'tier' => $tier,
                    'status' => 'active',
                    'current_period_start' => date('Y-m-d H:i:s', $stripeSub->current_period_start),
                    'current_period_end' => date('Y-m-d H:i:s', $stripeSub->current_period_end),
                ]);

                Subscription::syncUserTier($userId);

                // Update session data
                $userData = Session::get('user_data', []);
                $userData['subscription_tier'] = $tier;
                Session::set('user_data', $userData);

                AuditLog::log($userId, 'create', 'subscription', "Activated {$tier} subscription");
            } catch (\Exception $e) {
                // Log error but don't block - webhook will handle it
            }
        }

        Session::flash('success', __('subscription.activated'));
        $this->redirect('/dashboard');
    }

    public function portal(): void
    {
        $userId = (int) Session::get('user_id');
        $sub = Subscription::getActiveForUser($userId);

        if (!$sub || empty($sub['stripe_customer_id']) || empty($_ENV['STRIPE_SECRET_KEY'])) {
            $this->redirect('/pricing');
            return;
        }

        try {
            \Stripe\Stripe::setApiKey($_ENV['STRIPE_SECRET_KEY']);
            $portalSession = \Stripe\BillingPortal\Session::create([
                'customer' => $sub['stripe_customer_id'],
                'return_url' => url('dashboard'),
            ]);

            AuditLog::log($userId, 'view', 'subscription', 'Accessed billing portal');
            $this->redirect($portalSession->url);
        } catch (\Exception $e) {
            Session::flash('errors', __('subscription.portal_error'));
            $this->redirect('/dashboard');
        }
    }

    public function webhook(): void
    {
        $payload = file_get_contents('php://input');
        $sigHeader = $_SERVER['HTTP_STRIPE_SIGNATURE'] ?? '';
        $webhookSecret = $_ENV['STRIPE_WEBHOOK_SECRET'] ?? '';

        if (empty($webhookSecret)) {
            http_response_code(400);
            exit;
        }

        try {
            \Stripe\Stripe::setApiKey($_ENV['STRIPE_SECRET_KEY']);
            $event = \Stripe\Webhook::constructEvent($payload, $sigHeader, $webhookSecret);
        } catch (\Exception $e) {
            http_response_code(400);
            exit;
        }

        switch ($event->type) {
            case 'customer.subscription.updated':
            case 'customer.subscription.deleted':
                $stripeSub = $event->data->object;
                $this->handleSubscriptionChange($stripeSub);
                break;

            case 'invoice.payment_failed':
                $invoice = $event->data->object;
                $subId = $invoice->subscription;
                if ($subId) {
                    Subscription::updateByStripeSubscription($subId, ['status' => 'past_due']);
                    $sub = Subscription::findByStripeSubscription($subId);
                    if ($sub) {
                        Subscription::syncUserTier((int) $sub['user_id']);
                    }
                }
                break;
        }

        http_response_code(200);
        echo json_encode(['status' => 'ok']);
        exit;
    }

    private function handleSubscriptionChange(object $stripeSub): void
    {
        $subId = $stripeSub->id;
        $status = $stripeSub->status;

        $statusMap = [
            'active' => 'active',
            'trialing' => 'trialing',
            'past_due' => 'past_due',
            'canceled' => 'cancelled',
            'incomplete' => 'incomplete',
            'incomplete_expired' => 'cancelled',
            'unpaid' => 'past_due',
        ];

        $mappedStatus = $statusMap[$status] ?? 'cancelled';

        Subscription::updateByStripeSubscription($subId, [
            'status' => $mappedStatus,
            'current_period_start' => date('Y-m-d H:i:s', $stripeSub->current_period_start),
            'current_period_end' => date('Y-m-d H:i:s', $stripeSub->current_period_end),
            'cancel_at_period_end' => $stripeSub->cancel_at_period_end ? 1 : 0,
            'cancelled_at' => $stripeSub->canceled_at ? date('Y-m-d H:i:s', $stripeSub->canceled_at) : null,
        ]);

        $sub = Subscription::findByStripeSubscription($subId);
        if ($sub) {
            if ($mappedStatus === 'cancelled') {
                $db = \Core\Database::getInstance();
                $stmt = $db->prepare('UPDATE `users` SET `subscription_tier` = ? WHERE `id` = ?');
                $stmt->execute(['free', $sub['user_id']]);
            } else {
                Subscription::syncUserTier((int) $sub['user_id']);
            }
        }
    }
}
