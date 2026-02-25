<?php

namespace App\Mail;

use App\Models\Setting;

class WithdrawalRequestedMail extends BaseMail
{
    public function __construct(string $userName, string $userEmail, string $amount, string $bankDetails)
    {
        $this->templateKey  = 'withdrawal_requested';
        $this->templateData = [
            'user_name'    => $userName,
            'user_email'   => $userEmail,
            'amount'       => $amount,
            'bank_details' => $bankDetails,
            'site_name'    => Setting::get('site_name', config('app.name')),
            'request_date' => now()->format('d M Y, h:i A'),
        ];
    }

    public function build(): self
    {
        return $this->buildFromTemplate();
    }
}
