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
                                                @click="updatestatementActive(index)">
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
                            <textarea class="form-control" name="guide" rows="4" v-model="statementActive.guide">{{ statementActive?.guide }}</textarea>
                        </div>
                        <div class="mb-1">
                            <h6 class="text-sm font-weight-semibold me-1">
                                {{ `${collection?.messages?.review} ${collection?.messages?.type}` }}</h6>
                            <select class="form-select" name="plan_id">
                                <option value="">{{ collection?.messages?.pleaseSelect }}</option>
                                <option v-for="plan in statementActive?.plans" :key="plan.plan.id" :value="plan.plan.id"
                                        :selected="plan.selected">{{ plan.plan[`name_${locale}`] }}
                                </option>
                            </select>
                        </div>
                        <div class="mt-2">
                            <button type="button"
                                    class="btn btn-success waves-effect waves-float waves-light"
                                    :disabled="isSubmitting"
                                    @click="statementUpdate(statementActive?.id)">
                                            <span v-show="!isSubmitting"><i data-feather="check" class="me-25"></i>{{
                                                    collection?.messages?.update
                                                }}</span>
                                <span v-show="isSubmitting" class="spinner-border spinner-border-sm"
                                      role="status" aria-hidden="true"></span>
                                <span v-show="isSubmitting" class="ms-25 align-middle">{{
                                        collection?.messages?.submitting
                                    }}...</span>
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
    props: ['locale', 'actionId'],
    data() {
        return {
            collection: null,
            statementActive: {},
            isSubmitting: null,
        };
    },
    methods: {
        loadStatements() {
            let self = this;
            axios
                .get(`/${self.locale}/axios/organisations/plan/auditor/${self.actionId}`)
                .then(function (response) {
                    self.collection = response.data;
                    if (Object.keys(self.statementActive).length === 0) {
                        self.updatestatementActive(0);
                    } else {
                        let index = self.collection.statements.findIndex(statement => statement.id === self.statementActive.id);
                        self.updatestatementActive(index);
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
            let formData = new FormData(document.getElementById('plan-form'));
            formData.append('statement_id', id);
            self.isSubmitting = true;
            axios
                .post(`/${self.locale}/axios/organisations/plan/auditor/update`, formData)
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
        updatestatementActive(index) {
            let self = this;
            self.statementActive = self.collection?.statements?.[index];
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
