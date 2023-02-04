<template>
    <div class="row">
        <div class="col-12">
            <ul class="nav nav-pills mb-2" role="tablist">
                <li class="nav-item">
                    <button type="button" class="nav-link active" role="tab" data-bs-toggle="tab"
                            data-bs-target="#fines-by-component" aria-controls="fines-by-component"
                            aria-selected="true">{{ messages.fines_by_component }}
                    </button>
                </li>
                <li class="nav-item">
                    <button type="button" class="nav-link" role="tab" data-bs-toggle="tab"
                            data-bs-target="#fines-by-statement" aria-controls="fines-by-statement"
                            aria-selected="true">
                        {{ messages.fines_by_statement }}
                    </button>
                </li>
                <li class="nav-item">
                    <button type="button" class="nav-link" role="tab" data-bs-toggle="tab"
                            data-bs-target="#chronological" aria-controls="chronological" aria-selected="true">
                        {{ messages.fines_imposed_over_time }}
                    </button>
                </li>
                <li class="nav-item">
                    <button type="button" class="nav-link" role="tab" data-bs-toggle="tab"
                            data-bs-target="#fines-by-country" aria-controls="fines-by-country" aria-selected="true">
                        {{ messages.fines_by_country }}
                    </button>
                </li>
                <li class="nav-item">
                    <button type="button" class="nav-link" role="tab" data-bs-toggle="tab"
                            data-bs-target="#fines-by-sector" aria-controls="fines-by-sector" aria-selected="true">
                        {{ messages.fines_by_sector }}
                    </button>
                </li>
                <li class="nav-item">
                    <button type="button" class="nav-link" role="tab" data-bs-toggle="tab"
                            data-bs-target="#individual" aria-controls="individual" aria-selected="true">
                        {{ messages.individual_fines }}
                    </button>
                </li>
            </ul>
            <div class="tab-content">
                <div class="tab-pane fade show active" id="fines-by-component" role="tabpanel">
                    <div class="card">
                        <div class="card-body mt-1">
                            <h4 class="card-title">{{ `1. ${messages.by_total_sum_of_fines}` }}</h4>
                            <div class="row">
                                <div class="col-md-6">
                                    <div id="by-component-sum-chart"></div>
                                </div>
                                <div class="col-md-6">
                                    <div class="table-responsive">
                                        <table class="table table-sm">
                                            <thead>
                                            <tr>
                                                <th>{{ messages.component }}</th>
                                                <th>{{ `${messages.sum_of_fines} (€)` }}</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <tr v-for="(component, index) in byComponent?.sum?.categories">
                                                <td>{{ component }}</td>
                                                <td>{{ byComponent?.sum?.data[index].toLocaleString() }}</td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <h4 class="card-title">{{ `2. ${messages.by_total_number_of_fines}` }}</h4>
                            <div class="row">
                                <div class="col-md-6">
                                    <div id="by-component-count-chart"></div>
                                </div>
                                <div class="col-md-6">
                                    <div class="table-responsive">
                                        <div class="table-responsive">
                                            <table class="table table-sm">
                                                <thead>
                                                <tr>
                                                    <th>{{ messages.component }}</th>
                                                    <th>{{ messages.number_of_fines }}</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <tr v-for="(component, index) in byComponent?.count?.categories">
                                                    <td>{{ component }}</td>
                                                    <td>{{ byComponent?.count?.data[index] }}</td>
                                                </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade" id="fines-by-statement" role="tabpanel">
                    <div class="card">
                        <div class="card-body mt-1">
                            <div class="row">
                                <div class="col-md-6">
                                    <h4 class="card-title">{{ `1. ${messages.by_total_sum_of_fines}` }}</h4>
                                    <div class="table-responsive">
                                        <table class="table table-sm">
                                            <thead>
                                            <tr>
                                                <th>{{ messages.statement }}</th>
                                                <th>{{ `${messages.sum_of_fines} (€)` }}</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <tr v-for="(statement, index) in byStatement?.sum?.categories">
                                                <td>{{ statement }}</td>
                                                <td>{{ byStatement?.sum?.data[index].toLocaleString() }}</td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <h4 class="card-title">{{ `2. ${messages.by_total_number_of_fines}` }}</h4>
                                    <div class="table-responsive">
                                        <table class="table table-sm">
                                            <thead>
                                            <tr>
                                                <th>{{ messages.statement }}</th>
                                                <th>{{ messages.number_of_fines }}</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <tr v-for="(statement, index) in byStatement?.count?.categories">
                                                <td>{{ statement }}</td>
                                                <td>{{ byStatement?.count?.data[index] }}</td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade" id="chronological" role="tabpanel">
                    <div class="card">
                        <div class="card-body mt-1">
                            <div class="row">
                                <div class="col-md-8">
                                    <h4 class="card-title">{{ `1. ${messages.by_total_sum_of_fines}` }}</h4>
                                    <div id="chronological-sum-chart"></div>
                                    <h4 class="card-title">{{ `2. ${messages.by_total_number_of_fines}` }}</h4>
                                    <div id="chronological-count-chart"></div>
                                </div>
                                <div class="col-md-4">
                                    <div class="table-responsive">
                                        <table id="chronological-tbl" class="table table-sm">
                                            <thead>
                                            <tr>
                                                <th>{{ messages.statement }}</th>
                                                <th>{{ `${messages.sum_of_fines} (€)` }}</th>
                                                <th>{{ messages.number_of_fines }}</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade" id="fines-by-country" role="tabpanel">
                    <div class="card">
                        <div class="card-body mt-1">
                            <h4 class="card-title">{{
                                    `1. ${messages.by_total_sum_of_fines} (${messages.top} 10)`
                                }}</h4>
                            <div class="row">
                                <div class="col-md-6">
                                    <div id="by-country-sum-chart"></div>
                                </div>
                                <div class="col-md-6">
                                    <table class="table table-sm">
                                        <thead>
                                        <tr>
                                            <th>{{ messages.country }}</th>
                                            <th>{{ `${messages.sum_of_fines} (€)` }}</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr v-for="(country, index) in byCountry?.sum?.categories">
                                            <td>{{ country }}</td>
                                            <td>{{ byCountry?.sum?.data[index].toLocaleString() }}</td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <h4 class="card-title">{{
                                    `2. ${messages.by_total_number_of_fines} (${messages.top} 10)`
                                }}</h4>
                            <div class="row">
                                <div class="col-md-6">
                                    <div id="by-country-count-chart"></div>
                                </div>
                                <div class="col-md-6">
                                    <table class="table table-sm">
                                        <thead>
                                        <tr>
                                            <th>{{ messages.country }}</th>
                                            <th>{{ messages.number_of_fines }}</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr v-for="(country, index) in byCountry?.count?.categories">
                                            <td>{{ country }}</td>
                                            <td>{{ byCountry?.count?.data[index] }}</td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade" id="fines-by-sector" role="tabpanel">
                    <div class="card">
                        <div class="card-body mt-1">
                            <h4 class="card-title">{{ `1. ${messages.by_total_sum_of_fines}` }}</h4>
                            <div class="row">
                                <div class="col-md-6">
                                    <div id="by-sector-sum-chart"></div>
                                </div>
                                <div class="col-md-6">
                                    <table class="table table-sm">
                                        <thead>
                                        <tr>
                                            <th>{{ messages.sector }}</th>
                                            {{ `${messages.sum_of_fines} (€)` }}
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr v-for="(sector, index) in bySector?.sum?.categories">
                                            <td>{{ sector }}</td>
                                            <td>{{ bySector?.sum?.data[index].toLocaleString() }}</td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <h4 class="card-title">{{ `2. ${messages.by_total_number_of_fines}` }}</h4>
                            <div class="row">
                                <div class="col-md-6">
                                    <div id="by-sector-count-chart"></div>
                                </div>
                                <div class="col-md-6">
                                    <table class="table table-sm">
                                        <thead>
                                        <tr>
                                            <th>{{ messages.sector }}</th>
                                            <th>{{ messages.number_of_fines }}</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr v-for="(sector, index) in bySector?.count?.categories">
                                            <td>{{ sector }}</td>
                                            <td>{{ bySector?.count?.data[index] }}</td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade" id="individual" role="tabpanel">
                    <div class="card">
                        <div class="card-body mt-1">
                            <h4 class="card-title">{{ messages.highest_individual_fines }}</h4>
                            <div class="row">
                                <div class="col-12">
                                    <table id="individual-tbl" class="table table-sm">
                                        <thead>
                                        <tr>
                                            <th>{{ messages.title }}</th>
                                            <th>{{ messages.sector }}</th>
                                            <th>{{ messages.country }}</th>
                                            <th>{{ `${messages.fine} (€)` }}</th>
                                            <th>{{ messages.type }}</th>
                                            <th>{{ messages.party }}</th>
                                            <th>{{ messages.decidedOn }}</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-datatable table-responsive">
                    <table class="table" id="sanctions-tbl">
                        <thead>
                        <tr>
                            <th>{{ messages.id }}</th>
                            <th>{{ messages.createdAt }}</th>
                            <th>{{ messages.dpa }}</th>
                            <th>{{ messages.decidedOn }}</th>
                            <th>{{ messages.title }}</th>
                            <th class="text-center">{{ messages.actions }}</th>
                        </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade text-start modal-primary" id="sanction-show-modal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-extra-wide">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ activeSanction?.title }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                            @click="hideSanction"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="table-responsive">
                            <table class="table">
                                <thead class="table-light">
                                <tr>
                                    <th>{{ messages.key }}</th>
                                    <th>{{ messages.value }}</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td>{{ messages.id }}</td>
                                    <td>{{ activeSanction?.id }}</td>
                                </tr>
                                <tr>
                                    <td>{{ messages.createdAt }}</td>
                                    <td>{{ activeSanction?.created_at_for_humans }}</td>
                                </tr>
                                <tr>
                                    <td>{{ messages.title }}</td>
                                    <td>{{ activeSanction?.title }}</td>
                                </tr>
                                <tr>
                                    <td>{{ messages.dpa }}</td>
                                    <td>
                                        <div class="d-flex align-items-center justify-content-start">
                                            <div v-if="activeSanction?.dpa?.country != undefined" class="col-2">
                                                <img :src="`/images/flags/svg/${activeSanction.dpa.country.code}.svg`"
                                                     style="width: 30px"/>
                                            </div>
                                            <div class="col-10 align-items-center">
                                                <p class="mx-0 my-0">{{ activeSanction?.dpa?.name }}</p>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>{{ messages.startedOn }}</td>
                                    <td>{{ activeSanction?.started_at_for_humans }}</td>
                                </tr>
                                <tr>
                                    <td>{{ messages.decidedOn }}</td>
                                    <td>{{ activeSanction?.decided_at_for_humans }}</td>
                                </tr>
                                <tr>
                                    <td>{{ messages.publishedOn }}</td>
                                    <td>{{ activeSanction?.decided_at_for_humans }}</td>
                                </tr>
                                <tr>
                                    <td>{{ messages.articles }}</td>
                                    <td>
                                        <div v-for="article in activeSanction?.articlesSorted" :key="article.title">
                                            <a :href="article.url" target="_blank">{{ article.title }}</a>
                                        </div>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" @click="hideSanction">Ok</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: "Statistics",
    props: ['locale', 'messages'],
    data() {
        return {
            byComponent: {},
            byStatement: {},
            chronological: {},
            byCountry: {},
            bySector: {},
            individual: [],
            activeSanction: null,
            sanctionModal: null,
        }
    },
    methods: {
        getStatsByComponent() {
            const self = this;
            axios.get(`/${self.locale}/axios/statistics/sanctions/component`)
                .then(function (response) {
                    self.byComponent.sum = response.data.sum;
                    self.byComponent.count = response.data.count;

                    self.$nextTick(() => {
                        self.initByComponentCharts();
                    });
                })
                .catch(function (error) {

                });
        },
        getStatsByStatement() {
            const self = this;
            axios.get(`/${self.locale}/axios/statistics/sanctions/statement`)
                .then(function (response) {
                    self.byStatement.sum = response.data.sum;
                    self.byStatement.count = response.data.count;
                })
                .catch(function (error) {

                });
        },
        getChronologicalStats() {
            const self = this;
            axios.get(`/${self.locale}/axios/statistics/sanctions/chronological`)
                .then(function (response) {
                    self.chronological.sum = response.data.sum;
                    self.chronological.count = response.data.count;

                    self.$nextTick(() => {
                        self.initChronologicalCharts();
                    });
                })
                .catch(function (error) {

                });
        },
        getStatsByCountry() {
            const self = this;
            axios.get(`/${self.locale}/axios/statistics/sanctions/country`)
                .then(function (response) {
                    self.byCountry.sum = response.data.sum;
                    self.byCountry.count = response.data.count;

                    self.$nextTick(() => {
                        self.initByCountryCharts();
                    });
                })
                .catch(function (error) {

                });
        },
        getStatsBySector() {
            const self = this;
            axios.get(`/${self.locale}/axios/statistics/sanctions/sector`)
                .then(function (response) {
                    self.bySector.sum = response.data.sum;
                    self.bySector.count = response.data.count;

                    self.$nextTick(() => {
                        self.initBySectorCharts();
                    });
                })
                .catch(function (error) {

                });
        },
        getIndividualStats() {
            const self = this;
            axios.get(`/${self.locale}/axios/statistics/sanctions/individual`)
                .then(function (response) {
                    self.individual = response.data;
                    self.$nextTick(() => {
                        self.initIndividualTable();
                    });
                })
                .catch(function (error) {

                });
        },
        horizontalBarConfig() {
            return {
                chart: {
                    type: 'bar',
                    parentHeightOffset: 0,
                    toolbar: {
                        show: false
                    }
                },
                plotOptions: {
                    bar: {
                        horizontal: true,
                        barHeight: '30%',
                        endingShape: 'rounded'
                    }
                },
                grid: {
                    xaxis: {
                        lines: {
                            show: false
                        }
                    },
                    padding: {
                        top: -15,
                        bottom: -10
                    }
                },
                colors: window.colors.solid.info,
                dataLabels: {
                    enabled: false
                },
                tooltip: {
                    y: {
                        title: {
                            formatter: () => '',
                        }
                    }
                }
            }
        },
        areaChartConfig() {
            return {
                chart: {
                    height: 400,
                    type: 'area',
                    parentHeightOffset: 0,
                    toolbar: {
                        show: false
                    }
                },
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    show: true,
                    curve: 'smooth'
                },
                legend: {
                    show: true,
                    position: 'top',
                    horizontalAlign: 'start'
                },
                grid: {
                    xaxis: {
                        lines: {
                            show: true
                        }
                    }
                },
                colors: [window.colors.solid.info],
                fill: {
                    type: "gradient",
                    gradient: {
                        opacity: .5,
                    }
                },
                markers: {
                    size: 5,
                    hover: {
                        size: 7
                    }
                },
                tooltip: {
                    shared: false,
                    y: {
                        title: {
                            formatter: () => '',
                        }
                    }
                },
                yaxis: {
                    labels: {
                        formatter: function (value) {
                            return value.toLocaleString();
                        }
                    }
                }
            };
        },
        initByComponentCharts() {
            const self = this;
            const sumChartEl = document.querySelector('#by-component-sum-chart'),
                countChartEl = document.querySelector('#by-component-count-chart');

            let sumChartConfig = self.horizontalBarConfig();
            sumChartConfig.series = [{data: self.byComponent.sum.data}];
            sumChartConfig.xaxis = {
                categories: self.byComponent.sum.categories,
                labels: {
                    formatter: function (value) {
                        return value.toLocaleString();
                    }
                }
            };
            sumChartConfig.tooltip.y.formatter = function (value) {
                return '€ ' + value.toLocaleString();
            };
            new ApexCharts(sumChartEl, sumChartConfig).render();

            let countChartConfig = self.horizontalBarConfig();
            countChartConfig.series = [{data: self.byComponent.count.data}];
            countChartConfig.xaxis = {categories: self.byComponent.count.categories};
            new ApexCharts(countChartEl, countChartConfig).render();
        },
        initChronologicalCharts() {
            const self = this;
            const sumChartEl = document.querySelector('#chronological-sum-chart'),
                countChartEl = document.querySelector('#chronological-count-chart');

            let sumChartConfig = self.areaChartConfig();
            sumChartConfig.series = [{data: self.chronological.sum.data}];
            sumChartConfig.xaxis = {categories: self.chronological.sum.categories};
            sumChartConfig.tooltip.y.formatter = function (value) {
                return '€ ' + value.toLocaleString();
            };
            new ApexCharts(sumChartEl, sumChartConfig).render();

            let countChartConfig = self.areaChartConfig();
            countChartConfig.series = [{data: self.chronological.count.data}];
            countChartConfig.xaxis = {categories: self.chronological.count.categories};
            new ApexCharts(countChartEl, countChartConfig).render();

            self.initChronologicalTable();
        },
        initByCountryCharts() {
            const self = this;
            const sumChartEl = document.querySelector('#by-country-sum-chart'),
                countChartEl = document.querySelector('#by-country-count-chart');

            let sumChartConfig = self.horizontalBarConfig();
            sumChartConfig.series = [{data: self.byCountry.sum.data}];
            sumChartConfig.xaxis = {
                categories: self.byCountry.sum.categories,
                labels: {
                    formatter: function (value) {
                        return value.toLocaleString();
                    }
                }
            };
            sumChartConfig.tooltip.y.formatter = function (value) {
                return '€ ' + value.toLocaleString();
            };
            new ApexCharts(sumChartEl, sumChartConfig).render();

            let countChartConfig = self.horizontalBarConfig();
            countChartConfig.series = [{data: self.byCountry.count.data}];
            countChartConfig.xaxis = {categories: self.byCountry.count.categories};
            new ApexCharts(countChartEl, countChartConfig).render();
        },
        initBySectorCharts() {
            const self = this;
            const sumChartEl = document.querySelector('#by-sector-sum-chart'),
                countChartEl = document.querySelector('#by-sector-count-chart');

            let sumChartConfig = self.horizontalBarConfig();
            sumChartConfig.series = [{data: self.bySector.sum.data}];
            sumChartConfig.xaxis = {
                categories: self.bySector.sum.categories,
                labels: {
                    formatter: function (value) {
                        return value.toLocaleString();
                    }
                }
            };
            sumChartConfig.tooltip.y.formatter = function (value) {
                return '€ ' + value.toLocaleString();
            };
            new ApexCharts(sumChartEl, sumChartConfig).render();

            let countChartConfig = self.horizontalBarConfig();
            countChartConfig.series = [{data: self.bySector.count.data}];
            countChartConfig.xaxis = {categories: self.bySector.count.categories};
            new ApexCharts(countChartEl, countChartConfig).render();
        },
        initSanctionsTable() {
            let self = this;

            $("#sanctions-tbl").DataTable({
                processing: true,
                serverSide: true,
                paging: true,
                autoWidth: true,
                searching: true,
                ajax: `/${self.locale}/axios/statistics/sanctions`,
                columnDefs: [
                    {
                        //id
                        targets: 0,
                        responsivePriority: 0,
                        width: "10%",
                        render: function (data, type, full, meta) {
                            //console.log(full);
                            let r = `<p>${full.id}</p>`;
                            return r;
                        },
                    },
                    {
                        //created_at
                        targets: 1,
                        responsivePriority: 1,
                        width: "10%",
                        render: function (data, type, full, meta) {
                            //console.log(full);
                            let r = `<p>${full.created_at_for_humans}</p>`;
                            return r;
                        },
                    },
                    {
                        // DPA
                        targets: 2,
                        responsivePriority: 2,
                        width: "20%",
                        render: function (data, type, full, meta) {
                            // has image?
                            let r = `
                            <div class="d-flex align-items-center justify-content-start">
                                <div class="col-2">
                            `;
                            if (full.dpa.country) {
                                r += `
                                <img src='/images/flags/svg/${full.dpa?.country?.code}.svg' width="48"/>
                                `;
                            }
                            r += `
                                </div>
                                <div class="col-10 align-items-center px-1">
                                    <p class="mx-0 my-0">${full.dpa?.name}</p>
                                </div>
                            </row>`;
                            return r;
                        },
                    },
                    {
                        // decided_at
                        targets: 3,
                        responsivePriority: 3,
                        width: "10%",
                        render: function (data, type, full, meta) {
                            if (type === "sort") {
                                return Date.parse(full.decided_at_for_humans);
                            } else {
                                let r = `<p>${full.decided_at_for_humans}</p>`;
                                return r;
                            }
                        },
                    },
                    {
                        // title
                        targets: 4,
                        responsivePriority: 5,
                        width: "20%",
                        render: function (data, type, full, meta) {
                            let r = `<p>${full.title}</p>`;
                            return r;
                        },
                    },
                    {
                        // actions
                        targets: 5,
                        responsivePriority: 6,
                        width: "15%",
                        orderable: false,
                        render: function (data, type, full, meta) {
                            let r = `
                                <div class="d-flex justify-content-center align-items-center px-2">
                                    <div class="d-flex flex-column">
                                        <button type="button" class="btn btn-gradient-info waves-effect mb-1" onclick="window.open('${full.url}','_blank')">
                                            ${feather.icons["external-link"].toSvg({class: "me-25"})}
                                            <span>${self.messages.visit}</span>
                                        </button>
                                        <button type="button" class="btn btn-outline-primary waves-effect mb-1" onclick="showSanction(${full.id})">
                                            ${feather.icons["eye"].toSvg({class: "me-25"})}
                                            <span>${self.messages.view}</span>
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
                    <"col-lg-12 d-flex justify-content-start align-items-center"
                        <"#sanctions-card-header">
                    >
                    <"col-lg-6 d-flex align-items-center"l>
                    <"col-lg-6 d-flex align-items-center justify-content-lg-end flex-lg-nowrap flex-wrap pe-lg-1 p-0"f>
                >t
                <"d-flex justify-content-between mx-2 row"
                    <"col-sm-12 col-md-6"i>
                    <"col-sm-12 col-md-6"p>
                ">`,
                initComplete: function () {
                    let domHtml = `
                    <div class="card-body">
                        <h4 class="card-title">${self.messages.sanctions}</h4>
                        <h6 class="card-subtitle text-muted">${self.messages.sanctions} ${self.messages.table}</h6>
                    </div>
                    `;
                    $("#sanctions-card-header").html(domHtml);
                },
            });
        },
        initChronologicalTable() {
            let self = this;
            $('#chronological-tbl').DataTable({
                data: self.chronological.sum.categories,
                columnDefs: [
                    {
                        targets: 0,
                        render: function (data, type, row, meta) {
                            return row;
                        }
                    },
                    {
                        targets: 1,
                        render: function (data, type, row, meta) {
                            return self.chronological?.sum?.data[meta.row].toLocaleString();
                        }
                    },
                    {
                        targets: 2,
                        render: function (data, type, row, meta) {
                            return self.chronological?.count?.data[meta.row];
                        }
                    },
                ],
                lengthMenu: [[20, 50, -1], [20, 50, self.messages.all]],
                pageLength: 20,
                ordering: false,
                searching: false
            });
        },
        initIndividualTable() {
            let self = this;
            $('#individual-tbl').DataTable({
                data: self.individual,
                columnDefs: [
                    {
                        targets: 0,
                        render: function (data, type, row, meta) {
                            return row.title;
                        }
                    },
                    {
                        targets: 1,
                        render: function (data, type, row, meta) {
                            return row.sector;
                        }
                    },
                    {
                        targets: 2,
                        render: function (data, type, row, meta) {
                            return row.country;
                        }
                    },
                    {
                        targets: 3,
                        render: function (data, type, row, meta) {
                            return row.sum.toLocaleString();
                        }
                    },
                    {
                        targets: 4,
                        render: function (data, type, row, meta) {
                            return row.type;
                        }
                    },
                    {
                        targets: 5,
                        render: function (data, type, row, meta) {
                            return row.party;
                        }
                    },
                    {
                        targets: 6,
                        render: function (data, type, row, meta) {
                            return row.decided_at;
                        }
                    }
                ],
                lengthMenu: [[10, 20, 50, -1], [10, 20, 50, self.messages.all]],
                pageLength: 10,
                ordering: false,
                searching: false
            });
        },
        initSanctionModal() {
            this.sanctionModal = new bootstrap.Modal(document.getElementById('sanction-show-modal'));
        },
        showSanction(id) {
            const self = this;
            axios
                .get(`/${self.locale}/axios/sanctions/${id}`)
                .then(function (response) {
                    self.activeSanction = response.data;
                    self.sanctionModal.show();
                })
                .catch(function (error) {
                    console.log(error.response);
                });
        },
        hideSanction() {
            if (this.sanctionModal) {
                this.activeSanction = null;
                this.sanctionModal.hide();
            }
        }
    },
    mounted() {
        const self = this;
        self.getStatsByComponent();
        self.initSanctionsTable();
        self.initSanctionModal();
        self.getStatsByStatement();
        self.getChronologicalStats();
        self.getStatsByCountry();
        self.getStatsBySector();
        self.getIndividualStats();

        window.showSanction = function (id) {
            self.showSanction(id);
        }
    }
}
</script>

<style scoped>

</style>
