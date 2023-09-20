<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class InterviewStored extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $body;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user, $body)
    {
        //
        $this->user = $user;
        $this->body = $body;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from('hemmsidan@itsakerhetsbolaget.se', 'IT-Säkerhetsbolaget')->subject(__('messages.review').' '.__('messages.update'))->markdown('emails.interviews.stored');
    }
}
