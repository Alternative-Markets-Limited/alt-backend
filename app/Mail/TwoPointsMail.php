<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TwoPointsMail extends Mailable
{
    use Queueable, SerializesModels;
    public $referrer_data;


    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($referrer_data)
    {
        $this->referrer_data = $referrer_data;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from(getenv('MAIL_FROM_ADDRESS'), getenv('MAIL_FROM_NAME'))
            ->subject('A user you referred has just completed a purchase')
            ->view('email.twoPoints')
            ->with('data', $this->referrer_data);
    }
}
