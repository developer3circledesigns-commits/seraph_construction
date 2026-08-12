<?php
/**
 * SERAPH BUILD CONSTRUCTION — Simple transactional email (PHP mail()).
 * Configure MAIL_TO (and optionally MAIL_FROM) in .env.
 * If not configured, sends are skipped without breaking form flows.
 */

declare(strict_types=1);

class Mail
{
    public static function isConfigured(): bool
    {
        $to = env('MAIL_TO');
        return is_string($to) && trim($to) !== '';
    }

    /**
     * Send a plain-text email to MAIL_TO.
     *
     * @return bool True when mail() accepted the message; false when skipped or failed.
     */
    public static function send(string $subject, string $body, ?string $replyTo = null): bool
    {
        if (!self::isConfigured()) {
            return false;
        }

        $to = trim((string)env('MAIL_TO'));
        $from = trim((string)(env('MAIL_FROM') ?: 'noreply@localhost'));
        $fromName = trim((string)(env('MAIL_FROM_NAME') ?: 'Seraph Build Construction'));

        $encodedSubject = '=?UTF-8?B?' . base64_encode($subject) . '?=';
        $headers = [
            'MIME-Version: 1.0',
            'Content-Type: text/plain; charset=UTF-8',
            'Content-Transfer-Encoding: 8bit',
            'From: ' . self::formatAddress($fromName, $from),
            'X-Mailer: Seraph-Construction',
        ];

        if ($replyTo !== null && $replyTo !== '' && filter_var($replyTo, FILTER_VALIDATE_EMAIL)) {
            $headers[] = 'Reply-To: ' . $replyTo;
        }

        $ok = @mail($to, $encodedSubject, $body, implode("\r\n", $headers));
        if (!$ok) {
            error_log('Mail delivery failed for subject: ' . $subject);
        }

        return $ok;
    }

    private static function formatAddress(string $name, string $email): string
    {
        $safeName = str_replace(['"', "\r", "\n"], '', $name);
        $safeEmail = filter_var($email, FILTER_VALIDATE_EMAIL);
        if ($safeEmail === false) {
            return 'noreply@localhost';
        }
        return sprintf('"%s" <%s>', $safeName, $safeEmail);
    }
}
