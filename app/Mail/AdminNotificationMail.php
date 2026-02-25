<?php

namespace App\Mail;

use App\Models\Setting;

class AdminNotificationMail extends BaseMail
{
    public function __construct(string $title, string $message)
    {
        $this->templateKey  = 'admin_notification';
        $this->templateData = [
            'title'     => $title,
            'message'   => $message,
            'site_name' => Setting::get('site_name', config('app.name')),
        ];
    }

    public function build(): self
    {
        return $this->buildFromTemplate();
    }
}
