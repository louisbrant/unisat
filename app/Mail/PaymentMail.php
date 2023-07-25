<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PaymentMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @var
     */
    public $payment;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($payment)
    {
        $this->payment = $payment;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        if ($this->payment->status == 'completed') {
            $title = __('Payment completed');

            $data = [
                'introLines' => [__('The payment was successful.') . ' ' . __('Thank you!')],
                'actionText' => __('Invoice'),
                'actionUrl' => route('account.invoices.show', [$this->payment->id]),
            ];
        } else {
            $title = __('Payment cancelled');

            $data = [
                'introLines' => [__('The payment was cancelled.')],
            ];
        }

        return $this->subject(formatTitle([$title, config('settings.title')]))
            ->markdown('vendor.notifications.email', $data);
    }
}
