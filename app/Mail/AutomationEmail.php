<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AutomationEmail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public string $message;

    public function __construct($message)
    {
        $this->message = $message;
    }

    public function build(): AutomationEmail
    {
        return $this->view('emails.automation-email');
    }
}
