@extends('layouts/contentLayoutMaster')
@section('title', $title)
@section('vendor-style')
    {{-- vendor css files --}}
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/forms/select/select2.min.css')) }}">
@endsection
@section('page-style')
    <style>
        body {
            overflow-x: hidden;
        }
    </style>
@endsection
@section('content')
    <section id="multiple-column-form">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">@lang('messages.groups') {{ $action_msg }} @lang('messages.allFields')</h4>
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
                                        <label class="form-label" for="desc-en">@lang('messages.desc_en')</label>
                                        <input type="text" id="desc-en"
                                               class="form-control @error('desc_en') is-invalid @enderror"
                                               placeholder="Big 5" name="desc_en"
                                               value="{{ ($is_update ?? false) ? old('desc_en') ?? $group->desc_en : old('desc_en') }}"/>
                                        @error('desc_en')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="mb-1">
                                        <label class="form-label" for="desc-se">@lang('messages.desc_se')</label>
                                        <input type="text" id="desc-se"
                                               class="form-control @error('desc_se') is-invalid @enderror"
                                               placeholder="Stort 5" name="desc_se"
                                               value="{{ ($is_update ?? false) ? old('desc_se') ?? $group->desc_se : old('desc_se') }}"/>
                                        @error('desc_se')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class='col-md-6'>
                                    <div class="mb-1">
                                        <label class='form-label' for="countries">@lang('messages.countries')</label>
                                        <select id="countries"
                                                class="select2 form-select form-control @error('countries[]') is-invalid @enderror"
                                                name="countries[]" multiple
                                                data-placeholder="@lang('messages.pleaseSelect')">
                                            @foreach ($countries as $country)
                                                <option
                                                    @selected(($is_update ?? false) && in_array($country->id, $country_ids)) value="{{ $country->id }}">{{ $country->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('countries[]')
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
    {{-- vendor files --}}
    <script src="{{ asset(mix('vendors/js/forms/select/select2.full.min.js')) }}"></script>
@endsection
@section('page-script')
    <!-- Page js files -->
    <script>
        window.addEventListener('DOMContentLoaded', (event) => {
            $('.select2').select2();
        });
    </script>
    <script type="text/javascript" src="{{ asset('vendor/jsvalidation/js/jsvalidation.js') }}"></script>
    @if ($is_update ?? false)
        {!! JsValidator::formRequest('App\Http\Requests\GroupUpdateRequest', '#form') !!}
    @else
        {!! JsValidator::formRequest('App\Http\Requests\GroupStoreRequest', '#form') !!}
    @endif
@endsection
