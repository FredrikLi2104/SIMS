@extends('layouts/contentLayoutMaster')
@section('title', trans('messages.actionType') . ' ' . trans('messages.edit'))
@section('content')
    <section id="multiple-column-form">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">@lang('messages.actionType') @lang('messages.edit') @lang('messages.allFields')</h4>
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
                        <form id="form" class="form" action="{{ route('action_types.update', [App::currentLocale(), $actionType]) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <div class="row">
                                <div class="col-md-6 col-12">
                                    <div class="mb-1">
                                        <label class="form-label" for="name_en">@lang('messages.name_en')</label>
                                        <input type="text" id="name_en" class="form-control @error('name_en') is-invalid @enderror" placeholder="Exam" name="name_en" value="{{ $actionType->name_en }}" />
                                        @error('name_en')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="mb-1">
                                        <label class="form-label" for="name_se">@lang('messages.name_se')</label>
                                        <input type="text" id="name_se" class="form-control @error('name_se') is-invalid @enderror" placeholder="Undersökning" name="name_se" value="{{ $actionType->name_se }}" />
                                        @error('name_se')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="mb-1">
                                        <label class="form-label" for="desc_en">@lang('messages.desc_en')</label>
                                        <input type="text" id="desc_en" class="form-control @error('desc_en') is-invalid @enderror" placeholder="The statement is reviewed by sending a question via email to the respondent" name="desc_en" value="{{ $actionType->desc_en }}" />
                                        @error('desc_en')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="mb-1">
                                        <label class="form-label" for="desc_se">@lang('messages.desc_se')</label>
                                        <input type="text" id="desc_se" class="form-control @error('desc_se') is-invalid @enderror" placeholder="Påstående granskas genom att skicka fråga via mail till respondent" name="desc_se" value="{{ $actionType->desc_se }}" />
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
    {!! JsValidator::formRequest('App\Http\Requests\ActionTypeUpdateRequest', '#form') !!}
@endsection
