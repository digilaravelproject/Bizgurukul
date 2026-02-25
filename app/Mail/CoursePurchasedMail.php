<?php

namespace App\Mail;

use App\Models\Setting;

class CoursePurchasedMail extends BaseMail
{
    public function __construct(string $userName, string $courseName, string $amount, string $transactionId)
    {
        $this->templateKey  = 'course_purchased';
        $this->templateData = [
            'user_name'      => $userName,
            'course_name'    => $courseName,
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
