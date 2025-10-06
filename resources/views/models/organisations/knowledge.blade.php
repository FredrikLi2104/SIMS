@extends('layouts/contentLayoutMaster')
@section('title', __('messages.knowledge'))
@section('content')
    <organisation-knowledge locale="{{ App::currentLocale() }}" :faqs="{{ Js::from($faqs) }}"
                            :links="{{ Js::from($links) }}"
                            :messages="{{ Js::from($messages) }}"></organisation-knowledge>
@endsection
@section('page-script')
    <script src="{{ asset(mix('js/models/organisations/knowledge/app.js')) }}"></script>
@endsection
