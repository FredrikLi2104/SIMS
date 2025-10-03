@component('mail::message')
# @lang('messages.account') @lang('messages.created')

@lang('messages.yourAccountHasBeenCreated'), @lang('messages.credentials'):

Email:
@component('mail::panel')
{{$user->email}}
@endcomponent

@lang('messages.password')
@component('mail::panel')
{{$password}}
@endcomponent
 
@component('mail::button', ['url' => 'https://backend.gdpr.se', 'color' => 'success'])
@lang('messages.login')
@endcomponent
 
@lang('messages.greetings'),<br>
{{ config('app.name') }}
@endcomponent