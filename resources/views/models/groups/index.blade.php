@extends('layouts/contentLayoutMaster')

@section('title', trans('messages.groups'))

@section('content')
    @if (session()->get('success'))
        <div class="alert alert-success" role="alert">
            <h4 class="alert-heading">@lang('messages.success')!</h4>
            <div class="alert-body">
                {{ session()->get('success') }}.
            </div>
        </div>
    @endif
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">@lang('messages.groups')</h4>
                </div>
                <div class="card-body">
                    <p class="card-text">
                        @lang('messages.aTableOfAll') @lang('messages.groups').
                    </p>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered table-sm">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>@lang('messages.desc_en')</th>
                            <th>@lang('messages.desc_se')</th>
                            <th class="text-center">@lang('messages.actions')</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($groups as $group)
                            <tr>
                                <td>{{ $group->id }}</td>
                                <td>{{ $group->desc_en }}</td>
                                <td>{{ $group->desc_se }}</td>
                                <td class="text-center">
                                    <a href="{{ route('groups.edit', [App::currentLocale(), $group->id]) }}">
                                        <button type="button"
                                                class="btn btn-gradient-primary">@lang('messages.edit')</button>
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
