<?php

namespace Core;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Mailer
{
    public static function send(string $to, string $subject, string $body, ?string $project = null): bool
    {
        // Try direct SMTP first (production), fall back to claude_messenger (local dev)
        $smtpHost = $_ENV['SMTP_HOST'] ?? '';

        if ($smtpHost) {
            return self::sendViaSMTP($to, $subject, $body, $project);
        }

        return self::sendViaMessenger($to, $subject, $body, $project);
    }

    private static function sendViaSMTP(string $to, string $subject, string $body, ?string $project): bool
    {
        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host       = $_ENV['SMTP_HOST'];
            $mail->SMTPAuth   = true;
            $mail->Username   = $_ENV['SMTP_USERNAME'] ?? '';
            $mail->Password   = $_ENV['SMTP_PASSWORD'] ?? '';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = (int) ($_ENV['SMTP_PORT'] ?? 587);

            $fromEmail = $_ENV['SMTP_FROM_EMAIL'] ?? $_ENV['SMTP_USERNAME'] ?? 'noreply@example.com';
            $fromName  = $_ENV['MAIL_FROM_NAME'] ?? 'Claude Health';

            $mail->setFrom($fromEmail, $fromName);
            $mail->addAddress($to);

            $replyProject = $project ?? 'claude_health';
            $replyDomain = $_ENV['SMTP_REPLY_DOMAIN'] ?? '';
            if ($replyDomain) {
                $mail->addReplyTo("reply+{$replyProject}@{$replyDomain}", "Reply to {$replyProject}");
            }

            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = $body;
            $mail->AltBody = strip_tags(str_replace(['<br>', '<br/>', '<br />'], "\n", $body));

            $mail->send();
            return true;
        } catch (Exception $e) {
            error_log("Mailer SMTP error: " . $e->getMessage());
            return false;
        }
    }

    private static function sendViaMessenger(string $to, string $subject, string $body, ?string $project): bool
    {
        $script = $_ENV['MAIL_NOTIFY_SCRIPT'] ?? '';
        if (!$script || !file_exists($script)) {
            error_log("Mailer: No SMTP config and messenger script not found: {$script}");
            return false;
        }

        $cmd = sprintf(
            'php %s --subject %s --body %s --to %s --project %s',
            escapeshellarg($script),
            escapeshellarg($subject),
            escapeshellarg($body),
            escapeshellarg($to),
            escapeshellarg($project ?? 'claude_health')
        );

        exec($cmd . ' 2>&1', $output, $returnCode);
        if ($returnCode !== 0) {
            error_log("Mailer messenger error: " . implode("\n", $output));
            return false;
        }

        return true;
    }
}
