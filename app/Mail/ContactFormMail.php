<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ContactFormMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(String $name, String $email, String $subject, String $text_message)
    {
        $this->name = $name;
        $this->email = $email;
        $this->subject = $subject;
        $this->text_message = $text_message;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.ContactForm')
            ->from(env('MAIL_FROM_ADDRESS'), env('APP_NAME'))
            ->subject('Kontaktný formulár Notificate.me')
            ->with([
                'name' => $this->name,
                'email' => $this->email,
                'subject' => $this->subject,
                'text_message' => $this->text_message
            ]);
    }
}
