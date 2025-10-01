@extends('layouts/contentLayoutMaster')
@section('title', $title)
@section('content')
    <section id="multiple-column-form">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">@lang('messages.types') {{ $action_msg }} @lang('messages.allFields')</h4>
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
                                        <label class="form-label" for="text_en">Text @lang('messages.inEnglish')</label>
                                        <input type="text" id="text_en"
                                               class="form-control @error('text_en') is-invalid @enderror"
                                               placeholder="Unknown" name="text_en"
                                               value="{{ ($is_update ?? false) ? old('text_en') ?? $type->text_en : old('text_en') }}"/>
                                        @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="mb-1">
                                        <label class="form-label" for="text_se">Text @lang('messages.inSwedish')</label>
                                        <input type="text" id="text_se"
                                               class="form-control @error('text_se') is-invalid @enderror"
                                               placeholder="Okänd" name="text_se"
                                               value="{{ ($is_update ?? false) ? old('text_se') ?? $type->text_se : old('text_se') }}"/>
                                        @error('name')
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
        {!! JsValidator::formRequest('App\Http\Requests\TypeUpdateRequest', '#form') !!}
    @else
        {!! JsValidator::formRequest('App\Http\Requests\TypeStoreRequest', '#form') !!}
    @endif
@endsection
