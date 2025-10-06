@extends('layouts.contentLayoutMaster')
@section('title', __('messages.task_statuses') . ' ' . ($is_update ?? null ? __('messages.update') : __('messages.create')))
@section('vendor-style')
    {{-- vendor css files --}}
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/extensions/toastr.min.css')) }}">
@endsection
@section('page-style')
    <link rel="stylesheet" href="{{ asset(mix('css/base/plugins/extensions/ext-component-toastr.css')) }}">
@endsection
@section('content')
    <section id="multiple-column-form">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">@lang('messages.task_statuses') {{ $action_msg }} @lang('messages.allFields')</h4>
                    </div>
                    <div class="card-body">
                        <form id="form" class="form">
                            @csrf
                            @if($is_update ?? false)
                                @method('PUT')
                            @endif
                            <task-statuses locale="{{ App::currentLocale() }}"
                                           :messages="{{ Illuminate\Support\Js::from($messages) }}"
                                           :is-update="{{ $is_update ?? Illuminate\Support\Js::from(false) }}"
                                           :sort-order="{{ $sort_order }}"
                                           :task-status-data="{{ $task_status ?? Illuminate\Support\Js::from([]) }}">
                            </task-statuses>
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
@endsection
@section('page-script')
    <!-- Page js files -->
    <script src="{{ asset(mix('js/models/task_statuses/create/app.js')) }}"></script>
@endsection
