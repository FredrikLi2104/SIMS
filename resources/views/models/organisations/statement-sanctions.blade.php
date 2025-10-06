@extends('layouts/contentLayoutMaster')
@section('title', trans('messages.sanctions'))
@section('vendor-style')
    {{-- vendor css files --}}
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/dataTables.bootstrap5.min.css')) }}">
@endsection
@section('content')
    <sanctions locale="{{ App::currentLocale() }}" :messages="{{ Js::from(__('messages')) }}"
               statement-sub-code="{{ $statementSubCode }}"></sanctions>
@endsection
@section('vendor-script')
    {{-- vendor files --}}
    <script src="{{ asset(mix('vendors/js/tables/datatable/jquery.dataTables.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/tables/datatable/datatables.buttons.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/tables/datatable/dataTables.bootstrap5.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/tables/datatable/dataTables.responsive.min.js')) }}"></script>
@endsection
@section('page-script')
    <script src="{{ asset(mix('js/models/organisations/insights/statement-sanctions/app.js')) }}"></script>
@endsection

