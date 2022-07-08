@extends('layouts/contentLayoutMaster')
@section('title', trans('messages.sanction') . ' ' . trans('messages.edit'))
@section('vendor-style')
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/pickers/flatpickr/flatpickr.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/forms/select/select2.min.css')) }}">
@endsection
@section('page-style')
    <link rel="stylesheet" href="{{ asset(mix('css/base/plugins/forms/pickers/form-flat-pickr.css')) }}">
@endsection
@section('content')
    <section id="multiple-column-form">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">@lang('messages.sanction') @lang('messages.edit') @lang('messages.allFields')</h4>
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
                        <form id="form" class="form" action="{{ route('sanctions.update', [App::currentLocale(), $sanction->id]) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <div class="row">
                                <div class="form-group col-6">
                                    <label class="form-label">@lang('messages.title')</label>
                                    <div class="mb-1">
                                        <a href="{{ $sanction->url }}" target="_blank">
                                            <button type="button" class="btn btn-outline-primary waves-effect mb-1">
                                                <i data-feather="external-link"></i>
                                                <span>{{ $sanction->title }}</span>
                                            </button>
                                        </a>
                                    </div>
                                </div>
                                <div class="form-group col-6">
                                    <label class="form-label">@lang('messages.dpa')</label>
                                    <div class="row d-flex align-items-center justify-content-start">
                                        <div class="col-1">
                                            @if ($sanction->dpa->country)
                                                <img src="{{ asset('/images/flags/svg/' . $sanction->dpa->country->code . '.svg') }}" width="40px" />
                                            @endif
                                        </div>
                                        <div class="col-5 align-items-center">
                                            <p class="mx-0 my-0">{{ $sanction->dpa->name }}</p>
                                        </div>
                                    </div>

                                </div>
                                <div class="col-md-6">
                                    <div class="mb-1">
                                        <label class="form-label" for="started_at">@lang('messages.startedOn') [@lang('messages.optional')]</label>
                                        <input type="text" name="started_at" id="started_at" class="form-control flatpickr-basic @error('started_at') is-invalid @enderror" placeholder="YYYY-MM-DD" value="{{ $sanction->started_at }}" />
                                        @error('started_at')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-1">
                                        <label class="form-label" for="started_at_parsed">@lang('messages.decidedOn') @lang('messages.apiParsed')</label>
                                        <input type="text" id="started_at_parsed" class="form-control @error('started_at_parsed') is-invalid @enderror" value="{{ $sanction->htmlClean()['started_at'] }}" disabled />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-1">
                                        <label class="form-label" for="decided_at">@lang('messages.decidedOn') [@lang('messages.optional')]</label>
                                        <input type="text" name="decided_at" id="decided_at" class="form-control flatpickr-basic @error('decided_at') is-invalid @enderror" placeholder="YYYY-MM-DD" value="{{ $sanction->decided_at }}" />
                                        @error('decided_at')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-1">
                                        <label class="form-label" for="decided_at_parsed">@lang('messages.decidedOn') @lang('messages.apiParsed')</label>
                                        <input type="text" id="decided_at_parsed" class="form-control @error('decided_at_parsed') is-invalid @enderror" value="{{ $sanction->htmlClean()['decided_at'] }}" disabled />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-1">
                                        <label class="form-label" for="published_at">@lang('messages.publishedOn') [@lang('messages.optional')]</label>
                                        <input type="text" name="published_at" id="published_at" class="form-control flatpickr-basic @error('published_at') is-invalid @enderror" placeholder="YYYY-MM-DD" value="{{ $sanction->published_at }}" />
                                        @error('published_at')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-1">
                                        <label class="form-label" for="published_at_parsed">@lang('messages.decidedOn') @lang('messages.apiParsed')</label>
                                        <input type="text" id="published_at_parsed" class="form-control @error('published_at_parsed') is-invalid @enderror" value="{{ $sanction->htmlClean()['published_at'] }}" disabled />
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="mb-1">
                                        <label class="form-label" for="fine">@lang('messages.fine') [@lang('messages.optional')]</label>
                                        <input type="text" id="fine" class="form-control @error('fine') is-invalid @enderror" placeholder="3800" name="fine" value="{{ $sanction->fine }}" />
                                        @error('fine')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-1">
                                        <label class="form-label" for="fine_parsed">@lang('messages.fine') @lang('messages.apiParsed')</label>
                                        <input type="text" id="fine_parsed" class="form-control @error('fine_parsed') is-invalid @enderror" value="{{ $sanction->htmlClean()['fine'] }}" disabled />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-1">
                                        <label class="form-label" for="currency_id">@lang('messages.currency')</label>
                                        <select id="currency_id" class="select2 form-select form-control @error('currency_id') is-invalid @enderror" name="currency_id">
                                            <option value="">@lang('messages.pleaseSelect')</option>
                                            @foreach ($currencies as $currency)
                                                <option @selected($sanction->currency_id == $currency->id) value="{{ $currency->id }}">{{ $currency->symbol }}</option>
                                            @endforeach
                                        </select>
                                        @error('currency_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-1">
                                        <label class="form-label" for="currency_parsed">@lang('messages.currency') @lang('messages.apiParsed')</label>
                                        <input type="text" id="currency_parsed" class="form-control @error('currency_parsed') is-invalid @enderror" value="{{ $sanction->htmlClean()['currency'] }}" disabled />
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
@section('vendor-script')
    <script src="{{ asset(mix('vendors/js/pickers/flatpickr/flatpickr.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/forms/select/select2.full.min.js')) }}"></script>
@endsection
@section('page-script')
    <!-- Page js files -->
    <script src="{{ asset(mix('js/scripts/forms/form-select2.js')) }}"></script>
    <script type="text/javascript" src="{{ asset('vendor/jsvalidation/js/jsvalidation.js') }}"></script>
    {!! JsValidator::formRequest('App\Http\Requests\SanctionUpdateRequest', '#form') !!}
    <script src="{{ asset(mix('js/scripts/forms/pickers/form-pickers.js')) }}"></script>
@endsection
