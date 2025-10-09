<!DOCTYPE html>
<html lang="sv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('messages.meetingInvitation') }}</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">

    @if($type === 'cancelled')
        <div style="background-color: #f8d7da; border-left: 4px solid #dc3545; padding: 15px; margin-bottom: 20px;">
            <h2 style="color: #721c24; margin-top: 0;">{{ __('messages.meetingCancelled') }}</h2>
        </div>
    @elseif($type === 'updated')
        <div style="background-color: #fff3cd; border-left: 4px solid #ffc107; padding: 15px; margin-bottom: 20px;">
            <h2 style="color: #856404; margin-top: 0;">{{ __('messages.meetingUpdated') }}</h2>
        </div>
    @else
        <div style="background-color: #d1ecf1; border-left: 4px solid #17a2b8; padding: 15px; margin-bottom: 20px;">
            <h2 style="color: #0c5460; margin-top: 0;">{{ __('messages.meetingInvitation') }}</h2>
        </div>
    @endif

    @if($type === 'cancelled')
        <p>{{ __('messages.meetingCancelledBody') }}</p>
        <p><strong>{{ __('messages.interview') }}:</strong> {{ $interview->interviewee }}</p>
        <p><strong>{{ __('messages.organizer') }}:</strong> {{ $organizer->name }}</p>
    @else
        <p>{{ __('messages.meetingInvitationBody') }}</p>

        <div style="background-color: #f8f9fa; padding: 15px; border-radius: 5px; margin: 20px 0;">
            <h3 style="margin-top: 0;">📅 {{ __('messages.meetingDetails') }}</h3>

            <p style="margin: 10px 0;">
                <strong>{{ __('messages.interviewee') }}:</strong><br>
                {{ $interview->interviewee }}
            </p>

            <p style="margin: 10px 0;">
                <strong>{{ __('messages.date') }}:</strong><br>
                {{ \Carbon\Carbon::parse($interview->scheduled_date)->format('l, j F Y') }}
            </p>

            <p style="margin: 10px 0;">
                <strong>{{ __('messages.time') }}:</strong><br>
                {{ \Carbon\Carbon::parse($interview->scheduled_date)->format('H:i') }}
                ({{ $duration }} {{ __('messages.minutes') }})
            </p>

            <p style="margin: 10px 0;">
                <strong>{{ __('messages.agenda') }}:</strong><br>
                {{ $interview->agenda }}
            </p>

            <p style="margin: 10px 0;">
                <strong>{{ __('messages.organizer') }}:</strong><br>
                {{ $organizer->name }} ({{ $organizer->email }})
            </p>
        </div>

        <div style="background-color: #e7f3ff; border: 1px solid #b3d9ff; padding: 15px; border-radius: 5px; margin: 20px 0;">
            <h4 style="margin-top: 0;">💡 {{ __('messages.howToAddToCalendar') }}</h4>
            <ol style="margin: 10px 0; padding-left: 20px;">
                <li>{{ __('messages.clickAttachedFile') }}</li>
                <li>{{ __('messages.calendarWillOpen') }}</li>
                <li>{{ __('messages.clickAddToCalendar') }}</li>
            </ol>
            <p style="font-size: 12px; color: #666; margin-top: 10px;">
                {{ __('messages.icsCompatibility') }}
            </p>
        </div>

        @if($interview->notes)
        <div style="background-color: #fff; border: 1px solid #dee2e6; padding: 15px; border-radius: 5px; margin: 20px 0;">
            <h4 style="margin-top: 0;">📝 {{ __('messages.additionalNotes') }}</h4>
            <p>{{ $interview->notes }}</p>
        </div>
        @endif
    @endif

    <hr style="border: none; border-top: 1px solid #dee2e6; margin: 30px 0;">

    <p style="font-size: 12px; color: #666; text-align: center;">
        {{ __('messages.emailFooter') }}<br>
        <strong>GDPR.se</strong> - Interview Scheduler
    </p>

</body>
</html>
