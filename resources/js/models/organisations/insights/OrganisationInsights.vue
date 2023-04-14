<template>
    <div class="row">
        <div class="row match-height">
            <div class="col-12">
                <!-- Radar Chart-->
                <div class="card">
                    <div
                        class="card-header d-flex flex-sm-row flex-column justify-content-md-between align-items-start justify-content-start">
                        <div class="mb-1 width-200">
                            <label class="form-label" for="basicSelect">{{
                                    collection?.messages?.selectOrganisation
                                }}</label>
                            <select class="form-select" id="basicSelect" @change="updateOrg">
                                <option v-for="org in collection?.data" :key="org">{{ org.name }}</option>
                            </select>
                        </div>
                        <div class="mb-1">
                            <label class="form-label" for="yearSelect">{{ collection?.messages?.selectYear }}</label>
                            <select class="form-select" id="yearSelect" @change="updateOrg" style="width: 100px">
                                <option v-for="year in Object.keys(activeOrg)" :key="year">{{ year }}</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <div class="card-header">
                                <h4 class="card-title">{{ collection?.messages?.implementation }}
                                    {{ collection?.messages?.radar }}</h4>
                            </div>
                            <div class="card-body">
                                <canvas id="radar-chart" width="640" height="640"></canvas>
                            </div>
                        </div>
                        <div class="col-6">
                            <!-- Radar Chart End -->
                            <div class="card-header">
                                <h4 class="card-title">{{ collection?.messages?.risks }}
                                    {{ collection?.messages?.scatter }}</h4>
                            </div>
                            <div class="card-body">
                                <canvas class="bubble-chart-ex chartjs" width="640" height="640"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row match-height">
            <div class="col-12">
                <div class="card invoice-list-wrapper">
                    <div class="card-datatable table-responsive">
                        <table class="invoice-list-table table" id="dataTable"></table>
                    </div>
                </div>
            </div>
        </div>
        <div class="row match-height">
            <div class="col-12">
                <div class="card invoice-list-wrapper">
                    <div class="card-datatable table-responsive">
                        <table class="invoice-list-table table" id="kpiTable"></table>
                    </div>
                </div>
            </div>
        </div>
        <div class="row match-height">
            <div class="col-12">
                <div class="card invoice-list-wrapper">
                    <div class="card-datatable table-responsive">
                        <table class="invoice-list-table table" id="sanctionsTable"></table>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade text-start modal-primary" id="componentShowModal" tabindex="-1"
             aria-labelledby="componentShowLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="componentShowLabel">{{ collection?.messages?.component }}
                            {{ collection?.messages?.show }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                                @click="componentHide"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-1">
                                    <h6 class="text-sm font-weight-semibold me-1">{{
                                            collection?.messages?.component
                                        }}</h6>
                                    <span>{{ `${componentActive?.code} | ${componentActive?.fullname}` }}</span>
                                </div>
                                <div class="mb-1">
                                    <h6 class="text-sm font-weight-semibold me-1">{{
                                            collection?.messages?.desc
                                        }}</h6>
                                    <span>{{ componentActive?.desc }}</span>
                                </div>
                                <div class="d-flex align-items-center mb-1">
                                    <h6 class="text-sm font-weight-semibold me-1 mb-0">
                                        {{ `${collection?.messages?.value}:` }}</h6>
                                    <span>{{ componentActive?.mean }}</span>
                                </div>
                                <div class="d-flex align-items-center mb-1">
                                    <h6 class="text-sm font-weight-semibold me-1 mb-0">
                                        {{ `${collection?.messages?.commitment}:` }}</h6>
                                    <span>{{ componentActive?.commitment }}</span>
                                </div>
                                <div class="mb-1">
                                    <a :href="`/${locale}/insights/component/sanctions/${componentActive?.id}`"
                                       class="btn btn-outline-primary waves-effect"
                                       target="_blank">{{ collection?.messages?.sanctions }}</a>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                        <tr>
                                            <th colspan="5">{{ collection?.messages?.statements }}</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr v-for="(statement, index) in componentActive?.statements"
                                            :key="statement.id">
                                            <td>
                                                <span class="bullet bullet-sm"
                                                      :class="statusBulletColor(statement?.review?.review_status.name_en)"
                                                      data-bs-toggle="tooltip"
                                                      :data-bs-original-title="statement?.review?.review"></span>
                                            </td>
                                            <td>{{ `${componentActive?.code}.${statement.code}` }}</td>
                                            <td>{{ statement[`content_${locale}`] }}</td>
                                            <td>{{ statement.deed === null ? 0 : statement.deed.value }}</td>
                                            <td>
                                                <button type="button"
                                                        class="btn btn-icon btn-outline-primary waves-effect"
                                                        @click="updateActiveStatement(index)">
                                                    <i data-feather="chevron-right"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="divider divider-start mt-0">
                                    <div class="divider-text text-uppercase fs-6 fw-bold">
                                        {{ collection?.messages?.details }}
                                    </div>
                                </div>
                                <div class="mb-1">
                                    <h6 class="text-sm font-weight-semibold me-1">{{
                                            collection?.messages?.statement
                                        }}</h6>
                                    <span>{{ `${componentActive?.code}.${activeStatement?.code}` }}</span>
                                </div>
                                <div class="mb-1">
                                    <h6 class="text-sm font-weight-semibold me-1">{{ collection?.messages?.desc }}</h6>
                                    <span>{{ activeStatement?.[`desc_${locale}`] }}</span>
                                </div>
                                <div class="mb-1">
                                    <h6 class="text-sm font-weight-semibold me-1">
                                        {{ collection?.messages?.implementation }}</h6>
                                    <span>{{ activeStatement?.[`implementation_${locale}`] }}</span>
                                </div>
                                <div class="mb-1">
                                    <h6 class="text-sm font-weight-semibold me-1">
                                        {{
                                            `${collection?.messages?.organisation} ${collection?.messages?.implementation}`
                                        }}</h6>
                                    <span>{{ activeStatement?.implementation }}</span>
                                </div>
                                <div v-show="activeStatement !== undefined" class="mb-1">
                                    <a :href="`/${locale}/insights/statement/sanctions/${activeStatement?.id}`"
                                       class="btn btn-outline-primary waves-effect"
                                       target="_blank">{{ collection?.messages?.sanctions }}</a>
                                </div>
                                <hr>
                                <div id="statements-chart"></div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-primary" @click="componentHide">Ok</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 modal fade text-start modal-primary" id="kpiViewModal" tabindex="-1"
             aria-labelledby="kpiLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="kpiLabel">{{ collection?.messages?.kpi }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                                @click="kpiHide"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-4 table-responsive">
                                <table class="table">
                                    <thead class="table-light">
                                    <tr>
                                        <th>{{ collection?.messages?.key }}</th>
                                        <th>{{ collection?.messages?.value }}</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr>
                                        <td>{{ collection?.messages?.id }}</td>
                                        <td>{{ kpiActive?.id }}</td>
                                    </tr>
                                    <tr>
                                        <td>{{ collection?.messages?.name }}</td>
                                        <td>{{ kpiActive ? kpiActive["name_" + locale] : null }}</td>
                                    </tr>
                                    <tr>
                                        <td>{{ collection?.messages?.desc }}</td>
                                        <td>{{ kpiActive ? kpiActive["desc_" + locale] : null }}</td>
                                    </tr>
                                    <tr>
                                        <td>{{ collection?.messages?.target }}</td>
                                        <td>{{ kpiActive?.kpicomment ? kpiActive.kpicomment.target : null }}</td>
                                    </tr>
                                    <tr>
                                        <td>{{ collection?.messages?.value }}</td>
                                        <td>{{ kpiActive?.kpicomment ? kpiActive.kpicomment.value : null }}</td>
                                    </tr>
                                    <tr>
                                        <td>{{ collection?.messages?.comment }}</td>
                                        <td>{{ kpiActive?.kpicomment ? kpiActive.kpicomment.comment : null }}</td>
                                    </tr>
                                    <tr>
                                        <td>{{ collection?.messages?.lastUpdated }}</td>
                                        <td>{{
                                                kpiActive?.kpicomment ? kpiActive.kpicomment.created_at_for_humans : null
                                            }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>{{ collection?.messages?.by }}</td>
                                        <td>{{
                                                kpiActive?.kpicomment ? kpiActive.kpicomment.user.name + " [" +
                                                    kpiActive.kpicomment.user.role + "]" : null
                                            }}
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-8">
                                <div class="card">
                                    <div
                                        class="card-header d-flex flex-sm-row flex-column justify-content-md-between align-items-start justify-content-start">
                                        <div>
                                            <h4 class="card-title">{{ collection?.messages?.kpi }}
                                                {{ collection?.messages?.history }}</h4>
                                            <span class="card-subtitle text-muted">{{
                                                    collection?.messages?.kpiHistorySubtitle
                                                }}</span>
                                        </div>
                                        <div class="d-flex align-items-center"></div>
                                    </div>
                                    <div id="kpi-history-parent" class="card-body">
                                        <div id="kpi-history-chart"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-primary" @click="kpiHide">Ok</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade text-start modal-primary" id="sanctionShowModal" tabindex="-1"
             aria-labelledby="sanctionShowLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-extra-wide">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="sanctionShowLabel">{{ sanctionActive?.title }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                                @click="sanctionHide"></button>
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
                                        <td>{{ collection?.messages?.id }}</td>
                                        <td>{{ sanctionActive?.id }}</td>
                                    </tr>
                                    <tr>
                                        <td>{{ collection?.messages?.createdAt }}</td>
                                        <td>{{ sanctionActive?.created_at_for_humans }}</td>
                                    </tr>
                                    <tr>
                                        <td>{{ collection?.messages?.title }}</td>
                                        <td>{{ sanctionActive?.title }}</td>
                                    </tr>
                                    <tr>
                                        <td>{{ collection?.messages?.dpa }}</td>
                                        <td>
                                            <div class="d-flex align-items-center justify-content-start">
                                                <div v-if="sanctionActive?.dpa?.country != undefined" class="col-2">
                                                    <img
                                                        :src="`/images/flags/svg/${sanctionActive?.dpa?.country?.code}.svg`"
                                                        style="width: 30px"/>
                                                </div>
                                                <div class="col-10 align-items-center">
                                                    <p class="mx-0 my-0">{{ sanctionActive?.dpa?.name }}</p>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>{{ collection?.messages?.fine }}</td>
                                        <td>{{
                                                sanctionActive?.fine ? parseInt(sanctionActive?.fine) + " " +
                                                    (sanctionActive?.currency?.symbol ? sanctionActive?.currency.symbol : "") :
                                                    ""
                                            }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>{{ collection?.messages?.startedOn }}</td>
                                        <td>{{ sanctionActive?.started_at_for_humans }}</td>
                                    </tr>
                                    <tr>
                                        <td>{{ collection?.messages?.decidedOn }}</td>
                                        <td>{{ sanctionActive?.decided_at_for_humans }}</td>
                                    </tr>
                                    <tr>
                                        <td>{{ collection?.messages?.publishedOn }}</td>
                                        <td>{{ sanctionActive?.published_at_for_humans }}</td>
                                    </tr>
                                    <tr>
                                        <td>{{ collection?.messages?.articles }}</td>
                                        <td>
                                            <div v-for="article in sanctionActive?.articlesSorted" :key="article.title">
                                                <a :href="article?.url" target="_blank">{{ article?.title }}</a>
                                            </div>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-primary" @click="sanctionHide">Ok</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import Chart from "chart.js/auto";
import ChartDataLabels from "chartjs-plugin-datalabels";

export default {
    props: ["locale"],
    data() {
        return {
            collection: null,
            componentActive: null,
            dataTable: null,
            kpiActive: null,
            kpiChart: null,
            kpiTable: null,
            radarChart: null,
            sanctionActive: null,
            sanctionsTable: null,
            scatterChart: null,
            activeOrg: {},
            activeStatement: null,
            statementsChart: null,
            statementHistoryChart: null,
        };
    },
    methods: {
        buildTable() {
            //console.log(this.dataTable);
            // console.log("called");
            var thisComponent = this;
            if (thisComponent.dataTable) {
                thisComponent.dataTable.destroy();
                thisComponent.dataTable = null;
                document.getElementById("dataTable").innerHTML = "";
            }
            //console.log(thisComponent.dataTable);
            let header = `
            <thead>
                <tr>
                    <th class="">${thisComponent.collection?.messages?.code}</th>
                    <th>${thisComponent.collection?.messages?.component}</th>
                    <th class="text-center">${thisComponent.collection?.messages?.commitment}</th>
                    <th class="text-center">${thisComponent.collection?.messages?.implementation}</th>
                    <th class="text-center">${thisComponent.collection?.messages?.actions}</th>
                </tr>
            </thead>
            `;
            const activeYear = document.getElementById("yearSelect").value;
            var dataSource = thisComponent.activeOrg[activeYear].table;
            document.getElementById("dataTable").innerHTML = header;
            thisComponent.dataTable = $("#dataTable").DataTable({
                data: dataSource,
                createdRow: function (row, data, dataIndex) {
                    //$(row).addClass("row-auth-bg");
                },
                lengthMenu: [10, 20, 50, 100],
                paging: true,
                autoWidth: true,
                searching: true,
                columns: [{data: "code"}, {data: "fullname"}, {data: "commitment"}, {data: "mean"}],
                columnDefs: [
                    {
                        // columnA
                        targets: 0,
                        responsivePriority: 0,
                        width: "10%",
                        render: function (data, type, full, meta) {
                            let r = `<p>${full.code}</p>`;
                            return r;
                        },
                    },
                    {
                        // columnB
                        targets: 1,
                        responsivePriority: 1,
                        width: "40%",
                        render: function (data, type, full, meta) {
                            let r = `<p>${full.fullname}</p>`;
                            return r;
                        },
                    },
                    {
                        // columnB
                        targets: 2,
                        responsivePriority: 2,
                        width: "10%",
                        render: function (data, type, full, meta) {
                            let r = `<p class="text-center">${full.commitment}</p>`;
                            return r;
                        },
                    },
                    {
                        // columnB
                        targets: 3,
                        responsivePriority: 3,
                        width: "10%",
                        render: function (data, type, full, meta) {
                            let r = `<p class="text-center">${full.mean}</p>`;
                            return r;
                        },
                    },
                    {
                        // actions
                        targets: 4,
                        responsivePriority: 4,
                        width: "15%",
                        orderable: false,
                        render: function (data, type, full, meta) {
                            let r = `
                                <div class="d-flex justify-content-center align-items-center px-2">
                                    <div class="d-flex flex-column">
                                        <button type="button" class="btn btn-outline-primary waves-effect mb-1" onClick="window.component.componentShow(${full.id})">
                                            ${feather.icons["eye"].toSvg({class: "me-25"})}
                                            <span>${thisComponent.collection?.messages?.view}</span>
                                        </button>
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
                                <h4 class="card-title">${thisComponent.collection?.messages?.components}</h4>
                                <h6 class="card-subtitle text-muted">${thisComponent.collection?.messages?.components} ${thisComponent.collection?.messages?.table}</h6>
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
                },
            });
        },
        buildKpiTable() {
            var thisComponent = this;
            if (thisComponent.kpiTable) {
                thisComponent.kpiTable.destroy();
                thisComponent.kpiTable = null;
                document.getElementById("kpiTable").innerHTML = "";
            }
            let header = `
            <thead>
                <tr>
                    <th class="cell-fit">${thisComponent.collection?.messages?.id}</th>
                    <th>${thisComponent.collection?.messages?.name}</th>
                    <th>${thisComponent.collection?.messages?.desc}</th>
                    <th>${thisComponent.collection?.messages?.target}</th>
                    <th>${thisComponent.collection?.messages?.value}</th>
                    <th>${thisComponent.collection?.messages?.comment}</th>
                    <th class="text-center">${thisComponent.collection?.messages?.actions}</th>
                </tr>
            </thead>
            `;
            const activeYear = document.getElementById("yearSelect").value;
            const dataSource = thisComponent.activeOrg[activeYear].kpis;
            //console.log(dataSource);
            document.getElementById("kpiTable").innerHTML = header;
            thisComponent.kpiTable = $("#kpiTable").DataTable({
                data: dataSource,
                createdRow: function (row, data, dataIndex) {
                    //$(row).addClass("row-auth-bg");
                },
                lengthMenu: [10, 20, 50, 100],
                paging: true,
                autoWidth: true,
                searching: true,
                columns: [{data: "id"}, {data: "name"}, {data: "desc"}, {data: "target"}, {data: "value"}, {data: "comment"}],
                columnDefs: [
                    {
                        // id
                        targets: 0,
                        responsivePriority: 0,
                        width: "5%",
                        render: function (data, type, full, meta) {
                            let r = `<p>${full.id}</p>`;
                            return r;
                        },
                    },
                    {
                        // name
                        targets: 1,
                        responsivePriority: 1,
                        width: "10%",
                        render: function (data, type, full, meta) {
                            let r = `<p>${full.name}</p>`;
                            return r;
                        },
                    },
                    {
                        // desc
                        targets: 2,
                        responsivePriority: 2,
                        width: "15%",
                        render: function (data, type, full, meta) {
                            let r = `<p>${full.desc}</p>`;
                            return r;
                        },
                    },
                    {
                        // target
                        targets: 3,
                        responsivePriority: 3,
                        width: "7.5%",
                        render: function (data, type, full, meta) {
                            let r = `<p>${full.target}</p>`;
                            return r;
                        },
                    },
                    {
                        // value
                        targets: 4,
                        responsivePriority: 4,
                        width: "7.5%",
                        render: function (data, type, full, meta) {
                            let r = `<p>${full.value}</p>`;
                            return r;
                        },
                    },
                    {
                        // comment
                        targets: 5,
                        responsivePriority: 5,
                        width: "15%",
                        render: function (data, type, full, meta) {
                            let r = `<p>${full.comment}</p>`;
                            return r;
                        },
                    },
                    {
                        // actions
                        targets: 6,
                        responsivePriority: 6,
                        width: "10%",
                        orderable: false,
                        render: function (data, type, full, meta) {
                            let r = `
                                <div class="d-flex justify-content-center align-items-center px-2">
                                    <div class="d-flex flex-column">
                                        <button type="button" class="btn btn-outline-primary waves-effect mb-1" onClick="window.component.kpiShow(${full.id})">
                                            ${feather.icons["eye"].toSvg({class: "me-25"})}
                                            <span>${thisComponent.collection?.messages?.view}</span>
                                        </button>
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
                        <"#kpiCardHeader">
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
                                <h4 class="card-title">${thisComponent.collection?.messages?.kpis}</h4>
                                <h6 class="card-subtitle text-muted">${thisComponent.collection?.messages?.kpis} ${thisComponent.collection?.messages?.table}</h6>
                            </div>
                            `;
                    $("#kpiCardHeader").html(domHtml);
                },
                drawCallback: function () {
                    /*
                    if (window.thisComponent.scrollPos != null) {
                        window.thisComponent.$nextTick(() => {
                            window.scrollTo(0, window.component.scrollPos);
                        });
                    } else {
                        window.component.scrollPos = 500;
                    }
                    */
                },
            });
        },
        buildSanctionsTable() {
            var thisComponent = this;
            if (thisComponent.sanctionsTable) {
                thisComponent.sanctionsTable.destroy();
                thisComponent.sanctionsTable = null;
                document.getElementById("sanctionsTable").innerHTML = "";
            }
            let header = `
            <thead>
                <tr>
                    <th class="">${thisComponent.collection?.messages?.id}</th>
                    <th class="">${thisComponent.collection?.messages?.createdAt}</th>
                    <th class="">${thisComponent.collection?.messages?.dpa}</th>
                    <th>${thisComponent.collection?.messages?.decidedOn}</th>
                    <th>${thisComponent.collection?.messages?.fine}</th>
                    <th>${thisComponent.collection?.messages?.title}</th>
                    <th class="text-center">${thisComponent.collection?.messages?.actions}</th>
                </tr>
            </thead>
            `;
            document.getElementById("sanctionsTable").innerHTML = header;
            const ajaxUrl = `/${thisComponent.locale}/axios/organisations/insights/sanctions`;
            thisComponent.sanctionsTable = $("#sanctionsTable").DataTable({
                processing: true,
                serverSide: true,
                paging: true,
                autoWidth: true,
                searching: true,
                ajax: {
                    headers: {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                    },
                    url: ajaxUrl,
                    type: "POST",
                    /*
                    complete: function (xhr, responseText) {
                        console.log(xhr);
                    },
                    */

                },
                columnDefs: [
                    {
                        //id
                        targets: 0,
                        responsivePriority: 0,
                        width: "10%",
                        render: function (data, type, full, meta) {
                            //console.log(full);
                            let r = `<p>${full.id}</p>`;
                            return r;
                        },
                    },
                    {
                        //created_at
                        targets: 1,
                        responsivePriority: 1,
                        width: "10%",
                        render: function (data, type, full, meta) {
                            //console.log(full);
                            let r = `<p>${full.created_at_for_humans}</p>`;
                            return r;
                        },
                    },
                    {
                        // DPA
                        targets: 2,
                        responsivePriority: 2,
                        width: "20%",
                        render: function (data, type, full, meta) {
                            // has image?
                            let r = `
                            <div class="d-flex align-items-center justify-content-start">
                                <div class="col-2">
                            `;
                            if (full.dpa.country) {
                                r += `
                                <img src='/images/flags/svg/${full.dpa?.country?.code}.svg' width="48"/>
                                `;
                            }
                            r += `
                                </div>
                                <div class="col-10 align-items-center px-1">
                                    <p class="mx-0 my-0">${full.dpa?.name}</p>
                                </div>
                            </row>`;
                            return r;
                        },
                    },
                    {
                        // decided_at
                        targets: 3,
                        responsivePriority: 3,
                        width: "10%",
                        render: function (data, type, full, meta) {
                            if (type === "sort") {
                                return Date.parse(full.decided_at_for_humans);
                            } else {
                                let r = `<p>${full.decided_at_for_humans}</p>`;
                                return r;
                            }
                        },
                    },
                    {
                        // fine
                        targets: 4,
                        responsivePriority: 4,
                        width: "10%",
                        type: "numeric",
                        render: function (data, type, full, meta) {
                            if (type === "sort") {
                                return full.fine;
                            } else {
                                let r = ``;
                                if (full.fine) {
                                    r = `<p>${parseInt(full.fine)} ${full.currency?.symbol ? full.currency?.symbol : ""}</p>`;
                                }
                                return r;
                            }
                        },
                    },
                    {
                        // title
                        targets: 5,
                        responsivePriority: 5,
                        width: "20%",
                        render: function (data, type, full, meta) {
                            let r = `<p>${full.title}</p>`;
                            return r;
                        },
                    },
                    {
                        // actions
                        targets: 6,
                        responsivePriority: 6,
                        width: "15%",
                        orderable: false,
                        render: function (data, type, full, meta) {
                            let r = `
                                <div class="d-flex justify-content-center align-items-center px-2">
                                    <div class="d-flex flex-column">
                                        <button type="button" class="btn btn-gradient-info waves-effect mb-1" onClick="window.open('${full.url}','_blank')">
                                            ${feather.icons["external-link"].toSvg({class: "me-25"})}
                                            <span>${thisComponent.collection?.messages?.visit}</span>
                                        </button>
                                        <button type="button" class="btn btn-outline-primary waves-effect mb-1" onClick="window.component.sanctionShow(${full.id})">
                                            ${feather.icons["eye"].toSvg({class: "me-25"})}
                                            <span>${thisComponent.collection?.messages?.view}</span>
                                        </button>
                                    </div>
                                </div>
                                `;
                            return r;
                        },
                    },
                ],
                order: [[0, "desc"]],
                dom: `
                <"row d-flex justify-content-start align-items-center m-1"
                    <"col-lg-8 d-flex justify-content-start align-items-center"
                        <"#sanctionCardHeader">
                    >
                    <"col-lg-4 d-flex justify-content-end align-items-center"f>
                >
                <"row d-flex justify-content-start align-items-center m-1"
                    <"col-lg-4"l>
                >t
                <"d-flex justify-content-between mx-2 row"
                    <"col-sm-12 col-md-6"i>
                    <"col-sm-12 col-md-6"p>
                ">`,
                initComplete: function () {
                    let domHtml = `
                    <div class="card-body">
                        <h4 class="card-title">${thisComponent.collection?.messages?.sanctions}</h4>
                        <h6 class="card-subtitle text-muted">${thisComponent.collection?.messages?.sanctions} ${thisComponent.collection?.messages?.table}</h6>
                    </div>
                    `;
                    $("#sanctionCardHeader").html(domHtml);
                },
            });
        },
        drawKpiChart() {
            var thisComponent = this;
            var chartColors = {
                column: {
                    series1: "#826af9",
                    series2: "#d2b0ff",
                    bg: "#f8d3ff",
                },
                success: {
                    shade_100: "#7eefc7",
                    shade_200: "#06774f",
                },
                donut: {
                    series1: "#ffe700",
                    series2: "#00d4bd",
                    series3: "#826bf8",
                    series4: "#2b9bf4",
                    series5: "#FFA1A1",
                },
                area: {
                    series3: "#a4f8cd",
                    series2: "#60f2ca",
                    series1: "#2bdac7",
                },
            };
            var areaChartEl = document.querySelector("#kpi-history-chart"),
                areaChartConfig = {
                    chart: {
                        height: 400,
                        type: "area",
                        parentHeightOffset: 0,
                        toolbar: {
                            show: false,
                        },
                    },
                    colors: [chartColors.donut.series1, chartColors.donut.series3],
                    dataLabels: {
                        enabled: false,
                    },
                    fill: {
                        type: "gradient",
                        gradient: {
                            shadeIntensity: 1,
                            opacityFrom: 0.7,
                            opacityTo: 0.9,
                            stops: [0, 90, 100],
                        },
                    },
                    legend: {
                        show: true,
                        position: "top",
                        horizontalAlign: "start",
                    },
                    grid: {
                        xaxis: {
                            lines: {
                                show: true,
                            },
                        },
                    },
                    markers: {
                        size: [5, 5],
                    },
                    series: [
                        {
                            name: thisComponent.collection?.messages?.target,
                            data: thisComponent.kpiActive?.targets,
                        },
                        {
                            name: thisComponent.collection?.messages?.value,
                            data: thisComponent.kpiActive?.values,
                        },
                    ],
                    stroke: {
                        curve: "smooth",
                        width: 4,
                    },
                    tooltip: {
                        enabled: true,
                        shared: true,
                        custom: function ({series, seriesIndex, dataPointIndex, w, context}) {
                            let r = `
                            <div class="arrow_box" style="left: 445.159px; top: 124.792px;">
                                <div class="apexcharts-tooltip-title" style="font-family: Helvetica, Arial, sans-serif; font-size: 12px;">${thisComponent.kpiActive?.targets[dataPointIndex].x}</div>
                                <div class="apexcharts-tooltip-series-group apexcharts-active" style="order: 1; display: flex;">
                                    <span class="apexcharts-tooltip-marker" style="background-color: ${chartColors.donut.series1};"></span>
                                    <div class="apexcharts-tooltip-text" style="font-family: Helvetica, Arial, sans-serif; font-size: 12px;">
                                        <div class="apexcharts-tooltip-y-group">
                                            <span class="apexcharts-tooltip-text-label">${thisComponent.collection?.messages?.target}: </span>
                                            <span class="apexcharts-tooltip-text-value">${thisComponent.kpiActive?.targets[dataPointIndex].y}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="apexcharts-tooltip-series-group apexcharts-active" style="order: 1; display: flex;">
                                    <span class="apexcharts-tooltip-marker" style="background-color: ${chartColors.donut.series3};"></span>
                                    <div class="apexcharts-tooltip-text" style="font-family: Helvetica, Arial, sans-serif; font-size: 12px;">
                                        <div class="apexcharts-tooltip-y-group">
                                            <span class="apexcharts-tooltip-text-label">${thisComponent.collection?.messages?.value}: </span>
                                            <span class="apexcharts-tooltip-text-value">${thisComponent.kpiActive?.values[dataPointIndex].y}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="apexcharts-tooltip-series-group apexcharts-active" style="order: 1; display: flex;">
                                    <div class="apexcharts-tooltip-text" style="font-family: Helvetica, Arial, sans-serif; font-size: 12px;">
                                        <div class="apexcharts-tooltip-y-group">
                                            <span class="apexcharts-tooltip-text-label">${thisComponent.collection?.messages?.user}: </span>
                                            <span class="apexcharts-tooltip-text-value">${thisComponent.kpiActive?.targets[dataPointIndex].user}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="apexcharts-tooltip-series-group apexcharts-active" style="order: 1; display: flex;">
                                    <div class="apexcharts-tooltip-text" style="font-family: Helvetica, Arial, sans-serif; font-size: 12px;">
                                        <div class="apexcharts-tooltip-y-group">
                                            <span class="apexcharts-tooltip-text-label">${thisComponent.collection?.messages?.comment}: </span>
                                            <span class="apexcharts-tooltip-text-value">${thisComponent.kpiActive?.targets[dataPointIndex].comment}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            `;
                            return r;
                        },
                    },
                    xaxis: {
                        type: "category",
                        categories: thisComponent.kpiActive?.xaxis,
                    },
                    yaxis: {
                        opposite: false,
                    },
                };
            if (typeof areaChartEl !== undefined && areaChartEl !== null) {
                thisComponent.areaChart = new ApexCharts(areaChartEl, areaChartConfig);
                thisComponent.areaChart.render();
            }
        },
        drawRadar() {
            var thisComponent = this;
            let selectedYear = document.getElementById("yearSelect").value;
            const orgData = thisComponent.activeOrg[selectedYear];
            if (thisComponent.radarChart) {
                thisComponent.radarChart.destroy();
            }
            if (thisComponent.riskChart) {
                thisComponent.riskChart.destroy();
            }
            //console.log(orgData);
            const chartData = {
                labels: orgData.components,
                datasets: [
                    {
                        data: orgData.mean,
                        label: thisComponent.collection?.messages?.implementation,
                        fill: true,
                        backgroundColor: "rgba(230,62,98,0.5)",
                        borderColor: "rgba(230,62,98,1.00)",
                        pointBackgroundColor: "rgba(230,62,98,1.00)",
                        pointBorderColor: "#fff",
                        pointHoverBackgroundColor: "rgba(242,74,110,1.00)",
                        pointHoverBorderColor: "rgba(230,62,98,1.00)",
                        pointRadius: 8,
                        pointHoverRadius: 12,
                    },
                    {
                        data: orgData.commitment,
                        label: thisComponent.collection?.messages?.commitment,
                        fill: true,
                        backgroundColor: "rgba(102, 103, 171, 0.2)",
                        borderColor: "rgb(102, 103, 171)",
                        pointBackgroundColor: "rgb(102, 103, 171)",
                        pointBorderColor: "#fff",
                        pointBackgroundColor: "rgb(114, 115, 183)",
                        pointHoverBorderColor: "rgb(102, 103, 171)",
                        pointRadius: 8,
                        pointHoverRadius: 12,
                    },
                ],
            };
            const chartConfig = {
                type: "radar",
                plugins: [
                    // to add spacing between legends and chart
                    {
                        beforeInit(chart) {
                            // Get reference to the original fit function
                            const originalFit = chart.legend.fit;
                            // Override the fit function
                            chart.legend.fit = function fit() {
                                // Call original function and bind scope in order to use `this` correctly inside it
                                originalFit.bind(chart.legend)();
                                // Change the height as suggested in another answers
                                this.height += 15;
                            };
                        },
                    },
                ],
                data: chartData,
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        r: {
                            grid: {
                                color: "silver",
                            },
                            pointLabels: {
                                color: "slategray",
                            },
                            ticks: {
                                color: "black",
                            },
                            max: 5,
                        },
                    },
                    plugins: {
                        legend: {
                            display: true,
                            labels: {
                                color: "slategray",
                                padding: 24,
                            },
                        },
                        tooltip: {
                            callbacks: {
                                title: function (context) {
                                    let t = orgData.codenames[context[0].dataIndex];
                                    return t;
                                },
                            },
                        },
                    },
                },
            };
            var chartElement = document.getElementById("radar-chart").getContext("2d");
            thisComponent.radarChart = new Chart(chartElement, chartConfig);
            // Risk Chart
            const riskChart = $(".bubble-chart-ex");
            const riskData = orgData.risks;
            const riskChartConfig = {
                data: riskData,
                options: {
                    plugins: {
                        datalabels: {
                            anchor: function (context) {
                                return "center";
                            },
                            align: function (context) {
                                return "center";
                            },
                            color: function (context) {
                                return "white";
                            },
                            font: function (context) {
                                let v = context.dataset.data[context.dataIndex];
                                return {
                                    size: context.dataset.fs,
                                    weight: "bold",
                                };
                            },
                            formatter: function (value, context) {
                                return context.dataset.count;
                            },
                            offset: 0,
                            padding: 0,
                        },
                        legend: {
                            display: true,
                            labels: {
                                generateLabels(chart) {
                                    let labels = [];
                                    for (const leg of riskData.legend) {
                                        //console.log(leg);
                                        labels.push({text: leg.text, fillStyle: leg.colour});
                                    }
                                    return labels;
                                },
                                padding: 16,
                            },
                        },
                        tooltip: {
                            enabled: true,
                            callbacks: {
                                label: function (context) {
                                    console.log(context);
                                    let v = riskData.datasets[context.datasetIndex];
                                    return `${v.label}:${thisComponent.collection?.messages?.consequence}:${v.data[0].x},${thisComponent.collection?.messages?.probability}:${v.data[0].y},${thisComponent.collection?.messages?.risk}:${parseInt(v.data[0].x) * parseInt(v.data[0].y)}`;
                                },
                            },
                        },
                    },
                    scales: {
                        x: {
                            display: true,
                            grid: {
                                color: "rgba(200, 200, 200, 0.2)",
                            },
                            min: 0,
                            max: 6,
                            title: {
                                display: true,
                                text: thisComponent.collection?.messages?.consequence,
                                font: {
                                    size: 14,
                                    weight: "bold",
                                },
                            },
                        },
                        y: {
                            display: true,
                            grid: {
                                color: "rgba(200, 200, 200, 0.2)",
                            },
                            min: 0,
                            max: 6,
                            title: {
                                display: true,
                                text: thisComponent.collection?.messages?.probability,
                                font: {
                                    size: 14,
                                    weight: "bold",
                                },
                            },
                        },
                    },
                },
                plugins: [ChartDataLabels],
                type: "bubble",
            };
            thisComponent.riskChart = new Chart(riskChart, riskChartConfig);
        },
        drawStatementsChart() {
            let self = this;
            let statements = this.componentActive.statements;
            let filtered = [];
            statements.forEach(statement => {
                if (statement.deed !== null) {
                    filtered.push({
                        name: `${this.componentActive.code}.${statement.code}`,
                        date: moment(statement.deed.updated_at).format('YYYY-MM-DD'),
                        value: statement.deed.value,
                        comment: statement.deed.comment,
                        status: statement?.review?.review_status.name_en,
                    });
                }
            });

            let sorted = filtered.sort((a, b) => {
                if (moment(a.date).isBefore(b.date)) {
                    return -1;
                } else if (moment(a.date).isAfter(b.date)) {
                    return 1;
                } else {
                    return 0;
                }
            });

            let categories = [];
            sorted.forEach(statement => {
                if (categories.indexOf(statement.date) === -1) {
                    categories.push(statement.date);
                }
            });

            let series = [];
            let colors = [];
            categories.forEach((category, index) => {
                sorted.forEach(statement => {
                    let name = statement.name;
                    let itemIndex = series.findIndex(item => item.name == name);
                    let data = 0;
                    let comment = null;
                    if (statement.date === category) {
                        data = statement.value;
                        comment = statement.comment;
                    }
                    if (itemIndex === -1) {
                        series.push({name: name, data: [data], comment: [comment]});
                    } else {
                        series[itemIndex].data[index] = data;
                        series[itemIndex].comment[index] = comment;
                    }
                    colors.push(self.getColorByStatementStatus(statement.status));
                })
            });

            let options = {
                series: series,
                chart: {
                    type: 'bar',
                    height: 350,
                    toolbar: {
                        show: false
                    }
                },
                plotOptions: {
                    bar: {
                        horizontal: false,
                        columnWidth: '30%',
                        borderRadius: 0
                    },
                },
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    show: true,
                    width: 2,
                    colors: ['transparent']
                },
                xaxis: {
                    categories: categories,
                },
                yaxis: {
                    title: {
                        text: self.collection?.messages?.value
                    }
                },
                fill: {
                    opacity: 1
                },
                tooltip: {
                    y: {
                        formatter: function (value, {series, seriesIndex, dataPointIndex, w}) {
                            return w.config.series[seriesIndex].comment[dataPointIndex];
                        }
                    }
                },
                colors: colors
            };

            if (self.statementsChart !== null) {
                self.statementsChart.destroy();
            }
            self.statementsChart = new ApexCharts(document.getElementById('statements-chart'), options);
            self.statementsChart.render();
        },
        drawStatementHistoryChart() {
            let self = this;
            let history = self.activeStatement?.deed.deed_history === undefined ? [] : self.activeStatement.deed.deed_history;
            let data = [];
            history.forEach(item => {
                data.push({
                    date: moment(item.created_at).format('YYYY-MM-DD'),
                    value: item.value,
                });
            });

            let sorted = data.sort((a, b) => {
                if (moment(a.date).isBefore(b.date)) {
                    return -1;
                } else if (moment(a.date).isAfter(b.date)) {
                    return 1;
                } else {
                    return 0;
                }
            });

            let categories = [];
            sorted.forEach(item => {
                if (categories.indexOf(item.date) === -1) {
                    categories.push(item.date);
                }
            });

            let series = [{data: []}];
            categories.forEach(category => {
                sorted.forEach(item => {
                    if (item.date === category) {
                        series[0].data.push({x: category, y: item.value});
                    }
                });
            });

            let options = {
                chart: {
                    height: 400,
                    type: 'line',
                    zoom: {
                        enabled: false
                    },
                    parentHeightOffset: 0,
                    toolbar: {
                        show: false
                    }
                },
                series: series,
                markers: {
                    strokeWidth: 7,
                    strokeOpacity: 1,
                    strokeColors: [window.colors.solid.white],
                    colors: [self.getColorByStatementStatus(self.activeStatement?.review?.review_status.name_en)]
                },
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    curve: 'smooth'
                },
                colors: [self.getColorByStatementStatus(self.activeStatement?.review?.review_status.name_en)],
                grid: {
                    xaxis: {
                        lines: {
                            show: true
                        }
                    },
                    padding: {
                        top: -20
                    }
                },
                xaxis: {
                    categories: categories,
                },
                yaxis: {
                    min: 0,
                    max: 5,
                    forceNiceScale: true,
                    decimalsInFloat: 0
                }
            };

            if (self.statementsChart !== null) {
                self.statementsChart.destroy();
            }

            if (self.statementHistoryChart !== null) {
                self.statementHistoryChart.destroy();
            }

            self.statementHistoryChart = new ApexCharts(document.getElementById('statements-chart'), options);
            self.statementHistoryChart.render();
        },
        componentHide() {
            this.componentActive = null;
            $("#componentShowModal").modal("hide");
        },
        componentShow(id) {
            let self = this;
            const activeYear = document.getElementById("yearSelect").value;
            const dataSource = this.activeOrg[activeYear].table;
            let y = dataSource.filter((x) => x.id == id);
            this.componentActive = y[0];
            this.activeStatement = y[0]?.statements?.[0];
            $("#componentShowModal").modal("show");
            this.drawStatementsChart();
            this.$nextTick(() => {
                feather.replace();
                self.initTooltips();
            });
        },
        kpiHide() {
            $("#kpiViewModal").modal("hide");
            this.kpiActive = null;
            // kill chart
            if (this.kpiChart) {
                this.kpiChart.destroy();
                this.kpiChart = null;
            }
        },
        kpiShow(id) {
            var thisComponent = this;
            axios
                .get(`/${thisComponent.locale}/axios/organisations/kpis/${id}`, {})
                .then(function (response) {
                    //console.log(response.data);
                    thisComponent.kpiActive = response.data;
                    $("#kpiViewModal").modal("show");
                    thisComponent.$nextTick(() => {
                        thisComponent.drawKpiChart();
                    });
                })
                .catch(function (error) {
                    console.log(error);
                    console.log(error.response);
                });
        },
        sanctionShow(id) {
            var thisComponent = this;
            axios
                .get(`/${thisComponent.locale}/axios/sanctions/${id}`)
                .then(function (response) {
                    //console.log(response.data);
                    thisComponent.sanctionActive = response.data;
                })
                .catch(function (error) {
                    console.log(error.response);
                });
            //let y = this.collection?.sanctions?.filter((x) => x.id == id);
            //this.sanctionActive = y[0];
            $("#sanctionShowModal").modal("show");
        },
        sanctionHide() {
            this.sanctionActive = null;
            $("#sanctionShowModal").modal("hide");
        },
        updateOrg() {
            const org = document.getElementById("basicSelect").value;
            let selectedOrg = this.collection?.data?.filter((e) => {
                return e.name == org;
            });
            this.activeOrg = selectedOrg[0].data;
            //console.log(this.activeOrg);
            this.$nextTick(() => {
                this.drawRadar();
                this.buildTable();
            });
        },
        updateActiveStatement(index) {
            let self = this;
            self.activeStatement = self.componentActive?.statements?.[index];
            self.drawStatementHistoryChart();
        },
        statusBulletColor(status) {
            switch (status) {
                case 'Pending':
                    return 'bullet-warning';
                case 'Accepted':
                    return 'bullet-success';
                case 'Rejected':
                    return 'bullet-danger';
                default:
                    return 'bullet-warning';
            }
        },
        initTooltips() {
            const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
            const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));
        },
        getColorByStatementStatus(status) {
            switch (status) {
                case 'Pending':
                    return '#ff9f43';
                case 'Accepted':
                    return '#28c76f';
                case 'Rejected':
                    return '#ea5455';
                default:
                    return '#ff9f43';
            }
        }
    },
    mounted() {
        var thisComponent = this;
        window.component = thisComponent;
        axios
            .get("/" + thisComponent.locale + "/axios/organisations/insights", {})
            .then(function (response) {
                thisComponent.collection = response.data;
                thisComponent.activeOrg = response.data.data[0].data;
                thisComponent.$nextTick(() => {
                    thisComponent.drawRadar();
                    thisComponent.buildTable();
                    thisComponent.buildKpiTable();
                    thisComponent.buildSanctionsTable();
                });
            })
            .catch(function (error) {
                console.log(error);
                console.log(error.response);
            });
    },
};
</script>
