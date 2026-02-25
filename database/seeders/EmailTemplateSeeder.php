<?php

namespace Database\Seeders;

use App\Models\EmailTemplate;
use Illuminate\Database\Seeder;

class EmailTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $templates = [
            [
                'key'       => 'welcome',
                'name'      => 'Welcome Email',
                'subject'   => 'Welcome to {{site_name}}, {{user_name}}! 🎉',
                'variables' => ['user_name', 'site_name', 'login_url'],
                'body'      => $this->welcome(),
            ],
            [
                'key'       => 'lead_converted',
                'name'      => 'Lead Converted (Account Created)',
                'subject'   => 'Your {{site_name}} Account is Ready!',
                'variables' => ['user_name', 'site_name', 'login_email', 'password', 'login_url'],
                'body'      => $this->leadConverted(),
            ],
            [
                'key'       => 'course_purchased',
                'name'      => 'Course Purchase Confirmation',
                'subject'   => '✅ Purchase Confirmed: {{course_name}}',
                'variables' => ['user_name', 'course_name', 'amount', 'transaction_id', 'dashboard_url'],
                'body'      => $this->coursePurchased(),
            ],
            [
                'key'       => 'plan_upgraded',
                'name'      => 'Plan Upgrade Confirmation',
                'subject'   => '🚀 Plan Upgraded Successfully — {{plan_name}}',
                'variables' => ['user_name', 'plan_name', 'amount', 'transaction_id', 'dashboard_url'],
                'body'      => $this->planUpgraded(),
            ],
            [
                'key'       => 'reset_password',
                'name'      => 'Reset Password',
                'subject'   => '🔐 Your Password Has Been Reset',
                'variables' => ['user_name', 'site_name', 'login_url'],
                'body'      => $this->resetPassword(),
            ],
            [
                'key'       => 'forgot_password',
                'name'      => 'Forgot Password (Reset Link)',
                'subject'   => '🔑 Reset Your {{site_name}} Password',
                'variables' => ['user_name', 'reset_url', 'expiry_minutes'],
                'body'      => $this->forgotPassword(),
            ],
            [
                'key'       => 'coupon_purchased',
                'name'      => 'Coupon Purchase Confirmation',
                'subject'   => '🎟️ Coupon Purchase Confirmed!',
                'variables' => ['user_name', 'coupon_code', 'package_name', 'amount', 'quantity'],
                'body'      => $this->couponPurchased(),
            ],
            [
                'key'       => 'coupon_transfer_sender',
                'name'      => 'Coupon Transfer — Sender Notification',
                'subject'   => '📤 Coupon Transferred Successfully',
                'variables' => ['user_name', 'receiver_name', 'coupon_code', 'transfer_date'],
                'body'      => $this->couponTransferSender(),
            ],
            [
                'key'       => 'coupon_transfer_receiver',
                'name'      => 'Coupon Transfer — Receiver Notification',
                'subject'   => '🎁 You Received a Coupon from {{sender_name}}!',
                'variables' => ['user_name', 'sender_name', 'coupon_code', 'transfer_date'],
                'body'      => $this->couponTransferReceiver(),
            ],
            [
                'key'       => 'withdrawal_requested',
                'name'      => 'Withdrawal Request (Admin Notification)',
                'subject'   => '💰 New Withdrawal Request — ₹{{amount}}',
                'variables' => ['user_name', 'user_email', 'amount', 'bank_details', 'request_date'],
                'body'      => $this->withdrawalRequested(),
            ],
            [
                'key'       => 'withdrawal_approved',
                'name'      => 'Withdrawal Approved (User Notification)',
                'subject'   => '✅ Withdrawal Approved — ₹{{amount}} Credited',
                'variables' => ['user_name', 'amount', 'bank_name', 'approval_date'],
                'body'      => $this->withdrawalApproved(),
            ],
            [
                'key'       => 'admin_notification',
                'name'      => 'Generic Admin Notification',
                'subject'   => '🔔 {{title}} — {{site_name}} Alert',
                'variables' => ['title', 'message', 'site_name'],
                'body'      => $this->adminNotification(),
            ],
        ];

        foreach ($templates as $template) {
            EmailTemplate::updateOrCreate(
                ['key' => $template['key']],
                $template
            );
        }
    }

    private function welcome(): string
    {
        return <<<HTML
<p>Hi <strong>{{user_name}}</strong>,</p>
<p>Welcome to <strong>{{site_name}}</strong>! We're absolutely thrilled to have you on board. 🎉</p>
<p>Your account has been successfully created. You can now log in and start exploring our courses and resources.</p>
<div style="text-align:center; margin: 32px 0;">
  <a href="{{login_url}}" style="background:#6366f1; color:#fff; text-decoration:none; padding:14px 32px; border-radius:8px; font-weight:700; font-size:16px; display:inline-block;">Go to Dashboard &rarr;</a>
</div>
<p>If you have any questions, feel free to reach out to our support team. We're here to help!</p>
<p>Best regards,<br><strong>The {{site_name}} Team</strong></p>
HTML;
    }

    private function leadConverted(): string
    {
        return <<<HTML
<p>Hi <strong>{{user_name}}</strong>,</p>
<p>Great news! Your account on <strong>{{site_name}}</strong> has been created. Here are your login credentials:</p>
<div style="background:#f8fafc; border-left:4px solid #6366f1; border-radius:8px; padding:20px; margin:24px 0;">
  <p style="margin:0 0 8px 0;"><strong>Login Email:</strong> {{login_email}}</p>
  <p style="margin:0;"><strong>Password:</strong> {{password}}</p>
</div>
<p><strong>Please login and change your password immediately for security.</strong></p>
<div style="text-align:center; margin: 32px 0;">
  <a href="{{login_url}}" style="background:#6366f1; color:#fff; text-decoration:none; padding:14px 32px; border-radius:8px; font-weight:700; font-size:16px; display:inline-block;">Login Now &rarr;</a>
</div>
<p>Best regards,<br><strong>The {{site_name}} Team</strong></p>
HTML;
    }

    private function coursePurchased(): string
    {
        return <<<HTML
<p>Hi <strong>{{user_name}}</strong>,</p>
<p>Thank you for your purchase! Your enrollment in <strong>{{course_name}}</strong> is confirmed. 🎓</p>
<div style="background:#f8fafc; border-left:4px solid #10b981; border-radius:8px; padding:20px; margin:24px 0;">
  <p style="margin:0 0 8px 0;"><strong>Course:</strong> {{course_name}}</p>
  <p style="margin:0 0 8px 0;"><strong>Amount Paid:</strong> ₹{{amount}}</p>
  <p style="margin:0;"><strong>Transaction ID:</strong> {{transaction_id}}</p>
</div>
<div style="text-align:center; margin: 32px 0;">
  <a href="{{dashboard_url}}" style="background:#6366f1; color:#fff; text-decoration:none; padding:14px 32px; border-radius:8px; font-weight:700; font-size:16px; display:inline-block;">Start Learning &rarr;</a>
</div>
<p>Best regards,<br><strong>The {{site_name}} Team</strong></p>
HTML;
    }

    private function planUpgraded(): string
    {
        return <<<HTML
<p>Hi <strong>{{user_name}}</strong>,</p>
<p>Your plan has been upgraded successfully! 🚀 Here are the details:</p>
<div style="background:#f8fafc; border-left:4px solid #6366f1; border-radius:8px; padding:20px; margin:24px 0;">
  <p style="margin:0 0 8px 0;"><strong>Plan:</strong> {{plan_name}}</p>
  <p style="margin:0 0 8px 0;"><strong>Amount Paid:</strong> ₹{{amount}}</p>
  <p style="margin:0;"><strong>Transaction ID:</strong> {{transaction_id}}</p>
</div>
<div style="text-align:center; margin: 32px 0;">
  <a href="{{dashboard_url}}" style="background:#6366f1; color:#fff; text-decoration:none; padding:14px 32px; border-radius:8px; font-weight:700; font-size:16px; display:inline-block;">Go to Dashboard &rarr;</a>
</div>
<p>Best regards,<br><strong>The {{site_name}} Team</strong></p>
HTML;
    }

    private function resetPassword(): string
    {
        return <<<HTML
<p>Hi <strong>{{user_name}}</strong>,</p>
<p>Your password on <strong>{{site_name}}</strong> has been successfully reset.</p>
<p>If you did not request this change, please contact our support team immediately.</p>
<div style="text-align:center; margin: 32px 0;">
  <a href="{{login_url}}" style="background:#6366f1; color:#fff; text-decoration:none; padding:14px 32px; border-radius:8px; font-weight:700; font-size:16px; display:inline-block;">Login to Your Account &rarr;</a>
</div>
<p>Best regards,<br><strong>The {{site_name}} Team</strong></p>
HTML;
    }

    private function forgotPassword(): string
    {
        return <<<HTML
<p>Hi <strong>{{user_name}}</strong>,</p>
<p>We received a request to reset your password. Click the button below to set a new password. This link will expire in <strong>{{expiry_minutes}} minutes</strong>.</p>
<div style="text-align:center; margin: 32px 0;">
  <a href="{{reset_url}}" style="background:#ef4444; color:#fff; text-decoration:none; padding:14px 32px; border-radius:8px; font-weight:700; font-size:16px; display:inline-block;">Reset My Password &rarr;</a>
</div>
<p>If you did not request a password reset, you can safely ignore this email.</p>
<p>Best regards,<br><strong>The {{site_name}} Team</strong></p>
HTML;
    }

    private function couponPurchased(): string
    {
        return <<<HTML
<p>Hi <strong>{{user_name}}</strong>,</p>
<p>Your coupon purchase is confirmed! 🎟️</p>
<div style="background:#f8fafc; border-left:4px solid #f59e0b; border-radius:8px; padding:20px; margin:24px 0;">
  <p style="margin:0 0 8px 0;"><strong>Package:</strong> {{package_name}}</p>
  <p style="margin:0 0 8px 0;"><strong>Quantity:</strong> {{quantity}} coupons</p>
  <p style="margin:0 0 8px 0;"><strong>Coupon Code(s):</strong> {{coupon_code}}</p>
  <p style="margin:0;"><strong>Amount Paid:</strong> ₹{{amount}}</p>
</div>
<p>Your coupons are now available in your dashboard. You can transfer or use them as needed.</p>
<p>Best regards,<br><strong>The {{site_name}} Team</strong></p>
HTML;
    }

    private function couponTransferSender(): string
    {
        return <<<HTML
<p>Hi <strong>{{user_name}}</strong>,</p>
<p>Your coupon transfer was successful! 📤</p>
<div style="background:#f8fafc; border-left:4px solid #6366f1; border-radius:8px; padding:20px; margin:24px 0;">
  <p style="margin:0 0 8px 0;"><strong>Transferred To:</strong> {{receiver_name}}</p>
  <p style="margin:0 0 8px 0;"><strong>Coupon Code:</strong> {{coupon_code}}</p>
  <p style="margin:0;"><strong>Transfer Date:</strong> {{transfer_date}}</p>
</div>
<p>If you did not initiate this transfer, please contact support immediately.</p>
<p>Best regards,<br><strong>The {{site_name}} Team</strong></p>
HTML;
    }

    private function couponTransferReceiver(): string
    {
        return <<<HTML
<p>Hi <strong>{{user_name}}</strong>,</p>
<p>You've received a coupon! 🎁</p>
<div style="background:#f8fafc; border-left:4px solid #10b981; border-radius:8px; padding:20px; margin:24px 0;">
  <p style="margin:0 0 8px 0;"><strong>Sent By:</strong> {{sender_name}}</p>
  <p style="margin:0 0 8px 0;"><strong>Coupon Code:</strong> {{coupon_code}}</p>
  <p style="margin:0;"><strong>Transfer Date:</strong> {{transfer_date}}</p>
</div>
<p>The coupon is now available in your dashboard. Log in to view and use it.</p>
<p>Best regards,<br><strong>The {{site_name}} Team</strong></p>
HTML;
    }

    private function withdrawalRequested(): string
    {
        return <<<HTML
<p>Hello Admin,</p>
<p>A new withdrawal request has been submitted and requires your attention.</p>
<div style="background:#fef9ec; border-left:4px solid #f59e0b; border-radius:8px; padding:20px; margin:24px 0;">
  <p style="margin:0 0 8px 0;"><strong>User:</strong> {{user_name}} ({{user_email}})</p>
  <p style="margin:0 0 8px 0;"><strong>Amount:</strong> ₹{{amount}}</p>
  <p style="margin:0 0 8px 0;"><strong>Bank Details:</strong> {{bank_details}}</p>
  <p style="margin:0;"><strong>Request Date:</strong> {{request_date}}</p>
</div>
<p>Please log in to the admin panel to process this request.</p>
HTML;
    }

    private function withdrawalApproved(): string
    {
        return <<<HTML
<p>Hi <strong>{{user_name}}</strong>,</p>
<p>Great news! Your withdrawal request has been approved and the amount has been credited. ✅</p>
<div style="background:#f0fdf4; border-left:4px solid #10b981; border-radius:8px; padding:20px; margin:24px 0;">
  <p style="margin:0 0 8px 0;"><strong>Amount Credited:</strong> ₹{{amount}}</p>
  <p style="margin:0 0 8px 0;"><strong>Bank:</strong> {{bank_name}}</p>
  <p style="margin:0;"><strong>Approval Date:</strong> {{approval_date}}</p>
</div>
<p>The amount should reflect in your bank account within 2-3 business days.</p>
<p>Best regards,<br><strong>The {{site_name}} Team</strong></p>
HTML;
    }

    private function adminNotification(): string
    {
        return <<<HTML
<p>Hello Admin,</p>
<p>This is an automated notification from <strong>{{site_name}}</strong>.</p>
<div style="background:#f1f5f9; border-left:4px solid #6366f1; border-radius:8px; padding:20px; margin:24px 0;">
  <h3 style="margin:0 0 8px 0; color:#1e293b;">{{title}}</h3>
  <p style="margin:0; color:#475569;">{{message}}</p>
</div>
<p>Please log in to the admin panel to take action if required.</p>
HTML;
    }
}
