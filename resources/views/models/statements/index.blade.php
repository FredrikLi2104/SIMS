@extends('layouts/contentLayoutMaster')

@section('title', trans('messages.statements'))

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
                    <h4 class="card-title">@lang('messages.statements')</h4>
                </div>
                <div class="card-body">
                    <p class="card-text">
                        @lang('messages.aTableOfAll') @lang('messages.statements').
                    </p>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th>@lang('messages.subcode')</th>
                            <th>@lang('messages.content_en')</th>
                            <th>@lang('messages.content_se')</th>
                            <th>@lang('messages.desc_en')</th>
                            <th>@lang('messages.desc_se')</th>
                            <th class="text-center">@lang('messages.actions')</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($statements as $statement)
                            <tr>
                                <td>{{ $statement->subcode }}</td>
                                <td>{{ Str::limit($statement->content_en, 24) }}</td>
                                <td>{{ Str::limit($statement->content_se, 24) }}</td>
                                <td>{{ Str::limit($statement->desc_en, 24) }}</td>
                                <td>{{ Str::limit($statement->desc_se, 24) }}</td>
                                <td class="text-center">
                                    <a href="{{ route('statements.edit', [App::currentLocale(), $statement->id]) }}">
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
