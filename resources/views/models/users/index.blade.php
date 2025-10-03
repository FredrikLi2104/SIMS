@extends('layouts/contentLayoutMaster')

@section('title', trans('messages.users'))

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
                    <h4 class="card-title">@lang('messages.users')</h4>
                </div>
                <div class="card-body d-flex justify-content-between align-items-center">
                    <span class="card-text">
                        @lang('messages.aTableOfAll') @lang('messages.users').
                    </span>
                    <a href="{{ route('users.index', [App::currentLocale(), !$showDisabled]) }}"
                       class="btn btn-success waves-effect waves-float waves-light">{{ $showDisabled ? __('messages.hide_disabled') : __('messages.show_disabled')  }}</a>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>@lang('messages.name')</th>
                            <th>@lang('messages.email')</th>
                            <th>@lang('messages.role')</th>
                            <th class="text-center">@lang('messages.actions')</th>
                        </tr>
                        </thead>
                        <tbody>
                        <!-- Auth Layers -->
                        <!-- Super -->
                        @if (Auth::user()->role == 'super')
                            @foreach ($users as $user)
                                <tr>
                                    <td>{{ $user->id }}</td>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>{{$roles[$user->role]}} ({{ $user->role }})</td>
                                    <td class="text-center">
                                        <a href="{{ route('users.edit', [App::currentLocale(), $user->id]) }}">
                                            <button type="button" class="btn btn-gradient-primary">
                                                @lang('messages.edit')
                                            </button>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                        <!-- Admin -->
                        @if (Auth::user()->role == 'admin')
                            @foreach ($users as $user)
                                @if ($user->role != 'super')
                                    <tr>
                                        <td>{{ $user->id }}</td>
                                        <td>{{ $user->name }}</td>
                                        <td>{{ $user->email }}</td>
                                        <td>{{$roles[$user->role]}} ({{ $user->role }})</td>
                                        <td class="text-center">
                                            <a href="{{ route('users.edit', [App::currentLocale(), $user->id]) }}">
                                                <button type="button" class="btn btn-gradient-primary">
                                                    @lang('messages.edit')
                                                </button>
                                            </a>
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                        @endif
                        <!-- Moderator -->
                        @if (Auth::user()->role == 'moderator')
                            @foreach ($users as $user)
                                @if (!(in_array($user->role, ['super', 'admin'])))
                                    <tr>
                                        <td>{{ $user->id }}</td>
                                        <td>{{ $user->name }}</td>
                                        <td>{{ $user->email }}</td>
                                        <td>{{$roles[$user->role]}} ({{ $user->role }})</td>
                                        <td class="text-center">
                                            <a href="{{ route('users.edit', [App::currentLocale(), $user->id]) }}">
                                                <button type="button" class="btn btn-gradient-primary">
                                                    @lang('messages.edit')
                                                </button>
                                            </a>
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                        @endif

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <!-- Striped rows end -->

@endsection
