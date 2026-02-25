<?php

namespace App\Mail;

use App\Models\Setting;

class WithdrawalApprovedMail extends BaseMail
{
    public function __construct(string $userName, string $amount, string $bankName)
    {
        $this->templateKey  = 'withdrawal_approved';
        $this->templateData = [
            'user_name'     => $userName,
            'amount'        => $amount,
            'bank_name'     => $bankName,
            'site_name'     => Setting::get('site_name', config('app.name')),
            'approval_date' => now()->format('d M Y, h:i A'),
        ];
    }

    public function build(): self
    {
        return $this->buildFromTemplate();
    }
}
