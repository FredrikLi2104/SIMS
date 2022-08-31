<template>
    <div class="row">
        <div class="row match-height">
            <div class="col-12">
                <!-- Radar Chart-->
                <div class="card">
                    <div class="card-header d-flex flex-sm-row flex-column justify-content-md-between align-items-start justify-content-start">
                        <div class="mb-1">
                            <label class="form-label" for="basicSelect">{{ collection?.messages?.selectOrganisation }}</label>
                            <select class="form-select" id="basicSelect" @change="updateOrg">
                                <option v-for="org in collection?.data" :key="org">{{ org.name }}</option>
                            </select>
                        </div>
                        <div class="mb-1">
                            <label class="form-label" for="yearSelect">{{ collection?.messages?.selectYear }}</label>
                            <select class="form-select" id="yearSelect" @change="updateOrg" style="width: 100px">
                                <option v-for="year in Object.keys(activeOrg)" :key="year">{{ year }}</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <div class="card-header">
                                <h4 class="card-title">{{ collection?.messages?.implementation }} {{ collection?.messages?.radar }}</h4>
                            </div>
                            <div class="card-body">
                                <canvas id="radar-chart" width="640" height="640"></canvas>
                            </div>
                        </div>
                        <div class="col-6">
                            <!-- Radar Chart End -->
                            <div class="card-header">
                                <h4 class="card-title">{{ collection?.messages?.risks }} {{ collection?.messages?.scatter }}</h4>
                            </div>
                            <div class="card-body">
                                <canvas class="bubble-chart-ex chartjs" width="640" height="640"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row match-height">
            <div class="col-12">
                <div class="card invoice-list-wrapper">
                    <div class="card-datatable table-responsive">
                        <table class="invoice-list-table table" id="dataTable"></table>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade text-start modal-primary" id="componentShowModal" tabindex="-1" aria-labelledby="componentShowLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-extra-wide">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="componentShowLabel">{{ collection?.messages?.component }} {{ collection?.messages?.show }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" @click="componentHide"></button>
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
                                            <td>{{ componentActive?.code }}</td>
                                        </tr>

                                        <tr>
                                            <td>{{ collection?.messages?.name }}</td>
                                            <td>{{ componentActive?.fullname }}</td>
                                        </tr>
                                        <tr>
                                            <td>{{ collection?.messages?.statements }}</td>
                                            <td></td>
                                        </tr>
                                        <tr v-for="deed in componentActive?.deeds" :key="deed">
                                            <td>{{deed.statement[`content_${locale}`]}}</td>
                                            <td>{{deed.value}}</td>
                                        </tr>
                                        <tr>
                                            <td>{{ collection?.messages?.implementation }}</td>
                                            <td>{{ componentActive?.mean }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-primary" @click="componentHide">Ok</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import Chart from "chart.js/auto";
import ChartDataLabels from "chartjs-plugin-datalabels";
export default {
    props: ["locale"],
    data() {
        return {
            collection: null,
            componentActive: null,
            dataTable: null,
            radarChart: null,
            scatterChart: null,
            activeOrg: {},
        };
    },
    methods: {
        buildTable() {
            // console.log("called");
            var thisComponent = this;
            if (thisComponent.dataTable) {
                thisComponent.dataTable.destroy();
                thisComponent.dataTable = null;
                document.getElementById("dataTable").innerHTML = "";
            }
            //console.log(thisComponent.dataTable);
            let header = `
            <thead>
                <tr>
                    <th class="">${thisComponent.collection?.messages?.code}</th>
                    <th>${thisComponent.collection?.messages?.component}</th>
                    <th class="text-center">${thisComponent.collection?.messages?.commitment}</th>
                    <th class="text-center">${thisComponent.collection?.messages?.implementation}</th>
                    <th class="text-center">${thisComponent.collection?.messages?.actions}</th>
                </tr>
            </thead>
            `;
            const activeYear = document.getElementById("yearSelect").value;
            var dataSource = thisComponent.activeOrg[activeYear].table;
            document.getElementById("dataTable").innerHTML = header;
            thisComponent.dataTable = $(".invoice-list-table").DataTable({
                data: dataSource,
                createdRow: function (row, data, dataIndex) {
                    //$(row).addClass("row-auth-bg");
                },
                lengthMenu: [10, 20, 50, 100],
                paging: true,
                autoWidth: true,
                searching: true,
                columns: [{ data: "code" }, { data: "fullname" }, { data: "commitment" }, { data: "mean" }],
                columnDefs: [
                    {
                        // columnA
                        targets: 0,
                        responsivePriority: 0,
                        width: "10%",
                        render: function (data, type, full, meta) {
                            let r = `<p>${full.code}</p>`;
                            return r;
                        },
                    },
                    {
                        // columnB
                        targets: 1,
                        responsivePriority: 1,
                        width: "40%",
                        render: function (data, type, full, meta) {
                            let r = `<p>${full.fullname}</p>`;
                            return r;
                        },
                    },
                    {
                        // columnB
                        targets: 2,
                        responsivePriority: 2,
                        width: "10%",
                        render: function (data, type, full, meta) {
                            let r = `<p class="text-center">${full.commitment}</p>`;
                            return r;
                        },
                    },
                    {
                        // columnB
                        targets: 3,
                        responsivePriority: 3,
                        width: "10%",
                        render: function (data, type, full, meta) {
                            let r = `<p class="text-center">${full.mean}</p>`;
                            return r;
                        },
                    },
                    {
                        // actions
                        targets: 4,
                        responsivePriority: 4,
                        width: "15%",
                        orderable: false,
                        render: function (data, type, full, meta) {
                            let r = `
                                <div class="d-flex justify-content-center align-items-center px-2">
                                    <div class="d-flex flex-column">
                                        <button type="button" class="btn btn-outline-primary waves-effect mb-1" onClick="window.component.componentShow(${full.id})">
                                            ${feather.icons["eye"].toSvg({ class: "me-25" })}
                                            <span>${thisComponent.collection?.messages?.view}</span>
                                        </button>
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
                                <h4 class="card-title">${thisComponent.collection?.messages?.components}</h4>
                                <h6 class="card-subtitle text-muted">${thisComponent.collection?.messages?.components} ${thisComponent.collection?.messages?.table}</h6>
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
        drawRadar() {
            var thisComponent = this;
            let selectedYear = document.getElementById("yearSelect").value;
            const orgData = thisComponent.activeOrg[selectedYear];
            if (thisComponent.radarChart) {
                thisComponent.radarChart.destroy();
            }
            if (thisComponent.riskChart) {
                thisComponent.riskChart.destroy();
            }
            //console.log(orgData);
            const chartData = {
                labels: orgData.components,
                datasets: [
                    {
                        data: orgData.mean,
                        label: thisComponent.collection?.messages?.implementation,
                        fill: true,
                        backgroundColor: "rgba(230,62,98,0.5)",
                        borderColor: "rgba(230,62,98,1.00)",
                        pointBackgroundColor: "rgba(230,62,98,1.00)",
                        pointBorderColor: "#fff",
                        pointHoverBackgroundColor: "rgba(242,74,110,1.00)",
                        pointHoverBorderColor: "rgba(230,62,98,1.00)",
                        pointRadius: 8,
                        pointHoverRadius: 12,
                    },
                    {
                        data: orgData.commitment,
                        label: thisComponent.collection?.messages?.commitment,
                        fill: true,
                        backgroundColor: "rgba(102, 103, 171, 0.2)",
                        borderColor: "rgb(102, 103, 171)",
                        pointBackgroundColor: "rgb(102, 103, 171)",
                        pointBorderColor: "#fff",
                        pointBackgroundColor: "rgb(114, 115, 183)",
                        pointHoverBorderColor: "rgb(102, 103, 171)",
                        pointRadius: 8,
                        pointHoverRadius: 12,
                    },
                ],
            };
            const chartConfig = {
                type: "radar",
                plugins: [
                    // to add spacing between legends and chart
                    {
                        beforeInit(chart) {
                            // Get reference to the original fit function
                            const originalFit = chart.legend.fit;
                            // Override the fit function
                            chart.legend.fit = function fit() {
                                // Call original function and bind scope in order to use `this` correctly inside it
                                originalFit.bind(chart.legend)();
                                // Change the height as suggested in another answers
                                this.height += 15;
                            };
                        },
                    },
                ],
                data: chartData,
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        r: {
                            grid: {
                                color: "silver",
                            },
                            pointLabels: {
                                color: "slategray",
                            },
                            ticks: {
                                color: "black",
                            },
                            max: 5,
                        },
                    },
                    plugins: {
                        legend: {
                            display: true,
                            labels: {
                                color: "slategray",
                                padding: 24,
                            },
                        },
                        tooltip: {
                            callbacks: {
                                title: function (context) {
                                    let t = orgData.codenames[context[0].dataIndex];
                                    return t;
                                },
                            },
                        },
                    },
                },
            };
            var chartElement = document.getElementById("radar-chart").getContext("2d");
            thisComponent.radarChart = new Chart(chartElement, chartConfig);
            // Risk Chart
            const riskChart = $(".bubble-chart-ex");
            const riskData = orgData.risks;
            const riskChartConfig = {
                data: riskData,
                options: {
                    plugins: {
                        datalabels: {
                            anchor: function (context) {
                                return "center";
                            },
                            align: function (context) {
                                return "center";
                            },
                            color: function (context) {
                                return "white";
                            },
                            font: function (context) {
                                let v = context.dataset.data[context.dataIndex];
                                return {
                                    size: context.dataset.fs,
                                    weight: "bold",
                                };
                            },
                            formatter: function (value, context) {
                                return context.dataset.count;
                            },
                            offset: 0,
                            padding: 0,
                        },
                        legend: {
                            display: true,
                            labels: {
                                generateLabels(chart) {
                                    let labels = [];
                                    for (const leg of riskData.legend) {
                                        //console.log(leg);
                                        labels.push({ text: leg.text, fillStyle: leg.colour });
                                    }
                                    return labels;
                                },
                                padding: 16,
                            },
                        },
                        tooltip: {
                            enabled: true,
                            callbacks: {
                                label: function (context) {
                                    console.log(context);
                                    let v = riskData.datasets[context.datasetIndex];
                                    return `${v.label}:${thisComponent.collection?.messages?.consequence}:${v.data[0].x},${thisComponent.collection?.messages?.probability}:${v.data[0].y},${thisComponent.collection?.messages?.risk}:${parseInt(v.data[0].x) * parseInt(v.data[0].y)}`;
                                },
                            },
                        },
                    },
                    scales: {
                        x: {
                            display: true,
                            grid: {
                                color: "rgba(200, 200, 200, 0.2)",
                            },
                            min: 0,
                            max: 6,
                            title: {
                                display: true,
                                text: thisComponent.collection?.messages?.consequence,
                                font: {
                                    size: 14,
                                    weight: "bold",
                                },
                            },
                        },
                        y: {
                            display: true,
                            grid: {
                                color: "rgba(200, 200, 200, 0.2)",
                            },
                            min: 0,
                            max: 6,
                            title: {
                                display: true,
                                text: thisComponent.collection?.messages?.probability,
                                font: {
                                    size: 14,
                                    weight: "bold",
                                },
                            },
                        },
                    },
                },
                plugins: [ChartDataLabels],
                type: "bubble",
            };
            thisComponent.riskChart = new Chart(riskChart, riskChartConfig);
        },
        componentHide() {
            this.componentActive = null;
            $("#componentShowModal").modal("hide");
        },
        componentShow(id) {
            const activeYear = document.getElementById("yearSelect").value;
            const dataSource = this.activeOrg[activeYear].table;
            let y = dataSource.filter((x) => x.id == id);
            this.componentActive = y[0];
            $("#componentShowModal").modal("show");
        },
        updateOrg() {
            const org = document.getElementById("basicSelect").value;
            let selectedOrg = this.collection?.data?.filter((e) => {
                return e.name == org;
            });
            this.activeOrg = selectedOrg[0].data;
            //console.log(this.activeOrg);
            this.$nextTick(() => {
                this.drawRadar();
                this.buildTable();
            });
        },
    },
    mounted() {
        var thisComponent = this;
        window.component = thisComponent;
        axios
            .get("/" + thisComponent.locale + "/axios/organisations/act", {})
            .then(function (response) {
                console.log(response.data);
                thisComponent.collection = response.data;
                thisComponent.activeOrg = response.data.data[0].data;
                //console.log(thisComponent.activeOrg);
                thisComponent.$nextTick(() => {
                    thisComponent.drawRadar();
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
