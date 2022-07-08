@extends('layouts/contentLayoutMaster')

@section('title', trans('messages.componentsCreate'))

@section('content')
    <!-- Basic multiple Column Form section start -->
    <section id="multiple-column-form">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">@lang('messages.componentsCreate') @lang('messages.allFields')</h4>
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
                        <form id="form" class="form" action="{{ route('components.store', App::currentLocale()) }}" method="POST">
                            @csrf
                            <div class="row">
                                <div class="col-md-6 col-12">
                                    <div class="mb-1">
                                        <label class="form-label" for="name_en">@lang('messages.nameInEnglish')</label>
                                        <input type="text" id="name_en" class="form-control @error('name_en') is-invalid @enderror" placeholder="Lawfulness of personal data processing" name="name_en" value="{{ old('name_en') }}" />
                                        @error('name_en')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="mb-1">
                                        <label class="form-label" for="name_se">@lang('messages.nameInSwedish')</label>
                                        <input type="text" id="name_se" class="form-control @error('name_se') is-invalid @enderror" placeholder="Laglighet vid behandling av personuppgifter" name="name_se" value="{{ old('name_se') }}" />
                                        @error('name_se')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="mb-1">
                                        <label class="form-label" for="desc_en">@lang('messages.descInEnglish')</label>
                                        <textarea id="desc_en" class="form-control @error('desc_en') is-invalid @enderror" rows="3" placeholder="Article 6. Consent (as a legal basis), performance of contracts, performance of legal obligation, protection of fundamental interests, public interest task, exercise of authority, legitimate interests (balancing of interests). Article 9." name="desc_en">{{ old('desc_en') }}</textarea>
                                        @error('desc_en')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="mb-1">
                                        <label class="form-label" for="desc_se">@lang('messages.descInSwedish')</label>
                                        <textarea id="desc_se" class="form-control @error('desc_se') is-invalid @enderror" rows="3" placeholder="Artikel 6. Samtycke (som rättslig grund), fullgörande av avtal, fullgörande av rättslig förpliktelse, skydd av grundläggande intressen, uppgift av allmänt intresse, myndighetsutövning, legitima intressen (intresseavvägning). Artikel 9." name="desc_se">{{ old('desc_se') }}</textarea>
                                        @error('desc_se')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="mb-1">
                                        <label class="form-label" for="code">@lang('messages.code')</label>
                                        <input type="text" id="code" class="form-control @error('code') is-invalid @enderror" placeholder="P1" name="code" value="{{ old('code') }}" />
                                        @error('code')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="mb-1">
                                        <label class="form-label" for="sort_order">@lang('messages.sortOrder')</label>
                                        <input type="text" id="sort_order" class="form-control @error('sort_order') is-invalid @enderror" placeholder="{{ $sort_order_placeholder }}" name="sort_order" value="{{ old('sort_order') }}" />
                                        @error('sort_order')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="mb-1">
                                        <label class="form-label" for="period_id">@lang('messages.period')</label>
                                        <select id="period_id" class="form-select @error('sort_order') is-invalid @enderror" name="period_id">
                                            @foreach ($periods as $period)
                                                <option value="{{ $period->id }}">{{ $period->{'name_' . App::currentLocale()} }}</option>
                                            @endforeach
                                        </select>
                                        @error('period_id')
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
    {!! JsValidator::formRequest('App\Http\Requests\ComponentStoreRequest', '#form') !!}
@endsection
