<template>
    <div class="row">
        <div class="col-md-6 col-12">
            <div class="mb-1">
                <label class="form-label col-form-label-sm"
                       for="name_en">{{ messages.name_en }}</label>
                <input type="text" id="name_en" :class="`form-control ${errors?.name_en ? 'is-invalid' : ''}`"
                       name="name_en" v-model="nameEn" placeholder="Plan Components"/>
                <div v-if="errors?.name_en" class="invalid-feedback">{{ errors.name_en[0] }}</div>
            </div>
        </div>
        <div class="col-md-6 col-12">
            <div class="mb-1">
                <label class="form-label col-form-label-sm"
                       for="name_se">{{ messages.name_se }}</label>
                <input type="text" id="name_se" :class="`form-control ${errors?.name_se ? 'is-invalid' : ''}`"
                       name="name_se" v-model="nameSe" placeholder="Planerakomponenter"/>
                <div v-if="errors?.name_se" class="invalid-feedback">{{ errors.name_se[0] }}</div>
            </div>
        </div>
        <div class="col-md-6 col-12">
            <div class="mb-1">
                <label class="form-label" for="role">{{ messages.role }}</label>
                <select id="role" :class="`select2 ${errors?.role ? 'is-invalid' : ''}`" name="role"
                        :data-placeholder="messages.pleaseSelect">
                    <option value=""></option>
                    <option v-for="role in roles" :key="role" :value="role" :selected="actionTypeData?.role == role">
                        {{ role }}
                    </option>
                </select>
                <div v-if="errors?.role" class="invalid-feedback">{{ errors.role[0] }}</div>
            </div>
        </div>
        <div class="col-md-6 col-12">
            <div class="mb-1">
                <label class="form-label" for="url">URL</label>
                <select id="url" :class="`select2 ${errors?.url ? 'is-invalid' : ''}`" name="url"
                        :data-placeholder="messages.pleaseSelect">
                    <option value=""></option>
                    <option v-for="url in filteredUrls" :key="url" :value="url" :selected="actionTypeData?.url == url">
                        {{ url }}
                    </option>
                </select>
                <div v-if="errors?.url" class="invalid-feedback">{{ errors.url[0] }}</div>
            </div>
        </div>
        <div class="col-md-6 col-12">
            <div class="mb-1">
                <label class="form-label" for="model">{{ `${messages.model} [${messages.optional}]` }}</label>
                <select id="model" :class="`select2 ${errors?.model ? 'is-invalid' : ''}`" name="model"
                        :data-placeholder="messages.pleaseSelect" data-allow-clear="true">
                    <option value=""></option>
                    <option v-for="model in models" :key="model" :value="model"
                            :selected="actionTypeData?.model == model">{{ model }}
                    </option>
                </select>
                <div v-if="errors?.model" class="invalid-feedback">{{ errors.model[0] }}</div>
            </div>
        </div>
        <div class="col-12">
            <button type="button" class="btn btn-primary waves-effect waves-float waves-light"
                    :disabled="isSubmitting"
                    @click="submit">
                <span v-show="!isSubmitting">{{ messages.submit }}</span>
                <span v-show="isSubmitting" class="spinner-border spinner-border-sm" role="status"
                      aria-hidden="true"></span>
                <span v-show="isSubmitting" class="ms-25 align-middle">{{ messages.submitting }}...</span>
            </button>
        </div>
    </div>
</template>

<script>
export default {
    name: "ActionTypes",
    props: ['locale', 'messages', 'isUpdate', 'roles', 'urls', 'models', 'actionTypeData'],
    data() {
        return {
            errors: null,
            nameEn: this.actionTypeData?.name_en,
            nameSe: this.actionTypeData?.name_se,
            role: this.actionTypeData?.role,
            isSubmitting: false
        }
    },
    methods: {
        initSelect2() {
            $('.select2').select2();
        },
        handleRoleChange() {
            let self = this;
            $('#role').on('select2:select', function (e) {
                self.role = e.params.data.id;
            });
        },
        submit() {
            let self = this;
            let url = `/${self.locale}/action_types`;
            let formData = new FormData($('#form')[0]);
            let res = null;
            self.isSubmitting = true;

            if (self.isUpdate) {
                res = axios.post(`${url}/${self.actionTypeData.id}`, formData);
            } else {
                res = axios.post(url, formData);
            }

            res.then(function (response) {
                if (response.data.success) {
                    window.location.href = url;
                } else {
                    self.isSubmitting = false;

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
        }
    },
    computed: {
        filteredUrls() {
            return this.role !== undefined ? this.urls[this.role] : [];
        }
    },
    mounted() {
        this.initSelect2();
        this.handleRoleChange();
    }
}
</script>

<style scoped>

</style>
