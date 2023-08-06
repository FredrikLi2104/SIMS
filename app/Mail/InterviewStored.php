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
    public $creator;
    public $statements;
    public $agenda;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user, $creator, $statements, $agenda)
    {
        //
        $this->user = $user;
        $this->creator = $creator;
        $this->statements = $statements;
        $this->agenda = $agenda;
        
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from('hemmsidan@itsakerhetsbolaget.se', 'IT-Säkerhetsbolaget')->subject(__('messages.interview').' '.__('messages.created'))->markdown('emails.interviews.stored');
    }
}
