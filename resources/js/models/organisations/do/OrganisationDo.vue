<template>
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-6">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                            <tr>
                                <th colspan="5">{{ collection?.messages?.statements }}</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-for="(statement, index) in collection?.statements" :key="statement.id"
                                :class="statement.id === statementActive.id ? 'active' : ''">
                                <td>{{ statement.subcode }}</td>
                                <td>{{ statement[`content_${locale}`] }}</td>
                                <td>{{ statement.deed === null ? 0 : statement.deed.value }}</td>
                                <td>
                                    <span class="badge"
                                          :class="badgeColorClass(statement?.review?.review_status.name_en)">{{
                                            statement?.review?.review_status.name_en ?? collection?.messages?.pending
                                        }}</span>
                                </td>
                                <td>
                                    <div class="d-flex">
                                        <button type="button"
                                                class="btn btn-icon btn-outline-primary waves-effect me-50"
                                                @click="showUpdateModal(index)">
                                            <i data-feather="edit"></i>
                                        </button>
                                        <button type="button"
                                                class="btn btn-icon btn-outline-primary waves-effect"
                                                @click="updateActiveStatement(index)">
                                            <i data-feather="chevron-right"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="col-6">
                    <div class="divider divider-start mt-0">
                        <div class="divider-text text-uppercase fs-6 fw-bold">
                            {{ collection?.messages?.details }}
                        </div>
                    </div>
                    <div class="mb-1">
                        <h6 class="text-sm font-weight-semibold me-1">{{ collection?.messages?.statement }}</h6>
                        <span>{{ statementActive?.subcode }}</span>
                    </div>
                    <div class="mb-1">
                        <h6 class="text-sm font-weight-semibold me-1">{{ collection?.messages?.desc }}</h6>
                        <span>{{ statementActive?.[`desc_${locale}`] }}</span>
                    </div>
                    <div class="mb-1">
                        <h6 class="text-sm font-weight-semibold me-1">
                            {{ collection?.messages?.implementation }}</h6>
                        <span>{{ statementActive?.[`implementation_${locale}`] }}</span>
                    </div>
                    <div class="mb-1">
                        <h6 class="text-sm font-weight-semibold me-1">
                            {{
                                `${collection?.messages?.organisation} ${collection?.messages?.implementation}`
                            }}</h6>
                        <span>{{ statementActive?.implementation }}</span>
                    </div>
                    <div class="mb-1">
                        <h6 class="text-sm font-weight-semibold me-1">{{ collection?.messages?.comment }}</h6>
                        <span>{{ statementActive?.deed?.comment }}</span>
                    </div>
                    <hr>
                    <div id="statements-chart"></div>
                </div>
            </div>
        </div>
    </div>
    <div id="update-modal" class="modal fade text-start modal-primary">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{
                            `${collection?.messages?.statement} ${collection?.messages?.edit} | ${statementActive?.subcode}`
                        }}</h5>
                </div>
                <div class="modal-body">
                    <form id="update-form">
                        <div class="mb-50">
                            <label class="form-label">{{ collection?.messages?.value }}</label>
                            <div id="slider" class="mb-4"></div>
                            <p><small id="slider-hint" class="text-muted fw-bolder"></small></p>
                            <input type="hidden" id="value" name="value">
                        </div>
                        <div class="mb-50">
                            <label for="comment" class="form-label">{{ collection?.messages?.comment }}</label>
                            <textarea id="comment" class="form-control" name="comment" rows="3"
                                      :value="statementActive?.deed?.comment ?? collection?.messages?.initial_value"></textarea>
                        </div>
                        <div class="mt-2">
                            <button type="button" class="btn btn-success waves-effect waves-float waves-light"
                                    :disabled="isSubmitting" @click="statementActionUpdate(statementActive?.id)">
                                <span v-show="!isSubmitting"><i data-feather="check" class="me-25"></i>{{
                                        collection?.messages?.update
                                    }}</span>
                                <span v-show="isSubmitting" class="spinner-border spinner-border-sm" role="status"
                                      aria-hidden="true"></span>
                                <span v-show="isSubmitting"
                                      class="ms-25 align-middle">{{ collection?.messages?.submitting }}...</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    props: ["locale", 'actionId'],
    data() {
        return {
            collection: null,
            statementActive: null,
            value: null,
            comment: null,
            modal: null,
            isSubmitting: false,
            statementHistoryChart: null,
        };
    },
    methods: {
        loadStatements() {
            let self = this;
            axios
                .get(`/${self.locale}/axios/organisations/do/${self.actionId}`, {})
                .then(function (response) {
                    self.collection = response.data;
                    if (self.statementActive === null) {
                        self.updateActiveStatement(0);
                    } else {
                        let index = self.collection.statements.findIndex(statement => statement.id === self.statementActive.id);
                        self.updateActiveStatement(index);
                    }
                    self.$nextTick(() => {
                        self.initTooltips();
                        feather.replace();
                    });
                })
                .catch(function (error) {

                });
        },
        statementActionUpdate(id) {
            let self = this;
            let formData = new FormData(document.getElementById('update-form'));
            formData.append('statement_id', id);
            self.isSubmitting = true;
            axios
                .post(`/${self.locale}/axios/organisations/statements/deeds/update`, formData)
                .then(function (response) {
                    self.isSubmitting = false;
                    self.modal.hide();
                    self.loadStatements();
                    toastr["success"](`${self.collection?.messages?.itemUpdatedSuccessfully}.`, `${self.collection?.messages?.success}!`, {
                        showMethod: "slideDown",
                        hideMethod: "slideUp",
                        timeOut: 3000,
                        progressBar: true,
                        rtl: false,
                    });
                })
                .catch(function (error) {
                    self.isSubmitting = false;
                    toastr["error"](error.response?.data?.message, `${self.collection?.messages?.error}!`, {
                        showMethod: "slideDown",
                        hideMethod: "slideUp",
                        timeOut: 5000,
                        progressBar: true,
                        rtl: false,
                    });
                });
        },
        updateActiveStatement(index) {
            let self = this;
            self.statementActive = self.collection?.statements?.[index];
            self.drawStatementHistoryChart();
        },
        showUpdateModal(index) {
            this.updateActiveStatement(index);
            let slider = document.getElementById('slider');
            let value = this.statementActive.deed === null ? 0 : this.statementActive.deed.value;
            slider.noUiSlider.set(value);
            this.modal = new bootstrap.Modal(document.getElementById('update-modal'));
            this.modal.show();
        },
        initSlider() {
            let self = this;
            let slider = document.getElementById('slider');
            noUiSlider.create(slider, {
                start: [1],
                behaviour: 'tap-drag',
                step: 1,
                range: {
                    min: 1,
                    max: 5
                },
                pips: {
                    mode: 'steps',
                    stepped: true,
                    density: 5
                }
            });

            slider.noUiSlider.on('update', function (values, handle, unencoded, tap, positions, noUiSlider) {
                let value = unencoded[0];
                document.getElementById('slider-hint').innerHTML = `${self.statementActive?.[`k${value}_${self.locale}`]}`;
                document.getElementById('value').value = value;
            });
        },
        initTooltips() {
            const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
            const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));
        },
        drawStatementHistoryChart() {
            let self = this;
            let history = self.statementActive?.deed?.deed_history === undefined ? [] : self.statementActive.deed.deed_history;
            let data = [];
            history.forEach(item => {
                data.push({
                    date: moment(item.created_at).format('YYYY-MM-DD'),
                    value: item.value,
                });
            });

            let sorted = data.sort((a, b) => {
                if (moment(a.date).isBefore(b.date)) {
                    return -1;
                } else if (moment(a.date).isAfter(b.date)) {
                    return 1;
                } else {
                    return 0;
                }
            });

            let categories = [];
            sorted.forEach(item => {
                if (categories.indexOf(item.date) === -1) {
                    categories.push(item.date);
                }
            });

            let series = [{data: []}];
            categories.forEach(category => {
                sorted.forEach(item => {
                    if (item.date === category) {
                        series[0].data.push({x: category, y: item.value});
                    }
                });
            });

            let options = {
                chart: {
                    height: 400,
                    type: 'line',
                    zoom: {
                        enabled: false
                    },
                    parentHeightOffset: 0,
                    toolbar: {
                        show: false
                    }
                },
                series: series,
                markers: {
                    strokeWidth: 7,
                    strokeOpacity: 1,
                    strokeColors: [window.colors.solid.white],
                    colors: [self.getColorByStatementStatus(self.statementActive?.review?.review_status.name_en)]
                },
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    curve: 'smooth'
                },
                colors: [self.getColorByStatementStatus(self.statementActive?.review?.review_status.name_en)],
                grid: {
                    xaxis: {
                        lines: {
                            show: true
                        }
                    },
                    padding: {
                        top: -20
                    }
                },
                xaxis: {
                    categories: categories,
                },
                yaxis: {
                    min: 0,
                    max: 5,
                    forceNiceScale: true,
                    decimalsInFloat: 0
                }
            };

            if (self.statementHistoryChart !== null) {
                self.statementHistoryChart.destroy();
            }

            self.statementHistoryChart = new ApexCharts(document.getElementById('statements-chart'), options);
            self.statementHistoryChart.render();
        },
        getColorByStatementStatus(status) {
            switch (status) {
                case 'Pending':
                    return '#ff9f43';
                case 'Accepted':
                    return '#28c76f';
                case 'Rejected':
                    return '#ea5455';
                default:
                    return '#ff9f43';
            }
        },
        badgeColorClass(reviewStatus) {
            let colorClass = '';
            switch (reviewStatus) {
                case 'Pending':
                    colorClass = 'badge-light-warning';
                    break;
                case 'Accepted':
                    colorClass = 'badge-light-success';
                    break;
                case 'Rejected':
                    colorClass = 'badge-light-danger';
                    break;
                default:
                    colorClass = 'badge-light-warning';
            }

            return colorClass;
        }
    },
    mounted() {
        window.thisComponent = this;
        this.loadStatements();
        this.initSlider();
    },
};
</script>
<style scoped>
.dark-layout table tr.active {
    background-color: #161d31;
}

table tr.active {
    background-color: #f8f8f8;
}
</style>
