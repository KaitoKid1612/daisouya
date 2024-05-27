<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\SlackMessage;

class SlackNotification extends Notification
{
    use Queueable;

    protected $channel;
    protected $icon;
    protected $name;
    protected $message;

    /**
     * Create a new notification instance.
     * @return void
     */
    public function __construct($message)
    {
        // Slackに関数する情報はconfigに定義しておく
        $this->channel = config('slack.channel');
        $this->icon = config('slack.icon');
        $this->name = config('slack.sender_name');
        $this->message = $message;
    }

    /**
     * Get the notification's delivery channels.
     * どの配信チャネルに送信するか
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['slack'];
    }

    /**
     * Get the array representation of the notification.
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [];
    }

    /**
     * Get the Slack representation of the notification.
     * @param mixed $notifiable
     * @return SlackMessage
     */
    public function toSlack($notifiable)
    {
        return (new SlackMessage)
            ->from($this->name)
            ->image($this->icon)
            ->to($this->channel)
            ->content($this->message);
    }
}