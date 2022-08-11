<template>
    <div class="row">
        <div class="col-xl-12 col-md-6 col-12">
            <div class="card card-statistics mb-1">
                <div class="card-header">
                    <h4 class="card-title">{{ collection?.messages?.statistics }}</h4>
                    <div class="d-flex align-items-center">
                        <p class="card-text font-small-2 me-25 mb-0">{{ collection?.messages?.renderedAt }} {{ collection?.now }}</p>
                    </div>
                </div>
                <div class="card-body statistics-body">
                    <div class="row">
                        <!-- Scatter Chart Starts -->
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">{{ collection?.messages?.risks }} {{ collection?.messages?.scatter }}</h4>
                                    <div class="d-flex align-items-center">
                                        <div class="px-1 py-1">
                                            <i data-feather="calendar"></i>
                                        </div>
                                        <div class="col-6" style="width: 200px;">
                                            <select id="rangeSelect" class="select2 form-select form-control">
                                                <option v-for="rangeDate in collection?.rangeDates" :key="rangeDate" :value="rangeDate">{{rangeDate}}</option>
                                            </select>
                                        </div>
                                        <!--<input type="date" id="scatterRiskRange" class="form-control border-0 shadow-none bg-transparent pe-0" :placeholder="collection?.messages?.riskRange" />-->
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="row d-flex justify-content-center align-items-center" id="scatterLegend"></div>
                                    <canvas class="bubble-chart-ex chartjs" data-height="500"></canvas>
                                </div>
                            </div>
                        </div>
                        <!-- Scatter Chart Ends -->
                        <div class="col-4">
                            <div class="card">
                                <div class="card-header flex-column align-items-start">
                                    <h4 class="card-title mb-75">{{ collection?.messages?.risk }} {{ collection?.messages?.ratio }}</h4>
                                    <span class="card-subtitle text-muted">{{ collection?.messages?.riskPieChartPerRiskFactor }} </span>
                                </div>
                                <div class="card-body mt-4">
                                    <div id="ratio-chart"></div>
                                </div>
                            </div>
                        </div>
                        <!-- History Chart-->
                        <div class="col-8">
                            <div class="card">
                                <div class="card-header d-flex flex-sm-row flex-column justify-content-md-between align-items-start justify-content-start">
                                    <div>
                                        <h4 class="card-title">{{ collection?.messages?.risk }} {{ collection?.messages?.history }}</h4>
                                        <span class="card-subtitle text-muted">{{ collection?.messages?.oneYearPastFromToday }}</span>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div id="history-chart" class="apexcharts-history"></div>
                                </div>
                            </div>
                        </div>
                        <!-- History Chart End-->
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12">
            <div class="card invoice-list-wrapper">
                <div class="card-datatable table-responsive">
                    <table class="invoice-list-table table" id="dataTable"></table>
                </div>
            </div>
        </div>
        <!-- Risk Show Modal -->
        <div class="modal fade text-start modal-primary" id="riskViewModal" tabindex="-1" aria-labelledby="riskViewLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-extra-wide">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="riskViewLabel">{{ collection?.messages?.risk }} {{ collection?.messages?.show }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" @click="riskHide"></button>
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
                                            <td>{{ collection?.messages?.createdAt }}</td>
                                            <td>{{ riskActive?.created_at_for_humans }}</td>
                                        </tr>
                                        <tr>
                                            <td>{{ collection?.messages?.by }}</td>
                                            <td>{{ riskActive?.user?.name + ` [${riskActive?.user?.role}]` }}</td>
                                        </tr>
                                        <tr>
                                            <td>{{ collection?.messages?.title }}</td>
                                            <td>{{ riskActive?.title }}</td>
                                        </tr>
                                        <tr>
                                            <td>{{ collection?.messages?.desc }}</td>
                                            <td>{{ riskActive?.desc }}</td>
                                        </tr>
                                        <tr>
                                            <td>{{ collection?.messages?.probability }}</td>
                                            <td>{{ riskActive?.probability }}</td>
                                        </tr>
                                        <tr>
                                            <td>{{ collection?.messages?.consequence }}</td>
                                            <td>{{ riskActive?.consequence }}</td>
                                        </tr>
                                        <tr>
                                            <td>{{ collection?.messages?.risk }}</td>
                                            <td>
                                                <div class="d-flex justify-content-start align-items-start">
                                                    <span :class="`badge rounded-pill badge-light-${riskActive?.risk?.class}`">{{ riskActive?.risk?.text }}</span>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>{{ collection?.messages?.responsibilityOf }}</td>
                                            <td>{{ riskActive?.responsible }}</td>
                                        </tr>
                                        <tr>
                                            <td>{{ collection?.messages?.organisation }}</td>
                                            <td>{{ riskActive?.organisation?.name }}</td>
                                        </tr>
                                        <tr>
                                            <td>{{ collection?.messages?.component }}</td>
                                            <td>{{ riskActive?.component?.code_name }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-primary" @click="riskHide">Ok</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Risk Comment Start-->
        <div class="modal fade" id="riskComments" tabindex="-1" aria-labelledby="riskCommentsLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-transparent">
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" @click="riskHide"></button>
                    </div>
                    <div class="modal-body mx-50 pb-4">
                        <div class="col-lg-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">{{ collection?.messages?.risks }} {{ collection?.messages?.comments }}</h4>
                                </div>
                                <div class="card-body">
                                    <div class="row d-flex align-items-center justify-content-center">
                                        <div class="col-6">
                                            <div class="mb-1">
                                                <label class="form-label" for="comment">{{ collection?.messages?.comment }}</label>
                                                <textarea id="comment" :class="`form-control ${riskCommentStoreError ? 'is-invalid' : null}`" rows="3" :placeholder="locale == 'en' ? 'I believe this risk does not qualify for consequence 3 because so and so' : 'Jag anser att denna risk inte är berättigad till konsekvens 3 eftersom så och så'"></textarea>
                                                <div v-if="riskCommentStoreError" class="invalid-feedback">{{ riskCommentStoreError?.status + ":" + riskCommentStoreError?.data?.message }}</div>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <button type="button" class="btn btn-info me-1" @click="riskCommentStore">{{ collection?.messages.submit }}</button>
                                        </div>
                                    </div>
                                    <h4 class="card-title">{{ collection?.messages?.history }}</h4>
                                    <ul class="timeline">
                                        <li v-for="comment in riskActive?.risk_comments_sorted" :key="comment.id" class="timeline-item">
                                            <span class="timeline-point timeline-point border-info">
                                                <i data-feather="message-circle" style="color: #ffffff !important"></i>
                                            </span>
                                            <div class="timeline-event">
                                                <div class="d-flex justify-content-between flex-sm-row flex-column mb-sm-0 mb-1">
                                                    <h6>{{ comment.user?.name + " [" + comment.user?.role + "]" }}</h6>
                                                    <span class="timeline-event-time">{{ comment.created_at_for_humans }}</span>
                                                </div>
                                                <p>{{ comment.comment }}</p>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Risk Comment End-->
    </div>
</template>
<script>
import feather from "feather-icons";
export default {
    props: ["locale"],
    data() {
        return {
            dataTable: null,
            collection: null,
            riskActive: { risk_comments: [] },
            riskCommentStoreError: null,
            scatterChart: null,
        };
    },
    methods: {
        buildCharts() {
            var thisComponent = this;
            var colours = thisComponent.collection?.colours;
            var chartWrapper = $(".chartjs"),
                bubbleChartEx = $(".bubble-chart-ex");
            // Wrap charts with div of height according to their data-height
            if (chartWrapper.length) {
                chartWrapper.each(function () {
                    $(this).wrap($('<div style="height:' + this.getAttribute("data-height") + 'px"></div>'));
                });
            }
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
                grid_line_color = "rgba(200, 200, 200, 0.2)"; // RGBA color helps in dark layout
            Chart.plugins.register(ChartDataLabels);
            // scatter chart
            if (bubbleChartEx.length) {
                thisComponent.scatterChart = new Chart(bubbleChartEx, {
                    type: "bubble",
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            xAxes: [
                                {
                                    scaleLabel: {
                                        display: true,
                                        labelString: thisComponent.collection?.messages?.consequence,
                                        fontSize: 14,
                                        fontStyle: "bold",
                                    },
                                    display: true,
                                    gridLines: {
                                        color: grid_line_color,
                                        zeroLineColor: null,
                                    },
                                    ticks: {
                                        stepSize: 1,
                                        min: 0,
                                        max: 6,
                                        fontColor: labelColor,
                                        fontSize: 14,
                                        fontStyle: "bold",
                                        callback: function (value, index, ticks) {
                                            if (value != 0) {
                                                return value;
                                            }
                                        },
                                    },
                                },
                            ],
                            yAxes: [
                                {
                                    scaleLabel: {
                                        display: true,
                                        labelString: thisComponent.collection?.messages?.probability,
                                        fontSize: 14,
                                        fontStyle: "bold",
                                    },
                                    display: true,
                                    gridLines: {
                                        color: grid_line_color,
                                        zeroLineColor: null,
                                    },
                                    ticks: {
                                        stepSize: 1,
                                        min: 0,
                                        max: 6,
                                        fontColor: labelColor,
                                        fontSize: 14,
                                        fontStyle: "bold",
                                        callback: function (value, index, ticks) {
                                            if (value != 0) {
                                                return value;
                                            }
                                        },
                                    },
                                },
                            ],
                        },
                        plugins: {
                            datalabels: {
                                anchor: function (context) {
                                    var value = context.dataset.data[context.dataIndex];
                                    return value.x < 1000 ? "center" : "center";
                                },
                                align: function (context) {
                                    var value = context.dataset.data[context.dataIndex];
                                    return value.x < 1000 ? "center" : "center";
                                },
                                color: function (context) {
                                    var value = context.dataset.data[context.dataIndex];
                                    return value.x < 1000 ? "white" : "white";
                                },
                                font: function (context) {
                                    let v = context.dataset.data[context.dataIndex];
                                    return {
                                        size: 10 + 1.5 * (v.r / 10),
                                        weight: "bold",
                                    };
                                },
                                formatter: function (value, context) {
                                    return context.dataset.count;
                                },
                                offset: 0,
                                padding: 0,
                            },
                        },
                        legend: {
                            display: false,
                        },
                        legendCallback: function (chart) {
                            return `
                            <div class="col-1"></div>
                            <div class="col-2 apexcharts-legend-series text-center">
                                <span class="apexcharts-legend-marker" rel="2" data:collapsed="false" style="background: ${colours?.success} !important; color: ${colours?.success}; height: 12px; width: 36px; left: 0px; top: 0px; border-width: 0px; border-color: rgb(255, 255, 255); border-radius: 2px;"></span>
                                <span class="apexcharts-legend-text" rel="2" i="1" style="color: rgb(55, 61, 63); font-size: 14px; font-weight: 400; font-family: Helvetica, Arial, sans-serif; top: -2px;">${thisComponent.collection?.messages?.low}</span>
                            </div>
                             <div class="col-2 apexcharts-legend-series text-center">
                                <span class="apexcharts-legend-marker" rel="2" data:collapsed="false" style="background: ${colours?.lowMed} !important; color: ${colours?.lowMed}; height: 12px; width: 36px; left: 0px; top: 0px; border-width: 0px; border-color: rgb(255, 255, 255); border-radius: 2px;"></span>
                                <span class="apexcharts-legend-text" rel="2" i="1" style="color: rgb(55, 61, 63); font-size: 14px; font-weight: 400; font-family: Helvetica, Arial, sans-serif; top: -2px;">${thisComponent.collection?.messages?.lowMed}</span>
                            </div>
                             <div class="col-2 apexcharts-legend-series text-center">
                                <span class="apexcharts-legend-marker" rel="2" data:collapsed="false" style="background: ${colours?.warning} !important; color: ${colours?.warning}; height: 12px; width: 36px; left: 0px; top: 0px; border-width: 0px; border-color: rgb(255, 255, 255); border-radius: 2px;"></span>
                                <span class="apexcharts-legend-text" rel="2" i="1" style="color: rgb(55, 61, 63); font-size: 14px; font-weight: 400; font-family: Helvetica, Arial, sans-serif; top: -2px;">${thisComponent.collection?.messages?.medium}</span>
                            </div>
                             <div class="col-2 apexcharts-legend-series text-center">
                                <span class="apexcharts-legend-marker" rel="2" data:collapsed="false" style="background: ${colours?.medHigh} !important; color: ${colours?.medHigh}; height: 12px; width: 36px; left: 0px; top: 0px; border-width: 0px; border-color: rgb(255, 255, 255); border-radius: 2px;"></span>
                                <span class="apexcharts-legend-text" rel="2" i="1" style="color: rgb(55, 61, 63); font-size: 14px; font-weight: 400; font-family: Helvetica, Arial, sans-serif; top: -2px;">${thisComponent.collection?.messages?.mediumHigh}</span>
                            </div>
                             <div class="col-2 apexcharts-legend-series text-center">
                                <span class="apexcharts-legend-marker" rel="2" data:collapsed="false" style="background: ${colours?.danger} !important; color: ${colours?.danger}; height: 12px; width: 36px; left: 0px; top: 0px; border-width: 0px; border-color: rgb(255, 255, 255); border-radius: 2px;"></span>
                                <span class="apexcharts-legend-text" rel="2" i="1" style="color: rgb(55, 61, 63); font-size: 14px; font-weight: 400; font-family: Helvetica, Arial, sans-serif; top: -2px;">${thisComponent.collection?.messages?.high}</span>
                            </div>
                            <div class="col-1"></div>`;
                        },
                        tooltips: {
                            // Updated default tooltip UI
                            enabled: true,
                            shadowOffsetX: 1,
                            shadowOffsetY: 1,
                            shadowBlur: 8,
                            shadowColor: tooltipShadow,
                            backgroundColor: window.colors.solid.white,
                            titleFontColor: window.colors.solid.black,
                            bodyFontColor: window.colors.solid.black,
                            callbacks: {
                                label: function (context) {
                                    let v = thisComponent.collection?.dataSets[context.datasetIndex];
                                    return v.label + `:(${thisComponent.collection?.messages?.consequence}:${v.data[0].x},${thisComponent.collection?.messages?.probability}:${v.data[0].y},${thisComponent.collection?.messages?.risk}:${parseInt(v.data[0].x) * parseInt(v.data[0].y)})`;
                                    return 2;
                                },
                            },
                        },
                    },
                    data: {
                        animation: {
                            duration: 10000,
                        },
                        datasets: thisComponent.collection?.dataSets,
                    },
                });
            }
            thisComponent.$nextTick(() => {
                let d = thisComponent.scatterChart.generateLegend();
                document.getElementById("scatterLegend").innerHTML = d;
            });
            // donut chart
            var donutChartEl = document.querySelector("#ratio-chart"),
                donutChartConfig = {
                    chart: {
                        height: 350,
                        type: "donut",
                    },
                    legend: {
                        show: true,
                        position: "bottom",
                    },
                    labels: [thisComponent.collection?.messages?.low, thisComponent.collection?.messages?.lowMed, thisComponent.collection?.messages?.medium, thisComponent.collection?.messages?.mediumHigh, thisComponent.collection?.messages?.high],
                    series: thisComponent.collection?.series,
                    colors: [colours?.success, colours?.lowMed, colours?.warning, colours?.medHigh, colours?.danger],
                    dataLabels: {
                        enabled: true,
                        formatter: function (val, opt) {
                            return parseInt(val) + "%";
                        },
                    },
                    plotOptions: {
                        pie: {
                            donut: {
                                labels: {
                                    show: true,
                                    name: {
                                        fontSize: "2rem",
                                        fontFamily: "Montserrat",
                                    },
                                    value: {
                                        fontSize: "1rem",
                                        fontFamily: "Montserrat",
                                        formatter: function (val) {
                                            return parseInt(val) + "";
                                        },
                                    },
                                    total: {
                                        show: true,
                                        fontSize: "1.5rem",
                                        label: thisComponent.collection?.messages?.risks,
                                        formatter: function (w) {
                                            let s = thisComponent.collection?.series.reduce((a, b) => a + b, 0);
                                            return s;
                                        },
                                    },
                                },
                            },
                        },
                    },
                    responsive: [
                        {
                            breakpoint: 992,
                            options: {
                                chart: {
                                    height: 380,
                                },
                            },
                        },
                        {
                            breakpoint: 576,
                            options: {
                                chart: {
                                    height: 320,
                                },
                                plotOptions: {
                                    pie: {
                                        donut: {
                                            labels: {
                                                show: true,
                                                name: {
                                                    fontSize: "1.5rem",
                                                },
                                                value: {
                                                    fontSize: "1rem",
                                                },
                                                total: {
                                                    fontSize: "1.5rem",
                                                },
                                            },
                                        },
                                    },
                                },
                            },
                        },
                    ],
                };
            if (typeof donutChartEl !== undefined && donutChartEl !== null) {
                var donutChart = new ApexCharts(donutChartEl, donutChartConfig);
                donutChart.render();
            }
            /* History chart new, colmn */
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
            var columnChartEl = document.querySelector("#history-chart"),
                columnChartConfig = {
                    chart: {
                        height: 400,
                        type: "bar",
                        stacked: true,
                        parentHeightOffset: 0,
                        toolbar: {
                            show: false,
                        },
                    },
                    plotOptions: {
                        bar: {
                            columnWidth: "75%%",
                            borderRadius: 10,
                        },
                    },
                    dataLabels: {
                        enabled: true,
                        style: {
                            fontSize: "12px",
                            fontWeight: "boldest",
                            colors: ["#FFF"],
                        },
                    },
                    legend: {
                        show: false,
                        position: "top",
                        horizontalAlign: "start",
                    },
                    colors: [colours?.success, colours?.lowMed, colours?.warning, colours?.medHigh, colours?.danger],
                    /*
                    stroke: {
                        show: true,
                        colors: ["transparent"],
                    },
                    */
                    grid: {
                        xaxis: {
                            lines: {
                                show: false,
                            },
                        },
                        yaxis: {
                            lines: {
                                show: true,
                            },
                        },
                    },
                    series: [
                        {
                            name: thisComponent.collection?.messages?.low,
                            data: thisComponent.collection?.history?.yaxis?.historyLow,
                        },
                        {
                            name: thisComponent.collection?.messages?.lowMed,
                            data: thisComponent.collection?.history?.yaxis?.historyLowMed,
                        },
                        {
                            name: thisComponent.collection?.messages?.medium,
                            data: thisComponent.collection?.history?.yaxis?.historyMed,
                        },
                        {
                            name: thisComponent.collection?.messages?.mediumHigh,
                            data: thisComponent.collection?.history?.yaxis?.historyMedHigh,
                        },
                        {
                            name: thisComponent.collection?.messages?.high,
                            data: thisComponent.collection?.history?.yaxis?.historyHigh,
                        },
                    ],
                    xaxis: {
                        categories: thisComponent.collection?.history?.xaxis,
                    },
                    fill: {
                        opacity: 0.9,
                    },
                    yaxis: {
                        opposite: false,
                        title: {
                            text: thisComponent.collection?.messages?.risk,
                        },
                    },
                };
            if (typeof columnChartEl !== undefined && columnChartEl !== null) {
                var columnChart = new ApexCharts(columnChartEl, columnChartConfig);
                columnChart.render();
            }
            /* History chart new, column end */
            /* Old History Chart left for Legacy
            var colourStops = [];
            switch (true) {
                case 0 <= Math.max(...thisComponent.collection?.avgs) && Math.max(...thisComponent.collection?.avgs) < 5:
                    colourStops = [{ offset: 100, color: colours?.success, opacity: 0.15 }];
                    break;
                case 5 <= Math.max(...thisComponent.collection?.avgs) && Math.max(...thisComponent.collection?.avgs) < 10:
                    colourStops = [
                        { offset: 0, color: colours?.lowMed, opacity: 0.3 },
                        { offset: 100, color: colours?.success, opacity: 0.15 },
                    ];
                    break;
                case 10 <= Math.max(...thisComponent.collection?.avgs) && Math.max(...thisComponent.collection?.avgs) < 15:
                    colourStops = [
                        { offset: 0, color: colours?.warning, opacity: 0.5 },
                        { offset: 50, color: colours?.lowMed, opacity: 0.3 },
                        { offset: 100, color: colours?.success, opacity: 0.15 },
                    ];
                    break;
                case 15 <= Math.max(...thisComponent.collection?.avgs) && Math.max(...thisComponent.collection?.avgs) < 20:
                    colourStops = [
                        { offset: 0, color: colours?.medHigh, opacity: 0.75 },
                        { offset: 25, color: colours?.warning, opacity: 0.5 },
                        { offset: 50, color: colours?.lowMed, opacity: 0.3 },
                        { offset: 100, color: colours?.success, opacity: 0.15 },
                    ];
                    break;
                case 20 <= Math.max(...thisComponent.collection?.avgs):
                    colourStops = [
                        { offset: 0, color: colours?.danger, opacity: 0.95 },
                        { offset: 25, color: colours?.medHigh, opacity: 0.75 },
                        { offset: 50, color: colours?.warning, opacity: 0.5 },
                        { offset: 75, color: colours?.lowMed, opacity: 0.3 },
                        { offset: 100, color: colours?.success, opacity: 0.15 },
                    ];
                    break;
                default:
                    break;
            }
            var areaChartEl = document.querySelector("#history-chart"),
                areaChartConfig = {
                    chart: {
                        height: 400,
                        type: "area",
                        parentHeightOffset: 0,
                        toolbar: {
                            show: false,
                        },
                    },
                    dataLabels: {
                        enabled: true,
                    },
                    stroke: {
                        show: true,
                        curve: "smooth",
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
                    colors: ["#bbb"],
                    series: [
                        {
                            name: thisComponent.collection?.messages?.riskAveragePerMonth,
                            data: thisComponent.collection?.avgs,
                        },
                    ],
                    xaxis: {
                        categories: thisComponent.collection?.cats,
                    },
                    fill: {
                        opacity: 1,
                        gradient: {
                            shadeIntensity: 1,
                            opacityFrom: 0.7,
                            opacityTo: 0.9,
                            colorStops: colourStops,
                        },
                    },
                    tooltip: {
                        shared: false,
                    },
                    yaxis: {
                        opposite: false,
                    },
                };
            if (typeof areaChartEl !== undefined && areaChartEl !== null) {
                var areaChart = new ApexCharts(areaChartEl, areaChartConfig);
                areaChart.render();
            }
            */
        },
        buildTable() {
            var thisComponent = this;
            let header = `
             <thead>
                <tr>
                    <th>${thisComponent.collection?.messages?.id}</th>
                    <th>${thisComponent.collection?.messages?.title}</th>
                    <th>${thisComponent.collection?.messages?.desc}</th>
                    <th class="text-center">${thisComponent.collection?.messages?.risk}</th>
                    <th class="text-center">${thisComponent.collection?.messages?.actions}</th>
                </tr>
            </thead>
            `;
            document.getElementById("dataTable").innerHTML = header;
            var dataSource = thisComponent.collection.risks;
            thisComponent.dataTable = $(".invoice-list-table").DataTable({
                data: dataSource,
                createdRow: function (row, data, dataIndex) {
                    //$(row).addClass("row-auth-bg");
                },
                lengthMenu: [],
                paging: false,
                autoWidth: true,
                searching: true,
                columns: [{ data: "id" }, { data: "title" }, { data: "desc" }, { data: "probability" }],
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
                        // title
                        targets: 1,
                        responsivePriority: 1,
                        width: "10%",
                        render: function (data, type, full, meta) {
                            let r = `<p>${full.title}</p>`;
                            return r;
                        },
                    },
                    {
                        // desc
                        targets: 2,
                        responsivePriority: 2,
                        width: "10%",
                        render: function (data, type, full, meta) {
                            let r = `<p>${full.desc}</p>`;
                            return r;
                        },
                    },
                    {
                        // risk
                        targets: 3,
                        responsivePriority: 3,
                        width: "10%",
                        type: "num",
                        render: function (data, type, full, meta) {
                            let m = parseInt(full.probability) * parseInt(full.consequence);
                            //console.log(m);
                            if (type === "sort") {
                                return m;
                            } else {
                                let p = parseInt(full.probability);
                                let c = parseInt(full.consequence);
                                let cl = "info";
                                let t = "Undefined";
                                let rk = null;
                                if ((0 < p < 5 && c == 1) || (p == 1 && c == 2)) {
                                    cl = "success";
                                    t = thisComponent.collection?.messages?.low;
                                }
                                if ((p == 5 && c == 1) || (1 < p < 5 && c == 2) || (0 < p < 3 && c == 3)) {
                                    cl = "low-med";
                                    t = thisComponent.collection?.messages?.lowMed;
                                }
                                if ((p == 5 && c == 2) || (2 < p < 5 && c == 3) || (0 < p < 3 && c == 4) || (p == 1 && c == 5)) {
                                    cl = "warning";
                                    t = thisComponent.collection?.messages?.medium;
                                }
                                if ((p == 5 && c == 3) || (2 < p < 5 && c == 4) || (1 < p < 4 && c == 5)) {
                                    cl = "med-high";
                                    t = thisComponent.collection?.messages?.mediumHigh;
                                }
                                if ((p == 5) & (c == 4) || (3 < p && c == 5)) {
                                    cl = "danger";
                                    t = thisComponent.collection?.messages?.high;
                                }
                                let r = `
                                <div class="d-flex justify-content-center align-items-center px-2">
                                    <span class="badge rounded-pill badge-light-${cl}">${t}</span>
                                </div>
                                `;
                                return r;
                            }
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
                                        <button type="button" class="btn btn-gradient-primary mb-1" onClick="window.location.href='/${thisComponent.locale}/risks/${full.id}/edit';">
                                            ${feather.icons["edit"].toSvg({ class: "me-25" })}
                                            <span>${thisComponent.collection?.messages?.edit}</span>
                                        </button>
                                        <button type="button" class="btn btn-outline-primary waves-effect mb-1" onClick="window.thisComponent.riskShow(${full.id})">
                                            ${feather.icons["eye"].toSvg({ class: "me-25" })}
                                            <span>${thisComponent.collection?.messages?.view}</span>
                                        </button>
                                        <button type="button" class="btn btn-gradient-info mb-1" onClick="window.thisComponent.riskCommentsShow(${full.id})">
                                            ${feather.icons["message-square"].toSvg({ class: "me-25" })}
                                            <span>${thisComponent.collection?.messages?.comment} (${full.risk_comments?.length})</span>
                                        </button>
                                    </div>
                                </div>
                                `;
                            return r;
                        },
                    },
                ],
                order: [[3, "desc"]],
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
                        <h4 class="card-title">${thisComponent.collection?.messages?.risks}</h4>
                        <h6 class="card-subtitle text-muted">${thisComponent.collection?.messages?.risks} ${thisComponent.collection?.messages?.index}</h6>
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
                .get("/" + thisComponent.locale + "/axios/organisations/risks", {})
                .then(function (response) {
                    console.log(response.data);
                    thisComponent.collection = response.data;
                    thisComponent.$nextTick(() => {
                        thisComponent.buildTable();
                        thisComponent.buildCharts();
                        thisComponent.$nextTick(() => {
                            $("#rangeSelect").on("select2:select", function (e) {
                                thisComponent.scatterChartUpdate(e);
                            });
                        });
                    });
                })
                .catch(function (error) {
                    console.log(error);
                    console.log(error.response);
                });
        },
        riskCommentStore() {
            var thisComponent = this;
            axios
                .post(`/${thisComponent.locale}/axios/risk_comments/store`, {
                    comment: $("#comment").val(),
                    risk_id: thisComponent.riskActive.id,
                })
                .then(function (response) {
                    $("#comment").val(null);
                    // reload riskActive
                    axios
                        .get(`/${thisComponent.locale}/axios/risks/${thisComponent.riskActive.id}`, {})
                        .then(function (response) {
                            //console.log(response.data);
                            thisComponent.riskActive = response.data.risk;
                        })
                        .catch(function (error) {
                            console.log(error.response);
                        });
                    //console.log(response.data);
                })
                .catch(function (error) {
                    thisComponent.riskCommentStoreError = error.response;
                    console.log(error.response);
                });
        },
        riskCommentsShow(id) {
            let y = this.collection.risks.filter((x) => x.id == id);
            this.riskActive = y[0];
            //console.log(this.riskActive);
            $("#riskComments").modal("show");
            this.$nextTick(() => {
                if (feather) {
                    feather.replace({
                        width: 14,
                        height: 14,
                    });
                }
            });
        },
        riskHide() {
            this.riskActive = null;
            $("#riskViewModal").modal("hide");
        },
        riskShow(id) {
            let y = this.collection.risks.filter((x) => x.id == id);
            this.riskActive = y[0];
            $("#riskViewModal").modal("show");
        },
        scatterChartUpdate(e) {
            var thisComponent = this;
            let ranje = e.params.data.text;
            let dataSets = [];
            if(ranje != thisComponent.collection?.messages?.rangeAllTime) {
               dataSets = thisComponent.collection?.dataSets?.filter(element => element.date == ranje);
               thisComponent.scatterChart.data.datasets = dataSets;
               thisComponent.scatterChart.update();
            } else {
                // populate all
                thisComponent.scatterChart.data.datasets = thisComponent.collection?.dataSets;
                thisComponent.scatterChart.update();
            }
        },
    },
    mounted() {
        window.thisComponent = this;
        this.draw();
    },
};
</script>
