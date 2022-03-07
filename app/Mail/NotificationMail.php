<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $task_name;
    public $task_next_date;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($task_name, $task_next_date)
    {
        $this->task_name = $task_name;
        $this->task_next_date = $task_next_date;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $task_name = $this->task_name;
        $task_next_date = $this->task_next_date;

        return $this->view('emails.Notification')
            ->from(env('MAIL_FROM_ADDRESS'), env('APP_NAME'))
            ->subject('Notifikácia o naplánovanej úlohe');
    }
}
