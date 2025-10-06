@extends('layouts.contentLayoutMaster')
@section('title', __('messages.actionType') . ' ' . ($is_update ?? null ? __('messages.update') : __('messages.create')))
@section('vendor-style')
    {{-- vendor css files --}}
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
@section('content')
    <section id="multiple-column-form">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">@lang('messages.actionType') {{ $action_msg }} @lang('messages.allFields')</h4>
                    </div>
                    <div class="card-body">
                        <form id="form" class="form">
                            @csrf
                            @if($is_update ?? false)
                                @method('PUT')
                            @endif
                            <action-types locale="{{ App::currentLocale() }}"
                                          :messages="{{ Js::from($messages) }}"
                                          :is-update="{{ $is_update ?? Js::from(false) }}"
                                          :roles="{{ Js::from($roles) }}"
                                          :urls="{{ Js::from($urls) }}"
                                          :models="{{ Js::from($models) }}"
                                          :action-type-data="{{ $action_type ?? Js::from([]) }}">
                            </action-types>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
@section('vendor-script')
    {{-- vendor files --}}
    <script src="{{ asset(mix('vendors/js/extensions/toastr.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/forms/select/select2.full.min.js')) }}"></script>
@endsection
@section('page-script')
    <!-- Page js files -->
    <script src="{{ asset(mix('js/models/action_types/create/app.js')) }}"></script>
@endsection
