<?php

namespace App\Mail;

use App\Models\Setting;

class CouponPurchasedMail extends BaseMail
{
    public function __construct(string $userName, string $packageName, string $couponCode, string $amount, int $quantity)
    {
        $this->templateKey  = 'coupon_purchased';
        $this->templateData = [
            'user_name'    => $userName,
            'site_name'    => Setting::get('site_name', config('app.name')),
            'package_name' => $packageName,
            'coupon_code'  => $couponCode,
            'amount'       => $amount,
            'quantity'     => $quantity,
        ];
    }

    public function build(): self
    {
        return $this->buildFromTemplate();
    }
}
