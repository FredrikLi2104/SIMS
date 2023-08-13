<template>
    <div class="row">
        <div class="col-12">
            <div class="row">
                <div class="col-md-4 col-sm-6 col-12 kb-search-content ng-star-inserted">
                    <div class="card">
                        <div class="icon-wrapper text-center mt-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-airplay">
                                <path d="M5 17H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2h-1"></path>
                                <polygon points="12 15 17 21 7 21 12 15"></polygon>
                            </svg>
                        </div>
                        <div class="card-body text-center">
                            <h4>{{ plans[0]["name_" + locale] }}</h4>
                            <p class="text-body mt-1 mb-0" style="height: 7.2rem">{{ plans[0]["desc_" + locale] }}</p>
                            <button class="btn btn-primary mt-1" @click="showInterview">{{ collection?.messages?.show }}</button>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 col-sm-6 col-12 kb-search-content ng-star-inserted">
                    <div class="card">
                        <div class="icon-wrapper text-center mt-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit-3">
                                <path d="M12 20h9"></path>
                                <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path>
                            </svg>
                        </div>
                        <div class="card-body text-center">
                            <h4>{{ plans[1]["name_" + locale] }}</h4>
                            <p class="text-body mt-1 mb-0" style="height: 7.2rem">{{ plans[1]["desc_" + locale] }}</p>
                            <button class="btn btn-primary mt-1" disabled>{{ collection?.messages?.show }}</button>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="row pb-50">
                                <div class="mb-1 mb-lg-0">
                                    <h2 class="font-weight-bolder mb-25">{{ collection?.messages?.task }} {{ collection?.messages?.review }} {{ collection?.messages?.details }}</h2>
                                </div>
                            </div>
                            <hr />
                            <div class="row avg-sessions pt-50">
                                <div v-for="(plan, planId) in auditorStatements.planned" :key="planId" class="col-6 mb-2">
                                    <div class="progresss-wrapper mb-2" style="height: 14px">
                                        <div class="mb-1">{{ plans[planId - 1]["name_" + locale] }}: {{ plan["thisCount"] }} / {{ plan["totalCount"] }}</div>
                                        <div :class="plan['class']" style="height: 12px">
                                            <div class="progress-bar" role="progressbar" :aria-valuenow="plan['percentage']" aria-valuemin="100" aria-valuemax="100" showValue="true" :style="`width: ${plan['percentage']}%`">{{ plan["percentage"] }}%</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6 mb-2">
                                    <div class="progresss-wrapper mb-2" style="height: 6px">
                                        <div>{{ collection?.messages?.unplanned }}: {{ 45 }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 col-sm-6 col-12 kb-search-content ng-star-inserted">
                    <div class="card">
                        <div class="icon-wrapper text-center mt-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-file-text">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                <polyline points="14 2 14 8 20 8"></polyline>
                                <line x1="16" y1="13" x2="8" y2="13"></line>
                                <line x1="16" y1="17" x2="8" y2="17"></line>
                                <polyline points="10 9 9 9 8 9"></polyline>
                            </svg>
                        </div>
                        <div class="card-body text-center">
                            <h4>{{ plans[2]["name_" + locale] }}</h4>
                            <p class="text-body mt-1 mb-0" style="height: 10rem">{{ plans[2]["desc_" + locale] }}</p>
                            <button class="btn btn-primary mt-1" disabled>{{ collection?.messages?.show }}</button>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 col-sm-6 col-12 kb-search-content ng-star-inserted">
                    <div class="card">
                        <div class="icon-wrapper text-center mt-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-check-circle">
                                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                                <polyline points="22 4 12 14.01 9 11.01"></polyline>
                            </svg>
                        </div>
                        <div class="card-body text-center">
                            <h4>{{ plans[4]["name_" + locale] }}</h4>
                            <p class="text-body mt-1 mb-0" style="height: 10rem">{{ plans[4]["desc_" + locale] }}</p>
                            <button class="btn btn-primary mt-1" disabled>{{ collection?.messages?.show }}</button>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 col-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between pb-0">
                            <h4 class="card-title">{{ collection?.messages?.taskCompletion }}</h4>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12 d-flex justify-content-center">
                                    <div id="taskCompletionChart"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card invoice-list-wrapper">
                <div class="card-datatable table-responsive">
                    <table class="invoice-list-table table" id="dataTable"></table>
                </div>
            </div>
        </div>
        <!-- Modals -->
        <!-- Interview -->
        <div class="modal fade col-12 full-width" id="interviewModal" tabindex="-1" aria-labelledby="interviewModal" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ collection?.messages?.interview }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" @click="interviewHide"></button>
                    </div>
                    <div class="modal-body">
                        <p>{{ collection?.messages.interview }}</p>
                        <div class="col-12 mb-1 mb-lg-0 text-center">
                            <!-- button group checkbox -->
                            <div class="btn-group" role="group" aria-label="Basic checkbox toggle button group">
                                <input type="checkbox" class="btn-check" id="btncheck1" :checked="interviewActive == 'prepare' ? true : false" autocomplete="off" @click="interviewActive = 'prepare'" />
                                <label class="btn btn-primary" for="btncheck1">{{ collection?.messages?.interviewPrepare }}</label>
                                <input type="checkbox" class="btn-check" id="btncheck2" :checked="interviewActive == 'conduct' ? true : false" autocomplete="off" @click="interviewActiveSet('conduct')" />
                                <label class="btn btn-primary" for="btncheck2">{{ collection?.messages?.interviewConduct }}</label>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer justify-content-start">
                        <div class="row" v-if="interviewActive == 'prepare'">
                            <div class="col-6">
                                <div class="mb-1">
                                    <label class="form-label" for="agenda">{{ collection?.messages?.agenda }}</label>
                                    <textarea :class="`form-control ${interviewCreateErrors.agenda ? 'is-invalid' : ''}`" id="agenda" rows="3" placeholder="Interview Agenda..."></textarea>
                                    <div class="invalid-feedback">{{ interviewCreateErrors.agenda?.message }}</div>
                                </div>
                                <div class="mb-1">
                                    <label class="form-label" for="user_id">{{ collection?.messages?.interviewee }}</label>
                                    <select :class="`form-select ${interviewCreateErrors.user ? 'is-invalid' : ''}`" id="user_id">
                                        <option v-for="user in interviewStatements.users" :key="user" :value="user.id">{{ user.name }}</option>
                                    </select>
                                    <div class="invalid-feedback">{{ interviewCreateErrors.user?.message }}</div>
                                </div>
                                <div class="mb-1">
                                    <label class="form-label" for="interviewStatementsSelect">{{ collection?.messages?.statements }}</label>
                                    <select :class="`form-select ${interviewCreateErrors.statements ? 'is-invalid' : ''}`" id="interviewStatementsSelect" multiple="multiple" disabled style="height: 250px">
                                        <option v-for="interviewStatementSelected in interviewStatementsSelected" :key="interviewStatementSelected" :value="interviewStatementSelected.id">
                                            {{ interviewStatementSelected["content_" + locale].substr(0, 36) }}
                                        </option>
                                    </select>
                                    <div class="invalid-feedback">{{ interviewCreateErrors.statements?.message }}</div>
                                </div>
                                <button type="button" class="btn btn-primary" @click="interviewCreate">{{ collection?.messages?.create }}</button>
                            </div>
                            <div class="col-6">
                                <div class="accordion accordion-margin mt-2" id="interviewStatements">
                                    <div v-for="interviewStatement in interviewStatements.interviewStatements" :key="interviewStatement" class="card accordion-item">
                                        <div class="row">
                                            <div class="col-8">
                                                <button type="button" class="btn btn-outline-success" @click="interviewStatementAdd(interviewStatement)">+ {{ collection?.messages?.add }} {{ collection?.messages?.statement }}</button>
                                                <button v-if="interviewStatementRemovable(interviewStatement)" type="button" class="btn btn-outline-danger" @click="interviewStatementRemove(interviewStatement)">- {{ collection?.messages?.remove }} {{ collection?.messages?.statement }}</button>
                                            </div>
                                            <div class="col-4 d-flex justify-content-end align-items-center">
                                                <span :class="`badge rounded-pill badge-glow bg-${interviewStatement.class}`" style="height: 1.5rem">{{ interviewStatement.reviewStatus }}</span>
                                            </div>
                                        </div>
                                        <h2 class="accordion-header" :id="`interviewStatementHeader${interviewStatement?.id}`">
                                            <button class="accordion-button collapsed" data-bs-toggle="collapse" role="button" :data-bs-target="`#interviewStatement${interviewStatement?.id}`" aria-expanded="false" :aria-controls="`interviewStatement${interviewStatement?.id}`">
                                                {{ interviewStatement["content_" + locale] }}
                                            </button>
                                        </h2>
                                        <div :id="`interviewStatement${interviewStatement?.id}`" class="collapse accordion-collapse" :aria-labelledby="`interviewStatementHeader${interviewStatement?.id}`" data-bs-parent="interviewStatements">
                                            <div class="accordion-body">
                                                {{ interviewStatement["desc_" + locale] }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row" v-if="interviewActive == 'conduct'">
                            <div class="col-6">
                                <!--- Interviews Table-->
                                <div class="card">
                                    <div class="card-header">
                                        <h4 class="card-title">{{ collection?.messages?.tables }}</h4>
                                    </div>
                                    <div class="table">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>ID</th>
                                                    <th>{{ collection?.messages?.creator }}</th>
                                                    <th>{{ collection?.messages?.interviewee }}</th>
                                                    <th class="text-center">{{ collection?.messages?.actions }}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr v-for="inter in interviewStatements.interviews" :key="inter">
                                                    <td>
                                                        <span class="fw-bold">{{ inter.id }}</span>
                                                    </td>
                                                    <td>
                                                        {{ inter.creator }}
                                                    </td>
                                                    <td>
                                                        {{ inter.interviewee }}
                                                    </td>
                                                    <td class="text-center">
                                                        <button type="button" :class="`btn btn-relief-${inter.id == interviewExpanded.id ? 'warning' : 'dark'}`" @click="interviewConductedSet(inter.id)">
                                                            {{ inter.id == interviewExpanded.id ? collection?.messages?.expanded : collection?.messages?.expand }}
                                                        </button>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <!-- Agenda Display -->
                                <div class="card">
                                    <div class="card-header">
                                        <h4 class="card-title">{{ collection?.messages?.agenda }}</h4>
                                    </div>
                                    <div class="card-body">
                                        <div class="border p-3 rounded">
                                            {{ interviewExpanded?.agenda }}
                                        </div>
                                    </div>
                                </div>
                                <!-- Accordion for Interview Statements -->
                                <div class="accordion" :id="`interviewStatementsAccordion${interviewExpanded.id}`">
                                    <div v-for="statement in interviewExpanded.statements" :key="statement" class="card accordion-item">
                                        <h2 class="accordion-header" :id="`interviewStatementHeader${statement.id}`">
                                            <button class="accordion-button collapsed" data-bs-toggle="collapse" role="button" :data-bs-target="`#interviewStatement${statement.id}`" aria-expanded="false" :aria-controls="`interviewStatement${statement.id}`">
                                                {{ statement["content_" + locale] }}
                                            </button>
                                        </h2>
                                        <div :id="`interviewStatement${statement.id}`" class="collapse accordion-collapse" :aria-labelledby="`interviewStatementHeader${statement.id}`" :data-bs-parent="`#interviewStatementsAccordion${interviewExpanded.id}`">
                                            <div class="accordion-body">
                                                {{ statement["desc_" + locale] }}
                                            </div>
                                            <div class="card">
                                                <div class="card-header">
                                                    <h4 class="text-uppercase">{{ collection?.messages?.value }}</h4>
                                                </div>
                                                <div class="card-body">
                                                    <div :id="`interviewStatementValueSlider${interviewExpanded.id}_${statement.id}`" class="mt-1 mb-3"></div>
                                                    <div class="mb-3">
                                                        <label for="interviewReviewText{{ interviewExpanded.id }}_{{ statement.id }}" class="form-label">{{ collection?.messages?.review }}</label>
                                                        <textarea class="form-control" :id="`interviewReviewText${interviewExpanded.id}_${statement.id}`" :name="`interviewReviewText${interviewExpanded.id}_${statement.id}`" rows="4"></textarea>
                                                    </div>
                                                    <div class="mb-3">
                                                        <button type="button" class="btn btn-success w-25 me-2" @click="reviewUpdate('accept', statement.id, `interviewReviewText${interviewExpanded.id}_${statement.id}`, `interviewStatementValueSlider${interviewExpanded.id}_${statement.id}`, statement.latestDeed?.id )">{{ collection?.messages?.accept }}</button>
                                                        <button type="button" class="btn btn-danger w-25" @click="reviewUpdate('reject', statement.id, `interviewReviewText${interviewExpanded.id}_${statement.id}`, `interviewStatementValueSlider${interviewExpanded.id}_${statement.id}`, statement.latestDeed?.id)">{{ collection?.messages?.reject }}</button>
                                                    </div>
                                                    <p>{{ collection?.messages?.lastUpdated }}: {{ statement.latestDeed?.lastUpdated }}, {{ collection?.messages?.by }}: {{ statement.latestDeed?.user }}</p>
                                                    <p>{{ collection?.messages?.comment }}: {{ statement.latestDeed?.comment }}</p>
                                                    <p>{{ collection?.messages?.latestReview }}, {{ statement.latestReview?.user }}, {{ statement.latestReview?.lastUpdated }}: {{ statement.latestReview?.review }}</p>
                                                    <p :class="`text-${statement.latestReview?.class}`">{{ statement.latestReview?.review_status }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- XX -->
        <div class="modal fade text-start modal-primary" id="statementViewModal" tabindex="-1" aria-labelledby="statementViewLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-extra-wide">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="statementViewLabel">{{ collection?.messages?.statement }} {{ collection?.messages?.view }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" @click="statementViewHide"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="table-responsive">
                                <table class="table">
                                    <thead class="table-light">
                                        <tr>
                                            <th>{{ collection?.messages?.key }}</th>
                                            <th>{{ collection?.messages?.value }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>{{ collection?.messages?.code }}</td>
                                            <td>{{ statementActive?.component?.code }}</td>
                                        </tr>
                                        <tr>
                                            <td>{{ collection?.messages?.subcode }}</td>
                                            <td>{{ statementActive?.subcode }}</td>
                                        </tr>
                                        <tr>
                                            <td>{{ collection?.messages?.component }}</td>
                                            <td>{{ statementActive?.component[`name_${locale}`] }}</td>
                                        </tr>
                                        <tr>
                                            <td>{{ collection?.messages?.period }}</td>
                                            <td>{{ statementActive?.component?.organisation_period ? statementActive.component.organisation_period[`name_${locale}`] : null }}</td>
                                        </tr>
                                        <tr>
                                            <td>{{ collection?.messages?.statement }}</td>
                                            <td>{{ statementActive ? statementActive[`content_${locale}`] : null }}</td>
                                        </tr>
                                        <tr>
                                            <td>{{ collection?.messages?.desc }}</td>
                                            <td>{{ statementActive ? statementActive[`desc_${locale}`] : null }}</td>
                                        </tr>
                                        <tr>
                                            <td>K1</td>
                                            <td>{{ statementActive ? statementActive[`k1_${locale}`] : null }}</td>
                                        </tr>
                                        <tr>
                                            <td>K2</td>
                                            <td>{{ statementActive ? statementActive[`k2_${locale}`] : null }}</td>
                                        </tr>
                                        <tr>
                                            <td>K3</td>
                                            <td>{{ statementActive ? statementActive[`k3_${locale}`] : null }}</td>
                                        </tr>
                                        <tr>
                                            <td>K4</td>
                                            <td>{{ statementActive ? statementActive[`k4_${locale}`] : null }}</td>
                                        </tr>
                                        <tr>
                                            <td>K5</td>
                                            <td>{{ statementActive ? statementActive[`k5_${locale}`] : null }}</td>
                                        </tr>
                                        <tr>
                                            <td>{{ collection?.messages?.plan }}</td>
                                            <td>{{ statementActive?.plan ? statementActive.plan[`name_${locale}`] : null }}</td>
                                        </tr>

                                        <tr>
                                            <td>{{ collection?.messages?.guide }}</td>
                                            <td>{{ statementActive ? statementActive[`guide_${locale}`] : null }}</td>
                                        </tr>
                                        <tr>
                                            <td>{{ collection?.messages?.implementation }}</td>
                                            <td>{{ statementActive ? statementActive.implementation : null }}</td>
                                        </tr>
                                        <tr>
                                            <td>{{ collection?.messages?.value }}</td>
                                            <td>{{ statementActive?.deed ? statementActive.deed.value : null }}</td>
                                        </tr>
                                        <tr>
                                            <td>{{ collection?.messages?.comment }}</td>
                                            <td>{{ statementActive?.deed ? statementActive.deed.comment : null }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-primary" @click="statementViewHide">Ok</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import Swal from "sweetalert2";
export default {
    props: ["auditorStatements", "locale", "org", "plans", "reviewStatuses", "actionId"],
    data() {
        return {
            interviewActive: "prepare",
            interviewCreateErrors: {
                agenda: null,
                user: null,
                statements: null,
            },
            interviewExpanded: {
                id: null,
                agenda: null,
                statements: [],
            },
            interviewStatements: {
                interviewStatements: [],
                users: [],
                interviews: [],
            },
            interviewStatementsSelected: [],
            dataTable: null,
            collection: null,
            statementActive: null,
            status: null,
            review: null,
            toUpdate: [],
        };
    },
    methods: {
        buildTable() {
            var thisComponent = this;
            let header = `
             <thead>
                <tr>
                    <th>${thisComponent.collection?.messages?.statement}</th>
                    <th>${thisComponent.collection?.messages?.guide} ${thisComponent.collection?.messages?.example}</th>
                    <th>${thisComponent.collection?.messages?.guide} ${thisComponent.collection?.messages?.plan}</th>
                    <th>${thisComponent.collection?.messages?.review} ${thisComponent.collection?.messages?.plan}</th>
                    <th>${thisComponent.collection?.messages?.implementation}</th>
                    <th>${thisComponent.collection?.messages?.value}</th>
                    <th>${thisComponent.collection?.messages?.comment}</th>
                    <th>${thisComponent.collection?.messages?.status}</th>
                    <th>${thisComponent.collection?.messages?.review}</th>
                    <th class="text-center">${thisComponent.collection?.messages?.actions}</th>
                </tr>
            </thead>
            `;
            document.getElementById("dataTable").innerHTML = header;
            var dataSource = thisComponent.collection.statements;
            thisComponent.dataTable = $(".invoice-list-table").DataTable({
                data: dataSource,
                createdRow: function (row, data, dataIndex) {
                    //$(row).addClass("row-auth-bg");
                },
                lengthMenu: [],
                paging: false,
                autoWidth: true,
                searching: true,
                columns: [{ data: "id" }, { data: "plan" }, { data: "guide_" + thisComponent.locale }, { data: "implementation" }, { data: "deed" }, { data: "deed" }],
                columnDefs: [
                    {
                        // statement
                        targets: 0,
                        responsivePriority: 0,
                        width: "30%",
                        render: function (data, type, full, meta) {
                            let x = `${full.subcode + " - "}`;
                            x += eval(`full.component?.name_` + thisComponent.locale);
                            x += " - ";
                            x += eval(`full.content_` + thisComponent.locale);
                            x += " - ";
                            x += eval(`full.desc_` + thisComponent.locale);
                            if (type === "sort") {
                                return x;
                            } else {
                                let r = `<p>${x}</p>`;
                                return r;
                            }
                        },
                    },
                    {
                        // guide example
                        targets: 1,
                        responsivePriority: 1,
                        width: "10%",
                        render: function (data, type, full, meta) {
                            let r = `<p>${eval("full.guide_" + thisComponent.locale)}</p>`;
                            return r;
                        },
                    },
                    {
                        // guide plan
                        targets: 2,
                        responsivePriority: 2,
                        width: "10%",
                        render: function (data, type, full, meta) {
                            let p = ``;
                            p = full.guide;
                            let r = `<p>${p}</p>`;
                            return r;
                        },
                    },
                    {
                        // review type (plan)
                        targets: 3,
                        responsivePriority: 3,
                        width: "10%",
                        render: function (data, type, full, meta) {
                            let r = `<p>${eval("full.plan.name_" + thisComponent.locale)}</p>`;
                            return r;
                        },
                    },
                    {
                        // implementation
                        targets: 4,
                        responsivePriority: 4,
                        width: "10%",
                        render: function (data, type, full, meta) {
                            let r = `<p>${full.implementation ? full.implementation : ""}</p>`;
                            return r;
                        },
                    },
                    {
                        // value
                        targets: 5,
                        responsivePriority: 5,
                        width: "10%",
                        render: function (data, type, full, meta) {
                            if (type === "sort") {
                                let x = full.deed ? full.deed.value : "";
                                return x;
                            } else {
                                let r = `<p>${full.deed?.value ? full.deed.value : ""}</p>`;
                                return r;
                            }
                        },
                    },
                    {
                        // comment
                        targets: 6,
                        responsivePriority: 6,
                        width: "10%",
                        render: function (data, type, full, meta) {
                            if (type === "sort") {
                                let x = full.deed ? full.deed.comment : "";
                                return x;
                            } else {
                                let r = `<p>${full.deed?.comment ? full.deed.comment : ""}</p>`;
                                return r;
                            }
                        },
                    },
                    {
                        // status
                        targets: 7,
                        responsivePriority: 7,
                        width: "10%",
                        orderable: false,
                        render: function (data, type, full, meta) {
                            let dropDownClass = full.deed === null ? "btn-flat-secondary" : "btn-flat-warning";
                            let dropDownText = full.review?.review_status === undefined ? (full.deed === null ? thisComponent.collection?.messages?.pleaseSelect : thisComponent.collection?.messages?.pending) : full.review?.review_status?.[`name_${thisComponent.locale}`];
                            switch (full.review?.review_status?.name_en) {
                                case "Pending":
                                    dropDownClass = "btn-flat-warning";
                                    break;
                                case "Accepted":
                                    dropDownClass = "btn-flat-success";
                                    break;
                                case "Rejected":
                                    dropDownClass = "btn-flat-danger";
                                    break;
                            }
                            let r = `
                                <div class="btn-group">
                                    <button id="status-dropdown-btn-${full.id}" class="btn ${dropDownClass} dropdown-toggle" type="button" data-bs-toggle="dropdown">${dropDownText}</button>
                                    <div id="status-dropdown-menu-${full.id}" class="dropdown-menu">
                            `;
                            thisComponent.reviewStatuses.forEach((reviewStatus) => {
                                let itemColor = "";
                                if (reviewStatus.name_en == "Accepted") {
                                    itemColor = "text-success";
                                } else if (reviewStatus.name_en == "Rejected") {
                                    itemColor = "text-danger";
                                }
                                r += `<a class="dropdown-item" href="#" onclick="window.thisComponent.statementReviewButtonEnable(${full.id}, ${reviewStatus.id})"><span class="${itemColor} fw-bold">${reviewStatus[`name_${thisComponent.locale}`]}</span></a>`;
                            });
                            r += `</div>
                                </div>
                            `;
                            return r;
                        },
                    },
                    {
                        // review
                        targets: 8,
                        responsivePriority: 8,
                        orderable: false,
                        width: "10%",
                        render: function (data, type, full, meta) {
                            let r = `
                                <div class="form-group">
                                    <textarea class="form-control" type="text" placeholder="" id="statementReviewInput${full.id}" onchange="window.thisComponent.statementReviewButtonEnable(${full.id}, null)">${full.review ? full.review.review : ""}</textarea>
                                </div>
                                `;
                            return r;
                        },
                    },
                    {
                        // actions
                        targets: 9,
                        responsivePriority: 9,
                        width: "10%",
                        orderable: false,
                        render: function (data, type, full, meta) {
                            let r = `
                                <div class="d-flex justify-content-center align-items-center px-2">
                                    <div class="d-flex flex-column">
                                    <div class="mb-1 row mr-1">
                                            <button type="button" class="btn btn-primary waves-effect" id="statementReviewButton${full.id}" onclick="window.thisComponent.statementReviewUpdate(${full.id})" disabled>${thisComponent.collection?.messages?.update}</button>
                                        </div>
                                        <div class="row mr-1">
                                            <button type="button" class="btn btn-outline-primary waves-effect" onclick="window.thisComponent.statementViewShow(${full.id})">${thisComponent.collection?.messages?.view}</button>
                                        </div>

                                    </div>
                                </div>
                                `;
                            return r;
                        },
                    },
                ],
                order: [[0, "asc"]],
                dom: `
                <"row d-flex justify-content-start align-items-center m-1"
                    <"col-lg-8 d-flex justify-content-start align-items-center"
                        <"#cardHeader">
                    >
                    <"col-lg-4 d-flex justify-content-end align-items-center"f>
                >t
                <"d-flex justify-content-between mx-2 row"
                    <"col-sm-12 col-md-6"i>
                    <"col-sm-12 col-md-6"p>
                ">`,
                initComplete: function () {
                    let domHtml = `
                    <div class="card-body">
                        <h4 class="card-title">${thisComponent.collection?.messages?.statements}</h4>
                        <h6 class="card-subtitle text-muted">${thisComponent.collection?.messages?.statements} ${thisComponent.collection?.messages?.review}</h6>
                    </div>
                    `;
                    $("#cardHeader").html(domHtml);
                },
                drawCallback: function () {
                    /*
                    if (window.thisComponent.scrollPos != null) {
                        window.thisComponent.$nextTick(() => {
                            window.scrollTo(0, window.thisComponent.scrollPos);
                        });
                    } else {
                        window.thisComponent.scrollPos = 500;
                    }
                    */
                    $(".select2").select2();
                },
            });
        },
        draw() {
            var thisComponent = this;
            axios
                .get("/" + thisComponent.locale + "/axios/organisations/review/" + thisComponent.actionId, {})
                .then(function (response) {
                    //console.log(response.data);
                    thisComponent.collection = response.data;
                    thisComponent.$nextTick(() => {
                        thisComponent.taskCompletionChart();
                        thisComponent.buildTable();
                    });
                })
                .catch(function (error) {
                    console.log(error);
                    console.log(error.response);
                });
        },
        interviewActiveSet(t) {
            var thisComponent = this;
            this.interviewActive = t;
            this.$nextTick(() => {
                thisComponent.$nextTick(() => {
                    // create sliders for mounted interview statements
                    let slider = null;
                    thisComponent.interviewExpanded.statements.forEach((s) => {
                        slider = document.getElementById(`interviewStatementValueSlider${thisComponent.interviewExpanded.id}_${s.id}`);
                        console.log(slider);
                        noUiSlider.create(slider, {
                            start: s.latestDeed.value,
                            step: 1,
                            range: {
                                min: 1,
                                max: 5,
                            },
                            tooltips: true,
                            direction: "ltr",
                            pips: {
                                mode: "steps",
                                stepped: false,
                                density: 1,
                            },
                        });
                    });
                });
            });
        },
        interviewConductedSet(interviewId) {
            var thisComponent = this;
            // axios call to refresh
            axios
                .get(`/${this.locale}/axios/organisations/${this.org}/review/action/${this.actionId}/interview`)
                .then(function (response) {
                    console.log(response.data);
                    thisComponent.interviewStatements = response.data;
                    thisComponent.$nextTick(() => {
                        let theInter = thisComponent.interviewStatements.interviews.filter((i) => {
                            return i.id == interviewId;
                        });
                        thisComponent.interviewExpanded = theInter[0];
                        // build sliders again
                        thisComponent.slidersRebuild();
                    });
                })
                .catch(function (error) {
                    console.log(error.response);
                });
        },
        interviewCreate() {
            var thisComponent = this;
            Swal.fire({
                title: "Info!",
                text: `${thisComponent.collection?.messages?.working} ...`,
                icon: "info",
                showConfirmButton: false,
                customClass: {
                    confirmButton: "btn btn-primary",
                },
                buttonsStyling: false,
                allowOutsideClick: false,
                allowEscapeKey: false,
                onBeforeOpen: () => {
                    Swal.showLoading();
                },
            });
            // clear all errors
            this.interviewCreateErrors = {
                agenda: null,
                user: null,
                statements: null,
            };
            let load = {
                agenda: null,
                user_id: null,
                statements: [],
                locale: thisComponent.locale,
            };
            // make sure there is an agenda
            load.agenda = document.getElementById("agenda").value;
            if (load.agenda == "" || load.agenda == null) {
                this.interviewCreateErrors.agenda = {
                    message: "Agenda is required",
                };
            }
            load.user_id = document.getElementById("user_id").value;
            if (load.user_id == "" || load.user_id == null) {
                this.interviewCreateErrors.user = {
                    message: "User is required",
                };
            }
            let p = [];
            for (let index = 0; index < document.getElementById("interviewStatementsSelect").options.length; index++) {
                p.push(document.getElementById("interviewStatementsSelect").options.item(index).value);
            }
            load.statements = p;
            if (load.statements.length == 0) {
                this.interviewCreateErrors.statements = {
                    message: "At least one statement is required",
                };
            }
            if (this.interviewCreateErrors.agenda || this.interviewCreateErrors.user || this.interviewCreateErrors.statements) {
                Swal.close();
                return;
            } else {
                //console.log(load);
                axios
                    .post(`/${this.locale}/interviews/`, load)
                    .then(function (response) {
                        Swal.close();
                        thisComponent.interviewHide();
                        thisComponent.$nextTick(() => {
                            Swal.fire({
                                title: "Success!",
                                text: "Interview created!",
                                icon: "success",
                                customClass: {
                                    confirmButton: "btn btn-primary",
                                },
                                buttonsStyling: false,
                            });
                            return;
                        });
                        //console.log(response.data);
                    })
                    .catch(function (error) {
                        console.log(error.response);
                        Swal.close();
                        thisComponent.$nextTick(() => {
                            Swal.fire({
                                title: error,
                                text: error.response?.data?.message,
                                icon: "error",
                                customClass: {
                                    confirmButton: "btn btn-primary",
                                },
                                buttonsStyling: false,
                            });
                            return;
                        });
                    });
            }
        },
        interviewHide() {
            this.interviewActive = "prepare";
            // clear all errors
            this.interviewCreateErrors = {
                agenda: null,
                user: null,
                statements: null,
            };
            // empty state
            document.getElementById("agenda").value = null;
            //document.getElementById("interviewStatementsSelect").options = [];
            this.interviewStatementsSelected = [];
            this.$nextTick(() => {
                $("#interviewModal").modal("hide");
            });
        },
        interviewStatementAdd(statement) {
            let exists = this.interviewStatementsSelected.filter((is) => {
                return is.id == statement.id;
            });
            if (exists.length == 0) {
                this.interviewStatementsSelected.push(statement);
            }
        },
        interviewStatementRemove(statement) {
            this.interviewStatementsSelected = this.interviewStatementsSelected.filter((is) => {
                return is.id != statement.id;
            });
        },
        interviewStatementRemovable(statement) {
            let added = this.interviewStatementsSelected.filter((is) => {
                return is.id == statement.id;
            });
            if (added.length == 0) {
                return false;
            } else {
                return true;
            }
        },
        reviewUpdate(kind, statementId, inputName, sli, deedId) {
            let reviewStatusId = 2;
            if (kind == "accept") {
                reviewStatusId = 2;
            } else {
                reviewStatusId = 3;
            }
            let rev = document.getElementById(inputName).value;
            let sliderValue = document.getElementById(sli).noUiSlider.get();
            axios
                .post(`/${this.locale}/axios/organisations/${this.org}/review/conduct-update`, {
                    review_status_id: reviewStatusId,
                    review: rev,
                    statement_id: statementId,
                    value: sliderValue,
                    deed_id: deedId,
                })
                .then(function (response) {
                    console.log(response.data);
                        toastr["success"]("👋 Updated!.", "Success", {
                            closeButton: true,
                            tapToDismiss: false,
                            progressBar: true,
                            rtl: false,
                        });
                })
                .catch(function (error) {
                    console.log(error);
                    console.log(error.response);
                    toastr["error"](`👋 ${error.response?.data}`, "Error!", {
                        closeButton: true,
                        tapToDismiss: false,
                        rtl: false,
                    });
                });
        },
        showInterview() {
            var thisComponent = this;
            // load interview related data
            axios
                .get(`/${this.locale}/axios/organisations/${this.org}/review/action/${this.actionId}/interview`)
                .then(function (response) {
                    console.log(response.data);
                    thisComponent.interviewStatements = response.data;
                    thisComponent.$nextTick(() => {
                        thisComponent.interviewExpanded = thisComponent.interviewStatements.interviews[0];
                    });
                })
                .catch(function (error) {
                    console.log(error.response);
                });
            $("#interviewModal").modal("show");
        },
        slidersRebuild() {
            var thisComponent = this;
            thisComponent.$nextTick(() => {
                // create sliders for mounted interview statements
                let slider = null;
                thisComponent.interviewExpanded.statements.forEach((s) => {
                    slider = document.getElementById(`interviewStatementValueSlider${thisComponent.interviewExpanded.id}_${s.id}`);
                    //console.log(slider);
                    noUiSlider.create(slider, {
                        start: s.latestDeed.value,
                        step: 1,
                        range: {
                            min: 1,
                            max: 5,
                        },
                        tooltips: true,
                        direction: "ltr",
                        pips: {
                            mode: "steps",
                            stepped: false,
                            density: 1,
                        },
                    });
                });
            });
        },
        statementReviewButtonEnable(id, reviewStatusId) {
            if (reviewStatusId !== null) {
                this.toUpdate = this.toUpdate.filter((statement) => statement.id != id);
                this.toUpdate.push({ id: id, status: reviewStatusId });
                let selectedStatus = this.reviewStatuses.find((status) => status.id == reviewStatusId);
                let dropDownText = selectedStatus[`name_${this.locale}`];
                let dropDownClass = "";
                if (selectedStatus.name_en == "Accepted") {
                    dropDownClass = "btn-flat-success";
                } else if (selectedStatus.name_en == "Rejected") {
                    dropDownClass = "btn-flat-danger";
                }
                let dropDownBtn = document.getElementById("status-dropdown-btn-" + id);
                let classList = dropDownBtn.classList.value.split(" ");
                let className = classList.find((className) => className.includes("btn-flat"));
                dropDownBtn.classList.remove(className);
                dropDownBtn.classList.add(dropDownClass);
                dropDownBtn.innerHTML = dropDownText;
            }
            $("#statementReviewButton" + id).prop("disabled", false);
        },
        statementReviewUpdate(id) {
            let self = this;
            $("#statementReviewButton" + id).prop("disabled", true);
            let a = this.toUpdate.find((statement) => statement.id == id)?.status;
            let r = $(`#statementReviewInput${id}`).val();
            axios
                .post(`/${thisComponent.locale}/axios/organisations/statements/reviews/update`, {
                    statement_id: id,
                    review_status_id: a,
                    review: r,
                })
                .then(function (response) {
                    self.toUpdate = self.toUpdate.filter((statement) => statement.id != id);
                    toastr["success"](`${thisComponent.collection?.messages?.itemUpdatedSuccessfully}.`, `${thisComponent.collection?.messages?.success}!`, {
                        showMethod: "slideDown",
                        hideMethod: "slideUp",
                        timeOut: 3000,
                        progressBar: true,
                        rtl: false,
                    });
                })
                .catch(function (error) {
                    //console.log(error);
                    //console.log(error.response);
                    toastr["error"](error.response?.data?.message, `${thisComponent.collection?.messages?.error}!`, {
                        showMethod: "slideDown",
                        hideMethod: "slideUp",
                        timeOut: 5000,
                        progressBar: true,
                        rtl: false,
                    });
                });
        },
        statementViewHide() {
            this.statementActive = null;
            this.status = null;
            this.review = null;
            $("#statementViewModal").modal("hide");
        },
        statementViewShow(id) {
            let f = this.collection.statements.filter((x) => x.id == id);
            this.statementActive = f[0];
            // this.status = $(`#statementReviewSelect${id}`).select2("data")[0].text;
            this.review = $(`#statementReviewInput${id}`).val();
            $("#statementViewModal").modal("show");
        },
        taskCompletionChart() {
            var thisComponent = this;
            let supportTrackerChart = document.getElementById("taskCompletionChart");
            let bg = $("html").hasClass("dark-layout") ? "#283046" : "#fff";
            let textHeadingColor = $("html").hasClass("dark-layout") ? "#eee" : "#444";
            let colors = ["#F00", "#0F0", "#00F"];
            let completedTasks = this.collection?.messages?.completedTasks;
            let percentage = this.auditorStatements?.completion.percentage;
            let supportTrackerChartOptions = {
                chart: {
                    height: 270,
                    type: "radialBar",
                },
                plotOptions: {
                    radialBar: {
                        size: 150,
                        offsetY: 20,
                        startAngle: -150,
                        endAngle: 150,
                        hollow: {
                            size: "65%",
                        },
                        track: {
                            background: bg,
                            strokeWidth: "100%",
                        },
                        dataLabels: {
                            name: {
                                offsetY: -5,
                                color: textHeadingColor,
                                fontSize: "1rem",
                            },
                            value: {
                                offsetY: 15,
                                color: textHeadingColor,
                                fontSize: "1.714rem",
                            },
                        },
                    },
                },
                colors: [window.colors.solid.danger],
                fill: {
                    type: "gradient",
                    gradient: {
                        shade: "dark",
                        type: "horizontal",
                        shadeIntensity: 0.5,
                        gradientToColors: [window.colors.solid.primary],
                        inverseColors: true,
                        opacityFrom: 1,
                        opacityTo: 1,
                        stops: [0, 100],
                    },
                },
                stroke: {
                    dashArray: 8,
                },
                series: [percentage],
                labels: [completedTasks],
            };
            supportTrackerChart = new ApexCharts(supportTrackerChart, supportTrackerChartOptions);
            supportTrackerChart.render();
        },
    },
    mounted() {
        window.thisComponent = this;
        //console.log(this.org);
        //console.log(this.auditorStatements);
        //console.log(this.plans);
        this.draw();
        //console.log(window.colors);
        //this.taskCompletionChart();
    },
};
</script>
