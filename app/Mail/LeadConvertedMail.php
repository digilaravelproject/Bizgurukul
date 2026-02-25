<?php

namespace App\Mail;

use App\Models\Setting;

class LeadConvertedMail extends BaseMail
{
    public function __construct(string $name, string $email, string $password)
    {
        $this->templateKey  = 'lead_converted';
        $this->templateData = [
            'user_name'   => $name,
            'site_name'   => Setting::get('site_name', config('app.name')),
            'login_email' => $email,
            'password'    => $password,
            'login_url'   => url('/login'),
        ];
    }

    public function build(): self
    {
        return $this->buildFromTemplate();
    }
}
