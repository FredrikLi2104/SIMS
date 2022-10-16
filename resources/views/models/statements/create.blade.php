@extends('layouts/contentLayoutMaster')

@section('title', trans('messages.statementCreate'))
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
                        <h4 class="card-title">@lang('messages.statementCreate') @lang('messages.allFields')</h4>
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
                        <form id="form" class="form" action="{{ route('statements.store', App::currentLocale()) }}" method="POST">
                            @csrf
                            <div class="row">
                                <div class="col-md-6 col-12">
                                    <div class="mb-1">
                                        <label class="form-label" for="content_en">@lang('messages.content_en')</label>
                                        <textarea id="content_en" class="form-control @error('content_en') is-invalid @enderror" rows="3" placeholder="We have defined a lawful basis for all processes." name="content_en">{{ old('content_en') }}</textarea>
                                        @error('content_en')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="mb-1">
                                        <label class="form-label" for="content_se">@lang('messages.content_se')</label>
                                        <textarea id="content_se" class="form-control @error('content_se') is-invalid @enderror" rows="3" placeholder="Vi har definierat en laglig grund för alla processer." name="content_se">{{ old('content_se') }}</textarea>
                                        @error('content_se')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="mb-1">
                                        <label class="form-label" for="desc_en">@lang('messages.desc_en')</label>
                                        <textarea id="desc_en" class="form-control @error('desc_en') is-invalid @enderror" rows="3" placeholder="It means that all personal data process need a specific legal support according to DGPR. Without a legal support the process is unlawful. De lawful basis are regulated in art. 6 GDPR and for sensitive personal data, one extra lawful basis according to art. 9 GDPR is needed. " name="desc_en">{{ old('desc_en') }}</textarea>
                                        @error('desc_en')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="mb-1">
                                        <label class="form-label" for="desc_se">@lang('messages.desc_se')</label>
                                        <textarea id="desc_se" class="form-control @error('desc_se') is-invalid @enderror" rows="3" placeholder="Det innebär att alla personuppgifter behöver ett specifikt juridiskt stöd enligt DGPR. Utan juridiskt stöd är processen olaglig. De rättsliga grunderna regleras i artikel 6 GDPR och för känsliga personuppgifter krävs en extra laglig grund enligt artikel 9 GDPR." name="desc_se">{{ old('desc_se') }}</textarea>
                                        @error('desc_se')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="mb-1">
                                        <label class="form-label" for="k1_en">k1 @lang('messages.inEnglish') [@lang('messages.optional')]</label>
                                        <textarea id="k1_en" class="form-control @error('k1_en') is-invalid @enderror" rows="3" placeholder="We have a strategy or action plan, or we have regulated it by agreement" name="k1_en">{{ old('k1_en') }}</textarea>
                                        @error('k1_en')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="mb-1">
                                        <label class="form-label" for="k1_se">k1 @lang('messages.inSwedish') [@lang('messages.optional')]</label>
                                        <textarea id="k1_se" class="form-control @error('k1_se') is-invalid @enderror" rows="3" placeholder="Vi har en strategi eller handlingsplan, alternativt reglerat det genom avtal" name="k1_se">{{ old('k1_se') }}</textarea>
                                        @error('k1_se')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="mb-1">
                                        <label class="form-label" for="k2_en">k2 @lang('messages.inEnglish') [@lang('messages.optional')]</label>
                                        <textarea id="k2_en" class="form-control @error('k2_en') is-invalid @enderror" rows="3" placeholder="We have a strategy or action plan, or we have regulated it by agreement" name="k2_en">{{ old('k2_en') }}</textarea>
                                        @error('k2_en')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="mb-1">
                                        <label class="form-label" for="k2_se">k2 @lang('messages.inSwedish') [@lang('messages.optional')]</label>
                                        <textarea id="k2_se" class="form-control @error('k2_se') is-invalid @enderror" rows="3" placeholder="Vi har en strategi eller handlingsplan, alternativt reglerat det genom avtal" name="k2_se">{{ old('k2_se') }}</textarea>
                                        @error('k2_se')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="mb-1">
                                        <label class="form-label" for="k3_en">k3 @lang('messages.inEnglish') [@lang('messages.optional')]</label>
                                        <textarea id="k3_en" class="form-control @error('k3_en') is-invalid @enderror" rows="3" placeholder="We have a strategy or action plan, or we have regulated it by agreement" name="k3_en">{{ old('k3_en') }}</textarea>
                                        @error('k3_en')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="mb-1">
                                        <label class="form-label" for="k3_se">k3 @lang('messages.inSwedish') [@lang('messages.optional')]</label>
                                        <textarea id="k3_se" class="form-control @error('k3_se') is-invalid @enderror" rows="3" placeholder="Vi har en strategi eller handlingsplan, alternativt reglerat det genom avtal" name="k3_se">{{ old('k3_se') }}</textarea>
                                        @error('k3_se')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="mb-1">
                                        <label class="form-label" for="k4_en">k4 @lang('messages.inEnglish') [@lang('messages.optional')]</label>
                                        <textarea id="k4_en" class="form-control @error('k4_en') is-invalid @enderror" rows="3" placeholder="We have a strategy or action plan, or we have regulated it by agreement" name="k4_en">{{ old('k4_en') }}</textarea>
                                        @error('k4_en')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="mb-1">
                                        <label class="form-label" for="k4_se">k4 @lang('messages.inSwedish') [@lang('messages.optional')]</label>
                                        <textarea id="k4_se" class="form-control @error('k4_se') is-invalid @enderror" rows="3" placeholder="Vi har en strategi eller handlingsplan, alternativt reglerat det genom avtal" name="k4_se">{{ old('k4_se') }}</textarea>
                                        @error('k4_se')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="mb-1">
                                        <label class="form-label" for="k5_en">k5 @lang('messages.inEnglish') [@lang('messages.optional')]</label>
                                        <textarea id="k5_en" class="form-control @error('k5_en') is-invalid @enderror" rows="3" placeholder="We have a strategy or action plan, or we have regulated it by agreement" name="k5_en">{{ old('k5_en') }}</textarea>
                                        @error('k5_en')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="mb-1">
                                        <label class="form-label" for="k5_se">k5 @lang('messages.inSwedish') [@lang('messages.optional')]</label>
                                        <textarea id="k5_se" class="form-control @error('k5_se') is-invalid @enderror" rows="3" placeholder="Vi har en strategi eller handlingsplan, alternativt reglerat det genom avtal" name="k5_se">{{ old('k5_se') }}</textarea>
                                        @error('k5_se')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="mb-1">
                                        <label class="form-label" for="implementation_en">@lang('messages.implementation') @lang('messages.inEnglish')</label>
                                        <textarea id="implementation_en" class="form-control @error('implementation_en') is-invalid @enderror" rows="3" placeholder="Show the user (DPO) how to implement a plan for this statement" name="implementation_en">{{ old('implementation_en') }}</textarea>
                                        @error('implementation_en')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="mb-1">
                                        <label class="form-label" for="implementation_se">@lang('messages.implementation') @lang('messages.inSwedish')</label>
                                        <textarea id="implementation_se" class="form-control @error('implementation_se') is-invalid @enderror" rows="3" placeholder="Visa användaren (DPO) hur man implementerar en plan för denna programsats" name="implementation_se">{{ old('implementation_se') }}</textarea>
                                        @error('implementation_se')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="mb-1">
                                        <label class="form-label" for="guide_en">@lang('messages.guide_en')</label>
                                        <textarea id="guide_en" class="form-control @error('guide_en') is-invalid @enderror" rows="3" placeholder="Guide the auditor (DSO) how to review this statement" name="guide_en">{{ old('guide_en') }}</textarea>
                                        @error('guide_en')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="mb-1">
                                        <label class="form-label" for="guide_se">@lang('messages.guide_se')</label>
                                        <textarea id="guide_se" class="form-control @error('guide_se') is-invalid @enderror" rows="3" placeholder="Vägleda revisorn (DSO) hur man granskar detta uttalande" name="guide_se">{{ old('guide_se') }}</textarea>
                                        @error('guide_se')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="mb-1">
                                        <label class="form-label" for="component_id">@lang('messages.component')</label>
                                        <select id="component_id" class="select2 form-select form-control @error('component_id') is-invalid @enderror" name="component_id">
                                            <option value="">@lang('messages.pleaseSelect')</option>
                                            @foreach ($components as $component)
                                                <option @selected(old('component_id') == $component->id) value="{{ $component->id }}">{{ $component->code . ' | ' . $component->{'name_' . App::currentLocale()} }}</option>
                                            @endforeach
                                        </select>
                                        @error('component_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="mb-1">
                                        <label class="form-label" for="code">@lang('messages.code')</label>
                                        <input type="text" id="code" class="form-control @error('code') is-invalid @enderror" placeholder="1" name="code" value="{{ old('code') }}" />
                                        @error('code')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="mb-1">
                                        <label class="form-label" for="statement_type_id">@lang('messages.statementType')</label>
                                        <select id="statement_type_id" class="select2 form-select form-control @error('statement_type_id') is-invalid @enderror" name="statement_type_id">
                                            <option value="">@lang('messages.pleaseSelect')</option>
                                            @foreach ($statementTypes as $statementType)
                                                <option @selected(old('statement_type_id') == $statementType->id) value="{{ $statementType->id }}">{{ $statementType->code . ' | ' . $statementType->{'desc_' . App::currentLocale()} }}</option>
                                            @endforeach
                                        </select>
                                        @error('statement_type_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="mb-1">
                                        <label class="form-label" for="sort_order">@lang('messages.sort_order')</label>
                                        <input type="text" id="sort_order" class="form-control @error('sort_order') is-invalid @enderror" placeholder="{{ $sort_order_placeholder }}" name="sort_order" value="{{ old('sort_order') }}" />
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
@section('vendor-script')
    <!-- vendor files -->
    <script src="{{ asset(mix('vendors/js/forms/select/select2.full.min.js')) }}"></script>
@endsection
@section('page-script')
    <!-- Page js files -->
    <script src="{{ asset(mix('js/scripts/forms/form-select2.js')) }}"></script>
    <script type="text/javascript" src="{{ asset('vendor/jsvalidation/js/jsvalidation.js') }}"></script>
    {!! JsValidator::formRequest('App\Http\Requests\StatementStoreRequest', '#form') !!}
@endsection
