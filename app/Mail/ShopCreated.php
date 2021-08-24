<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ShopCreated extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public $shop, public $passcode) {}

    public function build()
    {
        return $this->subject('電子排隊系統: 服務已建立')->markdown('emails.shop');
    }
}
