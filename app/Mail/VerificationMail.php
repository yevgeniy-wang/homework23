<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class VerificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $id;
    public $name;
    public $verification_token;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(int $id, string $name, string $verification_token)
    {
        $this->id = $id;
        $this->name = $name;
        $this->verification_token = $verification_token;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('mails.test-mail');
    }
}
