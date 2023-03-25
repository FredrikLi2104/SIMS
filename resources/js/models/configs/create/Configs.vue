<template>
    <div class="row">
        <div class="col-md-6 col-12">
            <div class="mb-1">
                <label class="form-label col-form-label-sm"
                       for="name_en">{{ messages.name_en }}</label>
                <input type="text" id="name_en" :class="`form-control ${config.errors?.name_en ? 'is-invalid' : ''}`"
                       name="name_en" v-model="config.nameEn"/>
                <div v-if="config.errors?.name_en" class="invalid-feedback">{{ config.errors.name_en[0] }}</div>
            </div>
        </div>
        <div class="col-md-6 col-12">
            <div class="mb-1">
                <label class="form-label col-form-label-sm"
                       for="name_se">{{ messages.name_se }}</label>
                <input type="text" id="name_se" :class="`form-control ${config.errors?.name_se ? 'is-invalid' : ''}`"
                       name="name_se" v-model="config.nameSe"/>
                <div v-if="config.errors?.name_se" class="invalid-feedback">{{ config.errors.name_se[0] }}</div>
            </div>
        </div>
        <div class="col-md-6 col-12">
            <div class="mb-1">
                <label class="form-label" for="desc-en">{{ messages.descInEnglish }}</label>
                <div id="desc-en-editor"></div>
                <input id="desc-en" name="desc_en" type="hidden" :value="config.descEn"
                       :class="`${config.errors?.desc_en ? 'is-invalid' : ''}`"/>
                <div v-if="config.errors?.desc_en" class="invalid-feedback">{{ config.errors.desc_en[0] }}</div>
            </div>
        </div>
        <div class="col-md-6 col-12">
            <div class="mb-1">
                <label class="form-label" for="desc-se">{{ messages.descInSwedish }}</label>
                <div id="desc-se-editor"></div>
                <input id="desc-se" name="desc_se" type="hidden" :value="config.descSe"
                       :class="`${config.errors?.desc_se ? 'is-invalid' : ''}`"/>
                <div v-if="config.errors?.desc_se" class="invalid-feedback">{{ config.errors.desc_se[0] }}</div>
            </div>
        </div>
        <div class="col-md-6 col-12">
            <div class="d-flex align-items-center mb-1">
                <div class="flex-grow-1">
                    <label class="form-label" for="template">{{ messages.template }}</label>
                    <select id="template" :class="`select2 ${config.errors?.template_id ? 'is-invalid' : ''}`"
                            name="template_id[]"
                            multiple :data-placeholder="messages.pleaseSelect">
                        <option v-for="template in templates" :key="template.id" :value="template.id"
                                :selected="configData.templates?.some(configTemplate => configTemplate.id == template.id)">
                            {{ template[`name_${locale}`] }}
                        </option>
                    </select>
                    <div v-if="config.errors?.template_id" class="invalid-feedback">
                        {{ config.errors.template_id[0] }}
                    </div>
                </div>
                <button type="button" class="btn btn-flat-success waves-effect ms-1 text-nowrap"
                        :class="config.errors?.template_id ? 'align-self-center' : 'align-self-end'"
                        data-bs-toggle="modal" data-bs-target="#template-modal">
                    <i data-feather="plus" class="me-25"></i>{{ `${messages.template} ${messages.create}` }}
                </button>
            </div>
        </div>
        <div class="col-12">
            <button type="button" class="btn btn-primary waves-effect waves-float waves-light"
                    :disabled="config.isSubmitting"
                    @click="submit">
                <span v-show="!config.isSubmitting">{{ messages.submit }}</span>
                <span v-show="config.isSubmitting" class="spinner-border spinner-border-sm" role="status"
                      aria-hidden="true"></span>
                <span v-show="config.isSubmitting" class="ms-25 align-middle">{{ messages.submitting }}...</span>
            </button>
        </div>
    </div>

    <div class="modal fade text-start" id="template-modal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">{{ `${this.messages.template} ${this.messages.create}` }}</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="template-form">
                        <div class="mb-50">
                            <label for="name-en" class="form-label">{{ messages.nameInEnglish }}</label>
                            <input id="name-en" type="text"
                                   :class="`form-control ${template.errors?.name_en ? 'is-invalid' : ''}`"
                                   name="name_en" v-model="template.nameEn">
                            <div v-if="template.errors?.name_en" class="invalid-feedback">{{
                                    template.errors.name_en[0]
                                }}
                            </div>
                        </div>
                        <div class="mb-50">
                            <label for="name-se" class="form-label">{{ messages.nameInSwedish }}</label>
                            <input id="name-se" type="text"
                                   :class="`form-control ${template.errors?.name_se ? 'is-invalid' : ''}`"
                                   name="name_se" v-model="template.nameSe">
                            <div v-if="template.errors?.name_se" class="invalid-feedback">{{
                                    template.errors.name_se[0]
                                }}
                            </div>
                        </div>
                        <div class="mb-50">
                            <label class="form-label" for="tmpl-desc-en">{{ messages.descInEnglish }}</label>
                            <div id="tmpl-desc-en-editor"></div>
                            <input id="tmpl-desc-en" name="desc_en" type="hidden" :value="template.descEn"
                                   :class="`${template.errors?.desc_en ? 'is-invalid' : ''}`"/>
                            <div v-if="template.errors?.desc_en" class="invalid-feedback">{{
                                    template.errors.desc_en[0]
                                }}
                            </div>
                        </div>
                        <div class="mb-50">
                            <label class="form-label" for="tmpl-desc-se">{{ messages.descInSwedish }}</label>
                            <div id="tmpl-desc-se-editor"></div>
                            <input id="tmpl-desc-se" name="desc_se" type="hidden" :value="template.descSe"
                                   :class="`${template.errors?.desc_se ? 'is-invalid' : ''}`"/>
                            <div v-if="template.errors?.desc_se" class="invalid-feedback">{{
                                    template.errors.desc_se[0]
                                }}
                            </div>
                        </div>
                        <div class="mb-50">
                            <label class="form-label" for="start">{{ messages.start_date }}</label>
                            <input type="text" id="start"
                                   :class="`form-control flatpickr ${template.errors?.start ? 'is-invalid' : ''}`"
                                   placeholder="YYYY-MM-DD" name="start" v-model="template.start"/>
                            <div v-if="template.errors?.start" class="invalid-feedback">{{
                                    template.errors.start[0]
                                }}
                            </div>
                        </div>
                        <div class="mb-50">
                            <label class="form-label" for="end">{{ messages.end_date }}</label>
                            <input type="text" id="end"
                                   :class="`form-control flatpickr ${template.errors?.end ? 'is-invalid' : ''}`"
                                   placeholder="YYYY-MM-DD" name="end" v-model="template.end"/>
                            <div v-if="template.errors?.end" class="invalid-feedback">{{ template.errors.end[0] }}</div>
                        </div>
                        <div class="mb-50">
                            <label for="hours" class="form-label">{{ messages.hours }}</label>
                            <input id="hours" type="text"
                                   :class="`form-control ${template.errors?.hours ? 'is-invalid' : ''}`"
                                   name="hours" v-model="template.hours">
                            <div v-if="template.errors?.hours" class="invalid-feedback">{{
                                    template.errors.hours[0]
                                }}
                            </div>
                        </div>
                        <div class="mb-50">
                            <label class="form-label" for="status">{{ messages.status }}</label>
                            <select id="status"
                                    :class="`select2 ${template.errors?.task_status_id ? 'is-invalid' : ''}`"
                                    name="task_status_id" :data-placeholder="messages.pleaseSelect">
                                <option value=""></option>
                                <option v-for="status in taskStatuses" :value="status.id">
                                    {{ status[`name_${locale}`] }}
                                </option>
                            </select>
                            <div v-if="template.errors?.task_status_id" class="invalid-feedback">{{
                                    template.errors.task_status_id[0]
                                }}
                            </div>
                        </div>
                        <div class="mb-50">
                            <label class="form-label" for="assigned-to">{{ messages.action }}</label>
                            <select id="action"
                                    :class="`select2 ${template.errors?.action_type_id ? 'is-invalid' : ''}`"
                                    name="action_type_id[]" multiple :data-placeholder="messages.pleaseSelect">
                                <option v-for="actionType in actionTypes" :value="actionType.id">
                                    {{ actionType[`name_${locale}`] }}
                                </option>
                            </select>
                            <div v-if="template.errors?.action_type_id" class="invalid-feedback">{{
                                    template.errors.action_type_id[0]
                                }}
                            </div>
                        </div>
                        <div v-for="selectedAction in template.selectedActions" :key="selectedAction.id" class="mb-50">
                            <label class="form-label" :for="`action-${selectedAction.id}`">{{
                                    selectedAction.label
                                }}</label>
                            <select :id="`action-${selectedAction.id}`"
                                    :class="`select2 ${template.errors?.[`action_type_items.${selectedAction.id}`] ? 'is-invalid' : ''}`"
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
                            <div v-if="template.errors?.[`action_type_items.${selectedAction.id}`]"
                                 class="invalid-feedback">
                                {{ template.errors[`action_type_items.${selectedAction.id}`][0] }}
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success waves-effect waves-float waves-light"
                            :disabled="template.isSubmitting" @click="submitTemplate">
                        <span v-show="!template.isSubmitting"><i data-feather="check" class="me-25"></i>{{
                                messages.submit
                            }}</span>
                        <span v-show="template.isSubmitting" class="spinner-border spinner-border-sm" role="status"
                              aria-hidden="true"></span>
                        <span v-show="template.isSubmitting" class="ms-25 align-middle">{{
                                messages.submitting
                            }}...</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: "Configs",
    props: ['locale', 'messages', 'isUpdate', 'taskStatuses', 'actionTypes', 'configData'],
    data() {
        return {
            templates: [],
            config: {
                errors: null,
                nameEn: this.configData?.name_en,
                nameSe: this.configData?.name_se,
                descEn: this.configData?.desc_en,
                descSe: this.configData?.desc_se,
                templates: this.configData?.templates,
                isSubmitting: false,
            },
            template: {
                errors: null,
                nameEn: null,
                nameSe: null,
                descEn: null,
                descSe: null,
                start: null,
                end: null,
                hours: null,
                components: null,
                statements: null,
                selectedActions: {},
                isSubmitting: false,
            },
        }
    },
    methods: {
        getTemplates() {
            let self = this;
            axios.get(`/${self.locale}/axios/templates`)
                .then(function (response) {
                    self.templates = response.data;
                })
                .catch(function (error) {

                });
        },
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
            let quill = new Quill('#desc-en-editor', self.quillOptions());

            try {
                quill.setContents(JSON.parse(self.configData.desc_en));
            } catch (error) {

            }

            quill.on('text-change', function () {
                self.config.descEn = JSON.stringify(quill.getContents());
            });
        },
        initDescSeQuill() {
            let self = this;
            let quill = new Quill('#desc-se-editor', self.quillOptions());

            try {
                quill.setContents(JSON.parse(self.configData.desc_se));
            } catch (error) {

            }

            quill.on('text-change', function () {
                self.config.descSe = JSON.stringify(quill.getContents());
            });
        },
        initTmplDescEnQuill() {
            let self = this;
            let quill = new Quill('#tmpl-desc-en-editor', self.quillOptions());
            quill.on('text-change', function () {
                self.template.descEn = JSON.stringify(quill.getContents());
            });
        },
        initTmplDescSeQuill() {
            let self = this;
            let quill = new Quill('#tmpl-desc-se-editor', self.quillOptions());
            quill.on('text-change', function () {
                self.template.descSe = JSON.stringify(quill.getContents());
            });
        },
        initSelect2() {
            $('.select2').select2();
        },
        initFlatpickr() {
            let startDate = document.querySelector('#start');
            let endDate = document.querySelector('#end');
            startDate.flatpickr({monthSelectorType: "static"});
            endDate.flatpickr({monthSelectorType: "static"});
        },
        handleTemplateModalShown() {
            let self = this;
            document.getElementById('template-modal').addEventListener('shown.bs.modal', function () {
                self.initSelect2();
            });
        },
        handleTemplateModalHidden() {
            let self = this;
            document.getElementById('template-modal').addEventListener('hidden.bs.modal', function () {
                self.resetTemplateForm();
            });
        },
        hideTemplateModal() {
            bootstrap.Modal.getInstance(document.getElementById('template-modal')).hide();
        },
        handleActionTypeChange() {
            let self = this;
            $('#action').on('select2:select', function (e) {
                if (['1', '2'].includes(e.params.data.id)) {
                    axios.get(`/${self.locale}/axios/components`)
                        .then(function (response) {
                            self.template.components = response.data;
                            self.template.selectedActions[e.params.data.id] = {
                                id: e.params.data.id,
                                label: e.params.data.text,
                                type: 'component',
                                data: self.template.components
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
                            self.template.statements = response.data;
                            self.template.selectedActions[e.params.data.id] = {
                                id: e.params.data.id,
                                label: e.params.data.text,
                                type: 'statement',
                                data: self.template.statements
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
                delete self.template.selectedActions[e.params.data.id];
            });
        },
        resetTemplateForm() {
            this.template.errors = null;
            this.template.nameEn = null;
            this.template.nameSe = null;
            Quill.find(document.getElementById('tmpl-desc-en-editor')).setContents([]);
            Quill.find(document.getElementById('tmpl-desc-se-editor')).setContents([]);
            this.template.descEn = null;
            this.template.descSe = null;
            this.template.start = null;
            this.template.end = null;
            this.template.hours = null;
            this.template.components = null;
            this.template.statements = null;
            this.template.selectedActions = {};
            this.template.isSubmitting = false;
            $('#status').val(null).trigger('change');
            $('#action').val(null).trigger('change');
        },
        submitTemplate() {
            let self = this;
            let formData = new FormData(document.getElementById('template-form'));
            self.template.isSubmitting = true;

            axios.post(`/${self.locale}/templates`, formData)
                .then(function (response) {
                    self.template.isSubmitting = false;

                    if (response.data.success) {
                        toastr["success"](self.messages.itemCreatedSuccessfully, self.messages?.success, {
                            showMethod: "slideDown",
                            hideMethod: "slideUp",
                            timeOut: 3000,
                            progressBar: true,
                            "positionClass": "toast-top-center",
                        });

                        self.hideTemplateModal();
                        self.getTemplates();
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
                self.template.isSubmitting = false;
                self.template.errors = error.response?.data?.errors;

                self.$nextTick(() => {
                    self.initSelect2();
                });

                console.log(error);
                console.log(error.response);
            });
        },
        submit() {
            let self = this;
            let url = `/${self.locale}/configs`;
            let formData = new FormData($('#form')[0]);
            let res = null;
            self.config.isSubmitting = true;

            if (self.isUpdate) {
                res = axios.post(`${url}/${self.configData.id}`, formData);
            } else {
                res = axios.post(url, formData);
            }

            res.then(function (response) {
                if (response.data.success) {
                    window.location.href = url;
                } else {
                    self.config.isSubmitting = false;

                    toastr["error"](response.data.msg, self.messages?.error, {
                        showMethod: "slideDown",
                        hideMethod: "slideUp",
                        timeOut: 3000,
                        progressBar: true,
                        "positionClass": "toast-top-center",
                    });
                }
            }).catch(function (error) {
                self.config.isSubmitting = false;
                self.config.errors = error.response?.data?.errors;

                self.$nextTick(() => {
                    self.initSelect2();
                });

                console.log(error);
                console.log(error.response);
            });
        }
    },
    mounted() {
        this.getTemplates();
        this.initDescEnQuill();
        this.initDescSeQuill();
        this.initTmplDescEnQuill();
        this.initTmplDescSeQuill();
        this.initSelect2();
        this.initFlatpickr();
        this.handleTemplateModalShown();
        this.handleTemplateModalHidden();
        this.handleActionTypeChange();
    }
}
</script>

<style scoped>

</style>
