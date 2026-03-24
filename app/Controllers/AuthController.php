<?php

namespace App\Controllers;

use Core\Controller;
use Core\Mailer;
use Core\Session;
use Core\View;
use App\Models\User;
use App\Models\LoginAttempt;
use App\Models\AuditLog;

class AuthController extends Controller
{
    public function showLogin(): void
    {
        View::render('auth/login', ['title' => __('auth.login')], 'layouts/auth');
    }

    public function showRegister(): void
    {
        if (isset($_ENV['NEW_USER_REGISTRATION']) && $_ENV['NEW_USER_REGISTRATION'] === 'false') {
            Session::flash('errors', [__('auth.registration_disabled')]);
            $this->redirect('/login');
            return;
        }
        View::render('auth/register', ['title' => __('auth.register')], 'layouts/auth');
    }

    public function register(): void
    {
        if (isset($_ENV['NEW_USER_REGISTRATION']) && $_ENV['NEW_USER_REGISTRATION'] === 'false') {
            Session::flash('errors', [__('auth.registration_disabled')]);
            $this->redirect('/login');
            return;
        }

        $data = $this->validate([
            'first_name' => 'required|max:100',
            'last_name' => 'required|max:100',
            'email' => 'required|email',
            'password' => 'required|min:8',
            'password_confirm' => 'required',
        ]);

        if ($data['password'] !== $data['password_confirm']) {
            Session::flash('errors', ['Passwords do not match.']);
            $this->back();
            return;
        }

        // Check if email already exists
        $existing = User::findByEmail($data['email']);
        if ($existing) {
            Session::flash('errors', ['An account with this email already exists.']);
            $this->back();
            return;
        }

        $userId = User::createUser(
            $data['first_name'],
            $data['last_name'],
            $data['email'],
            $data['password']
        );

        $user = User::find($userId);

        // Send verification email
        $this->sendVerificationEmail($data['email'], $data['first_name'], $user['email_token']);

        AuditLog::log($userId, 'register', 'users');

        Session::flash('success', __('auth.registration_success'));
        $this->redirect('/login');
    }

    public function verifyEmail(string $token): void
    {
        $user = User::whereFirst('email_token', $token);

        if (!$user) {
            Session::flash('errors', ['Invalid verification link.']);
            $this->redirect('/login');
            return;
        }

        if (strtotime($user['email_token_expires']) < time()) {
            Session::flash('errors', ['Verification link has expired. Please register again.']);
            $this->redirect('/register');
            return;
        }

        User::update($user['id'], [
            'email_verified' => 1,
            'email_token' => null,
            'email_token_expires' => null,
        ]);

        AuditLog::log($user['id'], 'verify_email', 'users');

        Session::flash('success', 'Email verified successfully! You can now log in.');
        $this->redirect('/login');
    }

    public function login(): void
    {
        $data = $this->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';

        // Rate limit check
        if (LoginAttempt::isRateLimited($data['email'], $ip)) {
            Session::flash('errors', [__('auth.account_locked')]);
            $this->back();
            return;
        }

        $user = User::findByEmail($data['email']);

        if (!$user || !password_verify($data['password'], $user['password_hash'])) {
            LoginAttempt::record($data['email'], $ip, false);
            Session::flash('errors', [__('auth.invalid_credentials')]);
            $this->back();
            return;
        }

        if (!$user['email_verified']) {
            Session::flash('errors', [__('auth.email_not_verified')]);
            $this->back();
            return;
        }

        LoginAttempt::record($data['email'], $ip, true);

        // Check if 2FA is enabled (requires mail script)
        $twoFaEnabled = !empty($_ENV['TWOFA_ENABLED']) && $_ENV['TWOFA_ENABLED'] === 'true';

        if ($twoFaEnabled) {
            // Generate 2FA code
            $code = User::generateTwoFaCode($user['id']);
            $decrypted = User::decryptUser($user);

            // Send 2FA code via email
            $this->send2FACode($decrypted['email'], $decrypted['first_name'], $code);

            // Store user_id in session but mark as NOT fully verified yet
            Session::set('user_id', $user['id']);
            Session::set('auth_verified', false);
            Session::set('lang', $user['language']);

            AuditLog::log($user['id'], 'login_step1', 'auth');

            $this->redirect('/verify-code');
        } else {
            // Skip 2FA - go straight to dashboard
            Session::set('user_id', $user['id']);
            Session::set('auth_verified', true);
            Session::set('lang', $user['language']);
            Session::regenerate();

            $decrypted = User::decryptUser($user);
            Session::set('user_data', [
                'id' => $user['id'],
                'first_name' => $decrypted['first_name'],
                'last_name' => $decrypted['last_name'],
                'email' => $decrypted['email'],
                'language' => $user['language'],
                'role' => $user['role'] ?? 'user',
            ]);

            AuditLog::log($user['id'], 'login_complete', 'auth');
            Session::flash('success', __('auth.login_success'));
            $this->redirect('/dashboard');
        }
    }

    public function showVerifyCode(): void
    {
        if (!Session::has('user_id') || Session::get('auth_verified', false)) {
            $this->redirect('/dashboard');
            return;
        }
        View::render('auth/verify-code', ['title' => __('auth.verify_code')], 'layouts/auth');
    }

    public function verifyCode(): void
    {
        $userId = Session::get('user_id');
        if (!$userId) {
            $this->redirect('/login');
            return;
        }

        $code = trim($this->input('code', ''));

        if (!User::verifyTwoFaCode($userId, $code)) {
            Session::flash('errors', [__('auth.invalid_code')]);
            $this->redirect('/verify-code');
            return;
        }

        // Fully authenticated
        Session::set('auth_verified', true);
        Session::regenerate();

        $rawUser = User::find($userId);
        $user = User::decryptUser($rawUser);
        Session::set('user_data', [
            'id' => $userId,
            'first_name' => $user['first_name'],
            'last_name' => $user['last_name'],
            'email' => $user['email'],
            'language' => $user['language'],
            'role' => $rawUser['role'] ?? 'user',
        ]);

        AuditLog::log($userId, 'login_complete', 'auth');

        Session::flash('success', __('auth.login_success'));
        $this->redirect('/dashboard');
    }

    public function resendCode(): void
    {
        $userId = Session::get('user_id');
        if (!$userId) {
            $this->redirect('/login');
            return;
        }

        $code = User::generateTwoFaCode($userId);
        $user = User::decryptUser(User::find($userId));
        $this->send2FACode($user['email'], $user['first_name'], $code);

        Session::flash('success', __('auth.verify_code_sent'));
        $this->redirect('/verify-code');
    }

    public function logout(): void
    {
        $userId = Session::get('user_id');
        if ($userId) {
            AuditLog::log($userId, 'logout', 'auth');
        }
        Session::destroy();
        $this->redirect('/login');
    }

    public function showForgotPassword(): void
    {
        View::render('auth/forgot-password', ['title' => __('auth.forgot_password')], 'layouts/auth');
    }

    public function forgotPassword(): void
    {
        $data = $this->validate([
            'email' => 'required|email',
        ]);

        $token = User::generatePasswordResetToken($data['email']);

        if ($token) {
            $user = User::findByEmail($data['email']);
            $decrypted = User::decryptUser($user);
            $this->sendPasswordResetEmail($decrypted['email'], $decrypted['first_name'], $token);
            AuditLog::log($user['id'], 'password_reset_requested', 'auth');
        }

        // Always show success to prevent email enumeration
        Session::flash('success', __('auth.reset_link_sent'));
        $this->redirect('/forgot-password');
    }

    public function showResetPassword(string $token): void
    {
        $user = User::findByResetToken($token);
        if (!$user) {
            Session::flash('errors', [__('auth.invalid_reset_token')]);
            $this->redirect('/forgot-password');
            return;
        }

        View::render('auth/reset-password', [
            'title' => __('auth.reset_password'),
            'token' => $token,
        ], 'layouts/auth');
    }

    public function resetPassword(): void
    {
        $data = $this->validate([
            'token' => 'required',
            'password' => 'required|min:8',
            'password_confirm' => 'required',
        ]);

        if ($data['password'] !== $data['password_confirm']) {
            Session::flash('errors', ['Passwords do not match.']);
            $this->redirect('/reset-password/' . $data['token']);
            return;
        }

        $user = User::findByResetToken($data['token']);
        if (!$user) {
            Session::flash('errors', [__('auth.invalid_reset_token')]);
            $this->redirect('/forgot-password');
            return;
        }

        User::resetPassword($user['id'], $data['password']);
        AuditLog::log($user['id'], 'password_reset_complete', 'auth');

        Session::flash('success', __('auth.password_reset_success'));
        $this->redirect('/login');
    }

    private function sendPasswordResetEmail(string $email, string $firstName, string $token): void
    {
        $url = rtrim($_ENV['APP_URL'], '/') . '/reset-password/' . $token;
        $subject = __('app_name') . ' - ' . __('auth.reset_password');
        $body = "<h2>" . e($firstName) . ", " . e(__('auth.reset_password')) . "</h2>"
            . "<p>Click the button below to reset your password. This link expires in 1 hour.</p>"
            . "<p><a href=\"{$url}\" style=\"display:inline-block;padding:10px 24px;background:#0d6efd;color:#fff;text-decoration:none;border-radius:4px;\">Reset Password</a></p>"
            . "<p style=\"color:#666;\">If you did not request this, please ignore this email.</p>"
            . "<p style=\"color:#666;font-size:12px;\">" . e(__('hipaa_notice')) . "</p>";

        Mailer::send($email, $subject, $body, 'claude_health');
    }

    private function sendVerificationEmail(string $email, string $firstName, string $token): void
    {
        $url = rtrim($_ENV['APP_URL'], '/') . '/verify-email/' . $token;
        $subject = __('app_name') . ' - ' . __('auth.verify_email');
        $body = "<h2>" . e(__('welcome')) . ", " . e($firstName) . "!</h2>"
            . "<p>" . e(__('auth.verify_email_sent')) . "</p>"
            . "<p><a href=\"{$url}\" style=\"display:inline-block;padding:10px 24px;background:#0d6efd;color:#fff;text-decoration:none;border-radius:4px;\">Verify Email</a></p>"
            . "<p style=\"color:#666;font-size:12px;\">" . e(__('hipaa_notice')) . "</p>";

        Mailer::send($email, $subject, $body, 'claude_health');
    }

    private function send2FACode(string $email, string $firstName, string $code): void
    {
        $subject = __('app_name') . ' - ' . __('auth.verify_code');
        $body = "<h2>" . e($firstName) . ", " . e(__('auth.verify_code_sent')) . "</h2>"
            . "<p style=\"font-size:32px;font-weight:bold;letter-spacing:8px;text-align:center;padding:20px;background:#f8f9fa;border-radius:8px;\">{$code}</p>"
            . "<p style=\"color:#666;\">This code expires in 10 minutes.</p>"
            . "<p style=\"color:#666;font-size:12px;\">" . e(__('hipaa_notice')) . "</p>";

        Mailer::send($email, $subject, $body, 'claude_health');
    }
}
