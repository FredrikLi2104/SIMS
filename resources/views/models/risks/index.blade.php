@extends('layouts/contentLayoutMaster')
@section('title', trans('messages.risks'))
@section('vendor-style')
    {{-- vendor css files --}}
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/dataTables.bootstrap5.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/charts/apexcharts.css')) }}">
@endsection
@section('page-style')
 <link rel="stylesheet" href="{{ asset(mix('css/base/plugins/charts/chart-apex.css')) }}">
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
    <risks locale="{{App::currentLocale()}}"/>
@endsection
@section('vendor-script')
    {{-- vendor files --}}
    <script src="{{ asset(mix('vendors/js/charts/chart.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/charts/apexcharts.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/tables/datatable/jquery.dataTables.min.js')) }}"></script>
    <script src="{{ asset(mix('js/chartjs-plugin-datalabels.min.js')) }}"></script>
@endsection
@section('page-script')
    <script src="{{ asset(mix('js/models/risks/index/app.js')) }}"></script>
@endsection
