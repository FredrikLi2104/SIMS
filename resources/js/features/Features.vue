<template>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">{{ messages.tasks }}</h4>
                </div>
                <div class="card-body">
                    <form id="tasks-form">
                        <div class="row">
                            <div class="col-6">
                                <label for="tasks-year" class="form-label">{{ messages.selectYear }}</label>
                                <select id="tasks-year" class="select2">
                                    <option value="">{{ messages.all }}</option>
                                    <option v-for="year in tasks.years" :key="year">{{
                                            year
                                        }}
                                    </option>
                                </select>
                            </div>
                            <div class="col-6">
                                <label for="tasks-organisations" class="form-label">{{ messages.organisations }}</label>
                                <select name="organisations[]" id="tasks-organisations" class="select2"
                                        :class="tasks?.errors?.organisations ? 'is-invalid' : ''"
                                        :data-placeholder="messages.pleaseSelect" multiple>
                                    <option v-for="organisation in organisations" :value="organisation.id">
                                        {{ organisation.name }}
                                    </option>
                                </select>
                                <div v-if="tasks?.errors?.organisations" class="invalid-feedback">{{
                                        tasks.errors.organisations[0]
                                    }}
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="d-flex mb-50">
                            <button type="button" class="btn btn-success waves-effect waves-float waves-light me-50"
                                    @click="overwriteTasks"><i
                                data-feather="copy" class="me-25"></i>{{ messages.overwrite }}
                            </button>
                            <button type="button" class="btn btn-primary waves-effect waves-float waves-light me-50"
                                    @click="checkAllTasks"><i
                                data-feather="check-square" class="me-25"></i>{{ messages.select_all }}
                            </button>
                            <button type="button" class="btn btn-secondary waves-effect waves-float waves-light"
                                    @click="uncheckAllTasks">
                                {{ messages.deselect_all }}
                            </button>
                        </div>
                        <input type="hidden" :class="tasks?.errors?.tasks ? 'is-invalid' : ''">
                        <div v-if="tasks?.errors?.tasks" class="invalid-feedback">{{ tasks.errors.tasks[0] }}</div>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                <tr>
                                    <th>
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input" @click="toggleTasksCheck"
                                                   :checked="tasks.data.length === tasks.checked.length">
                                            <label class="form-check-label"></label>
                                        </div>
                                    </th>
                                    <th>{{ messages.title }}</th>
                                    <th>{{ messages.hours }}</th>
                                    <th>{{ messages.status }}</th>
                                    <th>{{ messages.start_date }}</th>
                                    <th>{{ messages.end_date }}</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-for="task in tasks.data" :key="task.id">
                                    <td>
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input" :value="task.id"
                                                   v-model="tasks.checked">
                                            <label for="" class="form-check-label"></label>
                                        </div>
                                    </td>
                                    <td>{{ task.title_truncated }}</td>
                                    <td>
                                        {{ parseFloat(task.hours).toLocaleString() }}
                                    </td>
                                    <td class="text-nowrap">
                                        <span :style="`color: ${task.task_status.color}`"
                                              class="fw-bold">{{ task.task_status[`name_${locale}`] }}</span>
                                    </td>
                                    <td>{{ task.start_for_humans }}</td>
                                    <td>{{ task.end_for_humans }}</td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">{{ messages.components }}</h4>
                </div>
                <div class="card-body">
                    <form id="components-form">
                        <div class="row">
                            <div class="col-6">
                                <label for="components" class="form-label">{{ messages.components }}</label>
                                <select name="components[]" id="components" class="select2"
                                        :class="reviews?.errors?.components ? 'is-invalid' : ''"
                                        :data-placeholder="messages.pleaseSelect" multiple>
                                    <option v-for="component in components" :value="component.id">
                                        {{ `${component.code} - ${component[`name_${locale}`]}` }}
                                    </option>
                                </select>
                                <div v-if="reviews?.errors?.components" class="invalid-feedback">
                                    {{ reviews.errors.components[0] }}
                                </div>
                            </div>
                            <div class="col-6">
                                <label for="components-organisations" class="form-label">{{
                                        messages.organisations
                                    }}</label>
                                <select name="organisations[]" id="components-organisations" class="select2"
                                        :class="reviews?.errors?.organisations ? 'is-invalid' : ''"
                                        :data-placeholder="messages.pleaseSelect" multiple>
                                    <option v-for="organisation in organisations" :value="organisation.id">
                                        {{ organisation.name }}
                                    </option>
                                </select>
                                <div v-if="reviews?.errors?.organisations" class="invalid-feedback">{{
                                        reviews.errors.organisations[0]
                                    }}
                                </div>
                            </div>
                            <div class="col-12 mt-2">
                                <button type="button" class="btn btn-success waves-effect waves-float waves-light me-50"
                                        :disabled="reviews.isSubmitting" @click="overwriteReviews">
                                    <span v-show="!reviews.isSubmitting">
                                        <i data-feather="copy" class="me-25"></i>{{ messages.overwrite }}
                                    </span>
                                    <span v-show="reviews.isSubmitting" class="spinner-border spinner-border-sm"
                                          role="status"
                                          aria-hidden="true"></span>
                                    <span v-show="reviews.isSubmitting" class="ms-25 align-middle">{{
                                            messages.submitting
                                        }}...</span>
                                </button>
                                <button type="button" class="btn btn-primary waves-effect waves-float waves-light me-50"
                                        @click="selectAllComponents"><i
                                    data-feather="check-square" class="me-25"></i>{{ messages.populate_all }}
                                </button>
                                <button type="button" class="btn btn-secondary waves-effect waves-float waves-light"
                                        @click="deselectAllComponents">
                                    {{ messages.depopulate_all }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">{{ messages.statements }}</h4>
                </div>
                <div class="card-body">
                    <form id="implementations-form">
                        <div class="row">
                            <div class="col-6">
                                <label for="statements" class="form-label">{{ messages.statements }}</label>
                                <select name="statements[]" id="statements" class="select2"
                                        :class="implementations?.errors?.statements ? 'is-invalid' : ''"
                                        :data-placeholder="messages.pleaseSelect" multiple>
                                    <option v-for="statement in statements" :value="statement.id">
                                        {{ `${statement.subcode} - ${statement.implementation}` }}
                                    </option>
                                </select>
                                <div v-if="implementations?.errors?.statements" class="invalid-feedback">
                                    {{ implementations.errors.statements[0] }}
                                </div>
                            </div>
                            <div class="col-6">
                                <label for="statements-organisations" class="form-label">{{
                                        messages.organisations
                                    }}</label>
                                <select name="organisations[]" id="statements-organisations" class="select2"
                                        :class="implementations?.errors?.organisations ? 'is-invalid' : ''"
                                        :data-placeholder="messages.pleaseSelect" multiple>
                                    <option v-for="organisation in organisations" :value="organisation.id">
                                        {{ organisation.name }}
                                    </option>
                                </select>
                                <div v-if="implementations?.errors?.organisations" class="invalid-feedback">{{
                                        implementations.errors.organisations[0]
                                    }}
                                </div>
                            </div>
                            <div class="col-12 mt-2">
                                <button type="button" class="btn btn-success waves-effect waves-float waves-light me-50"
                                        :disabled="implementations.isSubmitting" @click="overwriteImplementations">
                                    <span v-show="!implementations.isSubmitting"><i data-feather="copy"
                                                                                    class="me-25"></i>{{
                                            messages.overwrite
                                        }}</span>
                                    <span v-show="implementations.isSubmitting" class="spinner-border spinner-border-sm"
                                          role="status"
                                          aria-hidden="true"></span>
                                    <span v-show="implementations.isSubmitting" class="ms-25 align-middle">{{
                                            messages.submitting
                                        }}...</span>
                                </button>
                                <button type="button" class="btn btn-primary waves-effect waves-float waves-light me-50"
                                        @click="selectAllStatements"><i
                                    data-feather="check-square" class="me-25"></i>{{ messages.populate_all }}
                                </button>
                                <button type="button" class="btn btn-secondary waves-effect waves-float waves-light"
                                        @click="deselectAllStatements">
                                    {{ messages.depopulate_all }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import Swal from "sweetalert2";

export default {
    name: "Features",
    props: ['locale', 'messages', 'components', 'statements', 'organisations'],
    data() {
        return {
            tasks: {
                errors: null,
                isSubmitting: false,
                years: [],
                selectedYear: '',
                data: [],
                checked: [],
            },
            reviews: {
                errors: null,
                isSubmitting: false,
            },
            implementations: {
                errors: null,
                isSubmitting: false,
            },
        }
    },
    methods: {
        initSelect2() {
            $('.select2').select2();
        },
        getYearsForTasks() {
            let self = this;
            axios.get(`/${self.locale}/axios/features/tasks-years`)
                .then(function (response) {
                    self.tasks.years = response.data;
                })
                .catch(function (error) {

                });
        },
        getTasks() {
            let self = this;
            axios.get(`/${self.locale}/axios/features/tasks/${self.tasks.selectedYear}`)
                .then(function (response) {
                    self.tasks.data = response.data;
                    self.checkAllTasks();
                })
                .catch(function (error) {

                });
        },
        checkAllTasks() {
            let self = this;
            self.tasks.checked = [];
            this.tasks.data.forEach(task => {
                self.tasks.checked.push(task.id);
            });
        },
        uncheckAllTasks() {
            this.tasks.checked = [];
        },
        toggleTasksCheck() {
            if (this.tasks.data.length === this.tasks.checked.length) {
                this.uncheckAllTasks();
            } else {
                this.checkAllTasks();
            }
        },
        handleTasksYearChange() {
            let self = this;
            $('#tasks-year').on('select2:select', function (e) {
                self.tasks.selectedYear = e.params.data.id;
                self.getTasks();
            });
        },
        overwriteTasks() {
            let self = this;
            const swal = Swal.mixin({
                customClass: {
                    confirmButton: 'btn btn-gradient-danger me-1',
                    cancelButton: 'btn btn-gradient-secondary'
                },
                buttonsStyling: false
            });

            swal.fire({
                title: `${self.messages.tasks_overwrite_warning}`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: `${self.messages.ok}`,
                cancelButtonText: `${self.messages.cancel}`,
                buttonsStyling: false
            }).then(result => {
                if (result.value === true) {
                    self.isSubmitting = true;
                    axios.post(`/${self.locale}/features/tasks/update`, {
                        organisations: $('#tasks-organisations').val(),
                        tasks: self.tasks.checked
                    }).then(function (response) {
                        self.isSubmitting = false;
                        self.errors = null;
                        self.tasks.checked = [];
                        $('#tasks-organisations').val(null).trigger('change');
                        if (response.data.success) {
                            toastr["success"](self.messages.itemsUpdatedSuccessfully, self.messages?.success, {
                                showMethod: "slideDown",
                                hideMethod: "slideUp",
                                timeOut: 3000,
                                progressBar: true,
                                "positionClass": "toast-top-center",
                            });
                        }
                        self.$nextTick(() => {
                            self.initSelect2();
                        });
                    }).catch(function (error) {
                        self.tasks.isSubmitting = false;
                        self.tasks.errors = error.response?.data?.errors;

                        self.$nextTick(() => {
                            self.initSelect2();
                        });
                    });
                }
            });
        },
        selectAllComponents() {
            let components = this.components.flatMap(component => component.id);
            $('#components').val(components).trigger('change');
        },
        deselectAllComponents() {
            $('#components').val(null).trigger('change');
        },
        selectAllStatements() {
            let statements = this.statements.flatMap(statement => statement.id);
            $('#statements').val(statements).trigger('change');
        },
        deselectAllStatements() {
            $('#statements').val(null).trigger('change');
        },
        overwriteReviews() {
            let self = this;
            const swal = Swal.mixin({
                customClass: {
                    confirmButton: 'btn btn-gradient-danger me-1',
                    cancelButton: 'btn btn-gradient-secondary'
                },
                buttonsStyling: false
            });

            swal.fire({
                title: `${self.messages.components_overwrite_warning}`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: `${self.messages.ok}`,
                cancelButtonText: `${self.messages.cancel}`,
                buttonsStyling: false
            }).then(result => {
                if (result.value === true) {
                    self.reviews.isSubmitting = true;
                    let formData = new FormData(document.getElementById('components-form'));
                    axios.post(`/${self.locale}/features/components/update`, formData)
                        .then(function (response) {
                            self.reviews.isSubmitting = false;
                            self.reviews.errors = null;
                            self.resetReviewsForm();
                            if (response.data.success) {
                                toastr["success"](self.messages.itemsUpdatedSuccessfully, self.messages?.success, {
                                    showMethod: "slideDown",
                                    hideMethod: "slideUp",
                                    timeOut: 3000,
                                    progressBar: true,
                                    "positionClass": "toast-top-center",
                                });
                            }
                            self.$nextTick(() => {
                                self.initSelect2();
                            });
                        })
                        .catch(function (error) {
                            self.reviews.isSubmitting = false;
                            self.reviews.errors = error.response?.data?.errors;
                            self.$nextTick(() => {
                                self.initSelect2();
                            });
                        });
                }
            });
        },
        resetReviewsForm() {
            $('#components').val(null).trigger('change');
            $('#components-organisations').val(null).trigger('change');
        },
        overwriteImplementations() {
            let self = this;
            const swal = Swal.mixin({
                customClass: {
                    confirmButton: 'btn btn-gradient-danger me-1',
                    cancelButton: 'btn btn-gradient-secondary'
                },
                buttonsStyling: false
            });

            swal.fire({
                title: `${self.messages.implementations_overwrite_warning}`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: `${self.messages.ok}`,
                cancelButtonText: `${self.messages.cancel}`,
                buttonsStyling: false
            }).then(result => {
                if (result.value === true) {
                    self.implementations.isSubmitting = true;
                    let formData = new FormData(document.getElementById('implementations-form'));
                    axios.post(`/${self.locale}/features/implementations/update`, formData)
                        .then(function (response) {
                            self.implementations.isSubmitting = false;
                            self.implementations.errors = null;
                            self.resetImplementationsForm();
                            if (response.data.success) {
                                toastr["success"](self.messages.itemsUpdatedSuccessfully, self.messages?.success, {
                                    showMethod: "slideDown",
                                    hideMethod: "slideUp",
                                    timeOut: 3000,
                                    progressBar: true,
                                    "positionClass": "toast-top-center",
                                });
                            }
                            self.$nextTick(() => {
                                self.initSelect2();
                            });
                        })
                        .catch(function (error) {
                            self.implementations.isSubmitting = false;
                            self.implementations.errors = error.response?.data?.errors;
                            self.$nextTick(() => {
                                self.initSelect2();
                            });
                        });
                }
            });
        },
        resetImplementationsForm() {
            $('#statements').val(null).trigger('change');
            $('#statements-organisations').val(null).trigger('change');
        },
    },
    mounted() {
        this.initSelect2();
        this.getTasks();
        this.getYearsForTasks();
        this.handleTasksYearChange();
    }
}
</script>

<style scoped>

</style>
