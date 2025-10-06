@extends('layouts/contentLayoutMaster')
@section('title', trans('messages.sanction') . ' ' . trans('messages.edit'))
@section('vendor-style')
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/pickers/flatpickr/flatpickr.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/forms/select/select2.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/editors/quill/katex.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/editors/quill/monokai-sublime.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/editors/quill/quill.snow.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/editors/quill/quill.bubble.css')) }}">
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link
        href="https://fonts.googleapis.com/css2?family=Inconsolata&family=Roboto+Slab&family=Slabo+27px&family=Sofia&family=Ubuntu+Mono&display=swap"
        rel="stylesheet">
@endsection
@section('page-style')
    <link rel="stylesheet" href="{{ asset(mix('css/base/plugins/forms/pickers/form-flat-pickr.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('css/base/plugins/forms/form-quill-editor.css')) }}">
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
                        <form id="form" class="form"
                              action="{{ route('sanctions.update', [App::currentLocale(), $sanction->id]) }}"
                              method="POST">
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
                                                <img
                                                    src="{{ asset('/images/flags/svg/' . $sanction->dpa->country->code . '.svg') }}"
                                                    width="40px"/>
                                            @endif
                                        </div>
                                        <div class="col-5 align-items-center">
                                            <p class="mx-0 my-0">{{ $sanction->dpa->name }}</p>
                                        </div>
                                    </div>

                                </div>
                                <div class="col-md-6">
                                    <div class="mb-1">
                                        <label class="form-label" for="started_at">@lang('messages.startedOn')
                                            [@lang('messages.optional')]</label>
                                        <input type="text" name="started_at" id="started_at"
                                               class="form-control flatpickr-basic @error('started_at') is-invalid @enderror"
                                               placeholder="YYYY-MM-DD" value="{{ $sanction->started_at }}"/>
                                        @error('started_at')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-1">
                                        <label class="form-label"
                                               for="started_at_parsed">@lang('messages.decidedOn') @lang('messages.apiParsed')</label>
                                        <input type="text" id="started_at_parsed"
                                               class="form-control @error('started_at_parsed') is-invalid @enderror"
                                               value="{{ $sanction->htmlClean()['started_at'] }}" disabled/>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-1">
                                        <label class="form-label" for="decided_at">@lang('messages.decidedOn')
                                            [@lang('messages.optional')]</label>
                                        <input type="text" name="decided_at" id="decided_at"
                                               class="form-control flatpickr-basic @error('decided_at') is-invalid @enderror"
                                               placeholder="YYYY-MM-DD" value="{{ $sanction->decided_at }}"/>
                                        @error('decided_at')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-1">
                                        <label class="form-label"
                                               for="decided_at_parsed">@lang('messages.decidedOn') @lang('messages.apiParsed')</label>
                                        <input type="text" id="decided_at_parsed"
                                               class="form-control @error('decided_at_parsed') is-invalid @enderror"
                                               value="{{ $sanction->htmlClean()['decided_at'] }}" disabled/>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-1">
                                        <label class="form-label" for="published_at">@lang('messages.publishedOn')
                                            [@lang('messages.optional')]</label>
                                        <input type="text" name="published_at" id="published_at"
                                               class="form-control flatpickr-basic @error('published_at') is-invalid @enderror"
                                               placeholder="YYYY-MM-DD" value="{{ $sanction->published_at }}"/>
                                        @error('published_at')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-1">
                                        <label class="form-label"
                                               for="published_at_parsed">@lang('messages.decidedOn') @lang('messages.apiParsed')</label>
                                        <input type="text" id="published_at_parsed"
                                               class="form-control @error('published_at_parsed') is-invalid @enderror"
                                               value="{{ $sanction->htmlClean()['published_at'] }}" disabled/>
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="mb-1">
                                        <label class="form-label" for="fine">@lang('messages.fine')
                                            [@lang('messages.optional')]</label>
                                        <input type="text" id="fine"
                                               class="form-control @error('fine') is-invalid @enderror"
                                               placeholder="3800" name="fine" value="{{ $sanction->fine }}"/>
                                        @error('fine')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-1">
                                        <label class="form-label"
                                               for="fine_parsed">@lang('messages.fine') @lang('messages.apiParsed')</label>
                                        <input type="text" id="fine_parsed"
                                               class="form-control @error('fine_parsed') is-invalid @enderror"
                                               value="{{ $sanction->htmlClean()['fine'] }}" disabled/>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-1">
                                        <label class="form-label" for="currency_id">@lang('messages.currency')
                                            [@lang('messages.optional')]</label>
                                        <select id="currency_id"
                                                class="select2 form-select form-control @error('currency_id') is-invalid @enderror"
                                                name="currency_id">
                                            <option value="">@lang('messages.pleaseSelect')</option>
                                            @foreach ($currencies as $currency)
                                                <option
                                                    @selected($sanction->currency_id == $currency->id) value="{{ $currency->id }}">{{ $currency->symbol }}</option>
                                            @endforeach
                                        </select>
                                        @error('currency_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-1">
                                        <label class="form-label"
                                               for="currency_parsed">@lang('messages.currency') @lang('messages.apiParsed')</label>
                                        <input type="text" id="currency_parsed"
                                               class="form-control @error('currency_parsed') is-invalid @enderror"
                                               value="{{ $sanction->htmlClean()['currency'] }}" disabled/>
                                    </div>
                                </div>
                                <div class='col-md-6'>
                                    <div class="mb-1">
                                        <label class='form-label' for='articles[]'>@lang('messages.articles')
                                            [@lang('messages.optional')]</label>
                                        <select id='articles[]'
                                                class='select2 form-select form-control @error('articles[]') is-invalid @enderror'
                                                name='articles[]' multiple>
                                            @foreach ($articles as $article)
                                                <option value="{{ $article->id }}"
                                                        @if (in_array($article->id, $sanctionArticlesIds)) selected @endif>{{ $article->title }}</option>
                                            @endforeach
                                        </select>
                                        @error('articles[]')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class='col-md-6 articles-parsed'>
                                    <div class="mb-1">
                                        <label class='form-label'
                                               for='articles[]'>@lang('messages.articles') @lang('messages.apiParsed')</label>
                                        <select id='articles_parsed' class='select2 form-select form-control' multiple
                                                disabled>
                                            @foreach ($sanction->htmlClean()['articles'] as $key => $articleParsed)
                                                <option selected>{{ $articleParsed }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="mb-1">
                                        <label class="form-label" for="sni_id">@lang('messages.sni')
                                            [@lang('messages.optional')]</label>
                                        <select id="sni_id"
                                                class="select2 form-select form-control @error('sni_id') is-invalid @enderror"
                                                name="sni_id">
                                            <option value="">@lang('messages.pleaseSelect')</option>
                                            @foreach ($snis as $sni)
                                                <option
                                                    @selected($sanction->sni?->id == $sni->id) value="{{ $sni->id }}">{{ $sni->code.' | '.$sni->{'desc_' . App::currentLocale()} }}</option>
                                            @endforeach
                                        </select>
                                        @error('sni_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="mb-1">
                                        <label class="form-label" for="type">@lang('messages.type')
                                            [@lang('messages.optional')]</label>
                                        <select id="type"
                                                class="select2 form-select form-control @error('type') is-invalid @enderror"
                                                name="type_id">
                                            <option value="">@lang('messages.pleaseSelect')</option>
                                            @foreach ($types as $type)
                                                <option
                                                    @selected($sanction->type?->id == $type->id) value="{{ $type->id }}">{{ $type->{'text_' . App::currentLocale()} }}</option>
                                            @endforeach
                                        </select>
                                        @error('type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="mb-1">
                                        <label class="form-label" for="outcome">@lang('messages.outcome')
                                            [@lang('messages.optional')]</label>
                                        <select id="outcome"
                                                class="select2 form-select form-control @error('outcome') is-invalid @enderror"
                                                name="outcome_id">
                                            <option value="">@lang('messages.pleaseSelect')</option>
                                            @foreach ($outcomes as $outcome)
                                                <option
                                                    @selected($sanction->outcome?->id == $outcome->id) value="{{ $outcome->id }}">{{ $outcome->{'desc_' . App::currentLocale()} }}</option>
                                            @endforeach
                                        </select>
                                        @error('outcome')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="mb-1">
                                        <label class="form-label" for="party">@lang('messages.party')
                                            [@lang('messages.optional')]</label>
                                        <input type="text" id="party"
                                               class="form-control @error('party') is-invalid @enderror"
                                               placeholder="Rights International Spain (RIS)" name="party"
                                               value="{{ $sanction->party }}"/>
                                        @error('party')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="mb-1">
                                        <label class="form-label" for="issue-category">@lang('messages.issue_category')
                                            [@lang('messages.optional')]</label>
                                        <select id="issue-category"
                                                class="select2 form-select form-control @error('issue_category') is-invalid @enderror"
                                                name="issue_category_id">
                                            <option value="">@lang('messages.pleaseSelect')</option>
                                            @foreach ($issueCategories as $issueCategory)
                                                <option
                                                    @selected($sanction->issue_category?->id == $issueCategory->id) value="{{ $issueCategory->id }}">{{ $issueCategory->{'desc_' . App::currentLocale()} }}</option>
                                            @endforeach
                                        </select>
                                        @error('issue_category')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class='col-md-6'>
                                    <div class="mb-1">
                                        <label class='form-label' for="tags">@lang('messages.tags')
                                            [@lang('messages.optional')]</label>
                                        <select id="tags"
                                                class="select2 form-select form-control @error('tags[]') is-invalid @enderror"
                                                name="tags[]" multiple
                                                data-placeholder="@lang('messages.pleaseSelect')">
                                            @foreach ($tags as $tag)
                                                <option
                                                    @selected(in_array($tag->id, $tagIds)) value="{{ $tag->id }}">{{ $tag->{'tag_' . App::currentLocale()} }}</option>
                                            @endforeach
                                        </select>
                                        @error('tags[]')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class='col-md-6'>
                                    <div class="mb-1">
                                        <label class='form-label' for="statements">@lang('messages.statements')
                                            [@lang('messages.optional')]</label>
                                        <select id="statements"
                                                class="select2 form-select form-control @error('statements[]') is-invalid @enderror"
                                                name="statements[]" multiple
                                                data-placeholder="@lang('messages.pleaseSelect')">
                                            @foreach ($statements as $statement)
                                                <option
                                                    @selected(in_array($statement->id, $statementIds)) value="{{ $statement->id }}">{{ $statement->component->code . '.' . $statement->{'code'} }}</option>
                                            @endforeach
                                        </select>
                                        @error('statements[]')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="mb-1">
                                        <label class="form-label" for="et-id">ET-ID [@lang('messages.optional')]</label>
                                        <input type="text" id="et-id"
                                               class="form-control @error('etid') is-invalid @enderror"
                                               placeholder="1234" name="etid" value="{{ $sanction->etid }}"/>
                                        @error('etid')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="mb-1">
                                        <label class="form-label" for="source">@lang('messages.source')
                                            [@lang('messages.optional')]</label>
                                        <input type="text" id="source"
                                               class="form-control @error('source') is-invalid @enderror"
                                               placeholder="https://www.dvi.gov.lv/lv/lemumi" name="source"
                                               value="{{ $sanction->source }}"/>
                                        @error('source')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <quill locale="{{ App::currentLocale() }}"
                                       label-en="{{ __('messages.desc') . ' ' . __('messages.inEnglish') . ' [' . __('messages.optional') . ']' }}"
                                       label-se="{{ __('messages.desc') . ' ' . __('messages.inSwedish') . ' [' . __('messages.optional') . ']' }}"
                                       :old-desc-en="{{ Js::from($sanction->desc_en) }}"
                                       :old-desc-se="{{ Js::from($sanction->desc_se) }}"></quill>
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
    <script src="{{ asset(mix('vendors/js/editors/quill/katex.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/editors/quill/highlight.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/editors/quill/quill.min.js')) }}"></script>
@endsection
@section('page-script')
    <!-- Page js files -->
    <script src="{{ asset(mix('js/models/sanctions/edit/app.js')) }}"></script>
    <script src="{{ asset(mix('js/scripts/forms/form-select2.js')) }}"></script>
    <script type="text/javascript" src="{{ asset('vendor/jsvalidation/js/jsvalidation.js') }}"></script>
    {!! JsValidator::formRequest('App\Http\Requests\SanctionUpdateRequest', '#form') !!}
    <script src="{{ asset(mix('js/scripts/forms/pickers/form-pickers.js')) }}"></script>
    <script>
        window.onload = (event) => {
            $(".articles-parsed .mb-1 .position-relative .select2 .selection .select2-selection--multiple .select2-selection__rendered .select2-selection__choice").attr('style', 'background-color: wheat !important; border-color: wheat !important;');
        };
    </script>

@endsection
