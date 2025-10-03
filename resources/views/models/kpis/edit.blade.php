@extends('layouts/contentLayoutMaster')
@section('title', trans('messages.kpi') . ' ' . trans('messages.edit'))
@section('content')
    <section id="multiple-column-form">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">@lang('messages.kpi') @lang('messages.edit') @lang('messages.allFields')</h4>
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
                        <form id="form" class="form" action="{{ route('kpis.update', [App::currentLocale(), $kpi->id]) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <div class="row">
                                <div class="col-md-6 col-12">
                                    <div class="mb-1">
                                        <label class="form-label" for="name_en">@lang('messages.name_en')</label>
                                        <input type="text" id="name_en" class="form-control @error('name_en') is-invalid @enderror" placeholder="Number of treatments" name="name_en" value="{{ $kpi->name_en }}" />
                                        @error('name_en')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="mb-1">
                                        <label class="form-label" for="name_se">@lang('messages.name_se')</label>
                                        <input type="text" id="name_se" class="form-control @error('name_se') is-invalid @enderror" placeholder="Antal behandlingar" name="name_se" value="{{ $kpi->name_se }}" />
                                        @error('name_se')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="mb-1">
                                        <label class="form-label" for="desc_en">@lang('messages.desc_en')</label>
                                        <textarea id="desc_en" class="form-control @error('desc_en') is-invalid @enderror" rows="3" placeholder="The total number of treatments available in the records of treatments." name="desc_en">{{ $kpi->desc_en }}</textarea>
                                        @error('desc_en')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="mb-1">
                                        <label class="form-label" for="desc_se">@lang('messages.desc_se')</label>
                                        <textarea id="desc_se" class="form-control @error('desc_se') is-invalid @enderror" rows="3" placeholder="Totalt antal behandlingar som finns i register över behandlingar." name="desc_se">{{ $kpi->desc_se }}</textarea>
                                        @error('desc_se')
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
    {!! JsValidator::formRequest('App\Http\Requests\KpiUpdateRequest', '#form') !!}
@endsection
