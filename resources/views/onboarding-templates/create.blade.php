@extends('layouts/contentLayoutMaster')

@section('title', app()->getLocale() === 'en' ? 'Create Onboarding Template' : 'Skapa Onboarding-mall')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">{{ app()->getLocale() === 'en' ? 'Create Onboarding Template' : 'Skapa Onboarding-mall' }}</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('onboarding-templates.store', ['locale' => app()->getLocale()]) }}" method="POST">
                    @csrf

                    <div class="row">
                        <!-- Name in English -->
                        <div class="col-md-6 col-12">
                            <div class="mb-1">
                                <label class="form-label" for="name_en">{{ app()->getLocale() === 'en' ? 'Name in English' : 'Namn på engelska' }} <span class="text-danger">*</span></label>
                                <input type="text" id="name_en" class="form-control @error('name_en') is-invalid @enderror"
                                       name="name_en" value="{{ old('name_en') }}" required>
                                @error('name_en')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Name in Swedish -->
                        <div class="col-md-6 col-12">
                            <div class="mb-1">
                                <label class="form-label" for="name_se">{{ app()->getLocale() === 'en' ? 'Name in Swedish' : 'Namn på svenska' }} <span class="text-danger">*</span></label>
                                <input type="text" id="name_se" class="form-control @error('name_se') is-invalid @enderror"
                                       name="name_se" value="{{ old('name_se') }}" required>
                                @error('name_se')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Summary in English -->
                        <div class="col-md-6 col-12">
                            <div class="mb-1">
                                <label class="form-label" for="summary_en">{{ app()->getLocale() === 'en' ? 'Summary in English' : 'Sammanfattning på engelska' }}</label>
                                <textarea id="summary_en" class="form-control @error('summary_en') is-invalid @enderror"
                                          name="summary_en" rows="3">{{ old('summary_en') }}</textarea>
                                @error('summary_en')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Summary in Swedish -->
                        <div class="col-md-6 col-12">
                            <div class="mb-1">
                                <label class="form-label" for="summary_se">{{ app()->getLocale() === 'en' ? 'Summary in Swedish' : 'Sammanfattning på svenska' }}</label>
                                <textarea id="summary_se" class="form-control @error('summary_se') is-invalid @enderror"
                                          name="summary_se" rows="3">{{ old('summary_se') }}</textarea>
                                @error('summary_se')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Organization Type -->
                        <div class="col-md-4 col-12">
                            <div class="mb-1">
                                <label class="form-label" for="organization_type">{{ app()->getLocale() === 'en' ? 'Organization Type' : 'Organisationstyp' }}</label>
                                <select id="organization_type" class="form-select @error('organization_type') is-invalid @enderror"
                                        name="organization_type">
                                    <option value="">{{ app()->getLocale() === 'en' ? 'Any' : 'Alla' }}</option>
                                    <option value="public" {{ old('organization_type') === 'public' ? 'selected' : '' }}>
                                        {{ app()->getLocale() === 'en' ? 'Public' : 'Offentlig' }}
                                    </option>
                                    <option value="private" {{ old('organization_type') === 'private' ? 'selected' : '' }}>
                                        {{ app()->getLocale() === 'en' ? 'Private' : 'Privat' }}
                                    </option>
                                    <option value="nonprofit" {{ old('organization_type') === 'nonprofit' ? 'selected' : '' }}>
                                        {{ app()->getLocale() === 'en' ? 'Non-profit' : 'Ideell' }}
                                    </option>
                                    <option value="other" {{ old('organization_type') === 'other' ? 'selected' : '' }}>
                                        {{ app()->getLocale() === 'en' ? 'Other' : 'Annat' }}
                                    </option>
                                </select>
                                @error('organization_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Organization Size -->
                        <div class="col-md-4 col-12">
                            <div class="mb-1">
                                <label class="form-label" for="organization_size">{{ app()->getLocale() === 'en' ? 'Organization Size' : 'Organisationsstorlek' }}</label>
                                <select id="organization_size" class="form-select @error('organization_size') is-invalid @enderror"
                                        name="organization_size">
                                    <option value="">{{ app()->getLocale() === 'en' ? 'Any' : 'Alla' }}</option>
                                    <option value="small" {{ old('organization_size') === 'small' ? 'selected' : '' }}>
                                        {{ app()->getLocale() === 'en' ? 'Small (1-50)' : 'Liten (1-50)' }}
                                    </option>
                                    <option value="medium" {{ old('organization_size') === 'medium' ? 'selected' : '' }}>
                                        {{ app()->getLocale() === 'en' ? 'Medium (51-250)' : 'Mellan (51-250)' }}
                                    </option>
                                    <option value="large" {{ old('organization_size') === 'large' ? 'selected' : '' }}>
                                        {{ app()->getLocale() === 'en' ? 'Large (250+)' : 'Stor (250+)' }}
                                    </option>
                                </select>
                                @error('organization_size')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Estimated Months -->
                        <div class="col-md-4 col-12">
                            <div class="mb-1">
                                <label class="form-label" for="estimated_months">{{ app()->getLocale() === 'en' ? 'Estimated Duration (months)' : 'Beräknad tid (månader)' }}</label>
                                <input type="number" id="estimated_months" class="form-control @error('estimated_months') is-invalid @enderror"
                                       name="estimated_months" value="{{ old('estimated_months') }}" min="1">
                                @error('estimated_months')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Requires Existing GDPR -->
                        <div class="col-md-6 col-12">
                            <div class="mb-1">
                                <label class="form-label">{{ app()->getLocale() === 'en' ? 'Requires existing GDPR work?' : 'Kräver befintligt GDPR-arbete?' }}</label>
                                <div class="form-check form-switch">
                                    <input type="hidden" name="requires_existing_gdpr" value="0">
                                    <input class="form-check-input" type="checkbox" id="requires_existing_gdpr"
                                           name="requires_existing_gdpr" value="1" {{ old('requires_existing_gdpr') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="requires_existing_gdpr">
                                        {{ app()->getLocale() === 'en' ? 'Yes' : 'Ja' }}
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Is Active -->
                        <div class="col-md-6 col-12">
                            <div class="mb-1">
                                <label class="form-label">{{ app()->getLocale() === 'en' ? 'Active?' : 'Aktiv?' }}</label>
                                <div class="form-check form-switch">
                                    <input type="hidden" name="is_active" value="0">
                                    <input class="form-check-input" type="checkbox" id="is_active"
                                           name="is_active" value="1" {{ old('is_active', 1) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">
                                        {{ app()->getLocale() === 'en' ? 'Yes' : 'Ja' }}
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Sort Order -->
                        <div class="col-md-6 col-12">
                            <div class="mb-1">
                                <label class="form-label" for="sort_order">{{ app()->getLocale() === 'en' ? 'Sort Order' : 'Sorteringsordning' }}</label>
                                <input type="number" id="sort_order" class="form-control @error('sort_order') is-invalid @enderror"
                                       name="sort_order" value="{{ old('sort_order', 0) }}" min="0">
                                @error('sort_order')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">{{ app()->getLocale() === 'en' ? 'Lower numbers appear first' : 'Lägre nummer visas först' }}</small>
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-info mt-2">
                        <div class="alert-body">
                            <i data-feather="info" class="me-50"></i>
                            {{ app()->getLocale() === 'en' ? 'After creating the template, you can add tasks to it from the templates list.' : 'Efter att du skapat mallen kan du lägga till uppgifter från mallistan.' }}
                        </div>
                    </div>

                    <div class="d-flex justify-content-between mt-2">
                        <a href="{{ route('onboarding-templates.index', ['locale' => app()->getLocale()]) }}" class="btn btn-outline-secondary">
                            <i data-feather="arrow-left" class="me-50"></i>
                            {{ app()->getLocale() === 'en' ? 'Cancel' : 'Avbryt' }}
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i data-feather="save" class="me-50"></i>
                            {{ app()->getLocale() === 'en' ? 'Create Template' : 'Skapa mall' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
