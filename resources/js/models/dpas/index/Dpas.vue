<template>
    <div class="row">
        <div class="col-12">
            <div class="card invoice-list-wrapper">
                <div class="card-datatable table-responsive">
                    <table class="invoice-list-table table" id="dataTable"></table>
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
        };
    },
    methods: {
        buildTable() {
            var thisComponent = this;
            let header = `
             <thead>
                <tr>
                    <th>${thisComponent.collection?.messages?.id}</th>
                    <th>${thisComponent.collection?.messages?.dpa}</th>
                    <th>${thisComponent.collection?.messages?.createdAt}</th>
                    <th class="text-center">${thisComponent.collection?.messages?.actions}</th>
                </tr>
            </thead>
            `;
            document.getElementById("dataTable").innerHTML = header;
            var dataSource = thisComponent.collection.dpas;
            thisComponent.dataTable = $(".invoice-list-table").DataTable({
                data: dataSource,
                createdRow: function (row, data, dataIndex) {
                    //$(row).addClass("row-auth-bg");
                },
                lengthMenu: [],
                paging: false,
                autoWidth: true,
                searching: true,
                columns: [{ data: "id" }, { data: "name" }, { data: "created_at_for_humans" }],
                columnDefs: [
                    {
                        // columnA
                        targets: 0,
                        responsivePriority: 0,
                        width: "10%",
                        render: function (data, type, full, meta) {
                            let r = `<p class="mx-0 my-0">${full.id}</p>`;
                            return r;
                        },
                    },
                    {
                        // columnB
                        targets: 1,
                        responsivePriority: 1,
                        width: "20%",
                        render: function (data, type, full, meta) {
                            // has image?
                            let r = `
                            <div class="row d-flex align-items-center justify-content-start">
                                <div class="col-1 px-0">
                            `;
                            if (full.country) {
                                r += `
                                <img src='/images/flags/svg/${full.country.code}.svg' width="40px"/>
                                `;
                            }
                            r += `
                                </div>
                                <div class="col-4 align-items-center">
                                    <p class="mx-0 my-0">${full.name}</p>
                                </div>
                            </row>`;

                            return r;
                        },
                    },
                    {
                        // columnB
                        targets: 2,
                        responsivePriority: 2,
                        width: "10%",
                        type: "date",
                        render: function (data, type, full, meta) {
                            let r = `<p class="mx-0 my-0">${full.created_at_for_humans}</p>`;
                            return r;
                        },
                    },
                    {
                        // actions
                        targets: 3,
                        responsivePriority: 3,
                        width: "10%",
                        orderable: false,
                        render: function (data, type, full, meta) {
                            let r = `
                                <div class="d-flex justify-content-center align-items-center px-2">
                                    <div class="d-flex flex-column">
                                        <button type="button" class="btn btn-gradient-primary mb-1" onClick="window.location.href='/${thisComponent.locale}/dpas/${full.id}/edit';">
                                            ${feather.icons["edit"].toSvg({ class: "me-25" })}
                                            <span>${thisComponent.collection?.messages?.edit}</span>
                                        </button>
                                        <button type="button" class="btn btn-gradient-info waves-effect mb-1" onClick="window.open('${full.url}', '_blank')">
                                            ${feather.icons["external-link"].toSvg({ class: "me-25" })}
                                            <span>${thisComponent.collection?.messages?.visit}</span>
                                        </button>
                                    </div>
                                </div>
                                `;
                                /*
                            let r = `
                                <div class="d-flex justify-content-center align-items-center px-2">
                                    <div class="d-flex flex-column">
                                        <div class="row mr-1 mb-1">
                                            <a href="/${thisComponent?.locale}/dpas/${full.id}/edit" target="_self">
                                                <button type="button" class="btn btn-primary waves-effect">
                                                    ${feather.icons["edit"].toSvg({ class: "me-25" })}
                                                    <span>${thisComponent.collection?.messages?.edit}</span>
                                                </button>
                                            </a>
                                        </div>
                                        <div class="row mr-1 mb-1">
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
                                */

                            return r;
                        },
                    },
                ],
                order: [[2, "desc"]],
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
                        <h4 class="card-title">${thisComponent.collection?.messages?.dpas}</h4>
                        <h6 class="card-subtitle text-muted">${thisComponent.collection?.messages?.dpas} ${thisComponent.collection?.messages?.index}</h6>
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
                .get("/" + thisComponent.locale + "/axios/dpas", {})
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
    },
    mounted() {
        window.thisComponent = this;
        this.draw();
    },
};
</script>
