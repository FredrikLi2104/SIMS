@extends('layouts/contentLayoutMaster')

@section('title', trans('messages.periodsCreate'))

@section('content')
    <!-- Basic multiple Column Form section start -->
    <section id="multiple-column-form">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">@lang('messages.periodsCreate') @lang('messages.allFields')</h4>
                    </div>
                    <div class="card-body">
                        <form id="form" class="form" action="{{ route('periods.store', App::currentLocale()) }}" method="POST">
                            @csrf
                            <div class="row">
                                <div class="col-md-6 col-12">
                                    <div class="mb-1">
                                        <label class="form-label" for="name_en">@lang('messages.nameInEnglish')</label>
                                        <input type="text" id="name_en" class="form-control @error('name_en') is-invalid @enderror" placeholder="First Quarter" name="name_en" value="{{ old('name_en') }}" />
                                        @error('name_en')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="mb-1">
                                        <label class="form-label" for="name_se">@lang('messages.nameInSwedish')</label>
                                        <input type="text" id="name_se" class="form-control @error('name_se') is-invalid @enderror" placeholder="Första kvartalet" name="name_se" value="{{ old('name_se') }}" />
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="mb-1">
                                        <label class="form-label" for="start">@lang('messages.start') @lang('messages.monthIndexInYear')</label>
                                        <input type="text" id="start" class="form-control @error('start') is-invalid @enderror" placeholder="1" name="start" value="{{ old('start') }}" />
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="mb-1">
                                        <label class="form-label" for="end">@lang('messages.end') @lang('messages.monthIndexInYear')</label>
                                        <input type="text" id="end" class="form-control @error('end') is-invalid @enderror" placeholder="3" name="end" value="{{ old('end') }}" />
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="mb-1">
                                        <label class="form-label" for="sort_order">@lang('messages.sort_order')</label>
                                        <input type="text" id="sort_order" class="form-control @error('sort_order') is-invalid @enderror" placeholder="{{ $sortOrderPlaceholder }}" name="sort_order" value="{{ old('sort_order') }}" />
                                        @error('sort_order')
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
    <!-- Basic Floating Label Form section end -->

@endsection
@section('page-script')
    <!-- Page js files -->
    <script type="text/javascript" src="{{ asset('vendor/jsvalidation/js/jsvalidation.js') }}"></script>
    {!! JsValidator::formRequest('App\Http\Requests\PeriodStoreRequest', '#form') !!}
@endsection
