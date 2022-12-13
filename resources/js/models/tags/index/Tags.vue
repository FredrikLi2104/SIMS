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
                    <th>${thisComponent.collection?.messages?.tag} ${thisComponent.collection?.messages?.inEnglish}</th>
                    <th>${thisComponent.collection?.messages?.desc_en}</th>
                    <th>${thisComponent.collection?.messages?.tag} ${thisComponent.collection?.messages?.inSwedish}</th>
                    <th>${thisComponent.collection?.messages?.desc_se}</th>
                    <th class="text-center">${thisComponent.collection?.messages?.actions}</th>
                </tr>
            </thead>
            `;
            document.getElementById("dataTable").innerHTML = header;
            var dataSource = thisComponent.collection.tags;
            thisComponent.dataTable = $(".invoice-list-table").DataTable({
                data: dataSource,
                lengthMenu: [],
                paging: true,
                autoWidth: true,
                searching: true,
                columns: [{data: "id"}, {data: "tag_en"}, {data: "desc_en"}, {data: "tag_en"}, {data: "desc_en"}, {data: "id"}],
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
                        // Tag EN
                        targets: 1,
                        responsivePriority: 1,
                        width: "10%",
                        render: function (data, type, full, meta) {
                            let r = `<p>${full.tag_en ?? ''}</p>`;
                            return r;
                        },
                    },
                    {
                        // Desc. EN
                        targets: 2,
                        responsivePriority: 2,
                        width: "10%",
                        render: function (data, type, full, meta) {
                            let r = `<p>${full.desc_en ?? ''}</p>`;
                            return r;
                        },
                    },
                    {
                        // Tag SE
                        targets: 3,
                        responsivePriority: 3,
                        width: "10%",
                        render: function (data, type, full, meta) {
                            let r = `<p>${full.tag_se ?? ''}</p>`;
                            return r;
                        },
                    },
                    {
                        // Desc. SE
                        targets: 4,
                        responsivePriority: 4,
                        width: "10%",
                        render: function (data, type, full, meta) {
                            let r = `<p>${full.tag_se ?? ''}</p>`;
                            return r;
                        },
                    },
                    {
                        // actions
                        targets: 5,
                        responsivePriority: 5,
                        width: "10%",
                        orderable: false,
                        render: function (data, type, full, meta) {
                            let r = `
                                <div class="d-flex justify-content-center align-items-center px-2">
                                    <div class="d-flex flex-column">
                                        <div class="row mr-1">
                                            <a href="/${thisComponent.locale}/tags/${full.id}/edit">
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
                        <h4 class="card-title">${thisComponent.collection?.messages?.tags}</h4>
                        <h6 class="card-subtitle text-muted">${thisComponent.collection?.messages?.tags} ${thisComponent.collection?.messages?.index}</h6>
                    </div>
                    `;
                    $("#cardHeader").html(domHtml);
                },
            });
        },
        draw() {
            var thisComponent = this;
            axios
                .get("/" + thisComponent.locale + "/axios/tags", {})
                .then(function (response) {
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
