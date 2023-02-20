<template>
    <div class="row">
        <div class="col-12">
            <div class="card invoice-list-wrapper">
                <div class="card-datatable table-responsive">
                    <table class="invoice-list-table table" id="dataTable"></table>
                </div>
            </div>
        </div>
        <div class="modal fade text-start modal-primary" id="statementViewModal" tabindex="-1"
             aria-labelledby="statementViewLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-extra-wide">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="statementViewLabel">{{ collection?.messages?.statement }}
                            {{ collection?.messages?.view }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                                @click="statementViewHide"></button>
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
                                        <td>{{
                                                statementActive?.component?.organisation_period ? statementActive.component.organisation_period[`name_${locale}`] : null
                                            }}
                                        </td>
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
                                        <td>{{ collection?.messages?.implementation }}</td>
                                        <td>{{ statementActive ? statementActive.implementation : null }}</td>
                                    </tr>
                                    <tr>
                                        <td>{{ collection?.messages?.value }}</td>
                                        <td>{{
                                                statementActive ? (statementActive.deed ? statementActive.deed.value : null) : null
                                            }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>{{ collection?.messages?.comment }}</td>
                                        <td>{{
                                                statementActive ? (statementActive.deed ? statementActive.deed.comment : null) : null
                                            }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>{{ collection?.messages?.lastUpdated }}</td>
                                        <td>{{
                                                statementActive ? (statementActive.deed ? statementActive.deed.updated_at_for_humans : null) : null
                                            }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>{{ collection?.messages?.status }}</td>
                                        <td>
                                            <div class="d-flex justify-content-start align-items-start">
                                                <span
                                                    :class="`badge rounded-pill badge-light-${statementActive?.review?.accepted == true ? 'success' : statementActive?.review?.accepted == false ? 'danger' : 'secondary'}`">{{
                                                        statementActive?.review?.accepted == true ? collection?.messages?.accepted : statementActive?.review?.accepted == false ? collection?.messages?.rejected : collection?.messages?.unreviewed
                                                    }}</span>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>{{ collection?.messages?.review }}</td>
                                        <td>{{ statementActive?.review?.review }}</td>
                                    </tr>
                                    <tr>
                                        <td>{{
                                                collection?.messages?.review + " " + collection?.messages?.lastUpdated
                                            }}
                                        </td>
                                        <td>{{ statementActive?.review?.updated_at_for_humans }}</td>
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
import Swal from "sweetalert2";

export default {
    props: ["locale", 'actionId'],
    data() {
        return {
            dataTable: null,
            collection: null,
            statementActive: null,
            value: null,
            comment: null,
        };
    },
    methods: {
        buildTable() {
            var thisComponent = this;
            let header = `
             <thead>
                <tr>
                    <th>${thisComponent.collection?.messages?.statement}</th>
                    <th class="text-center">${thisComponent.collection?.messages?.value}</th>
                    <th>${thisComponent.collection?.messages?.comment}</th>
                    <th class="text-center">${thisComponent.collection?.messages?.status}</th>
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
                columns: [{data: "id"}, {data: "deed"}, {data: "deed"}, {data: "deed"}],
                columnDefs: [
                    {
                        // statement
                        targets: 0,
                        responsivePriority: 0,
                        width: "30%",
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
                        // Value
                        targets: 1,
                        responsivePriority: 1,
                        orderable: false,
                        width: "10%",
                        render: function (data, type, full, meta) {
                            // value tooltip
                            let k = `
                            1: ${eval("full.k1_" + thisComponent.locale)}</br> \n
                            2: ${eval("full.k2_" + thisComponent.locale)}</br> \n
                            3: ${eval("full.k3_" + thisComponent.locale)}</br> \n
                            4: ${eval("full.k4_" + thisComponent.locale)}</br> \n
                            5: ${eval("full.k5_" + thisComponent.locale)}</br> \n
                            `;
                            let r = `
                                <div class="d-flex">
                                    <select id="valueSelect${full.id}" class="select2 form-select form-control" onchange="window.thisComponent.statementActionButtonEnable(${full.id})">
                                `;
                            let o = `<option value="">${thisComponent.collection?.messages?.pleaseSelect}</option>`;
                            for (let index = 0; index < 5; index++) {
                                o += `
                                        <option ${full.deed?.value == index + 1 ? `selected` : ``} value="${index + 1}">${index + 1}</option>
                                    `;
                            }
                            r += o;
                            r += `
                                    </select>
                                    <button type="button" class="btn btn-icon btn-flat-warning px-1" data-html="true" data-bs-toggle="tooltip" data-bs-placement="bottom" title="${k}">
                                        <i data-feather="help-circle"></i>
                                    </button>
                                </div>
                                `;
                            return r;
                        },
                    },
                    {
                        // Comment
                        targets: 2,
                        responsivePriority: 2,
                        orderable: false,
                        width: "20%",
                        render: function (data, type, full, meta) {
                            let r = `
                            <div class="form-group">
                                <textarea class="form-control" type="text" placeholder="" id="commentInput${full.id}" onchange="window.thisComponent.statementActionButtonEnable(${full.id})">${full.deed ? full.deed.comment : ""}</textarea>
                            </div>
                            `;
                            return r;
                        },
                    },
                    {
                        // Status
                        targets: 3,
                        responsivePriority: 3,
                        width: "10%",
                        orderable: false,
                        render: function (data, type, full, meta) {
                            let c = "";
                            let t = "";
                            let b = "";
                            switch (full.review?.accepted) {
                                case null:
                                    break;
                                case 1:
                                    c = "success";
                                    t = thisComponent.collection?.messages.accepted;
                                    break;
                                case 0:
                                    c = "danger";
                                    t = thisComponent.collection?.messages.rejected;
                                    break;
                                default:
                                    break;
                            }
                            b = `
                                <div class="row mr-1">
                                    <button type="button" class="btn btn-gradient-${c}" data-bs-toggle="tooltip" data-bs-placement="bottom" title="${full.review ? full.review.updated_at_for_humans + `: ` + full.review.review : ""}">${t}</button>
                                </div>
                                `;
                            let badge = ``;
                            if (full.review?.new == true) {
                                badge = `
                                <span class="badge badge-glow bg-primary rounded-pill float-end new-badge" id="${"statementbadgeid_" + full.id}">${thisComponent.collection?.messages.new}</span>
                                `;
                            }

                            let r =
                                `
                                <div class="justify-content-center align-items-center px-2">
                                    <div class="d-flex flex-column">
                                    ` +
                                badge +
                                b +
                                `
                                    </div>
                                </div>
                                `;
                            return r;
                        },
                    },
                    {
                        // Actions
                        targets: 4,
                        responsivePriority: 4,
                        width: "10%",
                        orderable: false,
                        render: function (data, type, full, meta) {
                            let r = `
                                <div class="d-flex justify-content-center align-items-center px-2">
                                    <div class="d-flex flex-column">
                                        <div class="mb-1 row mr-1">
                                            <button type="button" class="btn btn-primary waves-effect" id="statementButton${full.id}" onclick="window.thisComponent.statementActionUpdate(${full.id})" disabled>${thisComponent.collection?.messages?.update}</button>
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
                    <"col-lg-7 d-flex justify-content-start align-items-center"
                        <"#cardHeader">
                    >
                    <"col-lg-3 d-flex justify-content-end align-items-center"f>
                    <"col-lg-2 d-flex justify-content-end align-items-center px-2"<"#updateAll">>


                >t
                <"d-flex justify-content-between mx-2 row"
                    <"col-sm-12 col-md-6"i>
                    <"col-sm-12 col-md-6"p>
                ">`,
                initComplete: function () {
                    let domHtml = `
                    <div class="card-body">
                        <h4 class="card-title">${thisComponent.collection?.messages?.statements}</h4>
                        <h6 class="card-subtitle text-muted">${thisComponent.collection?.messages?.statements} ${thisComponent.collection?.messages?.actions}</h6>
                    </div>
                    `;
                    $("#cardHeader").html(domHtml);
                    let updateAllHtml = `
                    <button type="button" class="btn btn-primary waves-effect" onClick="window.thisComponent.statementActionUpdateAll()">${thisComponent.collection?.messages?.updateAll}</button>
                    `;
                    $("#updateAll").html(updateAllHtml);
                },
                drawCallback: function () {
                    thisComponent.$nextTick(function () {
                        if (feather) {
                            feather.replace({
                                width: 14,
                                height: 14,
                            });
                        }
                        $(document).find('[data-bs-toggle="tooltip"]').tooltip({html: true});
                    });
                    $(".select2").select2();
                    /*
                    try {
                        //$(document).find('[data-bs-toggle="tooltip"]').tooltip({ html: true });
                        function sleep(ms) {
                            return new Promise((resolve) => setTimeout(resolve, ms));
                        }
                        async function create() {
                            await sleep(3200);
                            $(document).find('[data-bs-toggle="tooltip"]').tooltip({ html: true });
                        }
                        create();
                    } catch (error) {
                        function sleep(ms) {
                            return new Promise((resolve) => setTimeout(resolve, ms));
                        }
                        async function create() {
                            await sleep(4000);
                            $(document).find('[data-bs-toggle="tooltip"]').tooltip({ html: true });
                        }
                        create();
                    }*/
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
        draw() {
            var thisComponent = this;
            axios
                .get("/" + thisComponent.locale + "/axios/organisations/do/" + thisComponent.actionId, {})
                .then(function (response) {
                    //console.log(response.data);
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
        statementActionButtonEnable(id) {
            $("#statementButton" + id).prop("disabled", false);
        },
        statementActionUpdate(id) {
            var sid = id;
            $("#statementButton" + id).prop("disabled", true);
            let v = $(`#valueSelect${id}`).select2("data")[0].id;
            let c = $(`#commentInput${id}`).val();
            axios
                .post(`/${thisComponent.locale}/axios/organisations/statements/deeds/update`, {
                    statement_id: id,
                    value: v,
                    comment: c,
                })
                .then(function (response) {
                    //console.log(response.data);
                    // remove new tag
                    // find this statement in collection
                    thisComponent.collection?.statements?.forEach((element, index, array) => {
                        if ((element.id = sid && element.review)) {
                            array[index].review.new = false;
                            //datatables are not reactive also kill span
                            $("#statementbadgeid_" + sid).remove();
                        }
                    });
                    /*let s = thisComponent.collection?.statements?.filter((element) => {
                        return element.id == sid;
                    });
                    // has review?
                    if(s.review) {
                        s.review.new = false;
                    }*/
                    // end remove new tag
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
        statementActionUpdateAll() {
            let member = {};
            let load = [];
            thisComponent.collection?.statements.forEach((statement) => {
                member = {
                    id: statement.id,
                    value: $(`#valueSelect${statement.id}`).select2("data")[0].id,
                    comment: $(`#commentInput${statement.id}`).val()
                };
                load.push(member);
            });
            Swal.fire({
                title: `🤖 \n ${thisComponent.collection?.messages?.working}`,
                text: `🤖 \n ${thisComponent.collection?.messages?.working}`,
                icon: "info",
                html: `
                        <button class="btn btn-outline-info" type="button" disabled>
                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                            <span class="ms-25 align-middle">${thisComponent.collection?.messages?.updating}...</span>
                        </button>`,
                showCloseButton: false,
                showConfirmButton: false,
                customClass: {
                    confirmButton: "btn btn-primary",
                },
                buttonsStyling: false,
            });
            axios
                .post(`/${thisComponent.locale}/axios/organisations/statements/deeds/update-all`, {
                    statements: load,
                    locale: thisComponent.locale,
                })
                .then(function (response) {
                    console.log(response.data);
                    Swal.fire({
                        title: `${thisComponent.collection?.messages?.success}!`,
                        text: `${thisComponent.collection?.messages?.itemsUpdatedSuccessfully}!`,
                        icon: "success",
                        timer: 2000,
                        timerProgressBar: true,
                        customClass: {
                            confirmButton: "btn btn-success",
                        },
                        buttonsStyling: false,
                    });
                })
                .catch(function (error) {
                    console.log(error.response);
                    Swal.fire({
                        icon: "error",
                        title: "Uh oh!",
                        text: "{message: " + error.response?.data?.message + ", status: " + error.response.status + ", statusText: " + error.response.statusText + "}",
                        customClass: {
                            confirmButton: "btn btn-primary",
                        },
                    });
                });
        },
        statementViewHide() {
            this.statementActive = null;
            $("#statementViewModal").modal("hide");
        },
        statementViewShow(id) {
            var thisComponent = this;
            axios
                .get("/" + thisComponent.locale + "/axios/organisations/do", {})
                .then(function (response) {
                    //console.log(response.data);
                    let c = response.data;
                    let f = c.statements.filter((x) => x.id == id);
                    thisComponent.statementActive = f[0];
                    thisComponent.$nextTick(() => {
                        $("#statementViewModal").modal("show");
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
