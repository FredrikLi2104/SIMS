@extends('layouts/contentLayoutMaster')
@section('title', __('messages.features'))
@section('content')
    <features locale="{{ App::currentLocale() }}" :components="{{ Js::from($components) }}"
              :statements="{{ Js::from($statements) }}" :organisations="{{ Js::from($organisations) }}"
              :messages="{{ Js::from($messages) }}"></features>
@endsection
@section('vendor-style')
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/extensions/toastr.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/forms/select/select2.min.css')) }}">
@endsection
@section('page-style')
    <link rel="stylesheet" href="{{ asset(mix('css/base/plugins/extensions/ext-component-toastr.css')) }}">
    <style>
        body {
            overflow-x: hidden;
        }
    </style>
@endsection
@section('vendor-script')
    <script src="{{ asset(mix('vendors/js/extensions/toastr.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/forms/select/select2.full.min.js')) }}"></script>
@endsection
@section('page-script')
    <script src="{{ asset(mix('js/features/app.js')) }}"></script>
@endsection
