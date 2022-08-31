@extends('layouts/contentLayoutMaster')

@section('title', 'GDPR | ' . __('messages.home'))

@section('vendor-style')
    {{-- vendor css files --}}
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/dataTables.bootstrap5.min.css')) }}">
@endsection
@section('page-style')
    {{-- Page css files --}}
    <link rel="stylesheet" href="{{ asset(mix('css/base/pages/dashboard-ecommerce.css')) }}">
@endsection

@section('content')
    <!-- Dashboard Ecommerce Starts -->
    <section id="dashboard-ecommerce">
        <div class="row match-height">
            <!-- Medal Card -->
            <div class="col-xl-4 col-md-6 col-12">
                <div class="card card-congratulation-medal">
                    <div class="card-body" style="z-index: 11;">
                        <h5>@lang('messages.welcome') 🎉 {{ Auth::user()->name }}</h5>
                        <p class="card-text font-small-3">@lang('messages.welcomeTo') GDPR</p>
                        <p class="card-text font-small-3">&nbsp;</p>
                        <p class="card-text font-small-3">&nbsp;</p>
                        <img src="{{ asset('images/illustration/badge.svg') }}" class="congratulation-medal" alt="Medal Pic" />
                    </div>
                </div>
            </div>
            <!--/ Medal Card -->
            <!-- Statistics Card -->
            <!--/ Statistics Card -->
        </div>
        @if (Auth::user()->role == 'user')
            <organisation-dashboard locale="{{App::currentLocale()}}" />
        @endif
    </section>
    <!-- Dashboard Ecommerce ends -->
@endsection

@section('vendor-script')
    {{-- vendor files --}}
    <script src="{{ asset(mix('vendors/js/tables/datatable/jquery.dataTables.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/tables/datatable/datatables.buttons.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/tables/datatable/dataTables.bootstrap5.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/tables/datatable/dataTables.responsive.min.js')) }}"></script>  
@endsection
@section('page-script')
    {{-- Page js files --}}
    <script src="{{ asset(mix('js/models/organisations/dashboard/app.js')) }}"></script>
@endsection
