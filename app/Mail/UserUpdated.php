<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class UserUpdated extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $password;
    public $type;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $user, $password, $type)
    {
        //
        $this->user = $user;
        $this->password = $password;
        $this->type = $type;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from('hemmsidan@itsakerhetsbolaget.se', 'IT-Säkerhetsbolaget')->subject(__('messages.account').' '.__('messages.updated'))->markdown('emails.users.updated');
    }
}
