@extends('layouts/contentLayoutMaster')

@section('title', app()->getLocale() === 'en' ? 'Edit Onboarding Template' : 'Redigera Onboarding-mall')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">{{ app()->getLocale() === 'en' ? 'Edit Onboarding Template' : 'Redigera Onboarding-mall' }}</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('onboarding-templates.update', ['locale' => app()->getLocale(), 'onboarding_template' => $template->id]) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <!-- Name in English -->
                        <div class="col-md-6 col-12">
                            <div class="mb-1">
                                <label class="form-label" for="name_en">{{ app()->getLocale() === 'en' ? 'Name in English' : 'Namn på engelska' }} <span class="text-danger">*</span></label>
                                <input type="text" id="name_en" class="form-control @error('name_en') is-invalid @enderror"
                                       name="name_en" value="{{ old('name_en', $template->name_en) }}" required>
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
                                       name="name_se" value="{{ old('name_se', $template->name_se) }}" required>
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
                                          name="summary_en" rows="3">{{ old('summary_en', $template->summary_en) }}</textarea>
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
                                          name="summary_se" rows="3">{{ old('summary_se', $template->summary_se) }}</textarea>
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
                                    <option value="public" {{ old('organization_type', $template->organization_type) === 'public' ? 'selected' : '' }}>
                                        {{ app()->getLocale() === 'en' ? 'Public' : 'Offentlig' }}
                                    </option>
                                    <option value="private" {{ old('organization_type', $template->organization_type) === 'private' ? 'selected' : '' }}>
                                        {{ app()->getLocale() === 'en' ? 'Private' : 'Privat' }}
                                    </option>
                                    <option value="nonprofit" {{ old('organization_type', $template->organization_type) === 'nonprofit' ? 'selected' : '' }}>
                                        {{ app()->getLocale() === 'en' ? 'Non-profit' : 'Ideell' }}
                                    </option>
                                    <option value="other" {{ old('organization_type', $template->organization_type) === 'other' ? 'selected' : '' }}>
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
                                    <option value="small" {{ old('organization_size', $template->organization_size) === 'small' ? 'selected' : '' }}>
                                        {{ app()->getLocale() === 'en' ? 'Small (1-50)' : 'Liten (1-50)' }}
                                    </option>
                                    <option value="medium" {{ old('organization_size', $template->organization_size) === 'medium' ? 'selected' : '' }}>
                                        {{ app()->getLocale() === 'en' ? 'Medium (51-250)' : 'Mellan (51-250)' }}
                                    </option>
                                    <option value="large" {{ old('organization_size', $template->organization_size) === 'large' ? 'selected' : '' }}>
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
                                       name="estimated_months" value="{{ old('estimated_months', $template->estimated_months) }}" min="1">
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
                                           name="requires_existing_gdpr" value="1" {{ old('requires_existing_gdpr', $template->requires_existing_gdpr) ? 'checked' : '' }}>
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
                                           name="is_active" value="1" {{ old('is_active', $template->is_active) ? 'checked' : '' }}>
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
                                       name="sort_order" value="{{ old('sort_order', $template->sort_order) }}" min="0">
                                @error('sort_order')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">{{ app()->getLocale() === 'en' ? 'Lower numbers appear first' : 'Lägre nummer visas först' }}</small>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between mt-2">
                        <a href="{{ route('onboarding-templates.index', ['locale' => app()->getLocale()]) }}" class="btn btn-outline-secondary">
                            <i data-feather="arrow-left" class="me-50"></i>
                            {{ app()->getLocale() === 'en' ? 'Cancel' : 'Avbryt' }}
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i data-feather="save" class="me-50"></i>
                            {{ app()->getLocale() === 'en' ? 'Update Template' : 'Uppdatera mall' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Template Tasks Section -->
        <div class="card mt-2">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title">{{ app()->getLocale() === 'en' ? 'Template Tasks' : 'Malluppgifter' }} ({{ $template->templateTasks->count() }})</h4>
                <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#add-task-modal">
                    <i data-feather="plus" class="me-50"></i>
                    {{ app()->getLocale() === 'en' ? 'Add Task' : 'Lägg till uppgift' }}
                </button>
            </div>
            <div class="card-body">
                @if($template->templateTasks->isEmpty())
                    <p class="text-muted text-center">
                        {{ app()->getLocale() === 'en' ? 'No tasks yet. Add your first task!' : 'Inga uppgifter än. Lägg till din första uppgift!' }}
                    </p>
                @else
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>{{ app()->getLocale() === 'en' ? 'Title' : 'Titel' }}</th>
                                    <th>{{ app()->getLocale() === 'en' ? 'Offset' : 'Förskjutning' }}</th>
                                    <th>{{ app()->getLocale() === 'en' ? 'Duration' : 'Längd' }}</th>
                                    <th>{{ app()->getLocale() === 'en' ? 'Hours' : 'Timmar' }}</th>
                                    <th>{{ app()->getLocale() === 'en' ? 'Actions' : 'Åtgärder' }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($template->templateTasks->sortBy('sort_order') as $task)
                                    <tr>
                                        <td>{{ $task->sort_order }}</td>
                                        <td>
                                            <strong>{{ $task->{'title_' . app()->getLocale()} }}</strong><br>
                                            <small class="text-muted">{{ Str::limit($task->{'desc_' . app()->getLocale()}, 60) }}</small>
                                        </td>
                                        <td>{{ $task->offset_days }} {{ app()->getLocale() === 'en' ? 'days' : 'dagar' }}</td>
                                        <td>{{ $task->duration_days }} {{ app()->getLocale() === 'en' ? 'days' : 'dagar' }}</td>
                                        <td>{{ $task->hours }}h</td>
                                        <td>
                                            <form action="{{ route('onboarding-templates.tasks.destroy', ['locale' => app()->getLocale(), 'template' => $template->id, 'task' => $task->id]) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-icon btn-flat-danger" onclick="return confirm('{{ app()->getLocale() === 'en' ? 'Are you sure?' : 'Är du säker?' }}')">
                                                    <i data-feather="trash-2"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Add Task Modal -->
<div class="modal fade" id="add-task-modal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ app()->getLocale() === 'en' ? 'Add Task to Template' : 'Lägg till uppgift till mall' }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('onboarding-templates.tasks.store', ['locale' => app()->getLocale(), 'template' => $template->id]) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 col-12">
                            <div class="mb-1">
                                <label class="form-label" for="task_title_en">{{ app()->getLocale() === 'en' ? 'Title (English)' : 'Titel (engelska)' }} <span class="text-danger">*</span></label>
                                <input type="text" id="task_title_en" class="form-control" name="title_en" required>
                            </div>
                        </div>
                        <div class="col-md-6 col-12">
                            <div class="mb-1">
                                <label class="form-label" for="task_title_se">{{ app()->getLocale() === 'en' ? 'Title (Swedish)' : 'Titel (svenska)' }} <span class="text-danger">*</span></label>
                                <input type="text" id="task_title_se" class="form-control" name="title_se" required>
                            </div>
                        </div>
                        <div class="col-md-6 col-12">
                            <div class="mb-1">
                                <label class="form-label" for="task_desc_en">{{ app()->getLocale() === 'en' ? 'Description (English)' : 'Beskrivning (engelska)' }}</label>
                                <textarea id="task_desc_en" class="form-control" name="desc_en" rows="3"></textarea>
                            </div>
                        </div>
                        <div class="col-md-6 col-12">
                            <div class="mb-1">
                                <label class="form-label" for="task_desc_se">{{ app()->getLocale() === 'en' ? 'Description (Swedish)' : 'Beskrivning (svenska)' }}</label>
                                <textarea id="task_desc_se" class="form-control" name="desc_se" rows="3"></textarea>
                            </div>
                        </div>
                        <div class="col-md-3 col-6">
                            <div class="mb-1">
                                <label class="form-label" for="task_offset_days">{{ app()->getLocale() === 'en' ? 'Offset (days)' : 'Förskjutning (dagar)' }}</label>
                                <input type="number" id="task_offset_days" class="form-control" name="offset_days" value="0" min="0" required>
                            </div>
                        </div>
                        <div class="col-md-3 col-6">
                            <div class="mb-1">
                                <label class="form-label" for="task_duration_days">{{ app()->getLocale() === 'en' ? 'Duration (days)' : 'Längd (dagar)' }}</label>
                                <input type="number" id="task_duration_days" class="form-control" name="duration_days" value="30" min="1" required>
                            </div>
                        </div>
                        <div class="col-md-3 col-6">
                            <div class="mb-1">
                                <label class="form-label" for="task_hours">{{ app()->getLocale() === 'en' ? 'Hours' : 'Timmar' }}</label>
                                <input type="number" step="0.5" id="task_hours" class="form-control" name="hours" value="0" min="0" required>
                            </div>
                        </div>
                        <div class="col-md-3 col-6">
                            <div class="mb-1">
                                <label class="form-label" for="task_sort_order">{{ app()->getLocale() === 'en' ? 'Order' : 'Ordning' }}</label>
                                <input type="number" id="task_sort_order" class="form-control" name="sort_order" value="{{ $template->templateTasks->max('sort_order') + 1 }}" min="0" required>
                            </div>
                        </div>
                        <div class="col-md-6 col-12">
                            <div class="mb-1">
                                <label class="form-label" for="task_status_id">{{ app()->getLocale() === 'en' ? 'Task Status' : 'Uppgiftsstatus' }}</label>
                                <select id="task_status_id" class="form-select" name="task_status_id" required>
                                    @foreach($taskStatuses as $status)
                                        <option value="{{ $status->id }}" {{ $status->id == 1 ? 'selected' : '' }}>
                                            {{ $status->{'name_' . app()->getLocale()} }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6 col-12">
                            <div class="mb-1">
                                <label class="form-label" for="task_action_type">{{ app()->getLocale() === 'en' ? 'Action Type' : 'Åtgärdstyp' }}</label>
                                <select id="task_action_type" class="form-select" name="action_type">
                                    <option value="plan">Plan</option>
                                    <option value="do">Do</option>
                                    <option value="review">Review</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{{ app()->getLocale() === 'en' ? 'Cancel' : 'Avbryt' }}</button>
                    <button type="submit" class="btn btn-primary">{{ app()->getLocale() === 'en' ? 'Add Task' : 'Lägg till' }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
