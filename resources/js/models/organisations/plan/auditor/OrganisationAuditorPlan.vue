<template>
    <div class="row">
        <div class="row">
            <div class="col-12">
                <div class="card invoice-list-wrapper">
                    <div class="card-datatable table-responsive">
                        <table class="invoice-list-table table" id="dataTable"></table>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade text-start modal-primary" id="statementShowModal" tabindex="-1" aria-labelledby="statementShowLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-extra-wide">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="statementShowLabel">{{ collection?.messages?.statement }} {{ collection?.messages?.show }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" @click="statementHide"></button>
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
                                            <td>{{ collection?.messages?.statement }}</td>
                                            <td>{{ statementActive?.concat }}</td>
                                        </tr>
                                        <tr>
                                            <td>{{ collection?.messages?.guide+' '+collection?.messages?.example }}</td>
                                            <td>{{ statementActive ? statementActive['guide_'+locale] : '' }}</td>
                                        </tr>
                                        <tr>
                                            <td>{{ collection?.messages?.guide+' '+collection?.messages?.plan }}</td>
                                            <td>{{ statementActive?.guide }}</td>
                                        </tr>
                                        <tr>
                                            <td>{{ collection?.messages?.review+' '+collection?.messages?.plan }}</td>
                                            <td>{{ statementActivePlan? statementActivePlan.plan['name_'+locale] : '' }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-primary" @click="statementHide">Ok</button>
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
            statementActive: null,
            statementActivePlan: null,
        };
    },
    methods: {
        buildTable() {
            var thisComponent = this;
            if (thisComponent.dataTable) {
                thisComponent.dataTable.destroy();
                thisComponent.dataTable = null;
                document.getElementById("dataTable").innerHTML = "";
            }
            let header = `
                    <thead>
                        <tr>
                            <th>${thisComponent.collection?.messages?.id}</th>
                            <th>${thisComponent.collection?.messages?.statement}</th>
                            <th>${thisComponent.collection?.messages?.guide} ${thisComponent.collection?.messages?.example}</th>
                            <th>${thisComponent.collection?.messages?.guide} ${thisComponent.collection?.messages?.plan}</th>
                            <th>${thisComponent.collection?.messages?.review} ${thisComponent.collection?.messages?.plan}</th>
                            <th class="cell-fit">${thisComponent.collection?.messages?.actions}</th>
                        </tr>
                    </thead>
                    `;
            var dataSource = thisComponent.collection?.statements;
            document.getElementById("dataTable").innerHTML = header;
            thisComponent.dataTable = $(".invoice-list-table").DataTable({
                data: dataSource,
                createdRow: function (row, data, dataIndex) {
                    //$(row).addClass("row-auth-bg");
                },
                lengthMenu: [],
                paging: false,
                autoWidth: true,
                searching: true,
                columns: [{ data: "id" }, { data: "concat" }, { data: "guide_" + thisComponent.locale }, { data: "guide" }],
                columnDefs: [
                    {
                        // ID
                        targets: 0,
                        responsivePriority: 0,
                        width: "10%",
                        render: function (data, type, full, meta) {
                            let r = `<p>${full.id}</p>`;
                            return r;
                        },
                    },
                    {
                        // Statement
                        targets: 1,
                        responsivePriority: 1,
                        width: "10%",
                        render: function (data, type, full, meta) {
                            let r = `<p>${full.concat}</p>`;
                            return r;
                        },
                    },
                    {
                        // Guide Example
                        targets: 2,
                        responsivePriority: 2,
                        width: "10%",
                        render: function (data, type, full, meta) {
                            let r = `<p>${full["guide_" + thisComponent.locale]}</p>`;
                            return r;
                        },
                    },
                    {
                        // Guide Plan
                        targets: 3,
                        responsivePriority: 3,
                        width: "10%",
                        render: function (data, type, full, meta) {
                            let r = `
                                <div class="form-group">
                                    <textarea class="form-control" type="text" placeholder="" id="statementReviewInput${full.id}" onchange="window.thisComponent.statementUpdateEnable(${full.id})">${full.guide ? full.guide : ""}</textarea>
                                </div>
                                `;
                            return r;
                        },
                    },
                    {
                        // Review Plan
                        targets: 4,
                        responsivePriority: 4,
                        width: "10%",
                        orderable: false,
                        render: function (data, type, full, meta) {
                            let r = `
                                <div class="d-flex">
                                    <select id="statementReviewPlanSelect${full.id}" class=form-select form-control" onchange="window.thisComponent.statementUpdateEnable(${full.id})">
                                `;
                            let o = `<option value="">${thisComponent.collection?.messages?.pleaseSelect}</option>`;
                            full.plans.forEach((e) => {
                                o += `
                                <option value="${e.plan?.id}" ${e.selected ? "selected" : ""}>${e.plan["name_" + thisComponent.locale]}</option>
                                `;
                            });
                            r += o;
                            r += `
                                    </select>
                                `;
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
                                        <div class="mb-1 row mr-1">
                                            <button type="button" class="btn btn-primary waves-effect" id="statementUpdateButton${full.id}" onclick="window.thisComponent.statementUpdate(${full.id})" disabled>${thisComponent.collection?.messages?.update}</button>
                                        </div>
                                        <div class="row mr-1">
                                            <button type="button" class="btn btn-outline-primary waves-effect" onclick="window.thisComponent.statementShow(${full.id})">${thisComponent.collection?.messages?.view}</button>
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
                    <"col-sm-12 col-md-6"p>up
                ">`,
                initComplete: function () {
                    let domHtml = `
                            <div class="card-body">
                                <h4 class="card-title">${thisComponent.collection?.messages?.statements}</h4>
                                <h6 class="card-subtitle text-muted">${thisComponent.collection?.messages?.plan} ${thisComponent.collection?.messages?.statements}</h6>
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
        statementHide() {
            var thisComponent = this;
            $("#statementShowModal").modal("hide");
            this.$nextTick(() => {
                thisComponent.statementActive = null;
                thisComponent.statementActivePlan = null;
            });
        },
        statementShow(id) {
            //let y = this.collection?.statements.filter((x) => x.id == id);
            //this.statementActive = y[0];
            var thisComponent = this;
            axios
                .get("/" + thisComponent.locale + "/axios/organisations/plan/auditor", {})
                .then(function (response) {
                    console.log(response.data);
                    let y = response.data.statements.filter((x) => x.id == id);
                    thisComponent.statementActive = y[0];
                    let p = y[0].plans;
                    p = p.filter((e) => e.selected == true);
                    p = p[0];
                    console.log(p);
                    thisComponent.statementActivePlan = p;
                    thisComponent.$nextTick(() => {
                        $("#statementShowModal").modal("show");
                    });
                })
                .catch(function (error) {
                    console.log(error);
                    console.log(error.response);
                });
            
        },
        statementUpdate(id) {
            var thisComponent = this;
            let guidePlan = document.getElementById("statementReviewInput" + id).value;
            let reviewPlan = document.getElementById("statementReviewPlanSelect" + id).value;
            axios
                .post(`/${thisComponent.locale}/axios/organisations/plan/auditor/update`, {
                    statement_id: id,
                    guide: guidePlan,
                    plan_id: reviewPlan,
                })
                .then(function (response) {
                    console.log(response.data);
                    toastr["success"](`${thisComponent.collection?.messages?.itemUpdatedSuccessfully}.`, `${thisComponent.collection?.messages?.success}!`, {
                        showMethod: "slideDown",
                        hideMethod: "slideUp",
                        timeOut: 3000,
                        progressBar: true,
                        rtl: false,
                    });
                })
                .catch(function (error) {
                    console.log(error, error.response);
                    toastr["error"](error.response?.data?.message, `${thisComponent.collection?.messages?.error}!`, {
                        showMethod: "slideDown",
                        hideMethod: "slideUp",
                        timeOut: 3000,
                        progressBar: true,
                        rtl: false,
                    });
                });
        },
        statementUpdateEnable(id) {
            let e = document.getElementById("statementUpdateButton" + id);
            e.disabled = false;
        },
    },
    mounted() {
        window.thisComponent = this;
        var thisComponent = this;
        axios
            .get("/" + thisComponent.locale + "/axios/organisations/plan/auditor", {})
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
};
</script>
