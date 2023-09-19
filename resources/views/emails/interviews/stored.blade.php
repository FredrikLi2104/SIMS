@component('mail::message')
    #@lang('messages.interview') @lang('messages.created')
    @lang('messages.yourOrganisationUser') {{ $creator->name }} @lang('messages.hasCreatedAnInterviewForYou')<br>
    @lang('messages.agenda'):<br>
    {{ $agenda }}<br>
    @lang('messages.statements'):
    @component('mail::table')
        | @lang('messages.statements') |
        | ------------- |
        @foreach ($statements as $statement)
            | {{ $statement }} |
        @endforeach
    @endcomponent
    <br>
    @component('mail::button', ['url' => 'https://backend.gdpr.se', 'color' => 'success'])
        @lang('messages.login')
    @endcomponent

    @lang('messages.greetings'),<br>
    {{ config('app.name') }}
@endcomponent
