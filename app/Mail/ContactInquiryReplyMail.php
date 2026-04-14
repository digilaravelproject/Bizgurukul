<?php

namespace App\Mail;

use App\Models\ContactInquiry;
use App\Models\Setting;

class ContactInquiryReplyMail extends BaseMail
{
    public string $replyMessage;
    public ContactInquiry $inquiry;

    public function __construct(ContactInquiry $inquiry, string $replyMessage)
    {
        $this->inquiry = $inquiry;
        $this->replyMessage = $replyMessage;
        
        // We will try to use a 'contact_reply' template, but if it doesn't exist,
        // BaseMail's buildFromTemplate will handle it via EmailService::getTemplate
        $this->templateKey = 'contact_reply';
        
        $this->templateData = [
            'user_name'     => $inquiry->name,
            'reply_message' => nl2br(e($replyMessage)),
            'site_name'     => Setting::get('site_name', config('app.name')),
            'subject'       => str_replace('_', ' ', $inquiry->subject),
        ];
    }

    public function build(): self
    {
        // If the template doesn't exist, we'll provide a default body
        // via a custom build method or by ensuring EmailService returns something sensible.
        // Actually, let's just use buildFromTemplate and override if needed.
        
        try {
            return $this->buildFromTemplate();
        } catch (\Throwable $e) {
            // Fallback if template logic fails
            return $this->subject('Re: ' . str_replace('_', ' ', $this->inquiry->subject))
                        ->view('emails.generic', [
                            'templateBody' => "<p>Hello {$this->inquiry->name},</p><p>{$this->templateData['reply_message']}</p>",
                            'subject'      => 'Re: ' . str_replace('_', ' ', $this->inquiry->subject),
                        ]);
        }
    }
}
