<template>
    <div class="row">
        <div class="col-12">
            <div class="card invoice-list-wrapper">
                <div class="card-datatable table-responsive">
                    <table class="invoice-list-table table" id="dataTable"></table>
                </div>
            </div>
        </div>
        <div class="col-12 modal fade text-start modal-primary" id="kpiViewModal" tabindex="-1" aria-labelledby="kpiLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="kpiLabel">{{ collection?.messages?.kpi }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" @click="kpiHide"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-4 table-responsive">
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
                                            <td>{{ kpiActive?.id }}</td>
                                        </tr>
                                        <tr>
                                            <td>{{ collection?.messages?.name }}</td>
                                            <td>{{ kpiActive ? kpiActive["name_" + locale] : null }}</td>
                                        </tr>
                                        <tr>
                                            <td>{{ collection?.messages?.desc }}</td>
                                            <td>{{ kpiActive ? kpiActive["desc_" + locale] : null }}</td>
                                        </tr>
                                        <tr>
                                            <td>{{ collection?.messages?.target }}</td>
                                            <td>{{ kpiActive?.kpicomment ? kpiActive.kpicomment.target : null }}</td>
                                        </tr>
                                        <tr>
                                            <td>{{ collection?.messages?.value }}</td>
                                            <td>{{ kpiActive?.kpicomment ? kpiActive.kpicomment.value : null }}</td>
                                        </tr>
                                        <tr>
                                            <td>{{ collection?.messages?.comment }}</td>
                                            <td>{{ kpiActive?.kpicomment ? kpiActive.kpicomment.comment : null }}</td>
                                        </tr>
                                        <tr>
                                            <td>{{ collection?.messages?.lastUpdated }}</td>
                                            <td>{{ kpiActive?.kpicomment ? kpiActive.kpicomment.created_at_for_humans : null }}</td>
                                        </tr>
                                        <tr>
                                            <td>{{ collection?.messages?.by }}</td>
                                            <td>{{ kpiActive?.kpicomment ? kpiActive.kpicomment.user.name + " [" + kpiActive.kpicomment.user.role + "]" : null }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-8">
                                <div class="card">
                                    <div class="card-header d-flex flex-sm-row flex-column justify-content-md-between align-items-start justify-content-start">
                                        <div>
                                            <h4 class="card-title">{{ collection?.messages?.kpi }} {{ collection?.messages?.history }}</h4>
                                            <span class="card-subtitle text-muted">{{ collection?.messages?.kpiHistorySubtitle }}</span>
                                        </div>
                                        <div class="d-flex align-items-center"></div>
                                    </div>
                                    <div id="kpi-history-parent" class="card-body">
                                        <div id="kpi-history-chart"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-primary" @click="kpiHide">Ok</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    props: ["locale", "role"],
    data() {
        return {
            areaChart: null,
            dataTable: null,
            collection: null,
            kpiActive: null,
        };
    },
    methods: {
        buildKpiChart() {
            var thisComponent = this;
            var chartColors = {
                column: {
                    series1: "#826af9",
                    series2: "#d2b0ff",
                    bg: "#f8d3ff",
                },
                success: {
                    shade_100: "#7eefc7",
                    shade_200: "#06774f",
                },
                donut: {
                    series1: "#ffe700",
                    series2: "#00d4bd",
                    series3: "#826bf8",
                    series4: "#2b9bf4",
                    series5: "#FFA1A1",
                },
                area: {
                    series3: "#a4f8cd",
                    series2: "#60f2ca",
                    series1: "#2bdac7",
                },
            };
            var areaChartEl = document.querySelector("#kpi-history-chart"),
                areaChartConfig = {
                    chart: {
                        height: 400,
                        type: "area",
                        parentHeightOffset: 0,
                        toolbar: {
                            show: false,
                        },
                    },
                    colors: [chartColors.donut.series1, chartColors.donut.series3],
                    dataLabels: {
                        enabled: false,
                    },
                    fill: {
                        type: "gradient",
                        gradient: {
                            shadeIntensity: 1,
                            opacityFrom: 0.7,
                            opacityTo: 0.9,
                            stops: [0, 90, 100],
                        },
                    },
                    legend: {
                        show: true,
                        position: "top",
                        horizontalAlign: "start",
                    },
                    grid: {
                        xaxis: {
                            lines: {
                                show: true,
                            },
                        },
                    },
                    markers: {
                        size: [5, 5],
                    },
                    series: [
                        {
                            name: thisComponent.collection?.messages?.target,
                            data: thisComponent.kpiActive?.targets,
                        },
                        {
                            name: thisComponent.collection?.messages?.value,
                            data: thisComponent.kpiActive?.values,
                        },
                    ],
                    stroke: {
                        curve: "smooth",
                        width: 4,
                    },
                    tooltip: {
                        enabled: true,
                        shared: true,
                        custom: function ({ series, seriesIndex, dataPointIndex, w, context }) {
                            /*
                            let x = `
                            <div class="apexcharts-tooltip apexcharts-theme-light apexcharts-active" style="left: 445.159px; top: 124.792px;">
                                <div class="apexcharts-tooltip-title" style="font-family: Helvetica, Arial, sans-serif; font-size: 12px;">2022-06-18</div>
                                <div class="apexcharts-tooltip-series-group apexcharts-active" style="order: 1; display: flex;">
                                    <span class="apexcharts-tooltip-marker" style="background-color: rgb(130, 107, 248);"></span>
                                    <div class="apexcharts-tooltip-text" style="font-family: Helvetica, Arial, sans-serif; font-size: 12px;">
                                        <div class="apexcharts-tooltip-y-group">
                                            <span class="apexcharts-tooltip-text-label">Value: </span>
                                            <span class="apexcharts-tooltip-text-value">60</span>
                                        </div>
                                        <div class="apexcharts-tooltip-z-group">
                                            <span class="apexcharts-tooltip-text-z-label"></span>
                                            <span class="apexcharts-tooltip-text-z-value"></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="apexcharts-tooltip-series-group" style="order: 2; display: none;">
                                    <span class="apexcharts-tooltip-marker" style="background-color: rgb(130, 107, 248);"></span>
                                    <div class="apexcharts-tooltip-text" style="font-family: Helvetica, Arial, sans-serif; font-size: 12px;">
                                        <div class="apexcharts-tooltip-y-group">
                                            <span class="apexcharts-tooltip-text-label">Value: </span>
                                            <span class="apexcharts-tooltip-text-value">60</span>
                                        </div>
                                    <div class="apexcharts-tooltip-z-group">
                                        <span class="apexcharts-tooltip-text-z-label"></span>
                                        <span class="apexcharts-tooltip-text-z-value"></span>
                                    </div>
                                </div>
                            </div>
                            `;
                            */
                            let r = `
                            <div class="arrow_box" style="left: 445.159px; top: 124.792px;">
                                <div class="apexcharts-tooltip-title" style="font-family: Helvetica, Arial, sans-serif; font-size: 12px;">${thisComponent.kpiActive?.targets[dataPointIndex].x}</div>
                                <div class="apexcharts-tooltip-series-group apexcharts-active" style="order: 1; display: flex;">
                                    <span class="apexcharts-tooltip-marker" style="background-color: ${chartColors.donut.series1};"></span>
                                    <div class="apexcharts-tooltip-text" style="font-family: Helvetica, Arial, sans-serif; font-size: 12px;">
                                        <div class="apexcharts-tooltip-y-group">
                                            <span class="apexcharts-tooltip-text-label">${thisComponent.collection?.messages?.target}: </span>
                                            <span class="apexcharts-tooltip-text-value">${thisComponent.kpiActive?.targets[dataPointIndex].y}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="apexcharts-tooltip-series-group apexcharts-active" style="order: 1; display: flex;">
                                    <span class="apexcharts-tooltip-marker" style="background-color: ${chartColors.donut.series3};"></span>
                                    <div class="apexcharts-tooltip-text" style="font-family: Helvetica, Arial, sans-serif; font-size: 12px;">
                                        <div class="apexcharts-tooltip-y-group">
                                            <span class="apexcharts-tooltip-text-label">${thisComponent.collection?.messages?.value}: </span>
                                            <span class="apexcharts-tooltip-text-value">${thisComponent.kpiActive?.values[dataPointIndex].y}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="apexcharts-tooltip-series-group apexcharts-active" style="order: 1; display: flex;">
                                    <div class="apexcharts-tooltip-text" style="font-family: Helvetica, Arial, sans-serif; font-size: 12px;">
                                        <div class="apexcharts-tooltip-y-group">
                                            <span class="apexcharts-tooltip-text-label">${thisComponent.collection?.messages?.user}: </span>
                                            <span class="apexcharts-tooltip-text-value">${thisComponent.kpiActive?.targets[dataPointIndex].user}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="apexcharts-tooltip-series-group apexcharts-active" style="order: 1; display: flex;">
                                    <div class="apexcharts-tooltip-text" style="font-family: Helvetica, Arial, sans-serif; font-size: 12px;">
                                        <div class="apexcharts-tooltip-y-group">
                                            <span class="apexcharts-tooltip-text-label">${thisComponent.collection?.messages?.comment}: </span>
                                            <span class="apexcharts-tooltip-text-value">${thisComponent.kpiActive?.targets[dataPointIndex].comment}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            `;
                            return r;
                        },
                    },
                    xaxis: {
                        type: "category",
                        categories: thisComponent.kpiActive?.xaxis,
                    },
                    yaxis: {
                        opposite: false,
                    },
                };
            if (typeof areaChartEl !== undefined && areaChartEl !== null) {
                thisComponent.areaChart = new ApexCharts(areaChartEl, areaChartConfig);
                thisComponent.areaChart.render();
            }
        },
        buildTable() {
            var thisComponent = this;
            let header = `
             <thead>
                <tr>
                    <th>${thisComponent.collection?.messages?.id}</th>
                    <th>${thisComponent.collection?.messages?.name}</th>
                    <th>${thisComponent.collection?.messages?.desc}</th>
                    <th>${thisComponent.collection?.messages?.target}</th>
                    <th>${thisComponent.collection?.messages?.value}</th>
                    <th>${thisComponent.collection?.messages?.comment}</th>
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
                columns: [{ data: "id" }, { data: "name_" + thisComponent.locale }, { data: "desc_" + thisComponent.locale }, { data: "kpicomment" }, { data: "kpicomment" }, { data: "kpicomment" }],
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
                        // target
                        targets: 3,
                        responsivePriority: 3,
                        width: "10%",
                        render: function (data, type, full, meta) {
                            if (type == "sort") {
                                let s = full.kpicomment ? full.kpicomment.target : null;
                                return s;
                            } else {
                                let r = `<p>${full.kpicomment ? full.kpicomment.target : ""}</p>`;
                                if (thisComponent.role == "user") {
                                    r = `
                                    <div class="form-group">
                                        <input class="form-control" type="text" placeholder="50" id="targetInput${full.id}" onchange="window.thisComponent.enableUpdateButton(${full.id})" value="${full.kpicomment ? full.kpicomment.target : ""}" />
                                    </div>
                                    `;
                                }
                                return r;
                            }
                        },
                    },
                    {
                        // value
                        targets: 4,
                        responsivePriority: 4,
                        width: "10%",
                        render: function (data, type, full, meta) {
                            if (type == "sort") {
                                let s = full.kpicomment ? full.kpicomment.value : null;
                                return s;
                            } else {
                                let r = `<p>${full.kpicomment ? full.kpicomment.value : ""}</p>`;
                                if (thisComponent.role == "user") {
                                    r = `
                                    <div class="form-group">
                                        <input class="form-control" type="text" placeholder="50" id="valueInput${full.id}" onchange="window.thisComponent.enableUpdateButton(${full.id})" value="${full.kpicomment ? full.kpicomment.value : ""}" />
                                    </div>
                                    `;
                                }
                                return r;
                            }
                        },
                    },
                    {
                        // comment
                        targets: 5,
                        responsivePriority: 5,
                        orderable: false,
                        width: "10%",
                        render: function (data, type, full, meta) {
                            if (type == "sort") {
                                let s = full.kpicomment ? full.kpicomment.comment : null;
                                return s;
                            } else {
                                let r = `<p>${full.kpicomment ? full.kpicomment.comment : ""}</p>`;
                                if (thisComponent.role == "user") {
                                    r = `
                                    <div class="form-group">
                                        <textarea class="form-control" type="text" placeholder="" id="commentInput${full.id}" onchange="window.thisComponent.enableUpdateButton(${full.id})">${full.kpicomment ? full.kpicomment.comment : ""}</textarea>
                                    </div>
                                    `;
                                }
                                return r;
                            }
                        },
                    },
                    {
                        // actions
                        targets: 6,
                        responsivePriority: 6,
                        width: "10%",
                        orderable: false,
                        render: function (data, type, full, meta) {
                            let r = `
                                <div class="d-flex justify-content-center align-items-center px-2">
                                    <div class="d-flex flex-column">
                                `;
                            if (thisComponent.role == "user") {
                                r += `
                                 <button type="button" class="btn btn-gradient-primary mb-1" id="kpiButton${full.id}" onClick="window.thisComponent.kpiUpdate(${full.id})" disabled>
                                    <span>${thisComponent.collection?.messages?.update}</span>
                                </button>
                                `;
                            }
                            r += `
                             <button type="button" class="btn btn-outline-primary waves-effect mb-1" onClick="window.thisComponent.kpiShow(${full.id})">
                                <span>${thisComponent.collection?.messages?.view}</span>
                            </button>
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
                .get("/" + thisComponent.locale + "/axios/organisations/kpis", {})
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
        enableUpdateButton(id) {
            $("#kpiButton" + id).prop("disabled", false);
        },
        kpiHide() {
            $("#kpiViewModal").modal("hide");
            this.kpiActive = null;
            // kill chart
            this.areaChart.destroy();
            this.areaChart = null;
        },
        kpiShow(id) {
            axios
                .get(`/${thisComponent.locale}/axios/organisations/kpis/${id}`, {})
                .then(function (response) {
                    //console.log(response.data);
                    thisComponent.kpiActive = response.data;
                    $("#kpiViewModal").modal("show");
                    thisComponent.$nextTick(() => {
                        thisComponent.buildKpiChart();
                    });
                })
                .catch(function (error) {
                    console.log(error);
                    console.log(error.response);
                });

            //let y = this.collection.kpis.filter((x) => x.id == id);
            //this.kpiActive = y[0];
            //console.log(this.kpiActive.xaxis);
        },
        kpiUpdate(id) {
            $("#kpiButton" + id).prop("disabled", true);
            let t = $(`#targetInput${id}`).val();
            let v = $(`#valueInput${id}`).val();
            let c = $(`#commentInput${id}`).val();
            axios
                .post(`/${thisComponent.locale}/axios/organisations/kpicomments/store`, {
                    target: t,
                    value: v,
                    comment: c,
                    kpi_id: id,
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
    },
    mounted() {
        window.thisComponent = this;
        this.draw();
    },
};
</script>
