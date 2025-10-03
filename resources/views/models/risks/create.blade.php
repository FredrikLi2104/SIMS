@extends('layouts/contentLayoutMaster')
@section('title', trans('messages.risk') . ' ' . trans('messages.create'))
@section('vendor-style')
    <!-- vendor css files -->
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/forms/select/select2.min.css')) }}">
@endsection
@section('content')
    <section id="multiple-column-form">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">@lang('messages.risk') @lang('messages.create') @lang('messages.allFields')</h4>
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
                        <form id="form" class="form" action="{{ route('risks.store', App::currentLocale()) }}" method="POST">
                            @csrf
                            <risk-matrix locale="{{ App::currentLocale() }}"></risk-matrix>
                            <div class="row">
                                <div class="col-md-6 col-12">
                                    <div class="mb-1">
                                        <label class="form-label" for="title">@lang('messages.title')</label>
                                        <input type="text" id="title" class="form-control @error('title') is-invalid @enderror" placeholder="Hemsidor" name="title" value="{{ old('title') }}" />
                                        @error('title')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="mb-1">
                                        <label class="form-label" for="desc">@lang('messages.desc')</label>
                                        <input type="text" id="desc" class="form-control @error('desc') is-invalid @enderror" placeholder="Hemsidor är inte uppdaterade enligt GDPR, direkt risk för sanktioner" name="desc" value="{{ old('desc') }}" />
                                        @error('desc')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="mb-1">
                                        <label class="form-label" for="responsible">@lang('messages.responsibilityOf') [@lang('messages.optional')]</label>
                                        <input type="text" id="responsible" class="form-control @error('responsible') is-invalid @enderror" placeholder="John Smith" name="responsible" value="{{ old('responsible') }}" />
                                        @error('responsible')
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
                                                <option @selected(old('component_id') == $component->id) value="{{ $component->id }}">{{ $component->code_name }}</option>
                                            @endforeach
                                        </select>
                                        @error('component_id')
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
@section('vendor-script')
    <!-- vendor files -->
    <script src="{{ asset(mix('vendors/js/forms/select/select2.full.min.js')) }}"></script>
@endsection
@section('page-script')
    <!-- Page js files -->
    <script src="{{ asset(mix('js/models/risks/create/app.js')) }}"></script>
    <script src="{{ asset(mix('js/scripts/forms/form-select2.js')) }}"></script>
    <script type="text/javascript" src="{{ asset('vendor/jsvalidation/js/jsvalidation.js') }}"></script>
    {!! JsValidator::formRequest('App\Http\Requests\RiskStoreRequest', '#form') !!}

@endsection
