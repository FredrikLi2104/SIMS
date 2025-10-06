<template>
    <div class="row match-height">
        <div class="col-12 col-md-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">{{ collection?.messages?.insights }}</h4>
                    <div class="btn-group ms-50">
                        <button type="button" class="btn btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown"
                                aria-haspopup="true" aria-expanded="false">
                            {{ selectedYearForInsights }}
                        </button>
                        <div class="dropdown-menu">
                            <a v-for="year in Object.keys(activeOrg)" :key="year" class="dropdown-item" href="#"
                               @click="drawRadar(year)">{{ year }}</a>
                        </div>
                    </div>
                    <a :href="`/${locale}/insights`"
                       class="btn btn-warning waves-effect waves-float waves-light round ms-auto"
                       role="button">
                        <i data-feather="pie-chart" class="me-25"></i>
                        {{ collection?.messages?.insights }}
                    </a>
                </div>
                <div class="card-body">
                    <canvas id="radar-chart" width="640" height="640"></canvas>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">{{ collection?.messages?.risks }}</h4>
                    <div class="btn-group ms-50">
                        <button type="button" class="btn btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown"
                                aria-haspopup="true" aria-expanded="false">
                            {{ selectedYearForRisks }}
                        </button>
                        <div class="dropdown-menu">
                            <a v-for="year in Object.keys(activeOrg)" :key="year" class="dropdown-item" href="#"
                               @click="drawRiskChart(year)">{{ year }}</a>
                        </div>
                    </div>
                    <a :href="`/${locale}/risks`"
                       class="btn btn-warning waves-effect waves-float waves-light round ms-auto"
                       role="button">
                        <i data-feather="alert-triangle" class="me-25"></i>
                        {{ collection?.messages?.risks }}
                    </a>
                </div>
                <div class="card-body">
                    <canvas class="bubble-chart-ex chartjs" width="640" height="640"></canvas>
                </div>
            </div>
        </div>
    </div>
    <div class="row match-height">
        <div class="col-12 col-md-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">{{ collection?.messages?.tasks }}</h4>
                    <div class="btn-group ms-50">
                        <button type="button" class="btn btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown"
                                aria-haspopup="true" aria-expanded="false">
                            {{ selectedYearForWheel }}
                        </button>
                        <div class="dropdown-menu">
                            <a v-for="year in yearsForWheel" class="dropdown-item" href="#"
                               @click="updateYear(year)">{{ year }}</a>
                        </div>
                    </div>
                    <a :href="`/${locale}/tasks`"
                       class="btn btn-warning waves-effect waves-float waves-light round ms-auto"
                       role="button">
                        <i data-feather="layers" class="me-25"></i>
                        {{ collection?.messages?.tasks }}
                    </a>
                </div>
                <div v-if="monthsFoWheel.length" class="card-body mx-auto">
                    <tasks-wheel :months="monthsFoWheel" :selected-year="selectedYearForWheel"
                                 :tasks="tasksForWheel"></tasks-wheel>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">{{ collection?.messages?.knowledge }}</h4>
                    <a :href="`/${locale}/knowledge`"
                       class="btn btn-warning waves-effect waves-float waves-light round ms-auto"
                       role="button">
                        <i data-feather="help-circle" class="me-25"></i>
                        {{ collection?.messages?.knowledge }}
                    </a>
                </div>
                <div class="card-body"></div>
            </div>
        </div>
    </div>
    <div class="row match-height">
        <div class="col-12 col-md-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">{{ collection?.messages?.sanctions }}</h4>
                    <a :href="`/${locale}/statistics/sanctions`"
                       class="btn btn-warning waves-effect waves-float waves-light round" role="button">
                        <i data-feather="bar-chart-2" class="me-25"></i>{{ collection?.messages?.sanctions }}
                    </a>
                </div>
                <div v-if="sanctions !== null" class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                        <tr>
                            <th>{{ collection?.messages?.dpa }}</th>
                            <th>{{ collection?.messages?.decidedOn }}</th>
                            <th>{{ collection?.messages?.title }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="sanction in sanctions">
                            <td class="py-50">
                                <div class="d-flex align-items-center justify-content-start">
                                    <div class="col-2" v-if="sanction.dpa.country">
                                        <img :src="`/images/flags/svg/${sanction.dpa.country.code}.svg`" width="48"/>
                                    </div>
                                    <div class="col-10 align-items-center px-1">
                                        <p class="mx-0 my-0">{{ sanction.dpa.name }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="py-50">{{ sanction.decided_at_for_humans }}</td>
                            <td class="py-50">{{ sanction.title }}</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">{{ collection?.messages?.documents }}</h4>
                </div>
                <div class="card-body">
                    Coming soon...
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

    <div class="modal fade text-start modal-primary" id="componentShowModal" tabindex="-1"
         aria-labelledby="componentShowLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-extra-wide">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="componentShowLabel">{{ collection?.messages?.component }}
                        {{ collection?.messages?.show }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                            @click="componentHide"></button>
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
                                    <td>{{ deed.statement[`content_${locale}`] }}</td>
                                    <td>{{ deed.value }}</td>
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
</template>
<script>
import Chart from "chart.js/auto";
import ChartDataLabels from "chartjs-plugin-datalabels";
import TasksWheel from "../../tasks/index/TasksWheel.vue";

export default {
    props: ["locale"],
    components: {TasksWheel},
    data() {
        return {
            selectedYearForInsights: moment().format('Y'),
            selectedYearForRisks: moment().format('Y'),
            collection: null,
            componentActive: null,
            dataTable: null,
            radarChart: null,
            scatterChart: null,
            activeOrg: {},
            yearsForWheel: [],
            monthsFoWheel: [],
            selectedYearForWheel: null,
            tasksForWheel: [],
            sanctions: null,
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
                columns: [{data: "code"}, {data: "fullname"}, {data: "commitment"}, {data: "mean"}],
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
                                            ${feather.icons["eye"].toSvg({class: "me-25"})}
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
        drawRadar(year) {
            var thisComponent = this;
            thisComponent.selectedYearForInsights = year;
            const orgData = thisComponent.activeOrg[year];
            if (thisComponent.radarChart) {
                thisComponent.radarChart.destroy();
            }
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
        },
        drawRiskChart(year) {
            var thisComponent = this;
            thisComponent.selectedYearForRisks = year;
            const orgData = thisComponent.activeOrg[year];
            if (thisComponent.riskChart) {
                thisComponent.riskChart.destroy();
            }
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
                                        labels.push({text: leg.text, fillStyle: leg.colour});
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
        populateYearsForWheel() {
            this.yearsForWheel.push(moment().subtract(1, 'year').year());
            this.yearsForWheel.push(moment().year());
            this.yearsForWheel.push(moment().add(1, 'year').year());
            this.selectedYearForWheel = this.yearsForWheel[1];
        },
        populateMonthsForWheel() {
            for (let i = 0; i < 12; i++) {
                this.monthsFoWheel.push(moment().month(i).format('MMM'));
            }
        },
        getTasksForWheel() {
            let self = this;
            axios.get(`/${self.locale}/axios/tasks_for_wheel/${self.selectedYearForWheel}`)
                .then(function (response) {
                    self.tasksForWheel = response.data;
                })
                .catch(function (error) {

                });
        },
        updateYear(year) {
            this.selectedYearForWheel = year;
            this.getTasksForWheel();
        },
        getSanctions() {
            let self = this;
            axios.post(`/${self.locale}/axios/organisations/insights/sanctions`, {
                start: 0,
                length: 5,
                search: {value: null}
            })
                .then(function (response) {
                    self.sanctions = response.data.data;
                })
                .catch(function (error) {

                });
        }
    },
    mounted() {
        var thisComponent = this;
        window.component = thisComponent;
        axios
            .get("/" + thisComponent.locale + "/axios/organisations/insights", {})
            .then(function (response) {
                console.log(response.data);
                thisComponent.collection = response.data;
                thisComponent.activeOrg = response.data.data[0].data;
                thisComponent.$nextTick(() => {
                    thisComponent.drawRadar(thisComponent.selectedYearForInsights);
                    thisComponent.drawRiskChart(thisComponent.selectedYearForRisks);
                    thisComponent.getTasksForWheel();
                });
            })
            .catch(function (error) {
                console.log(error);
                console.log(error.response);
            });

        thisComponent.populateYearsForWheel();
        thisComponent.populateMonthsForWheel();
        thisComponent.getSanctions();
    },
};
</script>
