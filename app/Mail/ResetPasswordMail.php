<?php

namespace App\Mail;

use App\Models\User;
use App\Models\Setting;

class ResetPasswordMail extends BaseMail
{
    public function __construct(User $user)
    {
        $this->templateKey  = 'reset_password';
        $this->templateData = [
            'user_name' => $user->name,
            'site_name' => Setting::get('site_name', config('app.name')),
            'login_url' => url('/login'),
        ];
    }

    public function build(): self
    {
        return $this->buildFromTemplate();
    }
}
