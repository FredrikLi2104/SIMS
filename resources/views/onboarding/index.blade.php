@extends('layouts/contentLayoutMaster')

@section('title', 'Organisation Onboarding')

@section('vendor-style')
<link rel="stylesheet" href="{{ asset('vendors/css/forms/wizard/bs-stepper.min.css') }}">
@endsection

@section('page-style')
<style>
.template-card {
    cursor: pointer;
    transition: all 0.3s;
    border: 2px solid transparent;
}
.template-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 4px 24px 0 rgba(34,41,47,.1);
}
.template-card.selected {
    border-color: #7367f0;
    background-color: #f8f8fb;
}
.task-preview {
    max-height: 300px;
    overflow-y: auto;
}
</style>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Onboarding för ny organisation</h4>
            </div>
            <div class="card-body">
                <!-- Wizard -->
                <div class="bs-stepper wizard-modern wizard-modern-example">
                    <div class="bs-stepper-header">
                        <div class="step" data-target="#step-questions">
                            <button type="button" class="step-trigger">
                                <span class="bs-stepper-box">1</span>
                                <span class="bs-stepper-label">
                                    <span class="bs-stepper-title">Frågor</span>
                                    <span class="bs-stepper-subtitle">Besvara några frågor</span>
                                </span>
                            </button>
                        </div>
                        <div class="line"></div>
                        <div class="step" data-target="#step-templates">
                            <button type="button" class="step-trigger">
                                <span class="bs-stepper-box">2</span>
                                <span class="bs-stepper-label">
                                    <span class="bs-stepper-title">Välj mall</span>
                                    <span class="bs-stepper-subtitle">Rekommenderade mallar</span>
                                </span>
                            </button>
                        </div>
                        <div class="line"></div>
                        <div class="step" data-target="#step-confirmation">
                            <button type="button" class="step-trigger">
                                <span class="bs-stepper-box">3</span>
                                <span class="bs-stepper-label">
                                    <span class="bs-stepper-title">Bekräfta</span>
                                    <span class="bs-stepper-subtitle">Applicera mall</span>
                                </span>
                            </button>
                        </div>
                    </div>
                    <div class="bs-stepper-content">
                        <!-- Step 1: Questions -->
                        <div id="step-questions" class="content">
                            <div class="content-header">
                                <h5 class="mb-0">Organisationsinformation</h5>
                                <small class="text-muted">Besvara följande frågor för att få rätt rekommendationer.</small>
                            </div>

                            <div class="row mt-2">
                                <div class="col-12">
                                    <div class="mb-1">
                                        <label class="form-label" for="organization_type">Vilken typ av organisation är det?</label>
                                        <select class="form-select" id="organization_type">
                                            <option value="">Välj typ...</option>
                                            <option value="public">Offentlig myndighet</option>
                                            <option value="private">Privat bolag</option>
                                            <option value="nonprofit">Ideell organisation</option>
                                            <option value="other">Annat</option>
                                        </select>
                                    </div>

                                    <div class="mb-1">
                                        <label class="form-label" for="organization_size">Hur stor är organisationen?</label>
                                        <select class="form-select" id="organization_size">
                                            <option value="">Välj storlek...</option>
                                            <option value="small">Liten (1-50 anställda)</option>
                                            <option value="medium">Mellan (51-250 anställda)</option>
                                            <option value="large">Stor (250+ anställda)</option>
                                        </select>
                                    </div>

                                    <div class="mb-1">
                                        <label class="form-label">Har ni ett befintligt dataskyddsarbete på plats?</label>
                                        <div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="has_existing_gdpr" id="gdpr_yes" value="true">
                                                <label class="form-check-label" for="gdpr_yes">Ja</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="has_existing_gdpr" id="gdpr_no" value="false" checked>
                                                <label class="form-check-label" for="gdpr_no">Nej</label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mb-1">
                                        <label class="form-label" for="organisation_id">Välj organisation</label>
                                        <select class="form-select" id="organisation_id">
                                            <option value="">Laddar organisationer...</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between mt-2">
                                <button class="btn btn-outline-secondary btn-prev" disabled>
                                    <i data-feather="arrow-left" class="align-middle me-sm-25 me-0"></i>
                                    <span class="align-middle d-sm-inline-block d-none">Föregående</span>
                                </button>
                                <button class="btn btn-primary btn-next" onclick="getRecommendations()">
                                    <span class="align-middle d-sm-inline-block d-none">Nästa</span>
                                    <i data-feather="arrow-right" class="align-middle ms-sm-25 ms-0"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Step 2: Templates -->
                        <div id="step-templates" class="content">
                            <div class="content-header">
                                <h5 class="mb-0">Välj en mall</h5>
                                <small class="text-muted">Baserat på dina svar rekommenderar vi följande mallar.</small>
                            </div>

                            <div class="row mt-2" id="templates-container">
                                <div class="col-12 text-center">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="visually-hidden">Laddar...</span>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between mt-2">
                                <button class="btn btn-outline-secondary btn-prev" onclick="stepper.previous()">
                                    <i data-feather="arrow-left" class="align-middle me-sm-25 me-0"></i>
                                    <span class="align-middle d-sm-inline-block d-none">Föregående</span>
                                </button>
                                <button class="btn btn-primary btn-next" onclick="confirmTemplate()" disabled id="btn-confirm-template">
                                    <span class="align-middle d-sm-inline-block d-none">Nästa</span>
                                    <i data-feather="arrow-right" class="align-middle ms-sm-25 ms-0"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Step 3: Confirmation -->
                        <div id="step-confirmation" class="content">
                            <div class="content-header">
                                <h5 class="mb-0">Bekräfta och applicera</h5>
                                <small class="text-muted">Kontrollera valen innan du applicerar mallen.</small>
                            </div>

                            <div class="row mt-2" id="confirmation-details">
                                <!-- Details will be populated here -->
                            </div>

                            <div class="d-flex justify-content-between mt-2">
                                <button class="btn btn-outline-secondary btn-prev" onclick="stepper.previous()">
                                    <i data-feather="arrow-left" class="align-middle me-sm-25 me-0"></i>
                                    <span class="align-middle d-sm-inline-block d-none">Föregående</span>
                                </button>
                                <button class="btn btn-success" onclick="applyTemplate()" id="btn-apply">
                                    <i data-feather="check" class="align-middle me-sm-25 me-0"></i>
                                    <span class="align-middle d-sm-inline-block d-none">Applicera mall</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('vendor-script')
<script src="{{ asset('vendors/js/forms/wizard/bs-stepper.min.js') }}"></script>
@endsection

@section('page-script')
<script>
let stepper;
let selectedTemplate = null;
let recommendations = [];

document.addEventListener('DOMContentLoaded', function() {
    // Initialize stepper
    const stepperElement = document.querySelector('.wizard-modern-example');
    if (stepperElement) {
        stepper = new Stepper(stepperElement, {
            linear: true,
            animation: true
        });
    }

    // Load organizations
    loadOrganisations();
});

function loadOrganisations() {
    fetch('/{{ app()->getLocale() }}/axios/onboarding/organisations')
        .then(r => r.json())
        .then(data => {
            const select = document.getElementById('organisation_id');
            select.innerHTML = '<option value="">Välj organisation...</option>';

            if (data.organisations) {
                data.organisations.forEach(org => {
                    const option = document.createElement('option');
                    option.value = org.id;
                    option.textContent = org.name;
                    select.appendChild(option);
                });
            }
        })
        .catch(err => {
            console.error('Error loading organisations:', err);
            document.getElementById('organisation_id').innerHTML = '<option value="">Fel vid laddning</option>';
        });
}

function getRecommendations() {
    const orgType = document.getElementById('organization_type').value;
    const orgSize = document.getElementById('organization_size').value;
    const hasGdpr = document.querySelector('input[name="has_existing_gdpr"]:checked').value === 'true';
    const orgId = document.getElementById('organisation_id').value;

    if (!orgType || !orgSize || !orgId) {
        alert('Vänligen fyll i alla fält');
        return;
    }

    fetch('/{{ app()->getLocale() }}/axios/onboarding/recommendations', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            organization_type: orgType,
            organization_size: orgSize,
            has_existing_gdpr: hasGdpr
        })
    })
    .then(r => r.json())
    .then(data => {
        recommendations = data.templates || [];
        displayTemplates(recommendations);
        stepper.next();
    })
    .catch(err => {
        console.error('Error getting recommendations:', err);
        alert('Fel vid hämtning av rekommendationer');
    });
}

function displayTemplates(templates) {
    const container = document.getElementById('templates-container');

    if (templates.length === 0) {
        container.innerHTML = '<div class="col-12"><p class="text-center">Inga mallar hittades för dina val.</p></div>';
        return;
    }

    container.innerHTML = '';

    templates.forEach(template => {
        const col = document.createElement('div');
        col.className = 'col-md-6 mb-2';

        col.innerHTML = `
            <div class="card template-card" onclick="selectTemplate(${template.id})">
                <div class="card-body">
                    <h5 class="card-title">${template.name}</h5>
                    <p class="card-text">${template.description || ''}</p>

                    <div class="mb-1">
                        <small class="text-muted">
                            <strong>Sammanfattning:</strong><br>
                            ${template.summary || 'Ingen sammanfattning tillgänglig'}
                        </small>
                    </div>

                    <div class="d-flex justify-content-between align-items-center">
                        <span class="badge bg-light-primary">${template.task_count} uppgifter</span>
                        ${template.estimated_months ? `<span class="badge bg-light-info">${template.estimated_months} månader</span>` : ''}
                    </div>

                    ${template.tasks_preview && template.tasks_preview.length > 0 ? `
                        <div class="mt-2">
                            <small class="text-muted"><strong>Exempel på uppgifter:</strong></small>
                            <ul class="task-preview small mt-1">
                                ${template.tasks_preview.map(task => `<li>${task.title}</li>`).join('')}
                                ${template.task_count > 5 ? `<li class="text-muted">... och ${template.task_count - 5} till</li>` : ''}
                            </ul>
                        </div>
                    ` : ''}
                </div>
            </div>
        `;

        container.appendChild(col);
    });

    // Re-initialize feather icons
    if (typeof feather !== 'undefined') {
        feather.replace();
    }
}

function selectTemplate(templateId) {
    // Remove previous selection
    document.querySelectorAll('.template-card').forEach(card => {
        card.classList.remove('selected');
    });

    // Add selection to clicked card
    event.currentTarget.classList.add('selected');

    selectedTemplate = recommendations.find(t => t.id === templateId);
    document.getElementById('btn-confirm-template').disabled = false;
}

function confirmTemplate() {
    if (!selectedTemplate) {
        alert('Vänligen välj en mall');
        return;
    }

    const orgId = document.getElementById('organisation_id').value;
    const orgName = document.getElementById('organisation_id').selectedOptions[0].text;

    const confirmationHtml = `
        <div class="col-12">
            <div class="alert alert-info">
                <h5 class="alert-heading">Sammanfattning</h5>
                <p><strong>Organisation:</strong> ${orgName}</p>
                <p><strong>Vald mall:</strong> ${selectedTemplate.name}</p>
                <p><strong>Antal uppgifter:</strong> ${selectedTemplate.task_count}</p>
                <p><strong>Beräknad tid:</strong> ${selectedTemplate.estimated_months || 'N/A'} månader</p>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5>Uppgifter som kommer skapas</h5>
                </div>
                <div class="card-body">
                    <p class="card-text">${selectedTemplate.summary}</p>
                    ${selectedTemplate.tasks_preview ? `
                        <h6 class="mt-2">Exempel:</h6>
                        <ul>
                            ${selectedTemplate.tasks_preview.map(task => `
                                <li>
                                    <strong>${task.title}</strong><br>
                                    <small class="text-muted">${task.description || ''}</small><br>
                                    <small class="text-muted">Startar: ${task.offset_days} dagar från nu | Längd: ${task.duration_days} dagar</small>
                                </li>
                            `).join('')}
                        </ul>
                    ` : ''}
                </div>
            </div>
        </div>
    `;

    document.getElementById('confirmation-details').innerHTML = confirmationHtml;
    stepper.next();
}

function applyTemplate() {
    const orgId = document.getElementById('organisation_id').value;
    const btnApply = document.getElementById('btn-apply');

    btnApply.disabled = true;
    btnApply.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Applicerar...';

    fetch(`/{{ app()->getLocale() }}/axios/onboarding/organisations/${orgId}/apply-template`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            template_id: selectedTemplate.id
        })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            alert(`Framgång! ${data.tasks_created} uppgifter har skapats för organisationen.`);
            // Optionally redirect to tasks page
            window.location.href = '/{{ app()->getLocale() }}/organisations/' + orgId;
        } else {
            alert('Fel: ' + (data.message || 'Kunde inte applicera mallen'));
            btnApply.disabled = false;
            btnApply.innerHTML = '<i data-feather="check" class="align-middle me-sm-25 me-0"></i><span class="align-middle d-sm-inline-block d-none">Applicera mall</span>';
        }
    })
    .catch(err => {
        console.error('Error applying template:', err);
        alert('Ett fel uppstod vid applicering av mallen');
        btnApply.disabled = false;
        btnApply.innerHTML = '<i data-feather="check" class="align-middle me-sm-25 me-0"></i><span class="align-middle d-sm-inline-block d-none">Applicera mall</span>';
    });
}
</script>
@endsection
