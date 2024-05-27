<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class DriverTaskPaymentRefundSendDeliveryOfficeMail extends Mailable
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
        return $this->view('emails.driver_task.payment_refund_delivery_office.blade')
            ->from($this->data["config_system"]->email_no_reply, $this->data["config_base"]->site_name)
            ->replyTo($this->data["config_system"]->email_reply_to, $this->data["config_base"]->site_name)
            ->subject("{$this->data["config_base"]->site_name} 決済 {$this->data["task"]->joinTaskRefundStatus->name} 営業所様 [配信専用]")
            ->with(['data' => $this->data]);
    }
}
