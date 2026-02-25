<?php

namespace App\Services;

use App\Models\EmailTemplate;
use App\Models\Setting;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class EmailService
{
    /**
     * Apply dynamic SMTP configuration from database settings.
     */
    public static function applyMailConfig(): void
    {
        $host = Setting::get('mail_host');
        if (!$host) return; // No config saved yet — use defaults from .env

        Config::set('mail.mailers.smtp.host', $host);
        Config::set('mail.mailers.smtp.port', Setting::get('mail_port', 587));
        Config::set('mail.mailers.smtp.username', Setting::get('mail_username'));
        Config::set('mail.mailers.smtp.password', Setting::get('mail_password'));
        Config::set('mail.mailers.smtp.encryption', Setting::get('mail_encryption', 'tls'));

        Config::set('mail.default', 'smtp');
        Config::set('mail.from.address', Setting::get('mail_from_address', Setting::get('mail_username')));
        Config::set('mail.from.name', Setting::get('mail_from_name', config('app.name')));

        // EXTREMELY IMPORTANT: Purge the mailer to ensure it re-reads the configuration
        // This is critical for transitions between log/smtp or when settings change at runtime.
        Mail::purge();
    }

    /**
     * Get template data by key with variable replacement.
     *
     * @param string $key
     * @param array  $data  Replacement variables e.g. ['user_name' => 'John']
     * @return array{subject: string, body: string}
     */
    public static function getTemplate(string $key, array $data = []): array
    {
        $template = EmailTemplate::getByKey($key);

        if (!$template) {
            return [
                'subject' => ucfirst(str_replace('_', ' ', $key)),
                'body'    => 'No template found for key: ' . $key,
            ];
        }

        $subject = self::replacePlaceholders($template->subject, $data);
        $body    = self::replacePlaceholders($template->body, $data);

        return compact('subject', 'body');
    }

    /**
     * Replace {{variable}} placeholders in a string.
     */
    private static function replacePlaceholders(string $text, array $data): string
    {
        foreach ($data as $key => $value) {
            $text = str_replace('{{' . $key . '}}', (string) $value, $text);
        }
        return $text;
    }

    /**
     * Send a test email to verify SMTP configuration.
     */
    public static function sendTest(string $toEmail): bool
    {
        try {
            self::applyMailConfig();

            Mail::raw(
                'This is a test email from ' . config('app.name') . '. Your email configuration is working correctly!',
                function ($message) use ($toEmail) {
                    $message->to($toEmail)
                            ->subject('✅ Test Email — ' . config('app.name'));
                }
            );

            return true;
        } catch (\Throwable $e) {
            Log::error('Test email failed: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get the configured admin notification email.
     */
    public static function adminEmail(): ?string
    {
        return Setting::get('admin_notification_email') ?: Setting::get('company_email');
    }
}
