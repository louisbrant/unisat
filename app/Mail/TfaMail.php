<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TfaMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @var
     */
    public $code;

    /**
     * Create a new message instance.
     *
     * @param $code
     */
    public function __construct($code)
    {
        $this->code = $code;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $this->subject(formatTitle([__('Security code'), config('settings.title')]));

        return $this->markdown('emails.tfa', [
            'message' => __('Your security code is: :code.', ['code' => $this->code])
        ]);
    }
}
