<template>
    <div class="row">
        <div class="col-md-6 col-12">
            <div class="mb-1">
                <label class="form-label col-form-label-sm"
                       for="name_en">{{ messages.name_en }}</label>
                <input type="text" id="name_en" :class="`form-control ${errors?.name_en ? 'is-invalid' : ''}`"
                       name="name_en" placeholder="Pending" v-model="nameEn"/>
                <div v-if="errors?.name_en" class="invalid-feedback">{{ errors.name_en[0] }}</div>
            </div>
        </div>
        <div class="col-md-6 col-12">
            <div class="mb-1">
                <label class="form-label col-form-label-sm"
                       for="name_se">{{ messages.name_se }}</label>
                <input type="text" id="name_se" :class="`form-control ${errors?.name_se ? 'is-invalid' : ''}`"
                       name="name_se" placeholder="Väntar" v-model="nameSe"/>
                <div v-if="errors?.name_se" class="invalid-feedback">{{ errors.name_se[0] }}</div>
            </div>
        </div>
        <div class="col-md-6 col-12">
            <div class="mb-1">
                <label class="form-label col-form-label-sm"
                       for="color">{{ messages.color }}</label>
                <div id="color-picker"></div>
                <input type="hidden" :class="`${errors?.color ? 'is-invalid' : ''}`" name="color" :value="colorHex">
                <div v-if="errors?.color" class="invalid-feedback">{{ errors.color[0] }}</div>
            </div>
        </div>
        <div class="col-md-6 col-12">
            <div class="mb-1">
                <label class="form-label col-form-label-sm"
                       for="sort-order">{{ messages.sortOrder }}</label>
                <input type="text" id="sort-order"
                       :class="`form-control ${errors?.sort_order ? 'is-invalid' : ''}`"
                       :placeholder="sortOrder" name="sort_order" v-model="sortOrder">
                <div v-if="errors?.sort_order" class="invalid-feedback">{{ errors.sort_order[0] }}</div>
            </div>
        </div>
        <div class="col-12">
            <button type="button" class="btn btn-primary waves-effect waves-float waves-light" :disabled="isSubmitting"
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
import '@simonwep/pickr/dist/themes/monolith.min.css';
import Pickr from '@simonwep/pickr';

export default {
    name: "TaskStatuses",
    props: ['locale', 'messages', 'isUpdate', 'taskStatusData', 'sortOrder'],
    data() {
        return {
            errors: null,
            nameEn: this.taskStatusData?.name_en,
            nameSe: this.taskStatusData?.name_se,
            colorHex: this.taskStatusData?.color,
            isSubmitting: false
        }
    },
    methods: {
        initColorPicker() {
            let self = this;

            if (self.colorHex === undefined) {
                self.colorHex = '#666CE8';
            }

            const pickr = Pickr.create({
                el: '#color-picker',
                theme: 'monolith',
                default: self.colorHex,
                swatches: [
                    'rgba(102, 108, 232, 1)',
                    'rgba(40, 208, 148, 1)',
                    'rgba(255, 73, 97, 1)',
                    'rgba(255, 145, 73, 1)',
                    'rgba(30, 159, 242, 1)'
                ],
                defaultRepresentation: 'HEXA',
                components: {
                    preview: true,
                    opacity: true,
                    hue: true,
                    interaction: {
                        hex: false,
                        rgba: false,
                        hsva: false,
                        input: true,
                        clear: true,
                        save: true
                    }
                }
            });

            pickr.on('save', (color, instance) => {
                self.colorHex = color !== null ? color.toHEXA().toString() : null;
            });
        },
        submit() {
            let self = this;
            let url = `/${self.locale}/task_statuses`;
            let formData = new FormData($('#form')[0]);
            let res = null;
            self.isSubmitting = true;

            if (self.isUpdate) {
                res = axios.post(`${url}/${self.taskStatusData.id}`, formData);
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
                console.log(error);
                console.log(error.response);
            });
        }
    },
    mounted() {
        this.initColorPicker();
    }
}
</script>

<style scoped>

</style>
