<template>
    <div class="row">
        <div class="col-12">
            <div class="card invoice-list-wrapper">
                <div class="card-datatable table-responsive">
                    <table class="invoice-list-table table" id="dataTable"></table>
                </div>
            </div>
        </div>
        <div class="modal fade text-start modal-primary" id="sanctionShowModal" tabindex="-1" aria-labelledby="sanctionShowLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-extra-wide">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="sanctionShowLabel">{{ sanctionActive?.title }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" @click="sanctionShowClose"></button>
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
                                                        <img :src="`/images/flags/svg/${sanctionActive?.dpa?.country?.code}.svg`" style="width: 30px" />
                                                    </div>
                                                    <div class="col-10 align-items-center">
                                                        <p class="mx-0 my-0">{{ sanctionActive?.dpa?.name }}</p>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>{{ collection?.messages?.fine }}</td>
                                            <td>{{ sanctionActive?.fine ? parseInt(sanctionActive?.fine)+' '+(sanctionActive?.currency?.symbol ? sanctionActive?.currency.symbol : '') : '' }}</td>
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
    props: ["locale"],
    data() {
        return {
            dataTable: null,
            collection: null,
            sanctionActive: null,
        };
    },
    methods: {
        buildTable() {
            var thisComponent = this;
            let header = `
             <thead>
                <tr>
                    <th>${thisComponent.collection?.messages?.id}</th>
                    <th>${thisComponent.collection?.messages?.createdAt}</th>
                    <th>${thisComponent.collection?.messages?.dpa}</th>
                    <th>${thisComponent.collection?.messages?.decidedOn}</th>
                    <th>${thisComponent.collection?.messages?.fine}</th>
                    <th>${thisComponent.collection?.messages?.title}</th>
                    <th class="text-center">${thisComponent.collection?.messages?.actions}</th>
                </tr>
            </thead>
            `;
            document.getElementById("dataTable").innerHTML = header;
            var dataSource = thisComponent.collection.sanctions;
            thisComponent.dataTable = $(".invoice-list-table").DataTable({
                data: dataSource,
                createdRow: function (row, data, dataIndex) {
                    //$(row).addClass("row-auth-bg");
                },
                lengthMenu: [10, 25, 50, 75, 100],
                paging: true,
                autoWidth: true,
                searching: true,
                columns: [{ data: "id" }, { data: "created_at_for_humans" }, { data: "dpa" }, { data: "decided_at_for_humans" }, { data: "fine" }, { data: "title" }],
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
                                    r = `<p>${parseInt(full.fine)} ${full.currency?.symbol ? full.currency?.symbol : ''}</p>`;
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
                                        <button type="button" class="btn btn-gradient-primary mb-1" onClick="window.location.href='/${thisComponent.locale}/sanctions/${full.id}/edit';">
                                            ${feather.icons["edit"].toSvg({ class: "me-25" })}
                                            <span>${thisComponent.collection?.messages?.edit}</span>
                                        </button>
                                        <button type="button" class="btn btn-gradient-info waves-effect mb-1" onClick="window.open('${full.url}','_blank')">
                                            ${feather.icons["external-link"].toSvg({ class: "me-25" })}
                                            <span>${thisComponent.collection?.messages?.visit}</span>
                                        </button>
                                        <button type="button" class="btn btn-outline-primary waves-effect mb-1" onClick="window.thisComponent.sanctionShow(${full.id})">
                                            ${feather.icons["eye"].toSvg({ class: "me-25" })}
                                            <span>${thisComponent.collection?.messages?.view}</span>
                                        </button>
                                    </div>
                                </div>
                                `;
                            return r;
                            /*
                            let r = `
                                <div class="d-flex justify-content-center align-items-center px-2">
                                    <div class="d-flex flex-column">
                                        <div class="row mr-1">
                                            <a href="${full.url}" target="_blank">
                                                <button type="button" class="btn btn-outline-primary waves-effect">
                                                    ${feather.icons["external-link"].toSvg({ class: "me-25" })}
                                                    <span>${thisComponent.collection?.messages?.visit}</span>
                                                </button>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                `;
                            return r;
                            */
                        },
                    },
                ],
                order: [[0, "desc"]],
                dom: `
                <"row d-flex justify-content-start align-items-center m-1"
                    <"col-lg-8 d-flex justify-content-start align-items-center"
                        <"#cardHeader">
                    >
                    <"col-lg-4 d-flex justify-content-end align-items-center"f>l
                >t
                <"d-flex justify-content-between mx-2 row"
                    <"col-sm-12 col-md-6"i>
                    <"col-sm-12 col-md-6"p>
                ">`,
                initComplete: function () {
                    let domHtml = `
                    <div class="card-body">
                        <h4 class="card-title">${thisComponent.collection?.messages?.sanctions}</h4>
                        <h6 class="card-subtitle text-muted">${thisComponent.collection?.messages?.sanctions} ${thisComponent.collection?.messages?.index}</h6>
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
                    $(".select2").select2();
                    */
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
        draw() {
            var thisComponent = this;
            axios
                .get("/" + thisComponent.locale + "/axios/sanctions", {})
                .then(function (response) {
                    console.log(response.data);
                    thisComponent.collection = response.data;
                    thisComponent.$nextTick(() => {
                        thisComponent.buildTable();
                    });
                })
                .catch(function (error) {
                    console.log(error);
                    console.log(error.response);
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
        this.draw();
    },
};
</script>
