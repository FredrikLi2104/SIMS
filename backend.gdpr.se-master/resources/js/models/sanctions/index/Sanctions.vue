<template>
    <div class="row">
        <div class="col-12">
            <div class="card invoice-list-wrapper">
                <div class="card-body mt-2">
                    <form class="dt_adv_search" method="POST">
                        <div class="row g-1 mb-md-1">
                            <div class="col-md-3">
                                <label for="dpa-filter" class="form-label">{{ messages.dpa }}:</label>
                                <select id="dpa-filter" class="form-select form-control" v-model="dpaId"
                                        @change="filterTable">
                                    <option value="">{{ messages.pleaseSelect }}</option>
                                    <option v-for="dpa in dpas" :value="dpa.id">{{
                                            `${dpa.title} &mdash; ${dpa.count}`
                                        }}
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="sni-filter" class="form-label">{{ messages.sni }}:</label>
                                <select id="sni-filter" class="form-select form-control" v-model="sniId"
                                        @change="filterTable">
                                    <option value="">{{ messages.pleaseSelect }}</option>
                                    <option value="-1">{{ messages.empty }}</option>
                                    <option v-for="sni in snis" :value="sni.id">
                                        {{ `${sni.code} | ${sni[`desc_${locale}`]}` }}
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="type-filter" class="form-label">{{ messages.type }}:</label>
                                <select id="type-filter" class="form-select form-control" v-model="typeId"
                                        @change="filterTable">
                                    <option value="">{{ messages.pleaseSelect }}</option>
                                    <option value="-1">{{ messages.empty }}</option>
                                    <option v-for="type in types" :value="type.id">
                                        {{ `${type[`text_${locale}`]}` }}
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="type-filter" class="form-label">{{ messages.statements }}:</label>
                                <select id="type-filter" class="form-select form-control" v-model="statementId"
                                        @change="filterTable">
                                    <option value="">{{ messages.pleaseSelect }}</option>
                                    <option value="-1">{{ messages.empty }}</option>
                                    <option v-for="statement in statements" :value="statement.id">
                                        {{ statement.subcode }}
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="user-filter" class="form-label">{{
                                        `${messages.updated} ${messages.by}`
                                    }}:</label>
                                <select id="user-filter" class="form-select form-control" v-model="userId"
                                        @change="filterTable">
                                    <option value="">{{ messages.pleaseSelect }}</option>
                                    <option v-for="(name, id) in users" :value="id">{{ name }}</option>
                                </select>
                            </div>
                        </div>
                    </form>
                </div>
                <hr class="my-0">
                <div class="card-datatable table-responsive">
                    <table class="invoice-list-table table" id="dataTable"></table>
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
                                @click="sanctionShowClose"></button>
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
                                    <dt class="col-4">{{ messages.createdAt }}</dt>
                                    <dd class="col-8">{{ sanctionActive?.created_at_for_humans }}</dd>
                                </dl>
                                <dl class="row">
                                    <dt class="col-4">{{ messages.decidedOn }}</dt>
                                    <dd class="col-8">{{ sanctionActive?.decided_at_for_humans }}</dd>
                                </dl>
                                <dl class="row">
                                    <dt class="col-4">{{ messages.party }}</dt>
                                    <dd class="col-8">{{ sanctionActive?.party }}</dd>
                                </dl>
                                <dl class="row">
                                    <dt class="col-4">{{ messages.dpa }}</dt>
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
                                    <dt class="col-4">{{ messages.sni }}</dt>
                                    <dd class="col-8">
                                        {{
                                            sanctionActive?.sni === null ? '' : `${sanctionActive?.sni?.code} |
                                        ${sanctionActive?.sni?.[`desc_${locale}`]}`
                                        }}
                                    </dd>
                                </dl>
                                <dl class="row">
                                    <dt class="col-4">{{ messages.fine }}</dt>
                                    <dd class="col-8">{{
                                            sanctionActive?.fine ? parseInt(sanctionActive?.fine) + ' ' +
                                                (sanctionActive?.currency?.symbol ? sanctionActive?.currency.symbol : 'EUR') :
                                                ''
                                        }}
                                    </dd>
                                </dl>
                                <dl class="row">
                                    <dt class="col-4">{{ messages.type }}</dt>
                                    <dd class="col-8">{{ sanctionActive?.type?.[`text_${locale}`] }}</dd>
                                </dl>
                                <dl class="row">
                                    <dt class="col-4">{{ messages.outcome }}</dt>
                                    <dd class="col-8">{{ sanctionActive?.outcome }}</dd>
                                </dl>
                                <div class="d-flex mb-1">
                                    <a v-if="sanctionActive?.source" :href="sanctionActive?.source"
                                       class="btn btn-outline-primary waves-effect mb-25 me-50 d-flex" target="_blank">
                                        <i data-feather="external-link" class="me-25"></i>
                                        <span class="text-nowrap">{{ messages.source }}</span>
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
                                        <span class="text-nowrap">{{ messages.et_visit }}</span>
                                    </a>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="row">
                                    <div v-show="sanctionActive?.components.length" class="col-6">
                                        <dl>
                                            <dt>{{ messages.components }}</dt>
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
                                            <dt>{{ messages.statements }}</dt>
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
                                            <dt>{{ messages.articles }}</dt>
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
                                            <dt>{{ messages.tags }}</dt>
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
                                            <dt>{{ messages.documents }}</dt>
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
                            <button type="button" class="btn btn-primary" @click="sanctionShowClose">Ok</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <files :locale="locale" :sanction="sanctionActive" ref="files"></files>
    </div>
</template>
<script>
import Files from "./Files.vue";

export default {
    props: ['locale', 'messages', 'dpas', 'snis', 'statements', 'types', 'users'],
    components: {Files},
    data() {
        return {
            dataTable: null,
            collection: {},
            sanctionActive: null,
            dpaId: '',
            sniId: '',
            statementId: '',
            typeId: '',
            userId: '',
            descQuill: null,
        };
    },
    methods: {
        buildTable() {
            var thisComponent = this;
            let header = `
             <thead>
                <tr>
                    <th>${thisComponent.messages.id}</th>
                    <th>${thisComponent.messages.createdAt}</th>
                    <th>${thisComponent.messages.dpa}</th>
                    <th>${thisComponent.messages.decidedOn}</th>
                    <th>${thisComponent.messages.fine}</th>
                    <th>${thisComponent.messages.title}</th>
                    <th>${thisComponent.messages.party}</th>
                    <th>${thisComponent.messages.lastUpdated}</th>
                    <th class="text-center">${thisComponent.messages.actions}</th>
                </tr>
            </thead>
            `;
            document.getElementById("dataTable").innerHTML = header;
            thisComponent.dataTable = $(".invoice-list-table").DataTable({
                serverSide: true,
                processing: true,
                ajax: {
                    url: `/${thisComponent.locale}/axios/sanctions`,
                    dataSrc: function (json) {
                        thisComponent.collection.sanctions = json.sanctions;
                        return json.sanctions;
                    },
                    data: function (d) {
                        d.filters = {
                            'dpa_id': thisComponent.dpaId,
                            'sni_id': thisComponent.sniId,
                            'statement_id': thisComponent.statementId,
                            'type_id': thisComponent.typeId,
                            'user_id': thisComponent.userId,
                        }
                    }
                },
                "bStateSave": true,
                "fnStateSave": function (oSettings, oData) {
                    localStorage.setItem('DataTables_' + window.location.pathname, JSON.stringify(oData));
                },
                "fnStateLoad": function (oSettings) {
                    return JSON.parse(localStorage.getItem('DataTables_' + window.location.pathname));
                },
                lengthMenu: [10, 25, 50, 75, 100],
                paging: true,
                autoWidth: true,
                searching: true,
                columns: [{data: "id"}, {data: "created_at_for_humans"}, {data: "dpa"}, {data: "decided_at_for_humans"}, {data: "fine"}, {data: "title"}, {data: "party"}, {data: 'updated_at_for_humans'}],
                columnDefs: [
                    {
                        // ID
                        targets: 0,
                        responsivePriority: 0,
                        width: "5%",
                        render: function (data, type, full, meta) {
                            let r = `<p class="mx-0 my-0">${full.id}</p>`;
                            return r;
                        },
                    },
                    {
                        // created_at
                        targets: 1,
                        responsivePriority: 1,
                        width: "10%",
                        render: function (data, type, full, meta) {
                            if (type === "sort") {
                                return Date.parse(full.created_at_for_humans);
                            } else {
                                let r = `<p class="mx-0 my-0">${full.created_at_for_humans}</p>`;
                                return r;
                            }
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
                                <img src='/images/flags/svg/${full.dpa?.country?.code}.svg' width="100%"/>
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
                                    r = `<p>${parseInt(full.fine)} ${full.currency?.symbol ? full.currency?.symbol : "EUR"}</p>`;
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
                        // party
                        targets: 6,
                        responsivePriority: 6,
                        render: function (data, type, full, meta) {
                            let r = `<p>${full.party ?? ''}</p>`;
                            return r;
                        },
                    },
                    {
                        // actions
                        targets: 8,
                        responsivePriority: 7,
                        width: "15%",
                        orderable: false,
                        render: function (data, type, full, meta) {
                            let etBtn = '';

                            if (full.etid) {
                                etBtn = `
                                <a href="https://www.enforcementtracker.com/Etid-${full.etid}" class="btn btn-icon btn-flat-primary waves-effect" data-bs-toggle="tooltip" data-bs-original-title="${thisComponent.messages.et_visit}" target="_blank">
                                    <i data-feather="external-link"></i>
                                </a>
                            `;
                            }

                            return `
                            <div class="d-flex">
                                <a href="/${thisComponent.locale}/sanctions/${full.id}/edit" class="btn btn-icon btn-flat-primary waves-effect" data-bs-toggle="tooltip" data-bs-original-title="${thisComponent.messages.edit}">
                                    <i data-feather="edit"></i>
                                </a>
                                <a href="${full.url}" class="btn btn-icon btn-flat-primary waves-effect" data-bs-toggle="tooltip" data-bs-original-title="${thisComponent.messages.visit}" target="_blank">
                                    <i data-feather="external-link"></i>
                                </a>
                                ${etBtn}
                                <a href="#" class="btn btn-icon btn-flat-primary waves-effect" onclick="thisComponent.sanctionShow(${full.id})" data-bs-toggle="tooltip" data-bs-original-title="${thisComponent.messages.view}">
                                    <i data-feather="eye"></i>
                                </a>
                                <a href="#" class="btn btn-icon btn-flat-primary waves-effect" onclick="thisComponent.showFilesModal(${full.id})" data-bs-toggle="tooltip" data-bs-original-title="${thisComponent.messages.documents}">
                                    <i data-feather="upload"></i>
                                </a>
                            </div>
                            `;
                        },
                    },
                ],
                order: [[0, "desc"]],
                dom: `
                <"row d-flex justify-content-start align-items-center m-1"
                    <"col-lg-12 d-flex justify-content-start align-items-center"
                        <"#cardHeader">
                    >
                    <"col-lg-6 d-flex align-items-center"l>
                    <"col-lg-6 d-flex align-items-center justify-content-lg-end flex-lg-nowrap flex-wrap pe-lg-1 p-0"f>
                >t
                <"d-flex justify-content-between mx-2 row"
                    <"col-sm-12 col-md-6"i>
                    <"col-sm-12 col-md-6"p>
                ">`,
                initComplete: function () {
                    let domHtml = `
                    <div class="card-body">
                        <h4 class="card-title">${thisComponent.messages.sanctions}</h4>
                        <h6 class="card-subtitle text-muted">${thisComponent.messages.sanctions} ${thisComponent.messages.index}</h6>
                    </div>
                    `;
                    $("#cardHeader").html(domHtml);
                },
                drawCallback: function () {
                    thisComponent.$nextTick(() => {
                        if (feather) {
                            feather.replace({
                                width: 14,
                                height: 14,
                            });
                        }
                    });
                    thisComponent.initTooltips();
                },
            });
        },
        sanctionShow(id) {
            let self = this;
            let y = self.collection?.sanctions?.filter((x) => x.id == id);
            self.sanctionActive = y[0];
            self.initDescQuill();
            $("#sanctionShowModal").modal("show");
            self.$nextTick(() => {
                feather.replace();
                self.initTooltips();
            });
        },
        sanctionShowClose() {
            $("#sanctionShowModal").modal("hide");
        },
        filterTable() {
            let filters = {
                'dpaId': this.dpaId,
                'sniId': this.sniId,
                'statementId': this.statementId,
                'typeId': this.typeId,
                'userId': this.userId,
            };

            localStorage.setItem('SanctionDataTablesFilters', JSON.stringify(filters));

            this.dataTable.draw();
        },
        showFilesModal(id) {
            this.sanctionActive = this.collection?.sanctions?.find(sanction => sanction.id == id);
            this.$refs.files.uploadUrl = `/${this.locale}/axios/sanctions/${this.sanctionActive.id}/files`;
            this.$refs.files.uploadedFilesUrl = `/${this.locale}/axios/sanctions/${this.sanctionActive.id}/files`;
            this.$refs.files.initDropzone();
            this.$refs.files.getSanctionFiles();
            let modalEl = document.getElementById('files-modal');
            let modal = new bootstrap.Modal(modalEl);
            modal.show();
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
        initTooltips() {
            const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
            const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));
        }
    },
    mounted() {
        window.thisComponent = this;
        let filters = localStorage.getItem('SanctionDataTablesFilters');

        try {
            filters = JSON.parse(filters);

            this.dpaId = filters.dpaId;
            this.sniId = filters.sniId;
            this.statementId = filters.statementId;
            this.typeId = filters.typeId;
            this.userId = filters.userId;
        } catch (e) {

        }

        this.buildTable();
    },
};
</script>
