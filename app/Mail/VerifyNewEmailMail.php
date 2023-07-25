<?php

namespace App\Mail;

class VerifyNewEmailMail extends \ProtoneMedia\LaravelVerifyNewEmail\Mail\VerifyNewEmail
{
    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $this->subject(__('Verify New Email Address'));

        return $this->markdown('vendor.notifications.email', [
            'introLines' => [__('Please click the button below to verify your email address.')],
            'actionText' => __('Verify New Email Address'),
            'actionUrl' => $this->pendingUserEmail->verificationUrl(),
            'outroLines' => [__('If you did not update your email address, no further action is required.')]
        ]);
    }
}
