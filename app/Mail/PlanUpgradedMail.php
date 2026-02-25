<?php

namespace App\Mail;

use App\Models\Setting;

class PlanUpgradedMail extends BaseMail
{
    public function __construct(string $userName, string $planName, string $amount, string $transactionId)
    {
        $this->templateKey  = 'plan_upgraded';
        $this->templateData = [
            'user_name'      => $userName,
            'plan_name'      => $planName,
            'amount'         => $amount,
            'transaction_id' => $transactionId,
            'site_name'      => Setting::get('site_name', config('app.name')),
            'dashboard_url'  => route('student.dashboard'),
        ];
    }

    public function build(): self
    {
        return $this->buildFromTemplate();
    }
}
