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
                                <th>@lang('messages.sortOrder')</th>
                                <th>ID</th>
                                <th>@lang('messages.type')</th>
                                <th>@lang('messages.desc') - @lang('messages.english')</th>
                                <th>@lang('messages.desc') - @lang('messages.swedish')</th>
                                <th class="text-center">@lang('messages.actions')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($statements as $statement)
                                <tr>
                                    <td>{{ $statement->sort_order }}</td>
                                    <td>{{ $statement->id }}</td>
                                    <td>{{ $statement->statement_type->code }}</td>
                                    <td>{{ substr($statement->desc_en,0,24).'...' }}</td>
                                    <td>{{ substr($statement->desc_se,0,24).'...' }}</td>
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
