@component('mail::message')
# @lang('messages.account') @lang('messages.updated')

@lang('messages.yourAccountHasBeenUpdated'), @lang('messages.credentials'):

Email:
@component('mail::panel')
{{$user->email}}
@endcomponent

@if ($type == "creds")
@lang('messages.password')
@component('mail::panel')
{{$password}}
@endcomponent  
@endif
 
@component('mail::button', ['url' => 'https://backend.gdpr.se', 'color' => 'success'])
@lang('messages.login')
@endcomponent
 
@lang('messages.greetings'),<br>
{{ config('app.name') }}
@endcomponent