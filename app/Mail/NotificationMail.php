<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class NotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $task_name;
    public $task_next_date;
    public $language;
    public $task_url;
    public $user_email;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($task_name, $task_next_date, $language, $task_id, $user_email)
    {
        $this->task_name = $task_name;
        $this->task_next_date = $task_next_date;
        $this->language = $language;
        $this->task_url = "https://notificate.me/tasks/" . (new Controller)->JWT_encode($task_id);
        $this->user_email = $user_email;
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
        $language = $this->language;
        $task_url = $this->task_url;
        $user_email = $this->user_email;

        if ($language == 'sk') {
            return $this->view('emails.NotificationSK')
                ->from(env('MAIL_FROM_ADDRESS'), env('APP_NAME'))
                ->subject('Notifikácia o naplánovanej úlohe');
        } elseif ($language == 'en') {
            return $this->view('emails.NotificationEN')
                ->from(env('MAIL_FROM_ADDRESS'), env('APP_NAME'))
                ->subject('Notification of scheduled task');
        } else {
            return $this->view('emails.NotificationEN')
                ->from(env('MAIL_FROM_ADDRESS'), env('APP_NAME'))
                ->subject('Notification of scheduled task');
        };


    }
}
