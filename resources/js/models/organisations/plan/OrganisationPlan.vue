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
                    <input type="radio" class="btn-check" name="btnradio" id="btnradioc" autocomplete="off" @click="draw('report')" :checked="active == 'report'" />
                    <label class="btn btn-primary btn-lg" for="btnradioc">{{ collection?.messages?.report }}</label>
                </div>
            </div>
            <div class="col-xl-2"></div>
        </div>
        <div class="row">
            <!-- Report-->
            <div class="row match-height" v-if="active == 'report'">
                <!-- Organisation Details-->
                <div class="col-8">
                    <div class="card card-user-timeline">
                        <div class="card-header">
                            <div class="d-flex align-items-center">
                                <i data-feather="settings" class="user-timeline-title-icon"></i>
                                <h4 class="card-title">{{ collection?.messages?.organisationDetails }}</h4>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="col-8 mb-1">
                                    <label for="customFile1" class="form-label">{{ collection?.messages?.logo }}</label>
                                    <input id="logoInput" :class="errors?.logo ? 'form-control is-invalid' : 'form-control'" type="file" @change="orgUpdate" />
                                    <p>
                                        <small class="text-muted">{{ collection?.messages?.logoRequirements }}</small>
                                    </p>
                                    <div v-if="errors?.logo" class="invalid-feedback">{{ errors?.logo }}</div>
                                </div>
                                <div class="col-1"></div>
                                <div class="col-3 mb-1 align-items-center text-center bg-white">
                                    <img :src="collection?.organisation?.logo" height="64" />
                                </div>
                            </div>
                            <div class="d-flex align-items-center">
                                <div class="col-8 mb-1">
                                    <label for="colorInput" class="form-label">{{ collection?.messages?.accentColor }}</label>
                                    <input id="colorInput" type="color" class="form-control form-control-color" title="Choose your color" v-model="color" @change="orgUpdate" />
                                </div>
                            </div>
                            <div class="d-flex align-items-center">
                                <div class="col-8 mb-1">
                                    <label class="form-label">{{ collection?.messages?.component }} {{ collection?.messages?.graph }}</label>
                                    <div class="bg-white" style="position: relative; height: 30vh; width: 40vw">
                                        <canvas id="graphelementlarge" class="polar-area-chart-ex chartjs" data-height="800"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--/ Organisation Details-->
                <!-- Download Card -->
                <div class="col-4">
                    <div class="card card-browser-states">
                        <div class="card-header">
                            <div class="d-flex align-items-center">
                                <i data-feather="file-text" class="user-timeline-title-icon"></i>
                                <h4 class="card-title" style="margin-left: 10px !important">{{ collection?.messages?.report }}</h4>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="mb-1">
                                <label class="form-label">{{ collection?.messages?.template }}</label>
                            </div>
                            <div class="mb-1">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="inlineRadioOptions" id="inlineRadio1" value="option1" checked />
                                    <label class="form-check-label" for="inlineRadio1">Modern</label>
                                </div>
                                <!--
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="inlineRadioOptions" id="inlineRadio2" value="option2" />
                                    <label class="form-check-label" for="inlineRadio2">Modern</label>
                                </div>
                                -->
                            </div>
                            <div class="bg-white">
                                <div class="d-flex justify-content-between align-items-center mb-1 mt-1">
                                    <div class="col-6" :style="'font-family: \'Times New Roman\'; font-weight: 900; color: ' + color + ';'">
                                        <p class="my-0 px-1">{{ collection?.messages?.planningReport }}</p>
                                    </div>
                                    <div class="col-6 d-flex justify-content-end px-1 py-1">
                                        <img :src="collection?.organisation?.logo" height="60" />
                                    </div>
                                </div>
                                <div id="headerBar" class="d-flex justify-content-center align-items-end mb-2">
                                    <div class="col-9" :style="`height: 4px; background-color: ${color};`"></div>
                                    <div class="col-3" :style="`height: 10px; background-color: ${color};`"></div>
                                </div>
                                <div class="d-flex justify-content-start align-items-center">
                                    <div class="col-6" :style="'font-family: \'Times New Roman\'; font-weight: 900; color: ' + color + ';'">
                                        <p class="my-0 px-1">{{ collection?.messages?.summary }}</p>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-start align-items-center">
                                    <div class="col-12 text-black" :style="`font-family: 'Times New Roman'; font-weight: 400;`">
                                        <p class="my-0 px-1">{{ collection?.messages?.summaryDesc }}</p>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-center align-items-center mb-2">
                                    <div class="col-12">
                                        <canvas id="graphelement" class="polar-area-chart-ex chartjs" data-height="200"></canvas>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-start align-items-center">
                                    <div class="col-6" :style="'font-family: \'Times New Roman\'; font-weight: 900; color: ' + color + ';'">
                                        <p class="my-0 px-1">{{ collection?.messages?.dataProtectionPlan }}</p>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-start align-items-center">
                                    <div class="col-12 text-black" :style="`font-family: 'Times New Roman'; font-weight: 400;`">
                                        <p class="my-0 px-1">{{ collection?.messages?.dataProtectionText }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="browser-states">
                                <div class="d-flex flex-row">
                                    <img src="/images/icons/word_icon.png" class="rounded me-1" height="30" alt="Google Chrome" />
                                    <h6 class="align-self-center mb-0">Download A4 Word Document</h6>
                                </div>
                                <div class="d-flex align-items-center" style="position: relative">
                                    <button type="button" class="btn btn-icon btn-success waves-effect waves-float waves-light" @click="generateDocx">
                                        <i data-feather="download"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- End Report-->
            <div class="col-12" v-if="active != 'report'">
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
                                            <tr>
                                                <td>{{ collection?.messages?.responsibility }}</td>
                                                <td>{{ statementActive ? statementActive.responsibility : null }}</td>
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
import { AlignmentType, BorderStyle, convertInchesToTwip, Document, Header, ImageRun, Packer, PageBreak, Paragraph, ShadingType, SymbolRun, Table, TableCell, TableRow, TextRun, VerticalAlign, WidthType } from "docx";
import { saveAs } from "file-saver";
import domtoimage from "dom-to-image";
import hsl from "hsl-to-hex";
export default {
    props: ["locale"],
    data() {
        return {
            active: "components",
            dataTable: null,
            collection: null,
            statementActive: null,
            errors: null,
            color: null,
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
                            <th>${thisComponent.collection?.messages?.implementation + " " + thisComponent.collection?.messages?.example}</th>
                            <th>${thisComponent.collection?.messages?.implementation}</th>
                            <th>${thisComponent.collection?.messages?.responsibility}</th>
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
                    columns = [{ data: "component.organisation_period.sort_order" }, { data: "subcode" }, { data: "implementation_" + thisComponent.locale }, { data: "implementation" }, , { data: "responsibility" }];
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
                        },
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
                            width: "30%",
                            render: function (data, type, full, meta) {
                                let x = full.subcode + "-" + eval("full.content_" + thisComponent.locale) + "-" + eval("full.component.name_" + thisComponent.locale);
                                if (type === "sort") {
                                    return x;
                                } else {
                                    return `<p>${x}</p>`;
                                }
                            },
                        },
                        {
                            // admin implementation
                            targets: 2,
                            responsivePriority: 2,
                            orderable: false,
                            width: "20%",
                            render: function (data, type, full, meta) {
                                let x = eval("full.implementation_" + thisComponent.locale) ? eval("full.implementation_" + thisComponent.locale) : "";
                                let r = `<p>${x}</p>`;
                                return r;
                            },
                        },
                        {
                            // implementation
                            targets: 3,
                            responsivePriority: 3,
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
                            // responsibility
                            targets: 4,
                            responsivePriority: 4,
                            width: "25%",
                            render: function (data, type, full, meta) {
                                let r = `
                            <div class="form-group">
                                <textarea class="form-control" type="text" placeholder="${thisComponent.collection?.messages?.responsibilityPlaceholder}" id="responsibilityInput${full.id}" onchange="window.thisComponent.enablePlanButton(${full.id})">${full.responsibility ? full.responsibility : ""}</textarea>
                            </div>
                            `;
                                return r;
                            },
                        },
                        {
                            // actions
                            targets: 5,
                            responsivePriority: 5,
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
        buildReportChart() {
            Chart.plugins.register(ChartDataLabels);
            const polarAreaChartEx = $("#graphelement");
            const polarAreaChartExLarge = $("#graphelementlarge");
            // Color Variables
            var primaryColorShade = "#836AF9",
                yellowColor = "#ffe800",
                successColorShade = "#28dac6",
                warningColorShade = "#ffe802",
                warningLightColor = "#FDAC34",
                infoColorShade = "#299AFF",
                greyColor = "#4F5D70",
                blueColor = "#2c9aff",
                blueLightColor = "#84D0FF",
                greyLightColor = "#EDF1F4",
                tooltipShadow = "rgba(0, 0, 0, 0.25)",
                lineChartPrimary = "#666ee8",
                lineChartDanger = "#ff4961",
                labelColor = "#6e6b7b",
                grid_line_color = "rgba(200, 200, 200, 0.2)";
            var polarExample = new Chart(polarAreaChartEx, {
                type: "polarArea",
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    responsiveAnimationDuration: 500,
                    legend: {
                        position: "right",
                        labels: {
                            usePointStyle: true,
                            padding: 25,
                            boxWidth: 9,
                            fontColor: labelColor,
                        },
                    },
                    layout: {
                        padding: {
                            top: 10,
                            bottom: 10,
                        },
                    },
                    tooltips: {
                        // Updated default tooltip UI
                        shadowOffsetX: 1,
                        shadowOffsetY: 1,
                        shadowBlur: 8,
                        shadowColor: tooltipShadow,
                        backgroundColor: window.colors.solid.white,
                        titleFontColor: window.colors.solid.black,
                        bodyFontColor: window.colors.solid.black,
                    },
                    scale: {
                        scaleShowLine: true,
                        scaleLineWidth: 1,
                        ticks: {
                            display: false,
                            fontColor: labelColor,
                        },
                        reverse: false,
                        gridLines: {
                            display: false,
                        },
                    },
                    plugins: {
                        datalabels: {
                            color: function (context) {
                                var value = context.dataset.data[context.dataIndex];
                                return "white";
                            },
                            font: function (context) {
                                let size = context.dataset.data[context.dataIndex] > 0 ? 16 : 0;
                                return {
                                    size: size,
                                    weight: "bold",
                                };
                            },
                        },
                    },
                    animation: {
                        animateRotate: false,
                    },
                },
                data: {
                    labels: thisComponent.collection?.quarterchart?.labels,
                    datasets: [
                        {
                            label: "Data",
                            backgroundColor: [primaryColorShade, warningColorShade, infoColorShade, successColorShade],
                            data: thisComponent.collection?.quarterchart?.data,
                            borderWidth: 0,
                        },
                    ],
                },
            });
            // Large chart
            var polarExampleLarge = new Chart(polarAreaChartExLarge, {
                type: "polarArea",
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    responsiveAnimationDuration: 500,
                    legend: {
                        position: "right",
                        labels: {
                            usePointStyle: true,
                            padding: 72,
                            boxWidth: 256,
                            fontColor: labelColor,
                            fontSize: 18,
                        },
                    },
                    layout: {
                        padding: {
                            top: 10,
                            bottom: 10,
                        },
                    },
                    tooltips: {
                        // Updated default tooltip UI
                        shadowOffsetX: 1,
                        shadowOffsetY: 1,
                        shadowBlur: 8,
                        shadowColor: tooltipShadow,
                        backgroundColor: window.colors.solid.white,
                        titleFontColor: window.colors.solid.black,
                        bodyFontColor: window.colors.solid.black,
                    },
                    scale: {
                        scaleShowLine: true,
                        scaleLineWidth: 1,
                        ticks: {
                            display: false,
                            fontColor: labelColor,
                        },
                        reverse: false,
                        gridLines: {
                            display: false,
                        },
                    },
                    plugins: {
                        datalabels: {
                            color: function (context) {
                                var value = context.dataset.data[context.dataIndex];
                                return "white";
                            },
                            font: function (context) {
                                let size = context.dataset.data[context.dataIndex] > 0 ? 32 : 0;
                                return {
                                    size: size,
                                    weight: "bold",
                                };
                            },
                        },
                    },
                    animation: {
                        animateRotate: false,
                    },
                },
                data: {
                    labels: thisComponent.collection?.quarterchart?.labels,
                    datasets: [
                        {
                            label: "Data",
                            backgroundColor: [primaryColorShade, warningColorShade, infoColorShade, successColorShade],
                            data: thisComponent.collection?.quarterchart?.data,
                            borderWidth: 0,
                        },
                    ],
                },
            });
        },
        deleteModel(id) {},
        draw(type) {
            var thisComponent = this;
            thisComponent.active = type;
            if (type == "report") {
                axios
                    .get("/" + thisComponent.locale + "/axios/organisations/plan", {})
                    .then(function (response) {
                        console.log(response.data);
                        thisComponent.collection = response.data;
                        thisComponent.color = "#" + response.data.organisation.orgcolor;
                        thisComponent.$nextTick(() => {
                            thisComponent.buildReportChart();
                        });
                    })
                    .catch(function (error) {
                        console.log(error);
                        console.log(error.response);
                    });
                thisComponent.$nextTick(function () {
                    if (feather) {
                        feather.replace({
                            width: 14,
                            height: 14,
                        });
                    }
                });
            } else {
                axios
                    .get("/" + thisComponent.locale + "/axios/organisations/plan", {})
                    .then(function (response) {
                        console.log(response.data);
                        thisComponent.collection = response.data;
                        thisComponent.color = "#" + response.data.organisation.orgcolor;
                        thisComponent.$nextTick(() => {
                            thisComponent.buildTable();
                        });
                    })
                    .catch(function (error) {
                        console.log(error);
                        console.log(error.response);
                    });
            }
        },
        enableComponentButton(componentID) {
            $("#componentButton" + componentID).prop("disabled", false);
        },
        enablePlanButton(statementID) {
            $("#statementButton" + statementID).prop("disabled", false);
        },
        async generateDocx() {
            var thisComponent = this;
            // generate accent colour
            var primaryToAccent = function (hexColor) {
                var result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hexColor);
                let r = parseInt(result[1], 16);
                let g = parseInt(result[2], 16);
                let b = parseInt(result[3], 16);
                (r /= 255), (g /= 255), (b /= 255);
                var max = Math.max(r, g, b),
                    min = Math.min(r, g, b);
                var h,
                    s,
                    l = (max + min) / 2;
                if (max == min) {
                    h = s = 0; // achromatic
                } else {
                    var d = max - min;
                    s = l > 0.5 ? d / (2 - max - min) : d / (max + min);
                    switch (max) {
                        case r:
                            h = (g - b) / d + (g < b ? 6 : 0);
                            break;
                        case g:
                            h = (b - r) / d + 2;
                            break;
                        case b:
                            h = (r - g) / d + 4;
                            break;
                    }
                    h /= 6;
                }
                var HSL = new Object();
                HSL["h"] = h;
                HSL["s"] = s;
                HSL["l"] = 0.8;
                return hsl(h * 360, 75, 70);
            };
            const accentColor = primaryToAccent("#" + thisComponent.collection?.organisation?.orgcolor).substring(1);
            // End accent color /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            // header logo
            // do stuff to generate an aspect ratio respecting hight
            function getRatio(url) {
                return new Promise((resolve, reject) => {
                    let logoImage = new Image();
                    logoImage.src = url;
                    logoImage.onload = () => resolve(logoImage);
                    logoImage.onerror = () => reject();
                });
            }
            const logoImageFile = await getRatio(thisComponent.collection?.organisation?.logo);
            // end do ratio stuff
            const logoBlob = await fetch(thisComponent.collection?.organisation?.logo).then((r) => r.blob());
            const headerImage = new ImageRun({
                data: logoBlob,
                transformation: {
                    height: 60,
                    width: Math.round((logoImageFile.width / logoImageFile.height) * 60),
                },
            });
            // end header logo///////////////////////////////////////////////////////////////////////////////
            // header title
            const headerTitle = new TextRun({
                text: thisComponent.collection?.messages?.planningReport,
                bold: true,
                color: thisComponent.collection?.organisation?.orgcolor,
                font: "Times New Roman",
                size: 28,
            });
            /////////////////////////////////
            // header table
            const headerBorders = {
                top: {
                    style: BorderStyle.NONE,
                },
                bottom: {
                    style: BorderStyle.NONE,
                },
                left: {
                    style: BorderStyle.NONE,
                },
                right: {
                    style: BorderStyle.NONE,
                },
            };
            const headerTable = new Table({
                rows: [
                    new TableRow({
                        children: [
                            new TableCell({
                                borders: headerBorders,
                                children: [
                                    new Paragraph({
                                        children: [headerTitle],
                                        alignment: AlignmentType.LEFT,
                                    }),
                                ],
                                verticalAlign: VerticalAlign.CENTER,
                            }),
                            new TableCell({
                                borders: headerBorders,
                                children: [
                                    new Paragraph({
                                        children: [headerImage],
                                        alignment: AlignmentType.RIGHT,
                                    }),
                                ],
                                verticalAlign: VerticalAlign.CENTER,
                            }),
                        ],
                    }),
                ],
                width: {
                    size: 100,
                    type: WidthType.PERCENTAGE,
                },
                alignment: AlignmentType.CENTER,
                columnWidths: [convertInchesToTwip(3.5), convertInchesToTwip(3.5)],
            });
            // end header table /////////////////////////////////////////////////////////////////
            // headerbar
            const headerBarBlob = await domtoimage.toBlob(document.getElementById("headerBar")).then((blob) => blob);
            const headerBarImage = new ImageRun({
                data: headerBarBlob,
                transformation: {
                    height: 5,
                    width: 680,
                },
            });
            const headerBarTable = new Table({
                rows: [
                    new TableRow({
                        children: [
                            new TableCell({
                                borders: headerBorders,
                                children: [
                                    new Paragraph({
                                        children: [headerBarImage],
                                        alignment: AlignmentType.CENTER,
                                    }),
                                ],
                                verticalAlign: VerticalAlign.CENTER,
                                columnSpan: 2,
                            }),
                        ],
                    }),
                ],
                width: {
                    size: 100,
                    type: WidthType.PERCENTAGE,
                },
                alignment: AlignmentType.CENTER,
                columnWidths: [convertInchesToTwip(3.5), convertInchesToTwip(3.5)],
            });
            // end header bar/////////////////////////////////////////////////////////////////////
            // Summary paragraph
            const summaryParagraph = new Paragraph({
                children: [
                    new TextRun({
                        text: thisComponent.collection?.messages?.summary,
                        color: thisComponent.collection?.organisation?.orgcolor,
                        font: "Times New Roman",
                        size: 28,
                        break: 2,
                    }),
                ],
            });
            const summaryDescParagraph = new Paragraph({
                children: [
                    new TextRun({
                        text: thisComponent.collection?.messages?.summaryDesc,
                        font: "Times New Roman",
                        size: 24,
                    }),
                ],
            });
            //////////////////////////////////////////////////////////////////////////////////////
            // Graph paragraph
            const graphBlob = await domtoimage.toBlob(document.getElementById("graphelementlarge")).then((blob) => blob);
            const graphImage = new ImageRun({
                data: graphBlob,
                transformation: {
                    height: 240,
                    width: 600,
                },
            });
            const graphParagraph = new Paragraph({
                children: [graphImage],
                alignment: AlignmentType.CENTER,
                spacing: {
                    before: 200,
                },
            });
            //////////////////////////////////////////////////////////////////////////////////////
            // Plan Section
            const planTitle = new Paragraph({
                children: [
                    new TextRun({
                        text: thisComponent.collection?.messages?.dataProtectionPlan,
                        color: thisComponent.collection?.organisation?.orgcolor,
                        font: "Times New Roman",
                        size: 26,
                        break: 2,
                    }),
                ],
            });
            const planText = new Paragraph({
                children: [
                    new TextRun({
                        text: thisComponent.collection?.messages?.dataProtectionText,
                        font: "Times New Roman",
                        size: 24,
                    }),
                ],
                spacing: {
                    after: 200,
                },
            });
            /////////////// Plan Table
            let planTableRows = [
                new TableRow({
                    children: [
                        new TableCell({
                            children: [
                                new Paragraph({
                                    children: [
                                        new TextRun({
                                            text: thisComponent.collection?.messages?.quarter,
                                            font: "Times New Roman",
                                            size: 24,
                                        }),
                                    ],
                                    alignment: AlignmentType.CENTER,
                                }),
                            ],
                            shading: {
                                fill: accentColor,
                                type: ShadingType.PERCENT_60,
                                color: accentColor,
                            },
                            verticalAlign: VerticalAlign.CENTER,
                        }),
                        new TableCell({
                            children: [
                                new Paragraph({
                                    children: [
                                        new TextRun({
                                            text: thisComponent.collection?.messages?.component,
                                            font: "Times New Roman",
                                            size: 24,
                                        }),
                                    ],
                                    alignment: AlignmentType.CENTER,
                                }),
                            ],
                            shading: {
                                fill: accentColor,
                                type: ShadingType.PERCENT_60,
                                color: accentColor,
                            },
                            verticalAlign: VerticalAlign.CENTER,
                        }),
                    ],
                }),
            ];
            let planTableRow = null;
            for (let index = 0; index < 4; index++) {
                let carray = thisComponent.collection?.quarterchart?.components;
                carray = carray[index];
                carray = carray.join(", ");
                planTableRow = new TableRow({
                    children: [
                        new TableCell({
                            children: [
                                new Paragraph({
                                    children: [
                                        new TextRun({
                                            text: (index + 1).toString(),
                                            font: "Times New Roman",
                                            size: 24,
                                        }),
                                    ],
                                    alignment: AlignmentType.CENTER,
                                }),
                            ],
                            verticalAlign: VerticalAlign.CENTER,
                        }),
                        new TableCell({
                            children: [
                                new Paragraph({
                                    children: [
                                        new TextRun({
                                            text: carray,
                                            font: "Times New Roman",
                                            size: 24,
                                        }),
                                    ],
                                    alignment: AlignmentType.CENTER,
                                }),
                            ],
                            verticalAlign: VerticalAlign.CENTER,
                        }),
                    ],
                });
                planTableRows.push(planTableRow);
            }
            const planTableFooterRow = new TableRow({
                children: [
                    new TableCell({
                        children: [
                            new Paragraph({
                                children: [
                                    new TextRun({
                                        text: "",
                                        font: "Times New Roman",
                                        size: 24,
                                    }),
                                ],
                                alignment: AlignmentType.CENTER,
                            }),
                        ],
                        shading: {
                            fill: thisComponent.collection?.organisation?.orgcolor,
                            type: ShadingType.PERCENT_95,
                            color: thisComponent.collection?.organisation?.orgcolor,
                        },
                        verticalAlign: VerticalAlign.CENTER,
                        columnSpan: 2,
                    }),
                ],
            });
            planTableRows.push(planTableFooterRow);
            const planTable = new Table({
                rows: planTableRows,
                width: {
                    size: 100,
                    type: WidthType.PERCENTAGE,
                },
                alignment: AlignmentType.CENTER,
                columnWidths: [convertInchesToTwip(1.1), convertInchesToTwip(5.1)],
            });
            /////END plan table/////////////////////////////////////////////////////////////////////////////////////////////
            // New Page components
            //insert page break
            const pageBreak = new Paragraph({
                text: "",
                children: [new PageBreak()],
            });
            // Title
            const componentsDescTitle = new Paragraph({
                children: [
                    new TextRun({
                        text: thisComponent.collection?.messages?.componentsDesc,
                        color: thisComponent.collection?.organisation?.orgcolor,
                        font: "Times New Roman",
                        size: 28,
                        break: 2,
                    }),
                ],
                spacing: {
                    after: 300,
                }
            });
            let docChildren = [summaryParagraph, summaryDescParagraph, graphParagraph, planTitle, planText, planTable, pageBreak, componentsDescTitle];
            // Components paragraph
            let componentCodeName = null;
            let componentDesc = null;
            let componentImplementation = null;
            for (const comp of thisComponent.collection?.quarterchart?.componentsfinal) {
                componentCodeName = new Paragraph({
                    children: [
                        new TextRun({
                            text: comp.codename,
                            font: "Times New Roman",
                            size: 24,
                            color: thisComponent.collection?.organisation?.orgcolor,
                        }),
                    ],
                });
                docChildren.push(componentCodeName);
                componentDesc = new Paragraph({
                    children: [
                        new TextRun({
                            text: comp.desc,
                            font: "Times New Roman",
                            size: 24,
                        }),
                    ],
                    spacing: {
                        after: 100,
                    }
                });
                docChildren.push(componentDesc);
                componentImplementation = new Paragraph({
                    children: [
                        new TextRun({
                            text: comp.implementation,
                            font: "Times New Roman",
                            size: 24,
                            color: thisComponent.collection?.organisation?.orgcolor,
                        }),
                    ],
                    spacing: {
                        after: 150,
                    }
                });
                docChildren.push(componentImplementation);
            }
            //// End New Page components ///////////////////////////////////////////////////////////////////////////////////
            const doc = new Document({
                sections: [
                    {
                        headers: {
                            default: new Header({
                                children: [headerTable, headerBarTable],
                            }),
                        },
                        children: docChildren,
                    },
                ],
            });
            // end generate document
            // save document
            Packer.toBlob(doc).then((blob) => {
                saveAs(blob, thisComponent.collection?.organisation?.name + "_" + thisComponent.collection?.messages?.planningReport + "_" + new Date().toLocaleDateString() + ".docx");
            });
            // end save document
        },
        orgUpdate() {
            var thisComponent = this;
            const orgFormData = new FormData();
            // has logo?
            if (document.getElementById("logoInput").files.length > 0) {
                orgFormData.append("logo", document.getElementById("logoInput").files[0]);
            }
            // has color?
            if (thisComponent.color != null) {
                orgFormData.append("color", thisComponent.color.substring(1));
            }
            axios
                .post(`/${thisComponent.locale}/axios/organisations/update/`, orgFormData)
                .then(function (response) {
                    console.log(response.data);
                    thisComponent.collection.organisation = response.data;
                    thisComponent.errors = null;
                })
                .catch(function (error) {
                    //console.log(error.response);
                    thisComponent.errors = error.response.data.errors;
                });
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
            let r = $(`#responsibilityInput${statementID}`).val();
            axios
                .post(`/${thisComponent.locale}/axios/organisations/statements/plans/update`, {
                    statement_id: statementID,
                    //plan_id: v,
                    implementation: i,
                    responsibility: r,
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
                    console.log(error);
                    console.log(error.response.data);
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
        //axios;
        this.draw("components");
    },
};
</script>
