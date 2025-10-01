<template>
    <div class="row">
        <div class="col-12">
            <div class="card invoice-list-wrapper">
                <div class="card-datatable table-responsive">
                    <table class="invoice-list-table table" id="dataTable"></table>
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
                                @click="sanctionShowClose"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="table-responsive">
                                <table class="table">
                                    <thead class="table-light">
                                    <tr>
                                        <th>{{ messages.key }}</th>
                                        <th>{{ messages.value }}</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr>
                                        <td>{{ messages.id }}</td>
                                        <td>{{ sanctionActive?.id }}</td>
                                    </tr>
                                    <tr>
                                        <td>{{ messages.createdAt }}</td>
                                        <td>{{ sanctionActive?.created_at_for_humans }}</td>
                                    </tr>
                                    <tr>
                                        <td>{{ messages.lastUpdated }}</td>
                                        <td>{{ sanctionActive?.updated_at_for_humans }}</td>
                                    </tr>
                                    <tr>
                                        <td>{{ `${messages.updated} ${messages.by}` }}</td>
                                        <td>{{ sanctionActive?.user?.name }}</td>
                                    </tr>
                                    <tr>
                                        <td>{{ messages.title }}</td>
                                        <td>{{ sanctionActive?.title }}</td>
                                    </tr>
                                    <tr>
                                        <td>{{ messages.dpa }}</td>
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
                                        <td>{{ messages.fine }}</td>
                                        <td>{{
                                                sanctionActive?.fine ? parseInt(sanctionActive?.fine) + " " +
                                                    (sanctionActive?.currency?.symbol ? sanctionActive?.currency.symbol : "EUR")
                                                    :
                                                    ""
                                            }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>{{ messages.party }}</td>
                                        <td>{{ sanctionActive?.party }}</td>
                                    </tr>
                                    <tr>
                                        <td>{{ messages.startedOn }}</td>
                                        <td>{{ sanctionActive?.started_at_for_humans }}</td>
                                    </tr>
                                    <tr>
                                        <td>{{ messages.decidedOn }}</td>
                                        <td>{{ sanctionActive?.decided_at_for_humans }}</td>
                                    </tr>
                                    <tr>
                                        <td>{{ messages.publishedOn }}</td>
                                        <td>{{ sanctionActive?.published_at_for_humans }}</td>
                                    </tr>
                                    <tr>
                                        <td>{{ messages.articles }}</td>
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
                            <button type="button" class="btn btn-primary" @click="sanctionShowClose">Ok</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    props: ['locale', 'messages', 'statementSubCode'],
    data() {
        return {
            dataTable: null,
            collection: {},
            sanctionActive: null,
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
                    url: `/${thisComponent.locale}/axios/organisations/insights/statement/sanctions`,
                    dataSrc: function (json) {
                        thisComponent.collection.sanctions = json.sanctions;
                        return json.sanctions;
                    },
                },
                "search": {
                    "search": thisComponent.statementSubCode
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
                            let r = `
                                <div class="d-flex justify-content-center align-items-center px-2">
                                    <div class="d-flex flex-column text-nowrap">
                                        <button type="button" class="btn btn-gradient-info waves-effect mb-1" onClick="window.open('${full.url}','_blank')">
                                            ${feather.icons["external-link"].toSvg({class: "me-25"})}
                                            <span>${thisComponent.messages.visit}</span>
                                        </button>`;
                            if (full.etid) {
                                r += `<button type="button" class="btn btn-gradient-info waves-effect mb-1" onClick="window.open('https://www.enforcementtracker.com/Etid-${full.etid}','_blank')">
                                                ${feather.icons["external-link"].toSvg({class: "me-25"})}
                                                <span>${thisComponent.messages.et_visit}</span>
                                            </button>`;
                            }
                            r += `<button type="button" class="btn btn-outline-primary waves-effect mb-1" onClick="window.thisComponent.sanctionShow(${full.id})">
                                            ${feather.icons["eye"].toSvg({class: "me-25"})}
                                            <span>${thisComponent.messages.view}</span>
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
                drawCallback: function () {
                    thisComponent.$nextTick(() => {
                        if (feather) {
                            feather.replace({
                                width: 14,
                                height: 14,
                            });
                        }
                    });
                },
            });
        },
        sanctionShow(id) {
            let y = this.collection?.sanctions?.filter((x) => x.id == id);
            this.sanctionActive = y[0];
            $("#sanctionShowModal").modal("show");
        },
        sanctionShowClose() {
            $("#sanctionShowModal").modal("hide");
        },
    },
    mounted() {
        window.thisComponent = this;
        this.buildTable();
    },
};
</script>
