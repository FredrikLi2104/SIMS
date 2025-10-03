@extends('layouts/contentLayoutMaster')

@section('title', trans('messages.snis'))

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
                    <h4 class="card-title">@lang('messages.snis')</h4>
                </div>
                <div class="card-body">
                    <p class="card-text">
                        @lang('messages.aTableOfAll') @lang('messages.snis').
                    </p>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>@lang('messages.code')</th>
                                <th>@lang('messages.desc') - @lang('messages.english')</th>
                                <th>@lang('messages.desc') - @lang('messages.swedish')</th>
                                <th class="text-center">@lang('messages.actions')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($snis as $sni)
                                <tr>
                                    <td>{{ $sni->id }}</td>
                                    <td>{{ $sni->code }}</td>
                                    <td>{{ $sni->desc_en }}</td>
                                    <td>{{ $sni->desc_se }}</td>
                                    <td class="text-center">
                                        <a href="{{ route('snis.edit', [App::currentLocale(), $sni->id]) }}">
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
