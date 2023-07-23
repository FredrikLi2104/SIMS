<template>
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-6">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                            <tr>
                                <th colspan="3">{{ collection?.messages?.statements }}</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-for="(statement, index) in collection?.statements" :key="statement.id"
                                :class="statement.id === statementActive.id ? 'active' : ''">
                                <td>{{ statement.subcode }}</td>
                                <td>{{ statement[`content_${locale}`] }}</td>
                                <td>
                                    <div class="d-flex">
                                        <button type="button"
                                                class="btn btn-icon btn-outline-primary waves-effect"
                                                @click="updateStatementActive(index)">
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
                    <form id="plan-form">
                        <div class="mb-1">
                            <h6 class="text-sm font-weight-semibold me-1">{{ collection?.messages?.statement }}</h6>
                            <span>{{ statementActive?.subcode }}</span>
                        </div>
                        <div class="mb-1">
                            <h6 class="text-sm font-weight-semibold me-1">{{
                                    collection?.messages?.desc
                                }}</h6>
                            <span>{{ statementActive?.[`desc_${locale}`] }}</span>
                        </div>
                        <div class="mb-1">
                            <h6 class="text-sm font-weight-semibold me-1">{{ collection?.messages?.how_to_review }}</h6>
                            <span>{{ statementActive?.[`guide_${locale}`] }}</span>
                        </div>
                        <div class="mb-1">
                            <h6 class="text-sm font-weight-semibold me-1">{{ collection?.messages?.how_we_review }}</h6>
                            <textarea class="form-control" name="guide" rows="4" @change="checkStatementChanged"
                                      v-model="guide">{{ statementActive?.guide }}</textarea>
                        </div>
                        <div class="mb-1">
                            <h6 class="text-sm font-weight-semibold me-1">
                                {{ `${collection?.messages?.review} ${collection?.messages?.type}` }} <span
                                data-bs-toggle="tooltip"
                                :data-bs-original-title="`${collection?.messages?.review_type_alert}`"
                                v-show="statementActive?.deed === null" v-html="alertCircleSvg()"></span>
                            </h6>
                            <select class="form-select" name="plan_id" @change="checkStatementChanged" v-model="planId">
                                <option value="">{{ collection?.messages?.pleaseSelect }}</option>
                                <option v-for="plan in statementActive?.plans" :key="plan.plan.id" :value="plan.plan.id"
                                        :selected="plan.selected"
                                        :disabled="statementActive?.deed === null && plan.plan.name_en === 'Check'">
                                    {{ plan.plan[`name_${locale}`] }}
                                </option>
                            </select>
                        </div>
                        <div class="mt-2">
                            <div v-show="changedStatements.length" class="alert alert-warning" role="alert">
                                <div class="alert-body">
                                    {{ collection?.messages?.statements_update_warning }}
                                </div>
                            </div>
                            <div class="d-flex justify-content-between">
                                <button type="button"
                                        class="btn btn-success waves-effect waves-float waves-light"
                                        :disabled="isSubmitting"
                                        @click="statementUpdate(statementActive?.id)" data-bs-toggle="tooltip"
                                        :data-bs-original-title="`${collection?.messages?.active_statement_update_tooltip}`"
                                        data-bs-placement="right">
                                            <span v-show="!isSubmitting"><i data-feather="check" class="me-25"></i>{{
                                                    collection?.messages?.update
                                                }}</span>
                                    <span v-show="isSubmitting" class="spinner-border spinner-border-sm"
                                          role="status" aria-hidden="true"></span>
                                    <span v-show="isSubmitting" class="ms-25 align-middle">{{
                                            collection?.messages?.submitting
                                        }}...</span>
                                </button>
                                <button type="button"
                                        class="btn btn-success waves-effect waves-float waves-light"
                                        :disabled="isSubmitting"
                                        @click="statementUpdate()" data-bs-toggle="tooltip"
                                        :data-bs-original-title="`${collection?.messages?.changed_statements_update_tooltip}`"
                                        data-bs-placement="left">
                                            <span v-show="!isSubmitting"><i data-feather="zap" class="me-25"></i>{{
                                                    collection?.messages?.updateAll
                                                }}</span>
                                    <span v-show="isSubmitting" class="spinner-border spinner-border-sm"
                                          role="status" aria-hidden="true"></span>
                                    <span v-show="isSubmitting" class="ms-25 align-middle">{{
                                            collection?.messages?.submitting
                                        }}...</span>
                                </button>
                            </div>
                        </div>
                        <hr>
                        <div class="mb-1">
                            <h6 class="text-sm font-weight-semibold me-1">{{
                                    collection?.messages?.comment
                                }}</h6>
                            <span>{{ statementActive?.deed?.comment }}</span>
                        </div>
                        <div id="statements-chart"></div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    props: ['locale', 'actionId'],
    data() {
        return {
            collection: null,
            statementActive: {},
            isSubmitting: null,
            changedStatements: [],
            guide: null,
            planId: null,
            statementHistoryChart: null,
        }
    },
    methods: {
        loadStatements() {
            let self = this;
            axios
                .get(`/${self.locale}/axios/organisations/plan/auditor/${self.actionId}`)
                .then(function (response) {
                    self.collection = response.data;
                    if (Object.keys(self.statementActive).length === 0) {
                        self.guide = self.collection.statements[0].guide;
                        let selectedPlan = self.collection.statements[0].plans.find(plan => plan.selected === true);
                        self.planId = selectedPlan.plan.id;
                        self.updateStatementActive(0);
                    } else {
                        let index = self.collection.statements.findIndex(statement => statement.id === self.statementActive.id);
                        self.guide = self.statementActive.guide;
                        let selectedPlan = self.statementActive.plans.find(plan => plan.selected === true);
                        self.planId = selectedPlan.plan.id;
                        self.updateStatementActive(index);
                    }
                    self.$nextTick(() => {
                        feather.replace();
                    });
                })
                .catch(function (error) {
                    console.log(error);
                    console.log(error.response);
                });
        },
        statementUpdate(id) {
            let self = this;
            let data = self.changedStatements;
            if (id !== undefined) {
                data = data.filter(statement => statement.statement_id === id);
            }
            self.isSubmitting = true;
            axios
                .post(`/${self.locale}/axios/organisations/plan/auditor/update`, data)
                .then(function (response) {
                    self.isSubmitting = false;
                    self.loadStatements();
                    toastr["success"](`${self.collection?.messages?.itemUpdatedSuccessfully}.`, `${self.collection?.messages?.success}!`, {
                        showMethod: "slideDown",
                        hideMethod: "slideUp",
                        timeOut: 3000,
                        progressBar: true,
                        rtl: false,
                    });
                    self.changedStatements = [];
                })
                .catch(function (error) {
                    self.isSubmitting = false;
                    toastr["error"](error.response?.data?.message, `${self.collection?.messages?.error}!`, {
                        showMethod: "slideDown",
                        hideMethod: "slideUp",
                        timeOut: 3000,
                        progressBar: true,
                        rtl: false,
                    });
                });
        },
        updateStatementActive(index) {
            let self = this;
            self.statementActive = self.collection?.statements?.[index];
            self.guide = self.statementActive.guide;
            let selectedPlan = self.statementActive.plans.find(plan => plan.selected === true);
            self.planId = selectedPlan.plan.id;
            self.drawStatementHistoryChart();
            feather.replace();
        },
        checkStatementChanged() {
            let statement = this.changedStatements.find(statement => statement.id === this.statementActive.id);
            let selectedPlan = this.statementActive.plans.find(plan => plan.selected === true);
            if (statement === undefined) {
                this.changedStatements.push({
                    statement_id: this.statementActive.id,
                    guide: this.guide,
                    plan_id: this.planId,
                });
            } else if (this.statementActive.guide !== this.guide || selectedPlan.plan.id !== this.planId) {
                let index = this.changedStatements.findIndex(statement => statement.id === this.statementActive.id);
                this.changedStatements[index].guide = this.guide;
                this.changedStatements[index].planId = this.planId;
            } else {
                this.changedStatements = this.changedStatements.filter(statement => statement.id !== this.statementActive.id);
            }
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
        alertCircleSvg() {
            return feather.icons['alert-circle'].toSvg({class: 'text-warning review-type-alert ms-50'});
        }
    },
    mounted() {
        this.loadStatements();
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
