@component('mail::message')
# @lang('messages.interview') @lang('messages.invitation')

@lang('messages.dear') {{ $interview->interviewee }},

{{ $creator->name }} @lang('messages.fromOrganisation') {{ $organisation->name }} @lang('messages.hasScheduledAnInterviewWithYou').

## @lang('messages.interviewDetails')

**@lang('messages.organisation'):** {{ $organisation->name }}

**@lang('messages.interviewer'):** {{ $creator->name }}

**@lang('messages.agenda'):**
{{ $interview->agenda }}

---

## @lang('messages.statementsToBeReviewed')

@lang('messages.duringThisInterviewWeWillReviewTheFollowingStatements'):

@component('mail::table')
| # | @lang('messages.statement') |
|:---|:------------|
@foreach ($statements as $index => $statement)
| {{ $index + 1 }} | {{ $statement['content_' . $locale] }} |
@endforeach
@endcomponent

---

## @lang('messages.preparation')

@lang('messages.pleasePrepareToCover'):
@foreach ($statements as $statement)
- {{ $statement['content_' . $locale] }}
@endforeach

---

@component('mail::button', ['url' => url('/'), 'color' => 'primary'])
@lang('messages.loginToSystem')
@endcomponent

@lang('messages.ifYouHaveQuestionsContactInterviewer'):
- **@lang('messages.name'):** {{ $creator->name }}
- **@lang('messages.email'):** {{ $creator->email }}

@lang('messages.thankYouForYourCooperation'),<br>
{{ $organisation->name }}<br>
{{ config('app.name') }}
@endcomponent
