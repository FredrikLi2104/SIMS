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
    name: "Currencies",
    props: ['locale', 'messages'],
    data() {
        return {
            dataTable: null,
            collection: null,
        };
    },
    methods: {
        buildTable() {
            let thisComponent = this;
            let header = `
             <thead>
                <tr>
                    <th>${thisComponent.messages?.id}</th>
                    <th>Symbol</th>
                    <th>${thisComponent.messages?.value}</th>
                    <th>${thisComponent.messages?.lastUpdated}</th>
                    <th class="text-center">${thisComponent.messages?.actions}</th>
                </tr>
            </thead>
            `;
            document.getElementById("dataTable").innerHTML = header;
            thisComponent.dataTable = $(".invoice-list-table").DataTable({
                processing: true,
                ajax: {
                    url: `/${thisComponent.locale}/axios/currencies`,
                    dataSrc: function (json) {
                        // thisComponent.collection.currencies = json.currencies;
                        return json.currencies;
                    },
                },
                lengthMenu: [],
                paging: false,
                autoWidth: true,
                searching: true,
                columns: [{data: "id"}, {data: "name_" + thisComponent.locale}, {data: "desc_" + thisComponent.locale}, {data: "id"}],
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
                        // symbol
                        targets: 1,
                        responsivePriority: 1,
                        width: "10%",
                        render: function (data, type, full, meta) {
                            let r = `<p>${full.symbol}</p>`;
                            return r;
                        },
                    },
                    {
                        // value
                        targets: 2,
                        responsivePriority: 2,
                        width: "10%",
                        render: function (data, type, full, meta) {
                            let r = `<p>${full.value}</p>`;
                            return r;
                        },
                    },
                    {
                        // updated at
                        targets: 3,
                        responsivePriority: 3,
                        width: "10%",
                        render: function (data, type, full, meta) {
                            let r = `<p>${full.updated_at_for_humans}</p>`;
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
                                        <div class="row mr-1">
                                            <a href="/${thisComponent.locale}/currencies/${full.id}/edit">
                                                <button type="button" class="btn btn-gradient-primary">${thisComponent.messages?.edit}</button>
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
                    <"col-lg-4 d-flex justify-content-end align-items-center"
                        <"#btn-wrapper">
                    >
                >t
                <"d-flex justify-content-between mx-2 row"
                    <"col-sm-12 col-md-6"i>
                    <"col-sm-12 col-md-6"p>
                ">`,
                initComplete: function () {
                    let domHtml = `
                    <div class="card-body">
                        <h4 class="card-title">${thisComponent.messages?.currencies}</h4>
                        <h6 class="card-subtitle text-muted">${thisComponent.messages?.currencies} ${thisComponent.messages?.index}</h6>
                    </div>
                    `;
                    $("#cardHeader").html(domHtml);
                },
            });

            thisComponent.$nextTick(() => {
                document.getElementById('btn-wrapper').innerHTML = '<button id="update-rates-btn" class="btn btn-success me-1">Update Rates</button>';
                document.getElementById('update-rates-btn').addEventListener('click', function () {
                    thisComponent.updateRates();
                });
            });
        },
        draw() {
            let thisComponent = this;
            axios
                .get("/" + thisComponent.locale + "/axios/currencies", {})
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
        updateRates() {
            let self = this;
            let btn = document.getElementById('update-rates-btn');
            let btnText = btn.innerHTML;

            btn.setAttribute('disabled', 'disabled');
            btn.innerHTML = self.messages.updating + '...';

            axios.post(`/${self.locale}/axios/currencies/rates/update`)
                .then(function (response) {
                    btn.removeAttribute('disabled');
                    btn.innerHTML = btnText;
                    self.dataTable.ajax.reload();
                });
        }
    },
    mounted() {
        window.thisComponent = this;
        this.buildTable();
    },
}
</script>

<style scoped>

</style>
