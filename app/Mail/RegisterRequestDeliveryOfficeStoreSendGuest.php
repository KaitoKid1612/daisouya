<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RegisterRequestDeliveryOfficeStoreSendGuest extends Mailable
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
        return $this->view('emails.register_request_delivery_office.store_guest')
            ->from($this->data["config_system"]->email_no_reply, $this->data["config_base"]->site_name)
            ->replyTo($this->data["config_system"]->email_reply_to, $this->data["config_base"]->site_name)
            ->subject("{$this->data["config_base"]->site_name} 営業所登録申請を受け付けました [配信専用]")
            ->with(['data' => $this->data]);
    }
}
