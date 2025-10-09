@extends('layouts/contentLayoutMaster')

@section('title', 'Organisation Onboarding')

@section('page-style')
<style>
.template-card {
    cursor: pointer;
    transition: all 0.3s;
    border: 2px solid transparent;
    margin-bottom: 1rem;
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
.wizard-step {
    display: none;
}
.wizard-step.active {
    display: block;
}
.step-indicator {
    display: flex;
    justify-content: space-between;
    margin-bottom: 2rem;
}
.step-item {
    flex: 1;
    text-align: center;
    position: relative;
    padding: 10px;
}
.step-item:not(:last-child):after {
    content: '';
    position: absolute;
    top: 20px;
    left: 50%;
    width: 100%;
    height: 2px;
    background: #ddd;
    z-index: -1;
}
.step-item.active .step-number {
    background: #7367f0;
    color: white;
}
.step-item.completed .step-number {
    background: #28c76f;
    color: white;
}
.step-number {
    display: inline-block;
    width: 40px;
    height: 40px;
    line-height: 40px;
    border-radius: 50%;
    background: #ddd;
    color: #666;
    margin-bottom: 5px;
}
</style>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">{{ app()->getLocale() === 'en' ? 'Onboarding for new organisation' : 'Onboarding för ny organisation' }}</h4>
            </div>
            <div class="card-body">
                <!-- Step Indicator -->
                <div class="step-indicator">
                    <div class="step-item active" data-step="1">
                        <div class="step-number">1</div>
                        <div class="step-title">{{ app()->getLocale() === 'en' ? 'Questions' : 'Frågor' }}</div>
                    </div>
                    <div class="step-item" data-step="2">
                        <div class="step-number">2</div>
                        <div class="step-title">{{ app()->getLocale() === 'en' ? 'Select template' : 'Välj mall' }}</div>
                    </div>
                    <div class="step-item" data-step="3">
                        <div class="step-number">3</div>
                        <div class="step-title">{{ app()->getLocale() === 'en' ? 'Confirm' : 'Bekräfta' }}</div>
                    </div>
                </div>

                <!-- Step 1: Questions -->
                <div class="wizard-step active" id="step-1">
                    <h5 class="mb-1">{{ app()->getLocale() === 'en' ? 'Organisation information' : 'Organisationsinformation' }}</h5>
                    <p class="text-muted mb-3">{{ app()->getLocale() === 'en' ? 'Answer the following questions to get the right recommendations.' : 'Besvara följande frågor för att få rätt rekommendationer.' }}</p>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-1">
                                <label class="form-label" for="organization_type">{{ app()->getLocale() === 'en' ? 'What type of organisation is it?' : 'Vilken typ av organisation är det?' }}</label>
                                <select class="form-select" id="organization_type">
                                    <option value="">{{ app()->getLocale() === 'en' ? 'Select type...' : 'Välj typ...' }}</option>
                                    <option value="public">{{ app()->getLocale() === 'en' ? 'Public authority' : 'Offentlig myndighet' }}</option>
                                    <option value="private">{{ app()->getLocale() === 'en' ? 'Private company' : 'Privat bolag' }}</option>
                                    <option value="nonprofit">{{ app()->getLocale() === 'en' ? 'Non-profit organisation' : 'Ideell organisation' }}</option>
                                    <option value="other">{{ app()->getLocale() === 'en' ? 'Other' : 'Annat' }}</option>
                                </select>
                            </div>

                            <div class="mb-1">
                                <label class="form-label" for="organization_size">{{ app()->getLocale() === 'en' ? 'How large is the organisation?' : 'Hur stor är organisationen?' }}</label>
                                <select class="form-select" id="organization_size">
                                    <option value="">{{ app()->getLocale() === 'en' ? 'Select size...' : 'Välj storlek...' }}</option>
                                    <option value="small">{{ app()->getLocale() === 'en' ? 'Small (1-50 employees)' : 'Liten (1-50 anställda)' }}</option>
                                    <option value="medium">{{ app()->getLocale() === 'en' ? 'Medium (51-250 employees)' : 'Mellan (51-250 anställda)' }}</option>
                                    <option value="large">{{ app()->getLocale() === 'en' ? 'Large (250+ employees)' : 'Stor (250+ anställda)' }}</option>
                                </select>
                            </div>

                            <div class="mb-1">
                                <label class="form-label">{{ app()->getLocale() === 'en' ? 'Do you have existing data protection work in place?' : 'Har ni ett befintligt dataskyddsarbete på plats?' }}</label>
                                <div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="has_existing_gdpr" id="gdpr_yes" value="true">
                                        <label class="form-check-label" for="gdpr_yes">{{ app()->getLocale() === 'en' ? 'Yes' : 'Ja' }}</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="has_existing_gdpr" id="gdpr_no" value="false" checked>
                                        <label class="form-check-label" for="gdpr_no">{{ app()->getLocale() === 'en' ? 'No' : 'Nej' }}</label>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-1">
                                <label class="form-label" for="organisation_id">{{ app()->getLocale() === 'en' ? 'Select organisation' : 'Välj organisation' }}</label>
                                <select class="form-select" id="organisation_id">
                                    <option value="">{{ app()->getLocale() === 'en' ? 'Loading...' : 'Laddar...' }}</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end mt-2">
                        <button class="btn btn-primary" onclick="goToStep(2)">
                            {{ app()->getLocale() === 'en' ? 'Next' : 'Nästa' }} <i data-feather="arrow-right" class="ms-1"></i>
                        </button>
                    </div>
                </div>

                <!-- Step 2: Templates -->
                <div class="wizard-step" id="step-2">
                    <h5 class="mb-1">{{ app()->getLocale() === 'en' ? 'Select a template' : 'Välj en mall' }}</h5>
                    <p class="text-muted mb-3">{{ app()->getLocale() === 'en' ? 'Based on your answers, we recommend the following templates.' : 'Baserat på dina svar rekommenderar vi följande mallar.' }}</p>

                    <div class="row" id="templates-container">
                        <div class="col-12 text-center">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">{{ app()->getLocale() === 'en' ? 'Loading...' : 'Laddar...' }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between mt-2">
                        <button class="btn btn-outline-secondary" onclick="goToStep(1)">
                            <i data-feather="arrow-left" class="me-1"></i> {{ app()->getLocale() === 'en' ? 'Previous' : 'Föregående' }}
                        </button>
                        <button class="btn btn-primary" onclick="goToStep(3)" disabled id="btn-next-step3">
                            {{ app()->getLocale() === 'en' ? 'Next' : 'Nästa' }} <i data-feather="arrow-right" class="ms-1"></i>
                        </button>
                    </div>
                </div>

                <!-- Step 3: Confirmation -->
                <div class="wizard-step" id="step-3">
                    <h5 class="mb-1">{{ app()->getLocale() === 'en' ? 'Confirm and apply' : 'Bekräfta och applicera' }}</h5>
                    <p class="text-muted mb-3">{{ app()->getLocale() === 'en' ? 'Review your selections before applying the template.' : 'Kontrollera valen innan du applicerar mallen.' }}</p>

                    <div class="row" id="confirmation-details"></div>

                    <div class="d-flex justify-content-between mt-2">
                        <button class="btn btn-outline-secondary" onclick="goToStep(2)">
                            <i data-feather="arrow-left" class="me-1"></i> {{ app()->getLocale() === 'en' ? 'Previous' : 'Föregående' }}
                        </button>
                        <button class="btn btn-success" onclick="applyTemplate()" id="btn-apply">
                            <i data-feather="check" class="me-1"></i> {{ app()->getLocale() === 'en' ? 'Apply template' : 'Applicera mall' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('page-script')
<script>
let currentStep = 1;
let selectedTemplate = null;
let recommendations = [];
const locale = '{{ app()->getLocale() }}';
const translations = {
    en: {
        pleaseFillFields: 'Please fill in all fields',
        pleaseSelectTemplate: 'Please select a template',
        selectOrg: 'Select organisation...',
        noOrgsAvailable: 'No organisations available',
        errorLoading: 'Error loading',
        errorGettingRecs: 'Error getting recommendations',
        noTemplatesFound: 'No templates found for your selections.',
        summary: 'Summary',
        organisation: 'Organisation',
        selectedTemplate: 'Selected template',
        numberOfTasks: 'Number of tasks',
        estimatedTime: 'Estimated time',
        months: 'months',
        tasksToCreate: 'Tasks to be created',
        example: 'Example',
        startsIn: 'Starts',
        daysFromNow: 'days from now',
        length: 'Length',
        days: 'days',
        applying: 'Applying...',
        success: 'Success!',
        tasksCreated: 'tasks have been created for the organisation.',
        error: 'Error',
        couldNotApply: 'Could not apply the template',
        applyError: 'An error occurred while applying the template',
        applyTemplate: 'Apply template',
        tasks: 'tasks'
    },
    se: {
        pleaseFillFields: 'Vänligen fyll i alla fält',
        pleaseSelectTemplate: 'Vänligen välj en mall',
        selectOrg: 'Välj organisation...',
        noOrgsAvailable: 'Inga organisationer tillgängliga',
        errorLoading: 'Fel vid laddning',
        errorGettingRecs: 'Fel vid hämtning av rekommendationer',
        noTemplatesFound: 'Inga mallar hittades för dina val.',
        summary: 'Sammanfattning',
        organisation: 'Organisation',
        selectedTemplate: 'Vald mall',
        numberOfTasks: 'Antal uppgifter',
        estimatedTime: 'Beräknad tid',
        months: 'månader',
        tasksToCreate: 'Uppgifter som kommer skapas',
        example: 'Exempel',
        startsIn: 'Startar',
        daysFromNow: 'dagar från nu',
        length: 'Längd',
        days: 'dagar',
        applying: 'Applicerar...',
        success: 'Framgång!',
        tasksCreated: 'uppgifter har skapats för organisationen.',
        error: 'Fel',
        couldNotApply: 'Kunde inte applicera mallen',
        applyError: 'Ett fel uppstod vid applicering av mallen',
        applyTemplate: 'Applicera mall',
        tasks: 'uppgifter'
    }
};
const t = translations[locale];

document.addEventListener('DOMContentLoaded', function() {
    loadOrganisations();
    if (typeof feather !== 'undefined') {
        feather.replace();
    }
});

function goToStep(step) {
    if (step === 2 && currentStep === 1) {
        // Validate step 1
        const orgType = document.getElementById('organization_type').value;
        const orgSize = document.getElementById('organization_size').value;
        const orgId = document.getElementById('organisation_id').value;

        if (!orgType || !orgSize || !orgId) {
            alert(t.pleaseFillFields);
            return;
        }

        // Load recommendations
        getRecommendations();
    }

    if (step === 3 && !selectedTemplate) {
        alert(t.pleaseSelectTemplate);
        return;
    }

    if (step === 3) {
        showConfirmation();
    }

    // Hide all steps
    document.querySelectorAll('.wizard-step').forEach(s => s.classList.remove('active'));
    document.querySelectorAll('.step-item').forEach(s => s.classList.remove('active', 'completed'));

    // Show current step
    document.getElementById('step-' + step).classList.add('active');

    // Update indicators
    for (let i = 1; i <= 3; i++) {
        const stepItem = document.querySelector(`.step-item[data-step="${i}"]`);
        if (i < step) {
            stepItem.classList.add('completed');
        } else if (i === step) {
            stepItem.classList.add('active');
        }
    }

    currentStep = step;

    if (typeof feather !== 'undefined') {
        feather.replace();
    }
}

function loadOrganisations() {
    fetch('/{{ app()->getLocale() }}/axios/onboarding/organisations')
        .then(r => r.json())
        .then(data => {
            const select = document.getElementById('organisation_id');
            select.innerHTML = `<option value="">${t.selectOrg}</option>`;

            if (data.organisations && data.organisations.length > 0) {
                data.organisations.forEach(org => {
                    const option = document.createElement('option');
                    option.value = org.id;
                    option.textContent = org.name;
                    select.appendChild(option);
                });
            } else {
                select.innerHTML = `<option value="">${t.noOrgsAvailable}</option>`;
            }
        })
        .catch(err => {
            console.error('Error loading organisations:', err);
            document.getElementById('organisation_id').innerHTML = `<option value="">${t.errorLoading}</option>`;
        });
}

function getRecommendations() {
    const orgType = document.getElementById('organization_type').value;
    const orgSize = document.getElementById('organization_size').value;
    const hasGdpr = document.querySelector('input[name="has_existing_gdpr"]:checked').value === 'true';

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
    })
    .catch(err => {
        console.error('Error getting recommendations:', err);
        alert(t.errorGettingRecs);
    });
}

function displayTemplates(templates) {
    const container = document.getElementById('templates-container');

    if (templates.length === 0) {
        container.innerHTML = `<div class="col-12"><p class="text-center">${t.noTemplatesFound}</p></div>`;
        return;
    }

    container.innerHTML = '';

    templates.forEach(template => {
        const col = document.createElement('div');
        col.className = 'col-md-6';

        col.innerHTML = `
            <div class="card template-card" onclick="selectTemplate(${template.id})">
                <div class="card-body">
                    <h5 class="card-title">${template.name}</h5>
                    <p class="card-text">${template.description || ''}</p>

                    <div class="mb-1">
                        <small class="text-muted">
                            <strong>${t.summary}:</strong><br>
                            ${template.summary || (locale === 'en' ? 'No summary available' : 'Ingen sammanfattning tillgänglig')}
                        </small>
                    </div>

                    <div class="d-flex justify-content-between align-items-center">
                        <span class="badge bg-light-primary">${template.task_count} ${t.tasks}</span>
                        ${template.estimated_months ? `<span class="badge bg-light-info">${template.estimated_months} ${t.months}</span>` : ''}
                    </div>

                    ${template.tasks_preview && template.tasks_preview.length > 0 ? `
                        <div class="mt-2">
                            <small class="text-muted"><strong>${locale === 'en' ? 'Example tasks' : 'Exempel på uppgifter'}:</strong></small>
                            <ul class="task-preview small mt-1">
                                ${template.tasks_preview.map(task => `<li>${task.title}</li>`).join('')}
                                ${template.task_count > 5 ? `<li class="text-muted">... ${locale === 'en' ? 'and' : 'och'} ${template.task_count - 5} ${locale === 'en' ? 'more' : 'till'}</li>` : ''}
                            </ul>
                        </div>
                    ` : ''}
                </div>
            </div>
        `;

        container.appendChild(col);
    });

    if (typeof feather !== 'undefined') {
        feather.replace();
    }
}

function selectTemplate(templateId) {
    document.querySelectorAll('.template-card').forEach(card => card.classList.remove('selected'));
    event.currentTarget.classList.add('selected');

    selectedTemplate = recommendations.find(t => t.id === templateId);
    document.getElementById('btn-next-step3').disabled = false;
}

function showConfirmation() {
    const orgId = document.getElementById('organisation_id').value;
    const orgName = document.getElementById('organisation_id').selectedOptions[0].text;

    const confirmationHtml = `
        <div class="col-12">
            <div class="alert alert-info">
                <h5 class="alert-heading">${t.summary}</h5>
                <p><strong>${t.organisation}:</strong> ${orgName}</p>
                <p><strong>${t.selectedTemplate}:</strong> ${selectedTemplate.name}</p>
                <p><strong>${t.numberOfTasks}:</strong> ${selectedTemplate.task_count}</p>
                <p class="mb-0"><strong>${t.estimatedTime}:</strong> ${selectedTemplate.estimated_months || 'N/A'} ${t.months}</p>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5>${t.tasksToCreate}</h5>
                </div>
                <div class="card-body">
                    <p class="card-text">${selectedTemplate.summary}</p>
                    ${selectedTemplate.tasks_preview ? `
                        <h6 class="mt-2">${t.example}:</h6>
                        <ul>
                            ${selectedTemplate.tasks_preview.map(task => `
                                <li>
                                    <strong>${task.title}</strong><br>
                                    <small class="text-muted">${task.description || ''}</small><br>
                                    <small class="text-muted">${t.startsIn}: ${task.offset_days} ${t.daysFromNow} | ${t.length}: ${task.duration_days} ${t.days}</small>
                                </li>
                            `).join('')}
                        </ul>
                    ` : ''}
                </div>
            </div>
        </div>
    `;

    document.getElementById('confirmation-details').innerHTML = confirmationHtml;
}

function applyTemplate() {
    const orgId = document.getElementById('organisation_id').value;
    const btnApply = document.getElementById('btn-apply');

    btnApply.disabled = true;
    btnApply.innerHTML = `<span class="spinner-border spinner-border-sm me-1"></span>${t.applying}`;

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
            alert(`${t.success} ${data.tasks_created} ${t.tasksCreated}`);
            window.location.href = '/{{ app()->getLocale() }}/tasks';
        } else {
            alert(`${t.error}: ${data.message || t.couldNotApply}`);
            btnApply.disabled = false;
            btnApply.innerHTML = `<i data-feather="check" class="me-1"></i>${t.applyTemplate}`;
            if (typeof feather !== 'undefined') feather.replace();
        }
    })
    .catch(err => {
        console.error('Error applying template:', err);
        alert(t.applyError);
        btnApply.disabled = false;
        btnApply.innerHTML = `<i data-feather="check" class="me-1"></i>${t.applyTemplate}`;
        if (typeof feather !== 'undefined') feather.replace();
    });
}
</script>
@endsection
