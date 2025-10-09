<?php

namespace App\Services;

use App\Models\Interview;
use App\Models\User;
use Carbon\Carbon;

class CalendarService
{
    /**
     * Generera ICS-fil för intervjumöte
     *
     * @param Interview $interview
     * @param User $organizer
     * @param array $options
     * @return string
     */
    public function generateICS(Interview $interview, User $organizer, array $options = [])
    {
        $startDate = Carbon::parse($interview->scheduled_date);
        $duration = $options['duration'] ?? 60; // Default 60 minuter
        $endDate = $startDate->copy()->addMinutes($duration);

        // Generera unikt ID
        $uid = $this->generateUID($interview);

        // Formatera datum i UTC
        $dtstamp = $this->formatICSDateTime(now());
        $dtstart = $this->formatICSDateTime($startDate);
        $dtend = $this->formatICSDateTime($endDate);

        // Escape text
        $summary = $this->escapeICSText("Interview: {$interview->interviewee}");
        $description = $this->escapeICSText("Agenda: {$interview->agenda}");

        // Organisatör och deltagare
        $organizerName = $this->escapeICSText($organizer->name);
        $organizerEmail = $organizer->email;
        $attendeeEmail = $interview->interviewee;

        // Bygg ICS-innehåll
        $ics = "BEGIN:VCALENDAR\r\n";
        $ics .= "VERSION:2.0\r\n";
        $ics .= "PRODID:-//GDPR.se//Interview Scheduler//EN\r\n";
        $ics .= "CALSCALE:GREGORIAN\r\n";
        $ics .= "METHOD:REQUEST\r\n";

        $ics .= "BEGIN:VEVENT\r\n";
        $ics .= "UID:{$uid}\r\n";
        $ics .= "DTSTAMP:{$dtstamp}\r\n";
        $ics .= "DTSTART:{$dtstart}\r\n";
        $ics .= "DTEND:{$dtend}\r\n";
        $ics .= "SUMMARY:{$summary}\r\n";
        $ics .= "DESCRIPTION:{$description}\r\n";
        $ics .= "LOCATION:Online\r\n";
        $ics .= "ORGANIZER;CN={$organizerName}:MAILTO:{$organizerEmail}\r\n";
        $ics .= "ATTENDEE;CN={$attendeeEmail};RSVP=TRUE;ROLE=REQ-PARTICIPANT;PARTSTAT=NEEDS-ACTION:MAILTO:{$attendeeEmail}\r\n";
        $ics .= "STATUS:CONFIRMED\r\n";
        $ics .= "SEQUENCE:0\r\n";
        $ics .= "PRIORITY:5\r\n";
        $ics .= "CLASS:PUBLIC\r\n";
        $ics .= "TRANSP:OPAQUE\r\n";

        // Påminnelse 15 minuter före
        $ics .= "BEGIN:VALARM\r\n";
        $ics .= "TRIGGER:-PT15M\r\n";
        $ics .= "ACTION:DISPLAY\r\n";
        $ics .= "DESCRIPTION:Reminder: Interview in 15 minutes\r\n";
        $ics .= "END:VALARM\r\n";

        $ics .= "END:VEVENT\r\n";
        $ics .= "END:VCALENDAR\r\n";

        return $ics;
    }

    /**
     * Generera ICS för att avboka möte
     *
     * @param Interview $interview
     * @param User $organizer
     * @return string
     */
    public function generateCancellationICS(Interview $interview, User $organizer)
    {
        $uid = $this->generateUID($interview);
        $dtstamp = $this->formatICSDateTime(now());

        $summary = $this->escapeICSText("CANCELLED: Interview: {$interview->interviewee}");
        $organizerName = $this->escapeICSText($organizer->name);
        $organizerEmail = $organizer->email;
        $attendeeEmail = $interview->interviewee;

        $ics = "BEGIN:VCALENDAR\r\n";
        $ics .= "VERSION:2.0\r\n";
        $ics .= "PRODID:-//GDPR.se//Interview Scheduler//EN\r\n";
        $ics .= "METHOD:CANCEL\r\n";

        $ics .= "BEGIN:VEVENT\r\n";
        $ics .= "UID:{$uid}\r\n";
        $ics .= "DTSTAMP:{$dtstamp}\r\n";
        $ics .= "SUMMARY:{$summary}\r\n";
        $ics .= "ORGANIZER;CN={$organizerName}:MAILTO:{$organizerEmail}\r\n";
        $ics .= "ATTENDEE:MAILTO:{$attendeeEmail}\r\n";
        $ics .= "STATUS:CANCELLED\r\n";
        $ics .= "SEQUENCE:1\r\n";

        $ics .= "END:VEVENT\r\n";
        $ics .= "END:VCALENDAR\r\n";

        return $ics;
    }

    /**
     * Generera unikt ID för mötet
     *
     * @param Interview $interview
     * @return string
     */
    protected function generateUID(Interview $interview)
    {
        return "interview-{$interview->id}@gdpr.se";
    }

    /**
     * Formatera datetime till ICS-format (UTC)
     *
     * @param Carbon $datetime
     * @return string
     */
    protected function formatICSDateTime(Carbon $datetime)
    {
        return $datetime->utc()->format('Ymd\THis\Z');
    }

    /**
     * Escape specialtecken i ICS-text
     *
     * @param string $text
     * @return string
     */
    protected function escapeICSText($text)
    {
        if (empty($text)) {
            return '';
        }

        // Escape i rätt ordning
        $text = str_replace('\\', '\\\\', $text);
        $text = str_replace(',', '\,', $text);
        $text = str_replace(';', '\;', $text);
        $text = str_replace("\n", '\n', $text);
        $text = str_replace("\r", '', $text);

        return $text;
    }
}
