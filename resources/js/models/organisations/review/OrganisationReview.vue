<template>
    <div class="row">
        <div class="col-12">
            <div class="card invoice-list-wrapper">
                <div class="card-datatable table-responsive">
                    <table class="invoice-list-table table" id="dataTable"></table>
                </div>
            </div>
        </div>
        <div class="modal fade text-start modal-primary" id="statementViewModal" tabindex="-1" aria-labelledby="statementViewLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-extra-wide">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="statementViewLabel">{{ collection?.messages?.statement }} {{ collection?.messages?.view }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" @click="statementViewHide"></button>
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
                                            <td>{{ collection?.messages?.code }}</td>
                                            <td>{{ statementActive?.component?.code }}</td>
                                        </tr>
                                        <tr>
                                            <td>{{ collection?.messages?.subcode }}</td>
                                            <td>{{ statementActive?.subcode }}</td>
                                        </tr>
                                        <tr>
                                            <td>{{ collection?.messages?.component }}</td>
                                            <td>{{ statementActive?.component[`name_${locale}`] }}</td>
                                        </tr>
                                        <tr>
                                            <td>{{ collection?.messages?.period }}</td>
                                            <td>{{ statementActive?.component?.organisation_period ? statementActive.component.organisation_period[`name_${locale}`] : null  }}</td>
                                        </tr>
                                        <tr>
                                            <td>{{ collection?.messages?.statement }}</td>
                                            <td>{{ statementActive ? statementActive[`content_${locale}`] : null }}</td>
                                        </tr>
                                        <tr>
                                            <td>{{ collection?.messages?.desc }}</td>
                                            <td>{{ statementActive ? statementActive[`desc_${locale}`] : null }}</td>
                                        </tr>
                                        <tr>
                                            <td>K1</td>
                                            <td>{{ statementActive ? statementActive[`k1_${locale}`] : null }}</td>
                                        </tr>
                                        <tr>
                                            <td>K2</td>
                                            <td>{{ statementActive ? statementActive[`k2_${locale}`] : null }}</td>
                                        </tr>
                                        <tr>
                                            <td>K3</td>
                                            <td>{{ statementActive ? statementActive[`k3_${locale}`] : null }}</td>
                                        </tr>
                                        <tr>
                                            <td>K4</td>
                                            <td>{{ statementActive ? statementActive[`k4_${locale}`] : null }}</td>
                                        </tr>
                                        <tr>
                                            <td>K5</td>
                                            <td>{{ statementActive ? statementActive[`k5_${locale}`] : null }}</td>
                                        </tr>
                                        <tr>
                                            <td>{{ collection?.messages?.plan }}</td>
                                            <td>{{ statementActive?.plan ? statementActive.plan[`name_${locale}`] : null }}</td>
                                        </tr>
                                        
                                        <tr>
                                            <td>{{ collection?.messages?.guide }}</td>
                                            <td>{{ statementActive ? statementActive[`guide_${locale}`] : null }}</td>
                                        </tr>
                                        <tr>
                                            <td>{{ collection?.messages?.implementation }}</td>
                                            <td>{{ statementActive ? statementActive.implementation : null }}</td>
                                        </tr>
                                        <tr>
                                            <td>{{ collection?.messages?.value }}</td>
                                            <td>{{ statementActive?.deed ? statementActive.deed.value : null }}</td>
                                        </tr>
                                        <tr>
                                            <td>{{ collection?.messages?.comment }}</td>
                                            <td>{{ statementActive?.deed ? statementActive.deed.comment : null }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-primary" @click="statementViewHide">Ok</button>
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
            accepted: null,
            review: null,
        };
    },
    methods: {
        buildTable() {
            var thisComponent = this;
            let header = `
             <thead>
                <tr>
                    <th>${thisComponent.collection?.messages?.statement}</th>
                    <th>${thisComponent.collection?.messages?.plan}</th>
                    <th>${thisComponent.collection?.messages?.guide}</th>
                    <th>${thisComponent.collection?.messages?.implementation}</th>
                    <th>${thisComponent.collection?.messages?.value}</th>
                    <th>${thisComponent.collection?.messages?.comment}</th>
                    <th>${thisComponent.collection?.messages?.status}</th>
                    <th>${thisComponent.collection?.messages?.review}</th>
                    <th class="text-center">${thisComponent.collection?.messages?.actions}</th>
                </tr>
            </thead>
            `;
            document.getElementById("dataTable").innerHTML = header;
            var dataSource = thisComponent.collection.statements;
            thisComponent.dataTable = $(".invoice-list-table").DataTable({
                data: dataSource,
                createdRow: function (row, data, dataIndex) {
                    //$(row).addClass("row-auth-bg");
                },
                lengthMenu: [],
                paging: false,
                autoWidth: true,
                searching: true,
                columns: [{ data: "id" }, { data: "plan" }, { data: "guide_" + thisComponent.locale }, { data: "implementation" }, { data: "deed" }, { data: "deed" }],
                columnDefs: [
                    {
                        // statement
                        targets: 0,
                        responsivePriority: 0,
                        width: "10%",
                        render: function (data, type, full, meta) {
                            let x = `${full.subcode + " - "}`;
                            x += eval(`full.component?.name_` + thisComponent.locale);
                            x += " - ";
                            x += eval(`full.content_` + thisComponent.locale);
                            x += " - ";
                            x += eval(`full.desc_` + thisComponent.locale);
                            if (type === "sort") {
                                return x;
                            } else {
                                let r = `<p>${x}</p>`;
                                return r;
                            }
                        },
                    },
                    {
                        // plan
                        targets: 1,
                        responsivePriority: 1,
                        width: "10%",
                        render: function (data, type, full, meta) {
                            if (type === "sort") {
                                let x = null;
                                if (full.plan) {
                                    x = eval(`full.plan?.name_` + thisComponent.locale);
                                }
                                return x;
                            } else {
                                let p = ``;
                                if (full.plan) {
                                    p = eval(`full.plan?.name_` + thisComponent.locale);
                                }
                                let r = `<p>${p}</p>`;
                                return r;
                            }
                        },
                    },
                    {
                        // guide
                        targets: 2,
                        responsivePriority: 2,
                        width: "10%",
                        render: function (data, type, full, meta) {
                            let r = `<p>${eval("full.guide_" + thisComponent.locale)}</p>`;
                            return r;
                        },
                    },
                    {
                        // implementation
                        targets: 3,
                        responsivePriority: 3,
                        width: "10%",
                        render: function (data, type, full, meta) {
                            let r = `<p>${full.implementation ? full.implementation : ""}</p>`;
                            return r;
                        },
                    },
                    {
                        // value
                        targets: 4,
                        responsivePriority: 4,
                        width: "10%",
                        render: function (data, type, full, meta) {
                            if (type === "sort") {
                                let x = full.deed ? full.deed.value : "";
                                return x;
                            } else {
                                let r = `<p>${full.deed?.value ? full.deed.value : ""}</p>`;
                                return r;
                            }
                        },
                    },
                    {
                        // comment
                        targets: 5,
                        responsivePriority: 5,
                        width: "10%",
                        render: function (data, type, full, meta) {
                            if (type === "sort") {
                                let x = full.deed ? full.deed.comment : "";
                                return x;
                            } else {
                                let r = `<p>${full.deed?.comment ? full.deed.comment : ""}</p>`;
                                return r;
                            }
                        },
                    },
                    {
                        // accepted
                        targets: 6,
                        responsivePriority: 6,
                        width: "10%",
                        orderable: false,
                        render: function (data, type, full, meta) {
                            let r = `
                                <div class="d-flex">
                                    <select id="statementReviewSelect${full.id}" class="select2 form-select form-control" onchange="window.thisComponent.statementReviewButtonEnable(${full.id})">
                                `;
                            let o = `<option value="">${thisComponent.collection?.messages?.pleaseSelect}</option>`;
                            o += `
                            <option ${full.review?.accepted == true ? `selected` : ``} value="true">${thisComponent.collection?.messages?.accepted}</option>
                            `;
                            o += `
                            <option ${full.review?.accepted == false ? `selected` : ``} value="false">${thisComponent.collection?.messages?.rejected}</option>
                            `;
                            r += o;
                            r += `
                                    </select>
                                `;
                            return r;
                        },
                    },
                    {
                        // review
                        targets: 7,
                        responsivePriority: 7,
                        orderable: false,
                        width: "10%",
                        render: function (data, type, full, meta) {
                            let r = `
                                <div class="form-group">
                                    <textarea class="form-control" type="text" placeholder="" id="statementReviewInput${full.id}" onchange="window.thisComponent.statementReviewButtonEnable(${full.id})">${full.review ? full.review.review : ""}</textarea>
                                </div>
                                `;
                            return r;
                        },
                    },
                    {
                        // actions
                        targets: 8,
                        responsivePriority: 8,
                        width: "10%",
                        orderable: false,
                        render: function (data, type, full, meta) {
                            let r = `
                                <div class="d-flex justify-content-center align-items-center px-2">
                                    <div class="d-flex flex-column">
                                    <div class="mb-1 row mr-1">
                                            <button type="button" class="btn btn-primary waves-effect" id="statementReviewButton${full.id}" onclick="window.thisComponent.statementReviewUpdate(${full.id})" disabled>${thisComponent.collection?.messages?.update}</button>
                                        </div>
                                        <div class="row mr-1">
                                            <button type="button" class="btn btn-outline-primary waves-effect" onclick="window.thisComponent.statementViewShow(${full.id})">${thisComponent.collection?.messages?.view}</button>
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
                    <"col-sm-12 col-md-6"p>
                ">`,
                initComplete: function () {
                    let domHtml = `
                    <div class="card-body">
                        <h4 class="card-title">${thisComponent.collection?.messages?.statements}</h4>
                        <h6 class="card-subtitle text-muted">${thisComponent.collection?.messages?.statements} ${thisComponent.collection?.messages?.review}</h6>
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
                    $(".select2").select2();
                },
            });
        },
        draw() {
            var thisComponent = this;
            axios
                .get("/" + thisComponent.locale + "/axios/organisations/review", {})
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
        statementReviewButtonEnable(id) {
            $("#statementReviewButton" + id).prop("disabled", false);
        },
        statementReviewUpdate(id) {
            $("#statementReviewButton" + id).prop("disabled", true);
            let a = $(`#statementReviewSelect${id}`).select2("data")[0].id;
            if (a == "true") {
                a = true;
            }
            if (a == "false") {
                a = false;
            }
            let r = $(`#statementReviewInput${id}`).val();
            axios
                .post(`/${thisComponent.locale}/axios/organisations/statements/reviews/update`, {
                    statement_id: id,
                    accepted: a,
                    review: r,
                })
                .then(function (response) {
                    //console.log(response.data);
                    toastr["success"](`${thisComponent.collection?.messages?.itemUpdatedSuccessfully}.`, `${thisComponent.collection?.messages?.success}!`, {
                        showMethod: "slideDown",
                        hideMethod: "slideUp",
                        timeOut: 3000,
                        progressBar: true,
                        rtl: false,
                    });
                })
                .catch(function (error) {
                    //console.log(error);
                    //console.log(error.response);
                    toastr["error"](error.response?.data?.message, `${thisComponent.collection?.messages?.error}!`, {
                        showMethod: "slideDown",
                        hideMethod: "slideUp",
                        timeOut: 5000,
                        progressBar: true,
                        rtl: false,
                    });
                });
        },
        statementViewHide() {
            this.statementActive = null;
            this.accepted = null;
            this.review = null;
            $("#statementViewModal").modal("hide");
        },
        statementViewShow(id) {
            let f = this.collection.statements.filter((x) => x.id == id);
            this.statementActive = f[0];
            this.accepted = $(`#statementReviewSelect${id}`).select2("data")[0].text;
            this.review = $(`#statementReviewInput${id}`).val();
            $("#statementViewModal").modal("show");
        },
    },
    mounted() {
        window.thisComponent = this;
        this.draw();
    },
};
</script>
