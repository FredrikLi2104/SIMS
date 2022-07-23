<template>
    <div class="row">
        <div class="row d-flex justify-content-between align-items-center m-1">
            <div class="col-xl-2"></div>
            <div class="col-xl-8 d-flex justify-content-center">
                <div class="btn-group" role="group" aria-label="Basic radio toggle button group">
                    <input type="radio" class="btn-check" name="btnradio" id="btnradioa" autocomplete="off" @click="draw('components')" :checked="active == 'components'" />
                    <label class="btn btn-primary btn-lg" for="btnradioa">{{ collection?.messages?.components }}</label>
                    <input type="radio" class="btn-check" name="btnradio" id="btnradiob" autocomplete="off" @click="draw('statements')" :checked="active == 'statements'" />
                    <label class="btn btn-primary btn-lg" for="btnradiob">{{ collection?.messages?.statements }}</label>
                </div>
            </div>
            <div class="col-xl-2"></div>
        </div>
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
                                                <td>{{ collection?.messages?.period }}</td>
                                                <td>{{ statementActive?.component?.organisation_period ? statementActive.component.organisation_period[`name_${locale}`] : null }}</td>
                                            </tr>
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
                                                <td>{{ collection?.messages?.implementation + " " }}{{ collection?.messages?.example }}</td>
                                                <td>{{ statementActive ? statementActive[`implementation_${locale}`] : null }}</td>
                                            </tr>
                                            <tr>
                                                <td>{{ collection?.messages?.implementation }}</td>
                                                <td>{{ statementActive ? statementActive.implementation : null }}</td>
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
    </div>
</template>
<script>
export default {
    props: ["locale"],
    data() {
        return {
            active: "components",
            dataTable: null,
            collection: null,
            statementActive: null,
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
            let header = ``;
            switch (thisComponent.active) {
                case "components":
                    header = `
                    <thead>
                        <tr>
                            <th>${thisComponent.collection?.messages?.component}</th>
                            <th>${thisComponent.collection?.messages?.desc}</th>
                            <th>${thisComponent.collection?.messages?.period}</th>
                        </tr>
                    </thead>
                    `;
                    break;
                case "statements":
                    header = `
                    <thead>
                        <tr>
                            <th>${thisComponent.collection?.messages?.period}</th>
                            <th>${thisComponent.collection?.messages?.subcode + "-" + thisComponent.collection?.messages?.statement + "-" + thisComponent.collection?.messages?.component}</th>
                            <th>${thisComponent.collection?.messages?.implementation}</th>
                            <th>${thisComponent.collection?.messages?.implementation + " " + thisComponent.collection?.messages?.example}</th>
                            <th class="text-center">${thisComponent.collection?.messages?.actions}</th>
                        </tr>
                    </thead>
                    `;
                    break;
                default:
                    break;
            }
            var columns = [];
            switch (thisComponent.active) {
                case "components":
                    columns = [{ data: "code_name" }, { data: "desc_" + thisComponent.locale }, { data: "periods" }];
                    break;

                case "statements":
                    columns = [{ data: "component.organisation_period.sort_order" }, { data: "subcode" }, { data: "implementation" }, { data: "implementation_" + thisComponent.locale }];
                    break;
            }
            var columnDefs = [];
            switch (thisComponent.active) {
                case "components":
                    columnDefs = [
                        {
                            // code
                            targets: 0,
                            width: "10%",
                            render: function (data, type, full, meta) {
                                let r = `<p>${full.code_name}</p>`;
                                return r;
                            },
                        },
                        {
                            // desc
                            targets: 1,
                            responsivePriority: 1,
                            width: "10%",
                            render: function (data, type, full, meta) {
                                let r = `<p>${eval("full.desc_" + thisComponent.locale)}</p>`;
                                return r;
                            },
                        },
                        {
                            // period
                            targets: 2,
                            responsivePriority: 2,
                            width: "10%",
                            orderable: false,
                            render: function (data, type, full, meta) {
                                let r = `
                                <div class="d-flex mb-1">
                                    <select id="componentPeriodSelect${full.id}" class="select2 form-select form-control" onchange="window.thisComponent.enableComponentButton(${full.id})">
                                `;
                                let o = ``;
                                full.periods.forEach((element) => {
                                    o += `
                                        <option ${element.selected ? `selected` : ``} value="${element.id}">${eval("element.name_" + thisComponent.locale)}</option>
                                    `;
                                });
                                r += o;
                                r += `
                                    </select>
                                    <p>	&nbsp;</p>
                                    <button class="btn btn-primary" disabled id="componentButton${full.id}" onclick="window.thisComponent.updateComponentPeriod(${full.id})">${thisComponent.collection?.messages?.update}</button>
                                </div>
                                `;
                                return r;
                            },
                        } /*,
                    {
                        // actions
                        targets: 2,
                        responsivePriority: 2,
                        width: "10%",
                        orderable: false,
                        render: function (data, type, full, meta) {
                            let r;
                            r = `
                                <div class="d-flex align-items-center justify-content-center col-actions">
                                     <div class="dropdown">
                                        <a class="btn btn-sm btn-icon px-0" data-bs-toggle="dropdown">${feather.icons["more-vertical"].toSvg({ class: "font-medium-2" })}</a>
                                        <div class="dropdown-menu dropdown-menu-end">
                                            <a href="/components/${full.id}/edit" class="dropdown-item">${feather.icons["edit"].toSvg({ class: "font-small-4 me-50" })}${thisComponent.collection?.messages?.edit}</a>
                                            <button class="dropdown-item" type="button" onclick="window.thisComponent.deleteModel(${full.id})">${feather.icons["delete"].toSvg({ class: "font-small-4 me-50" })}${thisComponent.collection?.messages?.delete}</button>
                                        </div>
                                    </div>
                                </div>
                                `;
                            return r;
                        },
                    },
                    */,
                    ];
                    break;
                case "statements":
                    columnDefs = [
                        {
                            // period
                            targets: 0,
                            width: "15%",
                            render: function (data, type, full, meta) {
                                if (type === "sort") {
                                    return parseInt(full.component.organisation_period.sort_order);
                                } else {
                                    let r = `<p>${eval("full.component.organisation_period.name_" + thisComponent.locale)}</p>`;
                                    return r;
                                }
                            },
                        },
                        {
                            // subcode-statement-component
                            targets: 1,
                            responsivePriority: 1,
                            width: "35%",
                            render: function (data, type, full, meta) {
                                let x = full.subcode + "-" + eval("full.content_" + thisComponent.locale) + "-" + eval("full.component.name_" + thisComponent.locale);
                                if (type === "sort") {
                                    return x;
                                } else {
                                    return `<p>${x}</p>`;
                                }
                            },
                        },
                        /*
                        {
                            // code
                            targets: 1,
                            responsivePriority: 1,
                            width: "10%",
                            render: function (data, type, full, meta) {
                                let r = `<p>${full.component.code}</p>`;
                                return r;
                            },
                        },
                        {
                            // subcode
                            targets: 2,
                            responsivePriority: 2,
                            width: "10%",
                            render: function (data, type, full, meta) {
                                let r = `<p>${full.subcode}</p>`;
                                return r;
                            },
                        },
                        {
                            // component
                            targets: 3,
                            responsivePriority: 3,
                            width: "15%",
                            render: function (data, type, full, meta) {
                                let r = `<p>${eval("full.component.name_" + thisComponent.locale)}</p>`;
                                return r;
                            },
                        },
                        {
                            // statement
                            targets: 4,
                            responsivePriority: 4,
                            width: "10%",
                            render: function (data, type, full, meta) {
                                let r = `<p>${eval("full.content_" + thisComponent.locale)}</p>`;
                                return r;
                            },
                        },
                        {
                            // guide
                            targets: 5,
                            responsivePriority: 5,
                            width: "15%",
                            render: function (data, type, full, meta) {
                                let r = `<p>${eval("full.guide_" + thisComponent.locale)}</p>`;
                                return r;
                            },
                        },
                        */
                        {
                            // implementation
                            targets: 2,
                            responsivePriority: 2,
                            width: "20%",
                            orderable: false,
                            render: function (data, type, full, meta) {
                                let r = `
                            <div class="form-group">
                                <textarea class="form-control" type="text" placeholder="" id="implementationInput${full.id}" onchange="window.thisComponent.enablePlanButton(${full.id})">${full.implementation ? full.implementation : ""}</textarea>
                            </div>
                            `;
                                return r;
                            },
                        },
                        {
                            // admin implementation
                            targets: 3,
                            responsivePriority: 3,
                            orderable: false,
                            width: "20%",
                            render: function (data, type, full, meta) {
                                let x = eval("full.implementation_" + thisComponent.locale) ? eval("full.implementation_" + thisComponent.locale) : "";
                                let r = `<p>${x}</p>`;
                                return r;
                            },
                        },
                        /*
                        {
                            // plans
                            targets: 3,
                            responsivePriority: 3,
                            width: "10%",
                            orderable: false,
                            render: function (data, type, full, meta) {
                                let r = `
                                <div class="d-flex">
                                    <select id="statementPlanSelect${full.id}" class="select2 form-select form-control" onchange="window.thisComponent.enablePlanButton(${full.id})">
                                `;
                                let o = `<option value="">${thisComponent.collection?.messages?.pleaseSelect}</option>`;
                                full.plans.forEach((element) => {
                                    o += `
                                        <option ${element.selected ? `selected` : ``} value="${element.id}">${eval("element.name_" + thisComponent.locale)}</option>
                                    `;
                                });
                                r += o;
                                r += `
                                    </select>
                                `;
                                return r;
                            },
                        },*/
                        {
                            // actions
                            targets: 4,
                            responsivePriority: 4,
                            width: "20%",
                            render: function (data, type, full, meta) {
                                let r = `
                                <div class="d-flex justify-content-center align-items-center px-2">
                                    <div class="d-flex flex-column">
                                        <div class="mb-1 row mr-1">
                                            <button type="button" class="btn btn-primary waves-effect" id="statementButton${full.id}" onclick="window.thisComponent.updateStatementPlan(${full.id})" disabled>${thisComponent.collection?.messages?.update}</button>
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
                    ];
                    break;
            }
            var dataSource = eval(`thisComponent.collection.${thisComponent.active}`);
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
                columns: columns,
                columnDefs: columnDefs,
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
                    let domHtml = ``;
                    switch (thisComponent.active) {
                        case "components":
                            domHtml = `
                            <div class="card-body">
                                <h4 class="card-title">${thisComponent.collection?.messages?.components}</h4>
                                <h6 class="card-subtitle text-muted">${thisComponent.collection?.messages?.plan} ${thisComponent.collection?.messages?.components}</h6>
                            </div>
                            `;
                            break;
                        case "statements":
                            domHtml = `
                        <div class="card-body">
                            <h4 class="card-title">${thisComponent.collection?.messages?.statements}</h4>
                            <h6 class="card-subtitle text-muted">${thisComponent.collection?.messages?.plan} ${thisComponent.collection?.messages?.statements}</h6>
                        </div>
                        `;
                            break;
                        default:
                            break;
                    }
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
        deleteModel(id) {},
        draw(type) {
            var thisComponent = this;
            thisComponent.active = type;
            axios
                .get("/" + thisComponent.locale + "/axios/organisations/plan", {})
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
        enableComponentButton(componentID) {
            $("#componentButton" + componentID).prop("disabled", false);
        },
        enablePlanButton(statementID) {
            $("#statementButton" + statementID).prop("disabled", false);
        },
        statementPeriod(id) {
            let v = $(`#statementPlanSelect${id}`).select2("data")[0];
            let r = v.text ? v.text : null;
            return r;
        },
        statementViewHide() {
            this.statementActive = null;
            $("#statementViewModal").modal("hide");
        },
        statementViewShow(id) {
            var thisComponent = this;
            axios
                .get("/" + thisComponent.locale + "/axios/organisations/plan", {})
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
        updateComponentPeriod(componentID) {
            // set the button who called this to disabled
            $("#componentButton" + componentID).prop("disabled", true);
            let v = $(`#componentPeriodSelect${componentID}`).select2("data")[0].id;
            axios
                .post(`/${thisComponent.locale}/axios/organisations/components/periods/update`, {
                    component_id: componentID,
                    period_id: v,
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
                    // console.log(error);
                    // console.log(error.response);
                    toastr["error"](error, `${thisComponent.collection?.messages?.error}!`, {
                        showMethod: "slideDown",
                        hideMethod: "slideUp",
                        timeOut: 3000,
                        progressBar: true,
                        rtl: false,
                    });
                });
        },
        updateStatementPlan(statementID) {
            // set the button who called this to disabled
            $("#statementButton" + statementID).prop("disabled", true);
            //let v = $(`#statementPlanSelect${statementID}`).select2("data")[0].id;
            let i = $(`#implementationInput${statementID}`).val();
            axios
                .post(`/${thisComponent.locale}/axios/organisations/statements/plans/update`, {
                    statement_id: statementID,
                    //plan_id: v,
                    implementation: i,
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
                    //console.log(error.response.data);
                    toastr["error"](error.response?.data?.message, `${thisComponent.collection?.messages?.error}!`, {
                        showMethod: "slideDown",
                        hideMethod: "slideUp",
                        timeOut: 3000,
                        progressBar: true,
                        rtl: false,
                    });
                });
        },
    },
    mounted() {
        window.thisComponent = this;
        axios;
        this.draw("components");
    },
};
</script>
