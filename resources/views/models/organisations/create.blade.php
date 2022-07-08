@extends('layouts/contentLayoutMaster')

@section('title', trans('messages.organisationCreate'))
@section('vendor-style')
    <!-- vendor css files -->
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/forms/select/select2.min.css')) }}">
@endsection

@section('content')
    <!-- Basic multiple Column Form section start -->
    <section id="multiple-column-form">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">@lang('messages.organisationCreate') @lang('messages.allFields')</h4>
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
                        <form id="form" class="form" action="{{ route('organisations.store', App::currentLocale()) }}" method="POST">
                            @csrf
                            <div class="row">
                                <div class="col-md-6 col-12">
                                    <div class="mb-1">
                                        <label class="form-label" for="name">@lang('messages.name')</label>
                                        <input type="text" id="name" class="form-control @error('name') is-invalid @enderror" placeholder="Lärarnas riksförbund" name="name" value="{{ old('name') }}" />
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="mb-1">
                                        <label class="form-label" for="number">@lang('messages.number')</label>
                                        <input type="text" id="number" class="form-control @error('number') is-invalid @enderror" placeholder="556-123123123" name="number" value="{{ old('number') }}" />
                                        @error('number')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="mb-1">
                                        <label class="form-label" for="turnover">@lang('messages.turnover') [@lang('messages.optional')]</label>
                                        <input type="text" id="turnover" class="form-control @error('turnover') is-invalid @enderror" placeholder="100000" name="turnover" {{ old('turnover') }} />
                                        @error('turnover')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="mb-1">
                                        <label class="form-label" for="commitment">@lang('messages.commitment')</label>
                                        <select id="commitment" class="form-select form-control @error('commitment') is-invalid @enderror" name="commitment" {{ old('commitment') }}>
                                            @for ($i = 0; $i < 5; $i++)
                                                <option @selected(old('commitment') == $i + 1) value={{ $i + 1 }}>{{ $i + 1 }}</option>
                                            @endfor
                                        </select>
                                        @error('commitment')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="mb-1">
                                        <label class="form-label" for="sni_id">@lang('messages.sni') [@lang('messages.optional')]</label>
                                        <select id="sni_id" class="select2 form-select form-control @error('sni_id') is-invalid @enderror" name="sni_id">
                                            <option value="">@lang('messages.pleaseSelect')</option>
                                            @foreach ($snis as $sni)
                                                <option @selected(old('sni_id') == $sni->id) value="{{ $sni->id }}">{{ $sni->code . ' | ' . $sni->{'desc_' . App::currentLocale()} }}</option>
                                            @endforeach
                                        </select>
                                        @error('sni_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class='col-md-6 col-12'>
                                    <div class='mb-1'>
                                        <label class='form-label' for='organisation_id'>@lang('messages.organisation') [@lang('messages.optional')]</label>
                                        <select id='organisation_id' class='select2 form-select form-control @error('organisation_id') is-invalid @enderror' name='organisation_id'>
                                            <option value=''>@lang('messages.pleaseSelect')</option>
                                            @foreach ($organisations as $organisation)
                                                <option @selected(old('organisation_id') == $organisation->id) value='{{ $organisation->id }}'>{{ $organisation->name }}</option>
                                            @endforeach
                                        </select>
                                        <small class="text-muted"><i>@lang('messages.leaveThisBlankForTopOrg')</i></small>
                                        @error('organisation_id')
                                            <div class='invalid-feedback'>{{ $message }}</div>
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
@section('vendor-script')
    <!-- vendor files -->
    <script src="{{ asset(mix('vendors/js/forms/select/select2.full.min.js')) }}"></script>
@endsection
@section('page-script')
    <!-- Page js files -->
    <script src="{{ asset(mix('js/scripts/forms/form-select2.js')) }}"></script>
    <script type="text/javascript" src="{{ asset('vendor/jsvalidation/js/jsvalidation.js') }}"></script>
    {!! JsValidator::formRequest('App\Http\Requests\OrganisationStoreRequest', '#form') !!}
@endsection
