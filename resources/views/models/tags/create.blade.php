@extends('layouts/contentLayoutMaster')
@section('title', $title)
@section('content')
    <section id="multiple-column-form">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">@lang('messages.tags') {{ $action_msg }} @lang('messages.allFields')</h4>
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
                                        <label class="form-label"
                                               for="tag-en">@lang('messages.tag') @lang('messages.inEnglish')</label>
                                        <input type="text" id="tag-en"
                                               class="form-control @error('tag_en') is-invalid @enderror"
                                               placeholder="Verification" name="tag_en"
                                               value="{{ ($is_update ?? false) ? old('tag_en') ?? $tag->tag_en : old('tag_en') }}"/>
                                        @error('tag_en')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="mb-1">
                                        <label class="form-label" for="desc-en">@lang('messages.desc_en')</label>
                                        <input type="text" id="desc-en"
                                               class="form-control @error('desc_en') is-invalid @enderror"
                                               placeholder="Identity verification" name="desc_en"
                                               value="{{ ($is_update ?? false) ? old('desc_en') ?? $tag->desc_en : old('desc_en') }}"/>
                                        @error('desc_en')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="mb-1">
                                        <label class="form-label"
                                               for="tag-se">@lang('messages.tag') @lang('messages.inSwedish')</label>
                                        <input type="text" id="tag-se"
                                               class="form-control @error('tag_se') is-invalid @enderror"
                                               placeholder="Verifikation" name="tag_se"
                                               value="{{ ($is_update ?? false) ? old('tag_se') ?? $tag->tag_se : old('tag_se') }}"/>
                                        @error('tag_se')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="mb-1">
                                        <label class="form-label" for="desc-se">@lang('messages.desc_se')</label>
                                        <input type="text" id="desc-se"
                                               class="form-control @error('desc_se') is-invalid @enderror"
                                               placeholder="Verifiera identitet" name="desc_se"
                                               value="{{ ($is_update ?? false) ? old('desc_se') ?? $tag->desc_se : old('desc_se') }}"/>
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
    @if ($is_update ?? false)
        {!! JsValidator::formRequest('App\Http\Requests\TagUpdateRequest', '#form') !!}
    @else
        {!! JsValidator::formRequest('App\Http\Requests\TagStoreRequest', '#form') !!}
    @endif
@endsection
