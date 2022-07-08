@extends('layouts/contentLayoutMaster')

@section('title', trans('messages.organisations'))

@section('content')
    @if (session()->get('success'))
        <div class="alert alert-success" role="alert">
            <h4 class="alert-heading">@lang('messages.success')!</h4>
            <div class="alert-body">
                {{ session()->get('success') }}.
            </div>
        </div>
    @endif
    <div class="row" id="table-striped">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">@lang('messages.organisations')</h4>
                </div>
                <div class="card-body">
                    <p class="card-text">
                        @lang('messages.aTableOfAll') @lang('messages.organisations').
                    </p>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>@lang('messages.name')</th>
                                <th>@lang('messages.sni')</th>
                                <th>@lang('messages.commitment')</th>
                                <th class="text-center">@lang('messages.actions')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($organisations as $organisation)
                                <tr>
                                    <td>{{ $organisation->id }}</td>
                                    <td>{{ $organisation->name }}</td>
                                    <td>{{ $organisation->sni?->code }}</td>
                                    <td>{{ $organisation->commitment }}</td>
                                    <td class="text-center">
                                        <a href="{{ route('organisations.edit', [App::currentLocale(), $organisation->id]) }}">
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
    <!-- Striped rows end -->

@endsection
