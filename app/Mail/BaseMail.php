<?php

namespace App\Mail;

use App\Services\EmailService;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

/**
 * Base mailable that applies dynamic SMTP config before building.
 */
abstract class BaseMail extends Mailable
{
    use Queueable, SerializesModels;

    protected string $templateKey;
    protected array $templateData = [];

    protected function buildFromTemplate(): self
    {
        EmailService::applyMailConfig();
        $template = EmailService::getTemplate($this->templateKey, $this->templateData);

        $this->subject($template['subject']);

        return $this->view('emails.generic', [
            'templateBody' => $template['body'],
            'subject'      => $template['subject'],
        ]);
    }
}
