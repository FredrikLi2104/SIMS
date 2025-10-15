<?php

namespace App\Mail;

use App\Models\Interview;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class InterviewInvitation extends Mailable
{
    use Queueable, SerializesModels;

    public $interview;
    public $creator;
    public $statements;
    public $locale;
    public $organisation;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Interview $interview, $creator, $statements, $locale, $organisation)
    {
        $this->interview = $interview;
        $this->creator = $creator;
        $this->statements = $statements;
        $this->locale = $locale;
        $this->organisation = $organisation;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from(env('MAIL_FROM_ADDRESS', 'noreply@gdpr.se'), env('MAIL_FROM_NAME', 'GDPR System'))
            ->subject(__('messages.interview') . ' ' . __('messages.invitation') . ' - ' . $this->organisation->name)
            ->markdown('emails.interviews.invitation');
    }
}
