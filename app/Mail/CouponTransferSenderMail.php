<?php

namespace App\Mail;

use App\Models\Setting;

class CouponTransferSenderMail extends BaseMail
{
    public function __construct(string $senderName, string $receiverName, string $couponCode)
    {
        $this->templateKey  = 'coupon_transfer_sender';
        $this->templateData = [
            'user_name'     => $senderName,
            'receiver_name' => $receiverName,
            'coupon_code'   => $couponCode,
            'site_name'     => Setting::get('site_name', config('app.name')),
            'transfer_date' => now()->format('d M Y, h:i A'),
        ];
    }

    public function build(): self
    {
        return $this->buildFromTemplate();
    }
}
