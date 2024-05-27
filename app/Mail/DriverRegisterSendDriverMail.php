<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class DriverRegisterSendDriverMail extends Mailable
{
    use Queueable, SerializesModels;
        
    private $data;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.driver.store_register_driver')
            ->from($this->data["config_system"]->email_no_reply, $this->data["config_base"]->site_name)
            ->replyTo($this->data["config_system"]->email_reply_to, $this->data["config_base"]->site_name)
            ->subject("{$this->data["config_base"]->site_name} ドライバーアカウント登録 {$this->data["driver"]->full_name} ドライバー様 [配信専用]")
            ->with(['data' => $this->data]);
    }
}
