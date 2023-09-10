@extends('layouts/contentLayoutMaster')
@section('title', trans('messages.review'))
@section('vendor-style')
    {{-- vendor css files --}}
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/dataTables.bootstrap5.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/forms/select/select2.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/extensions/toastr.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/extensions/sweetalert2.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/extensions/nouislider.min.css')) }}">
@endsection
@section('page-style')
    <link rel="stylesheet" href="{{ asset(mix('css/base/plugins/extensions/ext-component-toastr.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('css/base/plugins/extensions/ext-component-sliders.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('css/base/core/colors/palette-noui.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('css/base/pages/app-ecommerce.css')) }}">

    <style>
        body {
            overflow-x: hidden;
        }
    </style>
@endsection
@section('content')
    @if (session()->get('success'))
        <div class="alert alert-success" role="alert">
            <h4 class="alert-heading">@lang('messages.success')!</h4>
            <div class="alert-body">
                {{ session()->get('success') }}.
            </div>
        </div>
    @endif
    @if (session()->get('error'))
        <div class="alert alert-danger" role="alert">
            <h4 class="alert-heading">@lang('messages.error')!</h4>
            <div class="alert-body">
                {{ session()->get('error') }}.
            </div>
        </div>
    @endif
    <organisation-review locale="{{ App::currentLocale() }}" :review-statuses="{{ Js::from($reviewStatuses) }}" action-id="{{ $actionId }}" :plans="{{ Js::from($plans) }}" :auditor-statements="{{ Js::from($auditorStatements) }}" :org="{{session()->get('selected_org')['id']}}" />
@endsection
@section('vendor-script')
    {{-- vendor files --}}
    <script src="{{ asset(mix('vendors/js/tables/datatable/jquery.dataTables.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/forms/select/select2.full.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/extensions/toastr.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/charts/apexcharts.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/extensions/sweetalert2.all.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/extensions/nouislider.min.js')) }}"></script>
@endsection
@section('page-script')
    <script src="{{ asset(mix('js/scripts/forms/form-select2.js')) }}"></script>
    <script src="{{ asset(mix('js/models/organisations/review/app.js')) }}"></script>
@endsection
