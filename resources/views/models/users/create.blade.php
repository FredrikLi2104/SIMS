@extends('layouts/contentLayoutMaster')

@section('title', trans('messages.user') . ' ' . trans('messages.create'))

@section('content')
    <!-- Basic multiple Column Form section start -->
    <section id="multiple-column-form">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">@lang('messages.user') @lang('messages.create') @lang('messages.allFields')</h4>
                    </div>
                    <div class="card-body">
                        @if (count($errors->all()) > 0)
                            <div class="alert alert-danger" role="alert">
                                <h4 class="alert-heading">Error!</h4>
                                @foreach ($errors->all() as $error)
                                    <div class="alert-body">
                                        {{ $error }}
                                    </div>
                                @endforeach
                            </div>
                        @endif
                        <form id="form" class="form" action="{{ route('users.store', App::currentLocale()) }}"
                              method="POST">
                            @csrf
                            <div class="row">
                                <div class="col-md-6 col-12">
                                    <div class="mb-1">
                                        <label class="form-label" for="name">@lang('messages.name')</label>
                                        <input type="text" id="name"
                                               class="form-control @error('name') is-invalid @enderror"
                                               placeholder="John Smith" name="name" value="{{ old('name') }}"/>
                                        @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="mb-1">
                                        <label class="form-label" for="email">@lang('messages.email')</label>
                                        <input type="text" id="email"
                                               class="form-control @error('email') is-invalid @enderror"
                                               placeholder="john@example.com" name="email" value="{{ old('email') }}"/>
                                        @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <label class="form-label" for="password">@lang('messages.password')</label>
                                    <div class="input-group">
                                        <input type="text" id="password"
                                               class="form-control @error('password') is-invalid @enderror"
                                               placeholder="some$TRong_Pass" name="password"
                                               aria-label="Recipient's username" aria-describedby="generate"
                                               value="{{ old('password') }}"/>
                                        <button class="input-group-text btn btn-primary" type="button"
                                                id="generate">@lang('messages.generate')</button>
                                    </div>
                                    @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small
                                        class="text-muted mb-1"><i>@lang('messages.leaveThisBlankToMaintainCurrentPass')</i></small>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="mb-1">
                                        <label class="form-label"
                                               for="password_confirmation">@lang('messages.password_confirm')</label>
                                        <input type="text" id="password_confirmation"
                                               class="form-control @error('password_confirmation') is-invalid @enderror"
                                               placeholder="some$TRong_Pass" name="password_confirmation"
                                               value="{{ old('password_confirmation') }}"/>
                                        <small
                                            class="text-muted"><i>@lang('messages.leaveThisBlankToMaintainCurrentPass')</i></small>
                                        @error('password_confirmation')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="mb-1">
                                        <label class="form-label" for="role">@lang('messages.role')</label>
                                        <select id="role"
                                                class="form-select form-control @error('role') is-invalid @enderror"
                                                name="role" {{ old('role') }}>
                                            @foreach ($roles as $desc => $role)
                                                <option @selected(old('role') == $role) value={{ $role }}>{{ $desc }}
                                                    ({{ $role }})
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('role')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="mb-1">
                                        <label class="form-label"
                                               for="organisation_id">@lang('messages.organisation')</label>
                                        <select id="organisation_id"
                                                class="select2 form-select form-control @error('organisation_id') is-invalid @enderror"
                                                name="organisation_id">
                                            <option value="">@lang('messages.pleaseSelect')</option>
                                            @foreach ($organisations as $organisation)
                                                <option
                                                    @selected(old('organisation?->id') == $organisation->id) value="{{ $organisation->id }}">{{ $organisation->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('organisation_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="form-check form-check-danger mb-1">
                                        <input type="checkbox" id="disabled" class="form-check-input" name="disabled"
                                               value="1">
                                        <label class="form-check-label" for="disabled">@lang('messages.disabled')</label>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary me-1">@lang('messages.submit')</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Basic Floating Label Form section end -->

@endsection
@section('page-script')
    <!-- Page js files -->
    <script type="text/javascript" src="{{ asset('vendor/jsvalidation/js/jsvalidation.js') }}"></script>
    {!! JsValidator::formRequest('App\Http\Requests\UserStoreRequest', '#form') !!}
    <script type="text/javascript">
        //function
        function generate() {
            let p = Array(16).fill("0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz~!@-#$_").map(function (x) {
                return x[Math.floor(Math.random() * x.length)]
            }).join('');
            $('#password').val(p);
            $('#password_confirmation').val(p);
        };
        $('#generate').click(generate);
    </script>
@endsection
