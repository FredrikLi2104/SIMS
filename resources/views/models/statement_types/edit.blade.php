@extends('layouts/contentLayoutMaster')

@section('title', trans('messages.statementTypes') . ' ' . trans('messages.edit'))

@section('content')
    <!-- Basic multiple Column Form section start -->
    <section id="multiple-column-form">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">@lang('messages.statementTypes') @lang('messages.edit') @lang('messages.allFields')</h4>
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
                        <form id="form" class="form" action="{{ route('statement_types.update', [App::currentLocale(), $statement_type->id]) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <div class="row">
                                <div class="col-md-6 col-12">
                                    <div class="mb-1">
                                        <label class="form-label" for="code">@lang('messages.code')</label>
                                        <input type="text" id="code" class="form-control @error('code') is-invalid @enderror" placeholder="L" name="code" value="{{ $statement_type->code }}" />
                                        @error('code')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="mb-1">
                                        <label class="form-label" for="desc_en">@lang('messages.descInEnglish')</label>
                                        <textarea id="desc_en" class="form-control @error('desc_en') is-invalid @enderror" rows="3" placeholder="StatementType L: Legal, which means something and something" name="desc_en">{{ $statement_type->desc_en }}</textarea>
                                        @error('desc_en')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="mb-1">
                                        <label class="form-label" for="desc_se">@lang('messages.descInSwedish')</label>
                                        <textarea id="desc_se" class="form-control @error('desc_se') is-invalid @enderror" rows="3" placeholder="Uttalanden Typ L: Legal, vilket betyder något och något" name="desc_se">{{ $statement_type->desc_se }}</textarea>
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
    <!-- Basic Floating Label Form section end -->

@endsection
@section('page-script')
    <!-- Page js files -->
    <script type="text/javascript" src="{{ asset('vendor/jsvalidation/js/jsvalidation.js') }}"></script>
    {!! JsValidator::formRequest('App\Http\Requests\StatementTypeUpdateRequest', '#form') !!}
@endsection
