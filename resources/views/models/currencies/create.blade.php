@extends('layouts/contentLayoutMaster')
@section('title', $title)
@section('content')
    <section id="multiple-column-form">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">@lang('messages.currencies') {{ $action_msg }} @lang('messages.allFields')</h4>
                    </div>
                    <div class="card-body">
                        @if (count($errors->all()) > 0)
                            <div class="alert alert-danger" role="alert">
                                <h4 class="alert-heading">{{ __('messages.error') }}</h4>
                                @foreach ($errors->all() as $error)
                                    <div class="alert-body">
                                        {{ $error }}
                                    </div>
                                @endforeach
                            </div>
                        @endif
                        <form id="form" class="form" action="{{ $action_url }}"
                              method="POST">
                            @csrf
                            @if($is_update ?? false)
                                @method('PATCH')
                            @endif
                            <div class="row">
                                <div class="col-md-6 col-12">
                                    <div class="mb-1">
                                        <label class="form-label" for="symbol">Symbol</label>
                                        <input type="text" id="symbol"
                                               class="form-control @error('symbol') is-invalid @enderror"
                                               placeholder="EUR" name="symbol"
                                               value="{{ ($is_update ?? false) ? old('symbol') ?? $currency->symbol : old('symbol') }}"/>
                                        @error('symbol')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="mb-1">
                                        <label class="form-label" for="value">@lang('messages.value')</label>
                                        <input type="text" id="value"
                                               class="form-control @error('value') is-invalid @enderror"
                                               placeholder="1" name="value"
                                               value="{{ ($is_update ?? false) ? old('value') ?? $currency->value : old('value') }}"/>
                                        @error('value')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
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
@endsection
@section('page-script')
    <!-- Page js files -->
    <script type="text/javascript" src="{{ asset('vendor/jsvalidation/js/jsvalidation.js') }}"></script>
    @if ($is_update ?? false)
        {!! JsValidator::formRequest('App\Http\Requests\CurrencyUpdateRequest', '#form') !!}
    @else
        {!! JsValidator::formRequest('App\Http\Requests\CurrencyStoreRequest', '#form') !!}
    @endif
@endsection
