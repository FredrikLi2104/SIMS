<template>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">{{ messages.implementations_overwrite_title }}</h4>
                </div>
                <div class="card-body">
                    <form id="implementations-form">
                        <div class="row">
                            <div class="col-6">
                                <label for="statements" class="form-label">{{ messages.statements }}</label>
                                <select name="statements[]" id="statements" class="select2"
                                        :class="errors?.statements ? 'is-invalid' : ''"
                                        :data-placeholder="messages.pleaseSelect" multiple>
                                    <option v-for="statement in statements" :value="statement.id">
                                        {{ `${statement.subcode} - ${statement.implementation}` }}
                                    </option>
                                </select>
                                <div v-if="errors?.statements" class="invalid-feedback">{{ errors.statements[0] }}</div>
                            </div>
                            <div class="col-6">
                                <label for="organisations" class="form-label">{{ messages.organisations }}</label>
                                <select name="organisations[]" id="organisations" class="select2"
                                        :class="errors?.organisations ? 'is-invalid' : ''"
                                        :data-placeholder="messages.pleaseSelect" multiple>
                                    <option v-for="organisation in organisations" :value="organisation.id">
                                        {{ organisation.name }}
                                    </option>
                                </select>
                                <div v-if="errors?.organisations" class="invalid-feedback">{{
                                        errors.organisations[0]
                                    }}
                                </div>
                            </div>
                            <div class="col-12 mt-2">
                                <button type="button" class="btn btn-success waves-effect waves-float waves-light"
                                        :disabled="isSubmitting" @click="submit">
                                    <span v-show="!isSubmitting"><i data-feather="check"
                                                                    class="me-25"></i>{{ messages.submit }}</span>
                                    <span v-show="isSubmitting" class="spinner-border spinner-border-sm" role="status"
                                          aria-hidden="true"></span>
                                    <span v-show="isSubmitting" class="ms-25 align-middle">{{
                                            messages.submitting
                                        }}...</span>
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
    props: ['locale', 'messages', 'statements', 'organisations'],
    data() {
        return {
            errors: null,
            isSubmitting: false,
        }
    },
    methods: {
        initSelect2() {
            $('.select2').select2();
        },
        submit() {
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
                    self.isSubmitting = true;
                    let formData = new FormData(document.getElementById('implementations-form'));
                    axios.post(`/${self.locale}/features/implementations/update`, formData)
                        .then(function (response) {
                            self.isSubmitting = false;
                            self.errors = null;
                            self.resetForm();
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
                            self.isSubmitting = false;
                            self.errors = error.response?.data?.errors;

                            self.$nextTick(() => {
                                self.initSelect2();
                            });
                        });
                }
            });
        },
        resetForm() {
            $('#statements').val(null).trigger('change');
            $('#organisations').val(null).trigger('change');
        }
    },
    mounted() {
        this.initSelect2();
    }
}
</script>

<style scoped>

</style>
