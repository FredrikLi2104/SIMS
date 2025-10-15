<template>
    <!-- Quick Test Modal -->
    <div class="modal fade" id="quickTestModal" tabindex="-1" aria-labelledby="quickTestModal" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ collection?.messages?.markAsTested || 'Mark as Tested' }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" v-if="currentStatement">
                    <!-- Statement Info -->
                    <div class="mb-3 p-3 bg-light rounded">
                        <h6 class="text-primary mb-2">
                            <strong>{{ currentStatement.subcode }}</strong>
                        </h6>
                        <p class="mb-0">{{ currentStatement["content_" + locale] }}</p>
                    </div>

                    <!-- Test Plan Info (Read-only) -->
                    <div class="mb-3 p-3 border rounded">
                        <h6 class="text-secondary">{{ collection?.messages?.testPlan || 'Test Plan' }}</h6>
                        <p class="mb-2"><strong>{{ collection?.messages?.testMethod || 'Method' }}:</strong> {{ currentStatement.test_method }}</p>
                        <p class="mb-0"><strong>{{ collection?.messages?.plan || 'Plan' }}:</strong> {{ currentStatement.test_plan }}</p>
                    </div>

                    <!-- Test Outcome Radio Buttons -->
                    <div class="mb-3">
                        <label class="form-label">
                            <strong>{{ collection?.messages?.testOutcome || 'Test Outcome' }}</strong>
                            <span class="text-danger">*</span>
                        </label>
                        <div class="d-flex gap-3">
                            <div class="form-check">
                                <input
                                    class="form-check-input"
                                    type="radio"
                                    name="quickTestOutcome"
                                    id="quickTestPassed"
                                    value="2"
                                    v-model="quickTestData.review_status_id"
                                >
                                <label class="form-check-label" for="quickTestPassed">
                                    <i class="feather icon-check text-success"></i>
                                    {{ collection?.messages?.passed || 'Passed' }}
                                </label>
                            </div>
                            <div class="form-check">
                                <input
                                    class="form-check-input"
                                    type="radio"
                                    name="quickTestOutcome"
                                    id="quickTestFailed"
                                    value="3"
                                    v-model="quickTestData.review_status_id"
                                >
                                <label class="form-check-label" for="quickTestFailed">
                                    <i class="feather icon-x text-danger"></i>
                                    {{ collection?.messages?.failed || 'Failed' }}
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Test Result / Notes -->
                    <div class="mb-3">
                        <label class="form-label" for="quickTestNote">
                            <strong>{{ collection?.messages?.testResult || 'Test Result' }}</strong>
                            <span class="text-danger">*</span>
                        </label>
                        <textarea
                            id="quickTestNote"
                            class="form-control"
                            rows="4"
                            v-model="quickTestData.test_result"
                            :placeholder="collection?.messages?.testResultPlaceholder || 'Describe the test results. What did you find?'"
                        ></textarea>
                    </div>

                    <!-- Test Evidence -->
                    <div class="mb-3">
                        <label class="form-label" for="quickTestEvidence">
                            <strong>{{ collection?.messages?.testEvidence || 'Evidence / Documentation' }}</strong>
                        </label>
                        <textarea
                            id="quickTestEvidence"
                            class="form-control"
                            rows="2"
                            v-model="quickTestData.test_evidence"
                            :placeholder="collection?.messages?.testEvidencePlaceholder || 'Reference to evidence, such as file names, log entries, screenshots...'"
                        ></textarea>
                    </div>

                    <!-- File Upload -->
                    <div class="mb-3">
                        <label class="form-label">
                            <strong>{{ collection?.messages?.uploadEvidence || 'Upload evidence' }}</strong>
                        </label>
                        <div class="d-flex gap-2 align-items-center">
                            <input
                                type="file"
                                ref="fileInput"
                                @change="handleFileSelect"
                                class="form-control"
                                :disabled="uploadingFile"
                            />
                            <button
                                type="button"
                                class="btn btn-sm btn-outline-primary"
                                @click="uploadFile"
                                :disabled="!selectedFile || uploadingFile"
                            >
                                <i class="feather" :class="uploadingFile ? 'icon-loader' : 'icon-upload'"></i>
                                {{ uploadingFile ? (collection?.messages?.uploadingFile || 'Uploading...') : (collection?.messages?.uploadFile || 'Upload') }}
                            </button>
                        </div>
                        <small class="form-text text-muted">{{ collection?.messages?.maxFileSize || 'Max file size: 10MB' }}</small>
                    </div>

                    <!-- Attached Files List -->
                    <div v-if="currentStatementAttachments.length > 0" class="mb-3">
                        <label class="form-label">
                            <strong>{{ collection?.messages?.attachedFiles || 'Attached files' }}</strong>
                        </label>
                        <div class="list-group">
                            <div v-for="attachment in currentStatementAttachments" :key="attachment.id"
                                 class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <i class="feather icon-file me-2"></i>
                                    <a :href="attachment.file_url" target="_blank" class="text-decoration-none">
                                        {{ attachment.file_name }}
                                    </a>
                                    <small class="text-muted ms-2">({{ attachment.file_size_human }})</small>
                                    <br>
                                    <small class="text-muted">
                                        {{ collection?.messages?.uploadedBy || 'Uploaded by' }}: {{ attachment.uploaded_by }} - {{ attachment.created_at }}
                                    </small>
                                </div>
                                <button
                                    type="button"
                                    class="btn btn-sm btn-outline-danger"
                                    @click="deleteAttachment(attachment.id)">
                                    <i class="feather icon-trash-2"></i>
                                    {{ collection?.messages?.deleteFile || 'Delete' }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        {{ collection?.messages?.cancel || 'Cancel' }}
                    </button>
                    <button
                        type="button"
                        class="btn btn-primary"
                        @click="saveQuickTest"
                        :disabled="!quickTestData.review_status_id || !quickTestData.test_result"
                    >
                        <i class="feather icon-save"></i>
                        {{ collection?.messages?.save || 'Save' }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Test Conduct Modal -->
    <div class="modal fade" id="testConductModal" tabindex="-1" aria-labelledby="testConductModal" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl modal-fullscreen-lg-down">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ collection?.messages?.test }} {{ collection?.messages?.conduct }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" @click="testConductHide"></button>
                </div>
                <div class="modal-body">
                    <p>{{ collection?.messages?.testConductDescription || 'Document the results of your tests. Describe what you found and provide evidence of the test execution.' }}</p>

                    <!-- Filter by Status -->
                    <div class="mb-3">
                        <label class="form-label">{{ collection?.messages?.filterByStatus || 'Filter by status' }}:</label>
                        <div class="btn-group" role="group">
                            <input type="radio" class="btn-check" name="statusFilter" id="filterAll" value="all" v-model="statusFilter" autocomplete="off" checked>
                            <label class="btn btn-outline-primary" for="filterAll">{{ collection?.messages?.all || 'All' }} ({{ testStatements.length }})</label>

                            <input type="radio" class="btn-check" name="statusFilter" id="filterPlanned" value="planned" v-model="statusFilter" autocomplete="off">
                            <label class="btn btn-outline-info" for="filterPlanned">{{ collection?.messages?.planned || 'Planned' }} ({{ getCountByStatus('planned') }})</label>

                            <input type="radio" class="btn-check" name="statusFilter" id="filterInProgress" value="in_progress" v-model="statusFilter" autocomplete="off">
                            <label class="btn btn-outline-warning" for="filterInProgress">{{ collection?.messages?.inProgress || 'In Progress' }} ({{ getCountByStatus('in_progress') }})</label>

                            <input type="radio" class="btn-check" name="statusFilter" id="filterCompleted" value="completed" v-model="statusFilter" autocomplete="off">
                            <label class="btn btn-outline-success" for="filterCompleted">{{ collection?.messages?.completed || 'Completed' }} ({{ getCountByStatus('completed') }})</label>
                        </div>
                    </div>

                    <!-- Test Statements List -->
                    <div v-if="filteredTestStatements.length === 0" class="alert alert-warning">
                        <i class="feather icon-alert-circle"></i> {{ collection?.messages?.noTestsToConduct || 'No tests available with this filter. Please prepare test plans first.' }}
                    </div>

                    <!-- Checklist View -->
                    <div v-else class="test-checklist">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th width="5%">{{ collection?.messages?.status || 'Status' }}</th>
                                        <th width="15%">{{ collection?.messages?.code || 'Code' }}</th>
                                        <th width="30%">{{ collection?.messages?.statement || 'Statement' }}</th>
                                        <th width="20%">{{ collection?.messages?.testMethod || 'Method' }}</th>
                                        <th width="15%">{{ collection?.messages?.testOutcome || 'Outcome' }}</th>
                                        <th width="15%">{{ collection?.messages?.actions || 'Actions' }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="statement in filteredTestStatements" :key="statement.id"
                                        :class="getRowClass(statement)">
                                        <!-- Status Icon -->
                                        <td class="text-center">
                                            <span v-if="!statement.test_status || statement.test_status === 'planned'"
                                                  class="badge bg-light-secondary rounded-circle p-2"
                                                  :title="collection?.messages?.notStarted || 'Not started'">
                                                <i class="feather icon-circle" style="width: 16px; height: 16px;"></i>
                                            </span>
                                            <span v-else-if="statement.test_status === 'in_progress'"
                                                  class="badge bg-light-warning rounded-circle p-2"
                                                  :title="collection?.messages?.inProgress || 'In progress'">
                                                <i class="feather icon-clock" style="width: 16px; height: 16px;"></i>
                                            </span>
                                            <span v-else-if="statement.test_status === 'completed' && statement.review_status_id == 2"
                                                  class="badge bg-light-success rounded-circle p-2"
                                                  :title="collection?.messages?.testPassed || 'Test passed'">
                                                <i class="feather icon-check" style="width: 16px; height: 16px;"></i>
                                            </span>
                                            <span v-else-if="statement.test_status === 'completed' && statement.review_status_id == 3"
                                                  class="badge bg-light-danger rounded-circle p-2"
                                                  :title="collection?.messages?.testFailed || 'Test failed'">
                                                <i class="feather icon-x" style="width: 16px; height: 16px;"></i>
                                            </span>
                                            <span v-else
                                                  class="badge bg-light-info rounded-circle p-2"
                                                  :title="collection?.messages?.completed || 'Completed'">
                                                <i class="feather icon-check-circle" style="width: 16px; height: 16px;"></i>
                                            </span>
                                        </td>

                                        <!-- Code -->
                                        <td>
                                            <strong>{{ statement.subcode }}</strong>
                                        </td>

                                        <!-- Statement -->
                                        <td>
                                            <small>{{ statement["content_" + locale].substring(0, 80) }}{{ statement["content_" + locale].length > 80 ? '...' : '' }}</small>
                                        </td>

                                        <!-- Method -->
                                        <td>
                                            <span class="badge bg-light-info">{{ statement.test_method }}</span>
                                        </td>

                                        <!-- Outcome -->
                                        <td>
                                            <span v-if="statement.review_status_id == 2" class="badge bg-success">
                                                <i class="feather icon-check"></i> {{ collection?.messages?.passed || 'Passed' }}
                                            </span>
                                            <span v-else-if="statement.review_status_id == 3" class="badge bg-danger">
                                                <i class="feather icon-x"></i> {{ collection?.messages?.failed || 'Failed' }}
                                            </span>
                                            <span v-else class="text-muted">
                                                <small>{{ collection?.messages?.pending || 'Pending' }}</small>
                                            </span>
                                        </td>

                                        <!-- Actions -->
                                        <td>
                                            <button
                                                class="btn btn-sm btn-primary"
                                                @click="openQuickTest(statement)">
                                                <i class="feather icon-edit-2"></i>
                                                {{ statement.test_status === 'completed' ? (collection?.messages?.edit || 'Edit') : (collection?.messages?.test || 'Test') }}
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Hidden: Original Accordion (backup) -->
                    <div v-if="false" class="accordion" id="testConductAccordion">
                        <div v-for="statement in filteredTestStatements" :key="statement.id" class="accordion-item mb-2">
                            <h2 class="accordion-header" :id="`conductHeading${statement.id}`">
                                <button
                                    class="accordion-button collapsed"
                                    type="button"
                                    data-bs-toggle="collapse"
                                    :data-bs-target="`#conductCollapse${statement.id}`"
                                    aria-expanded="false"
                                    :aria-controls="`conductCollapse${statement.id}`"
                                >
                                    <div class="d-flex justify-content-between w-100 align-items-center">
                                        <span>
                                            <strong>{{ statement.subcode }}</strong> - {{ statement["content_" + locale] }}
                                        </span>
                                        <span :class="`badge bg-${getStatusColor(statement.test_status)} ms-2`">
                                            {{ getStatusText(statement.test_status) }}
                                        </span>
                                    </div>
                                </button>
                            </h2>
                            <div
                                :id="`conductCollapse${statement.id}`"
                                class="accordion-collapse collapse"
                                :aria-labelledby="`conductHeading${statement.id}`"
                                data-bs-parent="#testConductAccordion"
                            >
                                <div class="accordion-body">
                                    <!-- Test Plan Info (Read-only) -->
                                    <div class="mb-3 p-3 bg-light rounded">
                                        <h6 class="text-primary">{{ collection?.messages?.testPlan || 'Test Plan' }}</h6>
                                        <p class="mb-2"><strong>{{ collection?.messages?.testMethod || 'Method' }}:</strong> {{ statement.test_method }}</p>
                                        <p class="mb-0"><strong>{{ collection?.messages?.plan || 'Plan' }}:</strong> {{ statement.test_plan }}</p>
                                    </div>

                                    <!-- Test Status Selection -->
                                    <div class="mb-3">
                                        <label class="form-label" :for="`testStatus${statement.id}`">
                                            <strong>{{ collection?.messages?.testStatus || 'Test Status' }}</strong>
                                        </label>
                                        <select
                                            :id="`testStatus${statement.id}`"
                                            class="form-select"
                                            v-model="statement.test_status"
                                            @change="markStatementChanged(statement.id)"
                                        >
                                            <option value="planned">{{ collection?.messages?.planned || 'Planned' }}</option>
                                            <option value="in_progress">{{ collection?.messages?.inProgress || 'In Progress' }}</option>
                                            <option value="completed">{{ collection?.messages?.completed || 'Completed' }}</option>
                                        </select>
                                    </div>

                                    <!-- Review Status Selection (Godkänd/Ej godkänd) -->
                                    <div class="mb-3">
                                        <label class="form-label" :for="`reviewStatus${statement.id}`">
                                            <strong>{{ collection?.messages?.testOutcome || 'Test Outcome' }}</strong>
                                        </label>
                                        <select
                                            :id="`reviewStatus${statement.id}`"
                                            class="form-select"
                                            v-model="statement.review_status_id"
                                            @change="markStatementChanged(statement.id)"
                                        >
                                            <option value="">{{ collection?.messages?.pleaseSelect || 'Please select' }}</option>
                                            <option value="2">✓ {{ collection?.messages?.testPassed || 'Test Passed - No deviations found' }}</option>
                                            <option value="3">✗ {{ collection?.messages?.testFailed || 'Test Failed - Deviations found' }}</option>
                                        </select>
                                        <small class="form-text text-muted">{{ collection?.messages?.testOutcomeHelp || 'This will set the review status for this statement' }}</small>
                                    </div>

                                    <!-- Test Result Text Area -->
                                    <div class="mb-3">
                                        <label class="form-label" :for="`testResult${statement.id}`">
                                            <strong>{{ collection?.messages?.testResult || 'Test Result' }}</strong>
                                        </label>
                                        <textarea
                                            :id="`testResult${statement.id}`"
                                            class="form-control"
                                            rows="4"
                                            v-model="statement.test_result"
                                            @input="markStatementChanged(statement.id)"
                                            :placeholder="collection?.messages?.testResultPlaceholder || 'Describe the test results. What did you find? Was the control working as expected?'"
                                        ></textarea>
                                    </div>

                                    <!-- Test Evidence Text Area -->
                                    <div class="mb-3">
                                        <label class="form-label" :for="`testEvidence${statement.id}`">
                                            <strong>{{ collection?.messages?.testEvidence || 'Evidence / Documentation' }}</strong>
                                        </label>
                                        <textarea
                                            :id="`testEvidence${statement.id}`"
                                            class="form-control"
                                            rows="3"
                                            v-model="statement.test_evidence"
                                            @input="markStatementChanged(statement.id)"
                                            :placeholder="collection?.messages?.testEvidencePlaceholder || 'Reference to evidence, such as file names, log entries, screenshots, or documentation reviewed.'"
                                        ></textarea>
                                    </div>

                                    <!-- Test Date Display -->
                                    <div v-if="statement.test_date" class="mb-3">
                                        <p class="text-muted mb-0">
                                            <i class="feather icon-calendar"></i>
                                            <strong>{{ collection?.messages?.lastTested || 'Last tested' }}:</strong>
                                            {{ formatDate(statement.test_date) }}
                                        </p>
                                    </div>

                                    <!-- Save Button -->
                                    <div class="d-flex justify-content-end">
                                        <button
                                            type="button"
                                            class="btn btn-primary"
                                            :id="`saveConductBtn${statement.id}`"
                                            @click="saveTestConduct(statement)"
                                            :disabled="!isStatementChanged(statement.id)"
                                        >
                                            <i class="feather icon-save"></i> {{ collection?.messages?.save || 'Save' }}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" @click="testConductHide">
                        {{ collection?.messages?.close || 'Close' }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import Swal from "sweetalert2";

export default {
    name: "TestConduct",
    props: ["actionId", "collection", "locale", "org"],
    data() {
        return {
            testStatements: [],
            changedStatements: new Set(),
            statusFilter: 'all',
            currentStatement: null,
            quickTestData: {
                review_status_id: null,
                test_result: '',
                test_evidence: '',
            },
            selectedFile: null,
            uploadingFile: false,
            currentStatementAttachments: [],
        };
    },
    computed: {
        filteredTestStatements() {
            if (this.statusFilter === 'all') {
                return this.testStatements;
            }
            return this.testStatements.filter(s => s.test_status === this.statusFilter);
        }
    },
    methods: {
        testConductShow() {
            this.loadTestStatements();
            $("#testConductModal").modal("show");
        },
        testConductHide() {
            this.changedStatements.clear();
            this.statusFilter = 'all';
            $("#testConductModal").modal("hide");
            // Rebuild parent to update statistics
            if (this.$parent && this.$parent.rebuild) {
                this.$parent.rebuild();
            }
        },
        loadTestStatements() {
            const thisComponent = this;
            axios
                .get(`/${this.locale}/axios/organisations/review/action/${this.actionId}/test`)
                .then(function (response) {
                    // Only show statements that have a test plan
                    thisComponent.testStatements = response.data.testStatements.filter(s => s.test_plan);
                })
                .catch(function (error) {
                    console.log(error);
                    Swal.fire({
                        title: "Error!",
                        text: error.response?.data?.message || "Failed to load test statements",
                        icon: "error",
                        customClass: {
                            confirmButton: "btn btn-primary",
                        },
                        buttonsStyling: false,
                    });
                });
        },
        markStatementChanged(statementId) {
            this.changedStatements.add(statementId);
        },
        isStatementChanged(statementId) {
            return this.changedStatements.has(statementId);
        },
        getCountByStatus(status) {
            return this.testStatements.filter(s => s.test_status === status).length;
        },
        saveTestConduct(statement) {
            const thisComponent = this;

            Swal.fire({
                title: "Info!",
                text: `${thisComponent.collection?.messages?.working || 'Working'} ...`,
                icon: "info",
                showConfirmButton: false,
                allowOutsideClick: false,
                allowEscapeKey: false,
                didOpen: () => {
                    Swal.showLoading();
                },
            });

            axios
                .post(`/${this.locale}/axios/organisations/test-conduct/update`, {
                    statement_id: statement.id,
                    test_result: statement.test_result,
                    test_evidence: statement.test_evidence,
                    test_status: statement.test_status,
                    review_status_id: statement.review_status_id,
                })
                .then(function (response) {
                    Swal.close();
                    thisComponent.changedStatements.delete(statement.id);

                    // Reload to get updated test_date
                    thisComponent.loadTestStatements();

                    Swal.fire({
                        title: thisComponent.collection?.messages?.success || "Success",
                        text: thisComponent.collection?.messages?.testConductSaved || "Test results saved successfully",
                        icon: "success",
                        customClass: {
                            confirmButton: "btn btn-primary",
                        },
                        buttonsStyling: false,
                    });
                })
                .catch(function (error) {
                    Swal.close();
                    Swal.fire({
                        title: thisComponent.collection?.messages?.error || "Error",
                        text: error.response?.data?.message || "Failed to save test results",
                        icon: "error",
                        customClass: {
                            confirmButton: "btn btn-primary",
                        },
                        buttonsStyling: false,
                    });
                });
        },
        getStatusColor(status) {
            const colors = {
                'planned': 'info',
                'in_progress': 'warning',
                'completed': 'success'
            };
            return colors[status] || 'secondary';
        },
        getStatusText(status) {
            const texts = {
                'planned': this.collection?.messages?.planned || 'Planned',
                'in_progress': this.collection?.messages?.inProgress || 'In Progress',
                'completed': this.collection?.messages?.completed || 'Completed'
            };
            return texts[status] || status;
        },
        formatDate(dateString) {
            if (!dateString) return '';
            const date = new Date(dateString);
            return date.toLocaleString(this.locale === 'se' ? 'sv-SE' : 'en-US', {
                year: 'numeric',
                month: 'long',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
        },
        openQuickTest(statement) {
            // Store current statement
            this.currentStatement = statement;

            // Pre-fill data if already completed
            this.quickTestData = {
                review_status_id: statement.review_status_id || null,
                test_result: statement.test_result || '',
                test_evidence: statement.test_evidence || '',
            };

            // Load attachments for this statement
            this.loadAttachments(statement.id);

            // Open quick test modal with proper z-index handling
            const quickModal = new bootstrap.Modal(document.getElementById('quickTestModal'), {
                backdrop: true,
                keyboard: true
            });
            quickModal.show();

            // Ensure the backdrop is properly positioned above parent modal
            setTimeout(() => {
                const backdrops = document.querySelectorAll('.modal-backdrop');
                if (backdrops.length > 0) {
                    const lastBackdrop = backdrops[backdrops.length - 1];
                    lastBackdrop.style.zIndex = '1055';
                }
                document.getElementById('quickTestModal').style.zIndex = '1060';
            }, 10);
        },
        saveQuickTest() {
            if (!this.quickTestData.review_status_id || !this.quickTestData.test_result) {
                Swal.fire({
                    title: this.collection?.messages?.error || "Error",
                    text: this.collection?.messages?.pleaseCompleteAllFields || "Please complete all required fields",
                    icon: "error",
                    customClass: {
                        confirmButton: "btn btn-primary",
                    },
                    buttonsStyling: false,
                });
                return;
            }

            const thisComponent = this;

            Swal.fire({
                title: "Info!",
                text: `${thisComponent.collection?.messages?.working || 'Working'} ...`,
                icon: "info",
                showConfirmButton: false,
                allowOutsideClick: false,
                allowEscapeKey: false,
                didOpen: () => {
                    Swal.showLoading();
                },
            });

            axios
                .post(`/${this.locale}/axios/organisations/test-conduct/update`, {
                    statement_id: this.currentStatement.id,
                    test_result: this.quickTestData.test_result,
                    test_evidence: this.quickTestData.test_evidence,
                    test_status: 'completed',
                    review_status_id: this.quickTestData.review_status_id,
                })
                .then(function (response) {
                    Swal.close();

                    // Close quick test modal properly
                    const quickModalEl = document.getElementById('quickTestModal');
                    const quickModal = bootstrap.Modal.getInstance(quickModalEl);
                    if (quickModal) {
                        quickModal.hide();
                    }

                    // Reload to get updated data
                    thisComponent.loadTestStatements();

                    Swal.fire({
                        title: thisComponent.collection?.messages?.success || "Success",
                        text: thisComponent.collection?.messages?.testConductSaved || "Test results saved successfully",
                        icon: "success",
                        customClass: {
                            confirmButton: "btn btn-primary",
                        },
                        buttonsStyling: false,
                    });
                })
                .catch(function (error) {
                    Swal.close();
                    Swal.fire({
                        title: thisComponent.collection?.messages?.error || "Error",
                        text: error.response?.data?.message || "Failed to save test results",
                        icon: "error",
                        customClass: {
                            confirmButton: "btn btn-primary",
                        },
                        buttonsStyling: false,
                    });
                });
        },
        getRowClass(statement) {
            // Return CSS class for row based on test status
            if (statement.test_status === 'completed' && statement.review_status_id == 2) {
                return 'table-success'; // Passed
            } else if (statement.test_status === 'completed' && statement.review_status_id == 3) {
                return 'table-danger'; // Failed
            } else if (statement.test_status === 'in_progress') {
                return 'table-warning'; // In progress
            }
            return ''; // Default (planned or not started)
        },
        handleFileSelect(event) {
            this.selectedFile = event.target.files[0];
        },
        async uploadFile() {
            if (!this.selectedFile || !this.currentStatement) {
                return;
            }

            this.uploadingFile = true;

            const formData = new FormData();
            formData.append('file', this.selectedFile);
            formData.append('statement_id', this.currentStatement.id);

            try {
                const response = await axios.post(
                    `/${this.locale}/axios/organisations/test-attachments/upload`,
                    formData,
                    {
                        headers: {
                            'Content-Type': 'multipart/form-data'
                        }
                    }
                );

                // Add to attachments list
                this.currentStatementAttachments.unshift(response.data.attachment);

                // Clear file input
                this.selectedFile = null;
                if (this.$refs.fileInput) {
                    this.$refs.fileInput.value = '';
                }

                Swal.fire({
                    title: this.collection?.messages?.success || "Success",
                    text: this.collection?.messages?.fileUploadedSuccessfully || "File uploaded successfully",
                    icon: "success",
                    timer: 2000,
                    showConfirmButton: false
                });

            } catch (error) {
                Swal.fire({
                    title: this.collection?.messages?.error || "Error",
                    text: error.response?.data?.message || "Failed to upload file",
                    icon: "error",
                    customClass: {
                        confirmButton: "btn btn-primary",
                    },
                    buttonsStyling: false,
                });
            } finally {
                this.uploadingFile = false;
            }
        },
        async loadAttachments(statementId) {
            try {
                const response = await axios.get(
                    `/${this.locale}/axios/organisations/test-attachments/${statementId}`
                );
                this.currentStatementAttachments = response.data.attachments;
            } catch (error) {
                console.error('Failed to load attachments:', error);
                this.currentStatementAttachments = [];
            }
        },
        async deleteAttachment(attachmentId) {
            const result = await Swal.fire({
                title: this.collection?.messages?.confirmDeleteFile || 'Are you sure?',
                text: this.collection?.messages?.confirmDeleteFile || 'Are you sure you want to delete this file?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: this.collection?.messages?.yes || 'Yes',
                cancelButtonText: this.collection?.messages?.cancel || 'Cancel',
                customClass: {
                    confirmButton: 'btn btn-danger',
                    cancelButton: 'btn btn-secondary'
                },
                buttonsStyling: false
            });

            if (!result.isConfirmed) {
                return;
            }

            try {
                await axios.delete(
                    `/${this.locale}/axios/organisations/test-attachments/${attachmentId}`
                );

                // Remove from list
                this.currentStatementAttachments = this.currentStatementAttachments.filter(
                    a => a.id !== attachmentId
                );

                Swal.fire({
                    title: this.collection?.messages?.success || "Success",
                    text: this.collection?.messages?.fileDeletedSuccessfully || "File deleted successfully",
                    icon: "success",
                    timer: 2000,
                    showConfirmButton: false
                });

            } catch (error) {
                Swal.fire({
                    title: this.collection?.messages?.error || "Error",
                    text: error.response?.data?.message || "Failed to delete file",
                    icon: "error",
                    customClass: {
                        confirmButton: "btn btn-primary",
                    },
                    buttonsStyling: false,
                });
            }
        },
    },
};
</script>

<style scoped>
.accordion-button:not(.collapsed) {
    background-color: #f8f9fa;
}

.btn-check:checked + .btn {
    font-weight: bold;
}

/* Ensure quick test modal appears above the main modal */
#quickTestModal {
    z-index: 1060 !important;
}

#quickTestModal .modal-backdrop {
    z-index: 1055 !important;
}
</style>
