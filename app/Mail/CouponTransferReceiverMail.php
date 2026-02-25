<?php

namespace App\Mail;

use App\Models\Setting;

class CouponTransferReceiverMail extends BaseMail
{
    public function __construct(string $receiverName, string $senderName, string $couponCode)
    {
        $this->templateKey  = 'coupon_transfer_receiver';
        $this->templateData = [
            'user_name'     => $receiverName,
            'sender_name'   => $senderName,
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
