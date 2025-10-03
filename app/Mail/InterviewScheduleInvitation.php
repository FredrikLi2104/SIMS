<?php

namespace App\Mail;

use App\Models\Interview;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class InterviewScheduleInvitation extends Mailable
{
    use Queueable, SerializesModels;

    public $interview;
    public $organizer;
    public $ics;
    public $duration;
    public $type;

    /**
     * Create a new message instance.
     *
     * @param Interview $interview
     * @param User $organizer
     * @param string $ics
     * @param int $duration
     * @param string $type (new|updated|cancelled)
     */
    public function __construct(Interview $interview, User $organizer, $ics, $duration, $type = 'new')
    {
        $this->interview = $interview;
        $this->organizer = $organizer;
        $this->ics = $ics;
        $this->duration = $duration;
        $this->type = $type;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $subject = $this->getSubject();

        return $this->subject($subject)
                    ->view('emails.interview-schedule-invitation')
                    ->attachData($this->ics, 'interview.ics', [
                        'mime' => 'text/calendar; charset=UTF-8; method=' . ($this->type === 'cancelled' ? 'CANCEL' : 'REQUEST')
                    ]);
    }

    /**
     * Get email subject based on type
     *
     * @return string
     */
    protected function getSubject()
    {
        switch ($this->type) {
            case 'updated':
                return __('messages.meetingUpdatedSubject') . ': ' . $this->interview->interviewee;
            case 'cancelled':
                return __('messages.meetingCancelledSubject') . ': ' . $this->interview->interviewee;
            default:
                return __('messages.meetingInvitationSubject') . ': ' . $this->interview->interviewee;
        }
    }
}
