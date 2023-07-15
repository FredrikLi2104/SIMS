<template>
    <div class="row">
        <div class="row match-height">
            <div class="col-12">
                <!-- Radar Chart-->
                <div class="card">
                    <div class="card-header">
                        <select class="form-select" id="yearSelect" @change="updateInsights" style="width: 100px">
                            <option v-for="year in years" :key="year">{{
                                    year
                                }}
                            </option>
                        </select>
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
            <div class="col-6">
                <div class="card invoice-list-wrapper">
                    <div class="card-datatable table-responsive">
                        <table class="invoice-list-table table table-sm" id="dataTable"></table>
                    </div>
                </div>
            </div>
            <div class="col-6">
                <div class="card invoice-list-wrapper">
                    <div class="card-datatable table-responsive">
                        <table class="invoice-list-table table table-sm" id="kpiTable"></table>
                    </div>
                </div>
            </div>
        </div>
        <div class="row match-height">
            <div class="col-12">
                <div class="card invoice-list-wrapper">
                    <div class="card-body">
                        <div class="row g-1 mb-md-1">
                            <div class="col-md-3">
                                <label for="value-filter" class="form-label">{{ collection?.messages?.value }}:</label>
                                <select id="value-filter" class="form-select"
                                        v-model="sanctionFilters.value"
                                        @change="filterSanctions">
                                    <option value="">{{ collection?.messages?.pleaseSelect }}</option>
                                    <option v-for="value in sanctionValues" :value="value">{{ value }}</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="dpa-filter" class="form-label">{{ collection?.messages?.dpa }}:</label>
                                <select id="dpa-filter" class="form-select"
                                        :data-placeholder="collection?.messages?.pleaseSelect">
                                    <option value=""></option>
                                    <option v-for="dpa in dpas" :value="dpa.id" :data-country-code="dpa.country?.code">
                                        {{
                                            `${dpa.title} &mdash; ${dpa.count}`
                                        }}
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="country-filter" class="form-label">{{
                                        collection?.messages?.country
                                    }}:</label>
                                <select id="country-filter" class="form-select"
                                        :data-placeholder="collection?.messages?.pleaseSelect">
                                    <option value=""></option>
                                    <option v-for="country in countries" :value="country.id"
                                            :data-country-code="country.code">{{ country.name }}
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="sni-filter" class="form-label">{{
                                        collection?.messages?.sni
                                    }}:</label>
                                <select id="sni-filter" class="form-select" v-model="sanctionFilters.sniId"
                                        @change="filterSanctions">
                                    <option value="">{{ collection?.messages?.pleaseSelect }}</option>
                                    <option v-for="sni in snis" :value="sni.id">
                                        {{ `${sni.code} | ${sni[`desc_${locale}`]}` }}
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="outcome-filter" class="form-label">{{
                                        collection?.messages?.outcome
                                    }}:</label>
                                <select id="outcome-filter" class="form-select" v-model="sanctionFilters.outcomeId"
                                        @change="filterSanctions">
                                    <option value="">{{ collection?.messages?.pleaseSelect }}</option>
                                    <option v-for="outcome in outcomes" :value="outcome.id">
                                        {{ outcome[`desc_${locale}`] }}
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="tag-filter" class="form-label">{{
                                        collection?.messages?.tags
                                    }}:</label>
                                <select id="tag-filter" class="form-select select2"
                                        :data-placeholder="collection?.messages?.pleaseSelect"
                                        v-model="sanctionFilters.tagIds" multiple>
                                    <option v-for="tag in tags" :value="tag.id">
                                        {{ tag[`tag_${locale}`] }}
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="type-filter" class="form-label">{{
                                        collection?.messages?.components
                                    }}:</label>
                                <select id="type-filter" class="form-select form-control"
                                        v-model="sanctionFilters.componentId"
                                        @change="filterSanctions">
                                    <option value="">{{ collection?.messages?.pleaseSelect }}</option>
                                    <option v-for="component in components" :value="component.id">
                                        {{ component.code }}
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="type-filter" class="form-label">{{
                                        collection?.messages?.statements
                                    }}:</label>
                                <select id="type-filter" class="form-select form-control"
                                        v-model="sanctionFilters.statementId"
                                        @change="filterSanctions">
                                    <option value="">{{ collection?.messages?.pleaseSelect }}</option>
                                    <option v-for="statement in statements" :value="statement.id">
                                        {{ statement.subcode }}
                                    </option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <hr class="my-0">
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
                                            :key="statement.id"
                                            :class="statement.id === activeStatement.id ? 'active' : ''">
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
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="sanctionShowLabel">{{ sanctionActive?.title }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                                @click="sanctionHide"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-6">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="border rounded mb-1 p-25">
                                            <div id="desc-quill"></div>
                                        </div>
                                    </div>
                                </div>
                                <dl class="row">
                                    <dt class="col-4">{{ collection?.messages.createdAt }}</dt>
                                    <dd class="col-8">{{ sanctionActive?.created_at_for_humans }}</dd>
                                </dl>
                                <dl class="row">
                                    <dt class="col-4">{{ collection?.messages.decidedOn }}</dt>
                                    <dd class="col-8">{{ sanctionActive?.decided_at_for_humans }}</dd>
                                </dl>
                                <dl class="row">
                                    <dt class="col-4">{{ collection?.messages.party }}</dt>
                                    <dd class="col-8">{{ sanctionActive?.party }}</dd>
                                </dl>
                                <dl class="row">
                                    <dt class="col-4">{{ collection?.messages.dpa }}</dt>
                                    <dd class="col-8">
                                        <div class="d-flex align-items-center justify-content-start">
                                            <img v-if="sanctionActive?.dpa?.country != undefined"
                                                 :src="`/images/flags/svg/${sanctionActive?.dpa?.country?.code}.svg`"
                                                 style="width: 30px"/>
                                            <span class="ms-1">{{ sanctionActive?.dpa?.name }}</span>
                                        </div>
                                    </dd>
                                </dl>
                                <dl class="row">
                                    <dt class="col-4">{{ collection?.messages.sni }}</dt>
                                    <dd class="col-8">
                                        {{
                                            sanctionActive?.sni === null ? '' : `${sanctionActive?.sni?.code} |
                                        ${sanctionActive?.sni?.[`desc_${locale}`]}`
                                        }}
                                    </dd>
                                </dl>
                                <dl class="row">
                                    <dt class="col-4">{{ collection?.messages.fine }}</dt>
                                    <dd class="col-8">{{
                                            sanctionActive?.fine_eur ? parseInt(sanctionActive.fine_eur).toLocaleString(undefined, {
                                                style: "currency",
                                                currency: "EUR",
                                                maximumFractionDigits: 0,
                                            }) : ''
                                        }}
                                    </dd>
                                </dl>
                                <dl class="row">
                                    <dt class="col-4">{{ collection?.messages.type }}</dt>
                                    <dd class="col-8">{{ sanctionActive?.type?.[`text_${locale}`] }}</dd>
                                </dl>
                                <dl class="row">
                                    <dt class="col-4">{{ collection?.messages.outcome }}</dt>
                                    <dd class="col-8">{{ sanctionActive?.outcome?.[`desc_${locale}`] }}</dd>
                                </dl>
                                <div class="d-flex mb-1">
                                    <a v-if="sanctionActive?.source" :href="sanctionActive?.source"
                                       class="btn btn-outline-primary waves-effect mb-25 me-50 d-flex" target="_blank">
                                        <i data-feather="external-link" class="me-25"></i>
                                        <span class="text-nowrap">{{ collection?.messages.source }}</span>
                                    </a>
                                    <a v-if="sanctionActive?.url" :href="sanctionActive?.url"
                                       class="btn btn-outline-primary waves-effect mb-25 me-50 d-flex" target="_blank">
                                        <i data-feather="external-link" class="me-25"></i>
                                        <span class="text-nowrap">GDPRhub</span>
                                    </a>
                                    <a v-if="sanctionActive?.etid"
                                       :href="`https://www.enforcementtracker.com/Etid-${sanctionActive?.etid}`"
                                       class="btn btn-outline-primary waves-effect mb-25 d-flex" target="_blank">
                                        <i data-feather="external-link" class="me-25"></i>
                                        <span class="text-nowrap">{{ collection?.messages.et_visit }}</span>
                                    </a>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="row">
                                    <div v-show="sanctionActive?.components.length" class="col-6">
                                        <dl>
                                            <dt>{{ collection?.messages.components }}</dt>
                                            <dd>
                                                <span v-for="component in sanctionActive?.components" :key="component"
                                                      class="badge badge-light-primary me-25 mb-25"
                                                      data-bs-toggle="tooltip"
                                                      :data-bs-original-title="`${component[`name_${locale}`]} &mdash; ${component[`desc_${locale}`]}`">{{
                                                        component.code
                                                    }}</span>
                                            </dd>
                                        </dl>
                                    </div>
                                    <div v-show="sanctionActive?.statements.length" class="col-6">
                                        <dl>
                                            <dt>{{ collection?.messages.statements }}</dt>
                                            <dd>
                                            <span v-for="statement in sanctionActive?.statements" :key="statement.id"
                                                  class="badge badge-light-primary me-25 mb-25" data-bs-toggle="tooltip"
                                                  :data-bs-original-title="`${statement[`content_${locale}`]} &mdash; ${statement[`desc_${locale}`]}`">{{
                                                    statement.subcode
                                                }}</span>
                                            </dd>
                                        </dl>
                                    </div>
                                </div>
                                <div v-show="sanctionActive?.articles.length" class="row">
                                    <div class="col-12">
                                        <dl>
                                            <dt>{{ collection?.messages.articles }}</dt>
                                            <dd class="d-flex flex-wrap">
                                                <a v-for="article in sanctionActive?.articlesSorted"
                                                   :key="article.title" :href="article?.url"
                                                   class="btn btn-outline-primary waves-effect mb-25 me-50 d-flex"
                                                   target="_blank">
                                                    <i data-feather="external-link" class="me-25"></i>
                                                    <span class="text-nowrap">{{ article?.title }}</span>
                                                </a>
                                            </dd>
                                        </dl>
                                    </div>
                                </div>
                                <div v-show="sanctionActive?.tags.length" class="row">
                                    <div class="col-12">
                                        <dl>
                                            <dt>{{ collection?.messages.tags }}</dt>
                                            <dd>
                                                <span v-for="tag in sanctionActive?.tags" :key="tag.id"
                                                      class="badge badge-light-primary me-25 mb-25">{{
                                                        tag[`tag_${locale}`]
                                                    }}</span>
                                            </dd>
                                        </dl>
                                    </div>
                                </div>
                                <div v-show="sanctionActive?.sanction_files.length" class="row">
                                    <div class="col-12">
                                        <dl>
                                            <dt>{{ collection?.messages.documents }}</dt>
                                            <dd class="d-flex flex-wrap">
                                                <a v-for="file in sanctionActive?.sanction_files" :key="file.id"
                                                   :href="file.url"
                                                   class="btn btn-outline-primary waves-effect mb-25 me-50 d-flex"
                                                   target="_blank">
                                                    <i data-feather="download" class="me-25"></i>
                                                    <span class="text-nowrap">{{ file.title }}</span>
                                                </a>
                                            </dd>
                                        </dl>
                                    </div>
                                </div>
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
    props: ['locale', 'dpas', 'countries', 'snis', 'outcomes', 'tags', 'components', 'statements'],
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
            descQuill: null,
            sanctionValues: [1, 2, 3, 4, 5],
            sanctionFilters: {
                value: '',
                dpaId: '',
                countryId: '',
                sniId: '',
                outcomeId: '',
                tagIds: '',
                componentId: '',
                statementId: '',
            },
            categoryFilters: []
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
                                        <button type="button" class="btn btn-outline-primary waves-effect mb-1 d-flex" onClick="window.component.componentShow(${full.id})">
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
                    <th>${thisComponent.collection?.messages?.target}</th>
                    <th>${thisComponent.collection?.messages?.value}</th>
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
                columns: [{data: "id"}, {data: "name"}, {data: "target"}, {data: "value"}],
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
                        // target
                        targets: 2,
                        responsivePriority: 2,
                        width: "7.5%",
                        render: function (data, type, full, meta) {
                            let r = `<p>${full.target}</p>`;
                            return r;
                        },
                    },
                    {
                        // value
                        targets: 3,
                        responsivePriority: 3,
                        width: "7.5%",
                        render: function (data, type, full, meta) {
                            let r = `<p>${full.value}</p>`;
                            return r;
                        },
                    },
                    {
                        // actions
                        targets: 4,
                        responsivePriority: 4,
                        width: "10%",
                        orderable: false,
                        render: function (data, type, full, meta) {
                            let r = `
                                <div class="d-flex justify-content-center align-items-center px-2">
                                    <div class="d-flex flex-column">
                                        <button type="button" class="btn btn-outline-primary waves-effect mb-1 d-flex" onClick="window.component.kpiShow(${full.id})">
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
                    <th class="">${thisComponent.collection?.messages?.dpa}</th>
                    <th class="">${thisComponent.collection?.messages?.date_added}</th>
                    <th>${thisComponent.collection?.messages?.fine}</th>
                    <th>${thisComponent.collection?.messages?.title}</th>
                    <th>${thisComponent.collection?.messages?.party}</th>
                    <th>${thisComponent.collection?.messages?.statement}/${thisComponent.collection?.messages?.value}</th>
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
                    data: function (d) {
                        d.filters = {
                            value: thisComponent.sanctionFilters.value,
                            dpa_id: thisComponent.sanctionFilters.dpaId,
                            country_id: thisComponent.sanctionFilters.countryId,
                            sni_id: thisComponent.sanctionFilters.sniId,
                            outcome_id: thisComponent.sanctionFilters.outcomeId,
                            tag_ids: thisComponent.sanctionFilters.tagIds,
                            component_id: thisComponent.sanctionFilters.componentId,
                            statement_id: thisComponent.sanctionFilters.statementId,
                            categories: thisComponent.categoryFilters,
                        }
                    }
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
                        // DPA
                        targets: 1,
                        responsivePriority: 1,
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
                        //created_at
                        targets: 2,
                        responsivePriority: 2,
                        width: "10%",
                        render: function (data, type, full, meta) {
                            //console.log(full);
                            let r = `<p>${full.created_at_for_humans}</p>`;
                            return r;
                        },
                    },
                    {
                        // fine
                        targets: 3,
                        responsivePriority: 3,
                        render: function (data, type, full, meta) {
                            let r = `<p>${full.fine_eur ? parseInt(full.fine_eur).toLocaleString(undefined, {
                                style: "currency",
                                currency: "EUR",
                                maximumFractionDigits: 0,
                            }) : ''}</p>`;
                            return r;
                        },
                    },
                    {
                        // title
                        targets: 4,
                        responsivePriority: 4,
                        render: function (data, type, full, meta) {
                            let r = `<p>${full.title}</p>`;
                            return r;
                        },
                    },
                    {
                        // party
                        targets: 5,
                        responsivePriority: 5,
                        render: function (data, type, full, meta) {
                            let r = `<p>${full.party ?? ''}</p>`;
                            return r;
                        },
                    },
                    {
                        // statement/value
                        targets: 6,
                        responsivePriority: 6,
                        render: function (data, type, full, meta) {
                            let r = document.createElement('div');
                            r.classList.add('d-flex', 'flex-wrap');
                            console.log(full.statements);
                            full.statements.forEach((statement, index) => {
                                if (statement.deed) {
                                    let a = document.createElement('a');
                                    a.href = '#';
                                    a.classList.add('me-25', 'fw-bold', 'show-statement-details');
                                    a.dataset.componentId = statement.component.id;
                                    a.dataset.statementId = statement.id;
                                    a.style.color = statement.deed?.color;
                                    a.style['text-decoration'] = 'underline';
                                    a.innerHTML = `${statement.subcode}/${statement.deed?.value}`;
                                    r.append(a);
                                }
                            });

                            return r.outerHTML;
                        },
                    },
                    {
                        // actions
                        targets: -1,
                        responsivePriority: -1,
                        width: "15%",
                        orderable: false,
                        render: function (data, type, full, meta) {
                            let r = `
                                <div class="d-flex justify-content-center align-items-center px-2">
                                    <div class="d-flex flex-column">
                                        <button type="button" class="btn btn-outline-primary waves-effect mb-50" onclick="window.component.sanctionShow(${full.id})">
                                            ${feather.icons["eye"].toSvg({class: "me-25"})}
                                            <span>${thisComponent.collection?.messages?.view}</span>
                                        </button>
                                        <button type="button" class="btn btn-gradient-info waves-effect" onclick="window.open('${full.url}','_blank')">
                                            ${feather.icons["external-link"].toSvg({class: "me-25"})}
                                            <span>${thisComponent.collection?.messages?.visit}</span>
                                        </button>
                                    </div>
                                </div>
                                `;
                            return r;
                        },
                    },
                ],
                order: [[6, 'asc']],
                dom: `
                <"row d-flex justify-content-start align-items-center m-1"
                    <"col-lg-8 d-flex justify-content-start align-items-center"
                        <"#sanctionCardHeader">
                    >
                    <"col-lg-4 d-flex justify-content-end align-items-center"f>
                >
                <"row d-flex justify-content-between align-items-center m-1"
                    <"col-lg-4"l>
                    <"col-lg-8"<"#category-filters">>
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

                    let categoryFilters = `
                    <div class="btn-group" role="group">
                        <input type="checkbox" id="maturity-filter" class="btn-check" autocomplete="off" name="category-filters[]" value="M" checked onchange="window.filterByCategory()">
                        <label for="maturity-filter" class="btn btn-primary waves-effect waves-float waves-light">${thisComponent.collection?.messages?.maturity}</label>
                        <input type="checkbox" id="principles-filter" class="btn-check" autocomplete="off" name="category-filters[]" value="P" checked onchange="window.filterByCategory()">
                        <label for="principles-filter" class="btn btn-primary waves-effect waves-float waves-light">${thisComponent.collection?.messages?.principles}</label>
                        <input type="checkbox" id="rights-filter" class="btn-check" autocomplete="off" name="category-filters[]" value="R" checked onchange="window.filterByCategory()">
                        <label for="rights-filter" class="btn btn-primary waves-effect waves-float waves-light">${thisComponent.collection?.messages?.rights}</label>
                        <input type="checkbox" id="obligations-filter" class="btn-check" autocomplete="off" name="category-filters[]" value="S" checked onchange="window.filterByCategory()">
                        <label for="obligations-filter" class="btn btn-primary waves-effect waves-float waves-light">${thisComponent.collection?.messages?.obligations}</label>
                    </div>
                    `;
                    document.getElementById('category-filters').classList.add('d-flex', 'justify-content-end');
                    document.getElementById('category-filters').innerHTML = categoryFilters;
                },
                drawCallback: function (settings) {
                    Array.prototype.forEach.call(document.getElementsByClassName('show-statement-details'), (trigger) => {
                        trigger.addEventListener('click', function () {
                            thisComponent.componentShow(trigger.dataset['componentId'], trigger.dataset['statementId']);
                        });
                    });
                }
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
        componentShow(id, statementId) {
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
            if (statementId !== undefined) {
                self.activeStatement = self.componentActive?.statements?.find(statement => statement.id == statementId);
                self.drawStatementHistoryChart();
            }
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
            this.initDescQuill();
            $("#sanctionShowModal").modal("show");
        },
        sanctionHide() {
            this.sanctionActive = null;
            $("#sanctionShowModal").modal("hide");
        },
        updateInsights() {
            this.drawRadar();
            this.buildTable();
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
        },
        initDescQuill() {
            let options = {
                readOnly: true,
                theme: 'bubble',
            };
            if (this.descQuill === null) {
                this.descQuill = new Quill('#desc-quill', options);
            }
            try {
                this.descQuill.setContents(JSON.parse(this.sanctionActive[`desc_${this.locale}`]));
            } catch (e) {

            }
        },
        handleSanctionModalShown() {
            let self = this;
            let modalEl = document.getElementById('sanctionShowModal');
            modalEl.addEventListener('shown.bs.modal', function (event) {
                feather.replace();
                self.initTooltips();
            });
        },
        filterSanctions() {
            this.sanctionsTable.draw();
        },
        initSelect2() {
            let self = this;
            $('.select2').select2({
                allowClear: true
            });
            $('#tag-filter').on('change.select2', function (e) {
                self.sanctionFilters.tagIds = $('#tag-filter').val();
                self.filterSanctions();
            });
        },
        initDpaSelect2() {
            let self = this;
            $('#dpa-filter').select2({
                templateResult: self.formatDpaAndCountry,
                templateSelection: self.formatDpaAndCountry,
                allowClear: true
            });
            $('#dpa-filter').on('select2:select', function (e) {
                self.sanctionFilters.dpaId = e.params.data.id;
                self.filterSanctions();
            });
            $('#dpa-filter').on('select2:unselect', function (e) {
                self.sanctionFilters.dpaId = '';
                self.filterSanctions();
            });
        },
        initCountrySelect2() {
            let self = this;
            $('#country-filter').select2({
                templateResult: self.formatDpaAndCountry,
                templateSelection: self.formatDpaAndCountry,
                allowClear: true
            });
            $('#country-filter').on('select2:select', function (e) {
                self.sanctionFilters.countryId = e.params.data.id;
                self.filterSanctions();
            });
            $('#country-filter').on('select2:unselect', function (e) {
                self.sanctionFilters.countryId = '';
                self.filterSanctions();
            });
        },
        formatDpaAndCountry(option) {
            let $wrapper = $('<span>');
            $wrapper.addClass('d-flex');
            let $text = $('<span>');
            $text.html(option.text);
            $wrapper.append($text);

            let countryCode = option.element?.dataset.countryCode;
            if (countryCode !== undefined) {
                let $img = $('<img>');
                $img.attr('src', `/images/flags/svg/${countryCode}.svg`);
                $img.attr('width', 30);
                $img.addClass('me-50');
                $wrapper.prepend($img);
            }

            return $wrapper;
        },
        filterByCategory() {
            let self = this;
            self.categoryFilters = [];
            document.getElementsByName('category-filters[]').forEach(filter => {
                if (filter.checked) {
                    self.categoryFilters.push(filter.value);
                }
            });
            self.filterSanctions();
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
                    thisComponent.initSelect2();
                    thisComponent.initDpaSelect2();
                    thisComponent.initCountrySelect2();
                });
            })
            .catch(function (error) {
                console.log(error);
                console.log(error.response);
            });

        this.handleSanctionModalShown();
        window.filterByCategory = function () {
            thisComponent.filterByCategory();
        };
    },
    computed: {
        years() {
            return Object.keys(this.activeOrg).sort((a, b) => b - a);
        }
    }
};
</script>
<style scoped>
.dark-layout table tr.active {
    background-color: #161d31;
}

table tr.active {
    background-color: #f8f8f8;
}
</style>
