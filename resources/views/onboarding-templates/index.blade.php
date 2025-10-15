@extends('layouts/contentLayoutMaster')

@section('title', app()->getLocale() === 'en' ? 'Onboarding Templates' : 'Onboarding-mallar')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title">{{ app()->getLocale() === 'en' ? 'Onboarding Templates' : 'Onboarding-mallar' }}</h4>
                <a href="{{ route('onboarding-templates.create', ['locale' => app()->getLocale()]) }}" class="btn btn-primary">
                    <i data-feather="plus" class="me-50"></i>
                    {{ app()->getLocale() === 'en' ? 'Create Template' : 'Skapa mall' }}
                </a>
            </div>
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>{{ app()->getLocale() === 'en' ? 'Name' : 'Namn' }}</th>
                                <th>{{ app()->getLocale() === 'en' ? 'Type' : 'Typ' }}</th>
                                <th>{{ app()->getLocale() === 'en' ? 'Size' : 'Storlek' }}</th>
                                <th>{{ app()->getLocale() === 'en' ? 'Tasks' : 'Uppgifter' }}</th>
                                <th>{{ app()->getLocale() === 'en' ? 'Duration' : 'Tidsåtgång' }}</th>
                                <th>{{ app()->getLocale() === 'en' ? 'Status' : 'Status' }}</th>
                                <th>{{ app()->getLocale() === 'en' ? 'Actions' : 'Åtgärder' }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($templates as $template)
                                <tr>
                                    <td>
                                        <strong>{{ $template->{'name_' . app()->getLocale()} }}</strong><br>
                                        <small class="text-muted">{{ Str::limit($template->{'summary_' . app()->getLocale()}, 60) }}</small>
                                    </td>
                                    <td>
                                        @if($template->organization_type)
                                            <span class="badge bg-light-info">
                                                {{ ucfirst($template->organization_type) }}
                                            </span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($template->organization_size)
                                            <span class="badge bg-light-secondary">
                                                {{ ucfirst($template->organization_size) }}
                                            </span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-light-primary">
                                            {{ $template->templateTasks->count() }} {{ app()->getLocale() === 'en' ? 'tasks' : 'uppgifter' }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($template->estimated_months)
                                            {{ $template->estimated_months }} {{ app()->getLocale() === 'en' ? 'months' : 'månader' }}
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($template->is_active)
                                            <span class="badge bg-light-success">{{ app()->getLocale() === 'en' ? 'Active' : 'Aktiv' }}</span>
                                        @else
                                            <span class="badge bg-light-secondary">{{ app()->getLocale() === 'en' ? 'Inactive' : 'Inaktiv' }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="dropdown">
                                            <button type="button" class="btn btn-sm btn-icon dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                                <i data-feather="more-vertical"></i>
                                            </button>
                                            <div class="dropdown-menu">
                                                <a class="dropdown-item" href="{{ route('onboarding-templates.edit', ['locale' => app()->getLocale(), 'onboarding_template' => $template->id]) }}">
                                                    <i data-feather="edit-2" class="me-50"></i>
                                                    <span>{{ app()->getLocale() === 'en' ? 'Edit' : 'Redigera' }}</span>
                                                </a>
                                                <form action="{{ route('onboarding-templates.destroy', ['locale' => app()->getLocale(), 'onboarding_template' => $template->id]) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="dropdown-item" onclick="return confirm('{{ app()->getLocale() === 'en' ? 'Are you sure?' : 'Är du säker?' }}')">
                                                        <i data-feather="trash" class="me-50"></i>
                                                        <span>{{ app()->getLocale() === 'en' ? 'Delete' : 'Ta bort' }}</span>
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center">
                                        {{ app()->getLocale() === 'en' ? 'No templates found. Create your first template!' : 'Inga mallar hittades. Skapa din första mall!' }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
