<template>
    <div class="mb-1">
        <button class="btn btn-outline-primary waves-effect me-50" data-bs-toggle="offcanvas"
                data-bs-target="#task-offcanvas">
            <i data-feather="plus" class="me-50"></i>{{ `${messages.task} ${messages.create}` }}
        </button>
        <div class="btn-group">
            <button type="button" class="btn btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown"
                    aria-haspopup="true" aria-expanded="false">
                {{ selectedYear }}
            </button>
            <div class="dropdown-menu">
                <a v-for="year in years" class="dropdown-item" href="#" @click="updateYear(year)">{{ year }}</a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-7 d-flex justify-content-center mb-1">
            <tasks-wheel :months="months" :selected-year="selectedYear" :tasks="tasksForWheel"
                         @edit-task="editTask"></tasks-wheel>
        </div>
        <div class="col-lg-5">
            <div class="card">
                <div class="table-responsive">
                    <table class="table table-sm table-borderless">
                        <tbody>
                        <template v-for="(tasksGroup, index) in tasks">
                            <tr>
                                <th colspan="5" class="text-dark fs-5">
                                    <div class="divider divider-start my-25"
                                         :class="tasksGroup.color ? `divider-${tasksGroup.color}` : ''">
                                        <div class="divider-text"
                                             :class="tasksGroup.color ? `text-${tasksGroup.color}` : ''">{{ index }}
                                        </div>
                                    </div>
                                </th>
                            </tr>
                            <tr v-for="task in tasksGroup.tasks">
                                <td>
                                    <a href="#" @click="viewTask(task.id)" class="link-secondary fw-bolder">
                                        {{ task.title_truncated }}
                                    </a>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <i data-feather="clock" class="me-25"></i> {{ task.hours }}
                                    </div>
                                </td>
                                <td class="text-nowrap">
                                    <span :style="`color: ${task.task_status.color}`"
                                          class="fw-bold">{{ task.task_status[`name_${locale}`] }}</span>
                                </td>
                                <td class="text-nowrap">{{ `${task.start_for_humans} - ${task.end_for_humans}` }}</td>
                                <td>
                                    <div class="d-flex">
                                        <button class="btn btn-icon btn-flat-primary waves-effect"
                                                @click="viewTask(task.id)">
                                            <i data-feather="eye"></i>
                                        </button>
                                        <button class="btn btn-icon btn-flat-primary waves-effect"
                                                @click="editTask(task.id)">
                                            <i data-feather="edit"></i>
                                        </button>
                                        <button class="btn btn-icon btn-flat-danger waves-effect"
                                                @click="deleteTask(task.id)">
                                            <i data-feather="trash-2"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </template>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="offcanvas offcanvas-end width-600" tabindex="-1" id="task-offcanvas">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title">{{ taskOffCanvasTitle }}</h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"></button>
        </div>
        <div class="offcanvas-body mx-0 flex-grow-0">
            <form id="task-form">
                <div class="mb-50">
                    <label for="title-en" class="form-label">{{ `${messages.title} ${messages.inEnglish}` }}</label>
                    <input id="title-en" type="text" :class="`form-control ${errors?.title_en ? 'is-invalid' : ''}`"
                           name="title_en" v-model="titleEn">
                    <div v-if="errors?.title_en" class="invalid-feedback">{{ errors.title_en[0] }}</div>
                </div>
                <div class="mb-50">
                    <label for="title-se" class="form-label">{{ `${messages.title} ${messages.inSwedish}` }}</label>
                    <input id="title-se" type="text" :class="`form-control ${errors?.title_se ? 'is-invalid' : ''}`"
                           name="title_se" v-model="titleSe">
                    <div v-if="errors?.title_se" class="invalid-feedback">{{ errors.title_se[0] }}</div>
                </div>
                <div class="mb-50">
                    <label class="form-label" for="desc-en">{{ messages.descInEnglish }}</label>
                    <div id="desc-en-editor"></div>
                    <input id="desc-en" name="desc_en" type="hidden" :value="descEn"
                           :class="`${errors?.desc_en ? 'is-invalid' : ''}`"/>
                    <div v-if="errors?.start" class="invalid-feedback">{{ errors.desc_en[0] }}</div>
                </div>
                <div class="mb-50">
                    <label class="form-label" for="desc-se">{{ messages.descInSwedish }}</label>
                    <div id="desc-se-editor"></div>
                    <input id="desc-se" name="desc_se" type="hidden" :value="descSe"
                           :class="`${errors?.desc_se ? 'is-invalid' : ''}`"/>
                    <div v-if="errors?.start" class="invalid-feedback">{{ errors.desc_se[0] }}</div>
                </div>
                <div class="mb-50">
                    <label class="form-label" for="start">{{ messages.start_date }}</label>
                    <input type="text" id="start" :class="`form-control flatpickr ${errors?.start ? 'is-invalid' : ''}`"
                           placeholder="YYYY-MM-DD" name="start" v-model="start"/>
                    <div v-if="errors?.start" class="invalid-feedback">{{ errors.start[0] }}</div>
                </div>
                <div class="mb-50">
                    <label class="form-label" for="end">{{ messages.end_date }}</label>
                    <input type="text" id="end" :class="`form-control flatpickr ${errors?.end ? 'is-invalid' : ''}`"
                           placeholder="YYYY-MM-DD" name="end" v-model="end"/>
                    <div v-if="errors?.end" class="invalid-feedback">{{ errors.end[0] }}</div>
                </div>
                <div class="mb-50">
                    <label for="hours" class="form-label">{{ messages.hours }}</label>
                    <input id="hours" type="text" :class="`form-control ${errors?.hours ? 'is-invalid' : ''}`"
                           name="hours" v-model="hours">
                    <div v-if="errors?.hours" class="invalid-feedback">{{ errors.hours[0] }}</div>
                </div>
                <div class="mb-50">
                    <label class="form-label" for="status">{{ messages.status }}</label>
                    <select id="status" :class="`select2 ${errors?.task_status_id ? 'is-invalid' : ''}`"
                            name="task_status_id" :data-placeholder="messages.pleaseSelect">
                        <option value=""></option>
                        <option v-for="status in statuses" :value="status.id"
                                :selected="taskData?.task_status_id === status.id">
                            {{ status[`name_${locale}`] }}
                        </option>
                    </select>
                    <div v-if="errors?.task_status_id" class="invalid-feedback">{{ errors.task_status_id[0] }}</div>
                </div>
                <div class="mb-50">
                    <label class="form-label" for="assigned-to">{{ messages.assigned_to }}</label>
                    <select id="assigned-to" :class="`select2 ${errors?.assigned_to ? 'is-invalid' : ''}`"
                            name="assigned_to" :data-placeholder="messages.pleaseSelect">
                        <option value=""></option>
                        <option v-for="assignee in assignees" :value="assignee.id"
                                :selected="taskData?.assigned_to === assignee.id">
                            {{ assignee.name }}
                        </option>
                    </select>
                    <div v-if="errors?.assigned_to" class="invalid-feedback">{{ errors.assigned_to[0] }}</div>
                </div>
                <div class="mb-50">
                    <label class="form-label" for="assigned-to">{{ messages.action }}</label>
                    <select id="action" :class="`select2 ${errors?.action_type_id ? 'is-invalid' : ''}`"
                            name="action_type_id[]" multiple :data-placeholder="messages.pleaseSelect">
                        <option v-for="actionType in actionTypes" :value="actionType.id"
                                :selected="taskData?.actions?.some(action => action.action_type_id === actionType.id)">
                            {{ actionType[`name_${locale}`] }}
                        </option>
                    </select>
                    <div v-if="errors?.action_type_id" class="invalid-feedback">{{ errors.action_type_id[0] }}</div>
                </div>
                <div v-for="selectedAction in selectedActions" :key="selectedAction.id" class="mb-50">
                    <label class="form-label" :for="`action-${selectedAction.id}`">{{ selectedAction.label }}</label>
                    <select :id="`action-${selectedAction.id}`"
                            :class="`select2 ${errors?.[`action_type_items.${selectedAction.id}`] ? 'is-invalid' : ''}`"
                            :name="`action_type_items[${selectedAction.id}][]`" multiple
                            :data-placeholder="messages.pleaseSelect">
                        <option v-for="item in selectedAction.data" :key="item.id" :value="item.id"
                                :selected="selectedAction.selected?.includes(item.id)">
                            {{
                                selectedAction.type == 'component' ? `${item.code} | ${item[`name_${locale}`]}` :
                                    item.subcode
                            }}
                        </option>
                    </select>
                    <div v-if="errors?.[`action_type_items.${selectedAction.id}`]" class="invalid-feedback">
                        {{ errors[`action_type_items.${selectedAction.id}`][0] }}
                    </div>
                </div>
                <div class="mt-2">
                    <button type="button" class="btn btn-success w-100 waves-effect waves-float waves-light"
                            :disabled="isSubmitting" @click="submitTask">
                        <span v-show="!isSubmitting"><i data-feather="check" class="me-25"></i>{{
                                messages.submit
                            }}</span>
                        <span v-show="isSubmitting" class="spinner-border spinner-border-sm" role="status"
                              aria-hidden="true"></span>
                        <span v-show="isSubmitting" class="ms-25 align-middle">{{ messages.submitting }}...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="offcanvas offcanvas-end width-600" tabindex="-1" id="task-view-offcanvas">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title">{{ `${messages.task} ${messages.view}` }}</h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"></button>
        </div>
        <div class="offcanvas-body mx-0 flex-grow-0">
            <div class="d-flex align-items-center mb-1">
                <h6 class="text-sm font-weight-semibold me-1 mb-0">{{ `${messages.title}:` }}</h6>
                <span>{{ taskData?.[`title_${locale}`] }}</span>
            </div>
            <div class="mb-1">
                <h6 class="text-sm font-weight-semibold me-1">{{ messages.desc }}:</h6>
                <div id="desc-en-html" v-html="deltaToHtml(taskData?.[`desc_${locale}`])"></div>
            </div>
            <div class="d-flex align-items-center mb-1">
                <h6 class="text-sm font-weight-semibold me-1 mb-0">{{ messages.start_date }}:</h6>
                <span>{{ taskData?.start_for_humans }}</span>
            </div>
            <div class="d-flex align-items-center mb-1">
                <h6 class="text-sm font-weight-semibold me-1 mb-0">{{ messages.end_date }}:</h6>
                <span>{{ taskData?.end_for_humans }}</span>
            </div>
            <div class="d-flex align-items-center mb-1">
                <h6 class="text-sm font-weight-semibold me-1 mb-0">{{ messages.hours }}:</h6>
                <span>{{ taskData?.hours }}</span>
            </div>
            <div class="d-flex align-items-center mb-1">
                <h6 class="text-sm font-weight-semibold me-1 mb-0">{{ messages.status }}:</h6>
                <span :style="`color: ${taskData?.task_status?.color};`">{{
                        taskData?.task_status?.[`name_${locale}`]
                    }}</span>
            </div>
            <div class="d-flex align-items-center mb-1">
                <h6 class="text-sm font-weight-semibold me-1 mb-0">{{ messages.assigned_to }}:</h6>
                <span>{{ taskData?.assignee?.name }}</span>
            </div>
            <div class="mb-1">
                <h6 class="text-sm font-weight-semibold me-1">{{ messages.action }}:</h6>
                <a :href="`/${locale}/${action.action_type.url}/${action.id}`"
                   class="btn btn-outline-primary waves-effect mb-25"
                   :class="index < taskData.actions.length - 1 ? 'me-25' : ''"
                   v-for="(action, index) in taskData?.actions" target="_blank">
                    {{ action.action_type[`name_${locale}`] }}
                </a>
            </div>
        </div>
    </div>
</template>

<script>
import TasksWheel from "./TasksWheel.vue";
import {QuillDeltaToHtmlConverter} from 'quill-delta-to-html';
import Swal from "sweetalert2";

export default {
    props: ['locale', 'messages', 'statuses', 'assignees', 'actionTypes', 'months', 'years'],
    name: "Tasks",
    components: {TasksWheel},
    data() {
        return {
            errors: null,
            selectedYear: this.years[1],
            titleEn: null,
            titleSe: null,
            descEn: null,
            descSe: null,
            start: null,
            end: null,
            hours: null,
            components: null,
            statements: null,
            selectedActions: {},
            tasks: [],
            isUpdate: false,
            taskData: {},
            tasksForWheel: [],
            isSubmitting: false
        }
    },
    methods: {
        quillOptions() {
            return {
                modules: {
                    formula: true,
                    syntax: true,
                    toolbar: [
                        [
                            {
                                font: [],
                            },
                            {
                                size: [],
                            },
                        ],
                        ["bold", "italic", "underline", "strike"],
                        [
                            {
                                color: [],
                            },
                            {
                                background: [],
                            },
                        ],
                        [
                            {
                                script: "super",
                            },
                            {
                                script: "sub",
                            },
                        ],
                        [
                            {
                                header: "1",
                            },
                            {
                                header: "2",
                            },
                            "blockquote",
                            "code-block",
                        ],
                        [
                            {
                                list: "ordered",
                            },
                            {
                                list: "bullet",
                            },
                            {
                                indent: "-1",
                            },
                            {
                                indent: "+1",
                            },
                        ],
                        [
                            "direction",
                            {
                                align: [],
                            },
                        ],
                        ["link", "image", "video", "formula"],
                        ["clean"],
                    ],
                },
                theme: "snow",
            };
        },
        initDescEnQuill() {
            let self = this;
            let descEnQuill = new Quill('#desc-en-editor', self.quillOptions());
            descEnQuill.on('text-change', function () {
                self.descEn = JSON.stringify(descEnQuill.getContents());
            });
        },
        initDescSeQuill() {
            let self = this;
            let descSeQuill = new Quill('#desc-se-editor', self.quillOptions());
            descSeQuill.on('text-change', function () {
                self.descSe = JSON.stringify(descSeQuill.getContents());
            });
        },
        initFlatpickr() {
            let startDate = document.querySelector('#start');
            let endDate = document.querySelector('#end');
            startDate.flatpickr({monthSelectorType: "static"});
            endDate.flatpickr({monthSelectorType: "static"});
        },
        initSelect2() {
            $('.select2').select2();
        },
        handleActionTypeChange() {
            let self = this;
            $('#action').on('select2:select', function (e) {
                if (['1', '2'].includes(e.params.data.id)) {
                    axios.get(`/${self.locale}/axios/components`)
                        .then(function (response) {
                            self.components = response.data;
                            self.selectedActions[e.params.data.id] = {
                                id: e.params.data.id,
                                label: e.params.data.text,
                                type: 'component',
                                data: self.components
                            };
                            self.$nextTick(() => {
                                self.initSelect2();
                            });
                        })
                        .catch(function (error) {

                        });
                } else if (['3', '4', '5'].includes(e.params.data.id)) {
                    axios.get(`/${self.locale}/axios/statements`)
                        .then(function (response) {
                            self.statements = response.data;
                            self.selectedActions[e.params.data.id] = {
                                id: e.params.data.id,
                                label: e.params.data.text,
                                type: 'statement',
                                data: self.statements
                            };
                            self.$nextTick(() => {
                                self.initSelect2();
                            });
                        })
                        .catch(function (error) {

                        });
                }
            });

            $('#action').on('select2:unselect', function (e) {
                delete self.selectedActions[e.params.data.id];
            });
        },
        submitTask() {
            let self = this;
            let url = `/${self.locale}/tasks`;
            let formData = new FormData(document.getElementById('task-form'));

            let res = null;
            self.isSubmitting = true;

            if (self.isUpdate) {
                res = axios.put(`${url}/${this.taskData.id}`, $('#task-form').serialize());
            } else {
                res = axios.post(url, formData);
            }

            res.then(function (response) {
                self.isSubmitting = false;

                if (response.data.success) {
                    let msg = self.isUpdate ? self.messages?.itemUpdatedSuccessfully : self.messages?.itemCreatedSuccessfully;
                    toastr["success"](msg, self.messages?.success, {
                        showMethod: "slideDown",
                        hideMethod: "slideUp",
                        timeOut: 3000,
                        progressBar: true,
                        "positionClass": "toast-top-center",
                    });

                    self.hideOffCanvas();
                    self.getTasks();
                    self.getTasksForWheel();
                } else {
                    toastr["error"](response.data.msg, self.messages?.error, {
                        showMethod: "slideDown",
                        hideMethod: "slideUp",
                        timeOut: 3000,
                        progressBar: true,
                        "positionClass": "toast-top-center",
                    });
                }
            }).catch(function (error) {
                self.isSubmitting = false;
                self.errors = error.response?.data?.errors;

                self.$nextTick(() => {
                    self.initSelect2();
                });

                console.log(error);
                console.log(error.response);
            });
        },
        getTasks() {
            let self = this;
            axios.get(`/${self.locale}/axios/tasks/${self.selectedYear}`)
                .then(function (response) {
                    self.tasks = response.data;
                    self.$nextTick(() => {
                        feather.replace();
                    })
                })
                .catch(function (error) {

                });
        },
        editTask(id) {
            let self = this;
            self.isUpdate = true;
            self.getTaskData(id);
            let offCanvas = new bootstrap.Offcanvas(document.getElementById('task-offcanvas'));
            offCanvas.show();
        },
        viewTask(id) {
            let self = this;
            self.getTaskData(id);
            let offCanvas = new bootstrap.Offcanvas(document.getElementById('task-view-offcanvas'));
            offCanvas.show();
        },
        deleteTask(id) {
            let self = this;
            const swal = Swal.mixin({
                customClass: {
                    confirmButton: 'btn btn-gradient-danger me-1',
                    cancelButton: 'btn btn-gradient-secondary'
                },
                buttonsStyling: false
            });

            swal.fire({
                title: `${self.messages.delete_confirm}`,
                icon: 'error',
                showCancelButton: true,
                confirmButtonText: `${self.messages.ok}`,
                cancelButtonText: `${self.messages.cancel}`,
                buttonsStyling: false
            }).then(result => {
                if (result.value === true) {
                    axios
                        .delete(`/${self.locale}/tasks/${id}`, {})
                        .then(function (response) {
                            if (response.data.success) {
                                toastr["success"](response.data.msg, `${self.messages.success}`, {
                                    showMethod: "slideDown",
                                    hideMethod: "slideUp",
                                    timeOut: 3000,
                                    progressBar: true,
                                    "positionClass": "toast-top-center",
                                });
                            } else {
                                toastr["error"](response.data.msg, `${self.messages.error}`, {
                                    showMethod: "slideDown",
                                    hideMethod: "slideUp",
                                    timeOut: 3000,
                                    progressBar: true,
                                    "positionClass": "toast-top-center",
                                });
                            }

                            self.getTasks();
                            self.getTasksForWheel();
                        })
                        .catch(function (error) {

                        });
                }
            });
        },
        getTaskData(id) {
            let self = this;
            axios.get(`/${self.locale}/tasks/${id}`)
                .then(function (response) {
                    self.taskData = response.data;
                    self.titleEn = response.data.title_en;
                    self.titleSe = response.data.title_se;
                    self.descEn = response.data.desc_en;
                    self.descSe = response.data.desc_se;
                    self.start = response.data.start_for_humans;
                    self.end = response.data.end_for_humans;
                    self.hours = response.data.hours;

                    let descEnQuill = Quill.find(document.getElementById('desc-en-editor'));
                    let descSeQuill = Quill.find(document.getElementById('desc-se-editor'));
                    try {
                        descEnQuill.setContents(JSON.parse(self.taskData.desc_en));
                        descSeQuill.setContents(JSON.parse(self.taskData.desc_se));
                    } catch (error) {

                    }

                    if (self.taskData.actions.some(action => [1, 2].includes(action.action_type_id))) {
                        axios.get(`/${self.locale}/axios/components`)
                            .then(function (response) {
                                self.components = response.data;
                                self.taskData.actions.forEach(action => {
                                    if ([1, 2].includes(action.action_type_id)) {
                                        self.selectedActions[action.action_type.id] = {
                                            id: action.action_type.id,
                                            label: action.action_type[`name_${self.locale}`],
                                            type: 'component',
                                            data: self.components,
                                            selected: action.components.map(component => component.id)
                                        };
                                    }
                                });

                                self.$nextTick(() => {
                                    self.initSelect2();
                                });
                            })
                            .catch(function (error) {

                            });
                    }

                    if (self.taskData.actions.some(action => [3, 4, 5].includes(action.action_type_id))) {
                        axios.get(`/${self.locale}/axios/statements`)
                            .then(function (response) {
                                self.statements = response.data;
                                self.taskData.actions.forEach(action => {
                                    if ([3, 4, 5].includes(action.action_type_id)) {
                                        self.selectedActions[action.action_type.id] = {
                                            id: action.action_type.id,
                                            label: action.action_type[`name_${self.locale}`],
                                            type: 'statement',
                                            data: self.statements,
                                            selected: action.statements.map(statement => statement.id)
                                        };
                                    }
                                });

                                self.$nextTick(() => {
                                    self.initSelect2();
                                });
                            })
                            .catch(function (error) {

                            });
                    }
                })
                .catch(function (error) {

                });
        },
        hideOffCanvas() {
            bootstrap.Offcanvas.getInstance(document.getElementById('task-offcanvas')).hide();
        },
        handleOffCanvasShown() {
            let self = this;
            document.getElementById('task-offcanvas').addEventListener('shown.bs.offcanvas', function () {
                self.initSelect2();
            });
        },
        handleOffCanvasHidden() {
            let self = this;
            document.getElementById('task-offcanvas').addEventListener('hidden.bs.offcanvas', function () {
                self.isUpdate = false;
                self.errors = null;
                self.resetForm();
            });
        },
        deltaToHtml(delta) {
            let deltaOps = [];

            try {
                deltaOps = JSON.parse(delta).ops;
            } catch (error) {

            }

            let converter = new QuillDeltaToHtmlConverter(deltaOps, {});
            return converter.convert();
        },
        getTasksForWheel() {
            let self = this;
            axios.get(`/${self.locale}/axios/tasks_for_wheel/${self.selectedYear}`)
                .then(function (response) {
                    self.tasksForWheel = response.data;
                })
                .catch(function (error) {

                });
        },
        resetForm() {
            this.taskData = {};
            this.titleEn = null;
            this.titleSe = null;
            Quill.find(document.getElementById('desc-en-editor')).setContents([]);
            Quill.find(document.getElementById('desc-se-editor')).setContents([]);
            this.descEn = null;
            this.descSe = null;
            this.start = null;
            this.end = null;
            this.hours = null;
            this.components = null;
            this.statements = null;
            this.selectedActions = {};
            $('#status').val(null).trigger('change');
            $('#assigned-to').val(null).trigger('change');
            $('#action').val(null).trigger('change');
        },
        updateYear(year) {
            this.selectedYear = year;
            this.getTasks();
            this.getTasksForWheel();
        }
    },
    mounted() {
        this.getTasks();
        this.getTasksForWheel();
        this.initDescEnQuill();
        this.initDescSeQuill();
        this.initFlatpickr();
        this.initSelect2();
        this.handleActionTypeChange();
        this.handleOffCanvasShown();
        this.handleOffCanvasHidden();
    },
    computed: {
        taskOffCanvasTitle() {
            return this.isUpdate ? `${this.messages.task} ${this.messages.edit}` : `${this.messages.task} ${this.messages.create}`;
        }
    }
}
</script>

<style scoped>
#desc-en-html:deep(p:last-child),
#desc-se-html:deep(p:last-child) {
    margin-bottom: 0;
}
</style>
