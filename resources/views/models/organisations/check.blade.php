@extends('layouts/contentLayoutMaster')
@section('title', __('messages.check'))
@section('content')
    <organisation-check locale="{{ App::currentLocale() }}" :faqs="{{ Js::from($faqs) }}"
                        :links="{{ Js::from($links) }}" :messages="{{ Js::from($messages) }}"></organisation-check>
@endsection
@section('page-script')
    <script src="{{ asset(mix('js/models/organisations/check/app.js')) }}"></script>
@endsection
