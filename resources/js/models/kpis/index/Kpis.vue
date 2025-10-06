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
                    <th>${thisComponent.collection?.messages?.name}</th>
                    <th>${thisComponent.collection?.messages?.desc}</th>
                    <th class="text-center">${thisComponent.collection?.messages?.actions}</th>
                </tr>
            </thead>
            `;
            document.getElementById("dataTable").innerHTML = header;
            var dataSource = thisComponent.collection.kpis;
            thisComponent.dataTable = $(".invoice-list-table").DataTable({
                data: dataSource,
                createdRow: function (row, data, dataIndex) {
                    //$(row).addClass("row-auth-bg");
                },
                lengthMenu: [],
                paging: false,
                autoWidth: true,
                searching: true,
                columns: [{ data: "id" }, { data: "name_" + thisComponent.locale }, { data: "desc_" + thisComponent.locale }, { data: "id" }],
                columnDefs: [
                    {
                        // id
                        targets: 0,
                        responsivePriority: 0,
                        width: "10%",
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
                            let r = `<p>${eval("full.name_" + thisComponent.locale)}</p>`;
                            return r;
                        },
                    },
                    {
                        // desc
                        targets: 2,
                        responsivePriority: 2,
                        width: "10%",
                        render: function (data, type, full, meta) {
                            let r = `<p>${eval("full.desc_" + thisComponent.locale)}</p>`;
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
                                        <div class="row mr-1">
                                            <a href="/${thisComponent.locale}/kpis/${full.id}/edit">
                                                <button type="button" class="btn btn-gradient-primary">${thisComponent.collection?.messages?.edit}</button>
                                            </a>
                                        </div>
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
                        <h4 class="card-title">${thisComponent.collection?.messages?.kpis}</h4>
                        <h6 class="card-subtitle text-muted">${thisComponent.collection?.messages?.kpis} ${thisComponent.collection?.messages?.index}</h6>
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
                },
            });
        },
        draw() {
            var thisComponent = this;
            axios
                .get("/" + thisComponent.locale + "/axios/kpis", {})
                .then(function (response) {
                   // console.log(response.data);
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
