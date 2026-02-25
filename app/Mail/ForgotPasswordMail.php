<?php

namespace App\Mail;

use App\Models\User;
use App\Models\Setting;

class ForgotPasswordMail extends BaseMail
{
    public function __construct(User $user, string $resetUrl, int $expiryMinutes = 60)
    {
        $this->templateKey  = 'forgot_password';
        $this->templateData = [
            'user_name'      => $user->name,
            'site_name'      => Setting::get('site_name', config('app.name')),
            'reset_url'      => $resetUrl,
            'expiry_minutes' => $expiryMinutes,
        ];
    }

    public function build(): self
    {
        return $this->buildFromTemplate();
    }
}
