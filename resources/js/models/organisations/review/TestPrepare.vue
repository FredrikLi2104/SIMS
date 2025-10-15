<template>
    <div class="modal fade" id="testPrepareModal" tabindex="-1" aria-labelledby="testPrepareModal" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ collection?.messages?.test }} {{ collection?.messages?.prepare }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" @click="testPrepareHide"></button>
                </div>
                <div class="modal-body">
                    <p>{{ collection?.messages?.testPrepareDescription || 'Plan how you will test each statement. Describe the test method and what you intend to verify.' }}</p>

                    <!-- Test Statements List -->
                    <div v-if="testStatements.length === 0" class="alert alert-info">
                        <i class="feather icon-info"></i> {{ collection?.messages?.noTestStatementsAvailable || 'No statements are marked for testing. Please set statements to "Test" plan type first.' }}
                    </div>

                    <div v-else class="accordion" id="testStatementsAccordion">
                        <div v-for="(statement, index) in testStatements" :key="statement.id" class="accordion-item mb-2">
                            <h2 class="accordion-header" :id="`heading${statement.id}`">
                                <button
                                    class="accordion-button collapsed"
                                    type="button"
                                    data-bs-toggle="collapse"
                                    :data-bs-target="`#collapse${statement.id}`"
                                    aria-expanded="false"
                                    :aria-controls="`collapse${statement.id}`"
                                >
                                    <div class="d-flex justify-content-between w-100 align-items-center">
                                        <span>
                                            <strong>{{ statement.subcode }}</strong> - {{ statement["content_" + locale] }}
                                        </span>
                                        <span v-if="statement.test_status" :class="`badge bg-${getStatusColor(statement.test_status)} ms-2`">
                                            {{ getStatusText(statement.test_status) }}
                                        </span>
                                    </div>
                                </button>
                            </h2>
                            <div
                                :id="`collapse${statement.id}`"
                                class="accordion-collapse collapse"
                                :aria-labelledby="`heading${statement.id}`"
                                data-bs-parent="#testStatementsAccordion"
                            >
                                <div class="accordion-body">
                                    <!-- Statement Details -->
                                    <div class="mb-3 p-2 bg-light rounded">
                                        <p class="mb-1"><strong>{{ collection?.messages?.statement }}:</strong> {{ statement["content_" + locale] }}</p>
                                        <p class="mb-0"><strong>{{ collection?.messages?.desc }}:</strong> {{ statement["desc_" + locale] }}</p>
                                    </div>

                                    <!-- Test Method Selection -->
                                    <div class="mb-3">
                                        <label class="form-label" :for="`testMethod${statement.id}`">
                                            <strong>{{ collection?.messages?.testMethod || 'Test Method' }}</strong>
                                        </label>
                                        <select
                                            :id="`testMethod${statement.id}`"
                                            class="form-select"
                                            v-model="statement.test_method"
                                            @change="markStatementChanged(statement.id)"
                                        >
                                            <option value="">{{ collection?.messages?.pleaseSelect || 'Please select' }}</option>
                                            <option value="Sampling">{{ collection?.messages?.sampling || 'Sampling' }}</option>
                                            <option value="Log Review">{{ collection?.messages?.logReview || 'Log Review' }}</option>
                                            <option value="System Test">{{ collection?.messages?.systemTest || 'System Test' }}</option>
                                            <option value="Process Walkthrough">{{ collection?.messages?.processWalkthrough || 'Process Walkthrough' }}</option>
                                            <option value="Documentation Review">{{ collection?.messages?.documentationReview || 'Documentation Review' }}</option>
                                            <option value="Other">{{ collection?.messages?.other || 'Other' }}</option>
                                        </select>
                                    </div>

                                    <!-- Test Plan Text Area -->
                                    <div class="mb-3">
                                        <label class="form-label" :for="`testPlan${statement.id}`">
                                            <strong>{{ collection?.messages?.testPlan || 'Test Plan' }}</strong>
                                        </label>
                                        <textarea
                                            :id="`testPlan${statement.id}`"
                                            class="form-control"
                                            rows="4"
                                            v-model="statement.test_plan"
                                            @input="markStatementChanged(statement.id)"
                                            :placeholder="collection?.messages?.testPlanPlaceholder || 'Describe how you plan to test this statement. For example: Take a sample of 10 employees training certificates, or review access logs for department X during one week.'"
                                        ></textarea>
                                    </div>

                                    <!-- Save Button -->
                                    <div class="d-flex justify-content-end">
                                        <button
                                            type="button"
                                            class="btn btn-primary"
                                            :id="`saveBtn${statement.id}`"
                                            @click="saveTestPlan(statement)"
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
                    <button type="button" class="btn btn-secondary" @click="testPrepareHide">
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
    name: "TestPrepare",
    props: ["actionId", "collection", "locale", "org"],
    data() {
        return {
            testStatements: [],
            changedStatements: new Set(),
        };
    },
    methods: {
        testPrepareShow() {
            this.loadTestStatements();
            $("#testPrepareModal").modal("show");
        },
        testPrepareHide() {
            this.changedStatements.clear();
            $("#testPrepareModal").modal("hide");
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
                    thisComponent.testStatements = response.data.testStatements;
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
        saveTestPlan(statement) {
            const thisComponent = this;

            if (!statement.test_method || !statement.test_plan) {
                Swal.fire({
                    title: thisComponent.collection?.messages?.error || "Error",
                    text: thisComponent.collection?.messages?.testMethodAndPlanRequired || "Both test method and test plan are required",
                    icon: "error",
                    customClass: {
                        confirmButton: "btn btn-primary",
                    },
                    buttonsStyling: false,
                });
                return;
            }

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
                .post(`/${this.locale}/axios/organisations/test-plans/store`, {
                    statement_id: statement.id,
                    test_plan: statement.test_plan,
                    test_method: statement.test_method,
                })
                .then(function (response) {
                    Swal.close();
                    thisComponent.changedStatements.delete(statement.id);
                    statement.test_status = 'planned';

                    Swal.fire({
                        title: thisComponent.collection?.messages?.success || "Success",
                        text: thisComponent.collection?.messages?.testPlanSaved || "Test plan saved successfully",
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
                        text: error.response?.data?.message || "Failed to save test plan",
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
    },
};
</script>

<style scoped>
.accordion-button:not(.collapsed) {
    background-color: #f8f9fa;
}
</style>
