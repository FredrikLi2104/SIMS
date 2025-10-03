@extends('layouts/contentLayoutMaster')

@section('title', trans('messages.periods'))

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
                    <h4 class="card-title">@lang('messages.periods')</h4>
                </div>
                <div class="card-body">
                    <p class="card-text">
                        @lang('messages.aTableOfAll') @lang('messages.periods').
                    </p>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>@lang('messages.sort_order')</th>
                                <th>ID</th>
                                <th>@lang('messages.name') - @lang('messages.english')</th>
                                <th>@lang('messages.name') - @lang('messages.swedish')</th>
                                <th>@lang('messages.start')</th>
                                <th>@lang('messages.end')</th>
                                <th class="text-center">@lang('messages.actions')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($periods as $period)
                                <tr>
                                    <td>{{ $period->sort_order }}</td>
                                    <td>{{ $period->id }}</td>
                                    <td>
                                        {{ $period->name_en }}
                                    </td>
                                    <td>{{ $period->name_se }}</td>
                                    <td>{{ \Carbon\Carbon::create()->startOfMonth()->month($period->start)->locale(__('messages.localeCarbon'))->getTranslatedMonthName('M') }}</td>
                                    <td>{{ \Carbon\Carbon::create()->startOfMonth()->month($period->end)->locale(__('messages.localeCarbon'))->getTranslatedMonthName('M') }}</td>
                                    <td class="text-center">
                                        <a href="{{ route('periods.edit', [App::currentLocale(), $period->id]) }}">
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
