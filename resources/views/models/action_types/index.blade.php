@extends('layouts/contentLayoutMaster')
@section('title', trans('messages.actionTypes'))
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
    <div class="row" id="table-striped">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">@lang('messages.actionTypes')</h4>
                </div>
                <div class="card-body">
                    <p class="card-text">
                        @lang('messages.aTableOfAll') @lang('messages.actionTypes').
                    </p>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>id</th>
                                <th>@lang('messages.name') - @lang('messages.english')</th>
                                <th>@lang('messages.name') - @lang('messages.swedish')</th>
                                <th class="text-center">@lang('messages.actions')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($actionTypes as $actionType)
                                <tr>
                                    <td>{{ $actionType->id }}</td>
                                    <td>{{ $actionType->name_en }}</td>
                                    <td>{{ $actionType->name_se }}</td>
                                    <td class="text-center">
                                        <a href="{{ route('action_types.edit', [App::currentLocale(), $actionType->id]) }}">
                                            <button type="button" class="btn btn-gradient-primary">
                                                @lang('messages.edit')
                                            </button>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
