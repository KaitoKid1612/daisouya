<?php

namespace App\Services;

use Illuminate\Notifications\Notifiable;
use App\Notifications\SlackNotification;

class SlackNotificationService
{
    use Notifiable;

    public function send($message)
    {
        $this->notify(new SlackNotification($message));
    }

    protected function routeNotificationForSlack()
    {
        return config('slack.webhook_url');
    }

    /**
     * Get the value of the notifiable's primary key.
     * ※テストで必要（ないとエラーになる）
     *
     * @return mixed
     */
    public function getKey()
    {
        //
    }
}