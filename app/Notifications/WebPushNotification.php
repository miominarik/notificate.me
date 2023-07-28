<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushMessage;
use NotificationChannels\WebPush\WebPushChannel;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class WebPushNotification extends Notification
{
    use Queueable;

    public $task_name;
    public $task_date;

    public function __construct($task_name, $task_date)
    {
        $this->task_name = $task_name;
        $this->task_date = $task_date;
    }

    public function via($notifiable)
    {
        return [WebPushChannel::class];
    }

    public function toWebPush($notifiable, $notification)
    {
        $text = "Blíži sa termín Vašej úlohy. " . PHP_EOL;
        $text .= "Názov úlohy: " . $this->task_name . PHP_EOL;
        $text .= "Termín: " . $this->task_date;

        return (new WebPushMessage)
            ->title('Notificate.me Pripomienka')
            ->icon('https://notificate.me/images/favicon_io/apple-touch-icon.png')
            ->body($text);
    }
}
