<?php

namespace App\Mail;

use App\Models\ContactInquiry;
use App\Models\Setting;

class ContactInquiryMail extends BaseMail
{
    public function __construct(ContactInquiry $inquiry, string $type = 'user')
    {
        $this->templateKey = ($type === 'admin') ? 'contact_admin' : 'contact_user';
        
        $this->templateData = [
            'user_name'  => $inquiry->name,
            'user_email' => $inquiry->email,
            'subject'    => $inquiry->subject,
            'message'    => $inquiry->message,
            'site_name'  => Setting::get('site_name', config('app.name')),
        ];
    }

    public function build(): self
    {
        return $this->buildFromTemplate();
    }
}
