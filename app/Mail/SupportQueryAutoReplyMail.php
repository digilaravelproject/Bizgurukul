<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SupportQueryAutoReplyMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $querySubject;
    public $queryMessage;

    public function __construct($user, $querySubject, $queryMessage)
    {
        $this->user = $user;
        $this->querySubject = $querySubject;
        $this->queryMessage = $queryMessage;
    }

    public function build()
    {
        return $this->subject('We received your message: ' . $this->querySubject)
                    ->view('emails.support.reply');
    }
}