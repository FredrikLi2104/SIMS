<template>
    <div id="files-modal" class="modal fade">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ sanction?.title }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12">
                            <label for="title" class="form-label">Title</label>
                            <input type="text" id="title" class="form-control"
                                   :class="errors?.title ? 'is-invalid' : ''" v-model="title">
                            <div v-if="errors?.title" class="invalid-feedback">
                                {{ errors.title[0] }}
                            </div>
                        </div>
                    </div>
                    <hr>
                    <input type="hidden" :class="errors?.file ? 'is-invalid' : ''">
                    <div v-if="errors?.file" class="invalid-feedback">{{ errors.file[0] }}</div>
                    <form action="#" class="dropzone dropzone-area" id="dpz-multiple-files">
                        <input type="hidden" name="_token" :value="csrfToken">
                        <input type="hidden" name="title" :value="title">
                        <div class="dz-message">Drop files here or click to upload.</div>
                    </form>
                    <hr>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                            <tr>
                                <th class="text-center">{{ this.$parent.messages.title }}</th>
                                <th class="text-center">{{ `${this.$parent.messages.size} (KB)` }}</th>
                                <th class="text-center">{{ this.$parent.messages.createdAt }}</th>
                                <th class="text-center">{{ this.$parent.messages.actions }}</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-for="file in uploadedFiles" :key="file.id">
                                <td>
                                    <a :href="file.url" target="_blank">{{ file.title }}</a>
                                </td>
                                <td class="text-center">{{ file.size }}</td>
                                <td class="text-center">{{ file.created_at_for_humans }}</td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-icon btn-flat-danger waves-effect"
                                            @click="deleteFile(file.id)">
                                        <i data-feather="trash-2"></i>
                                    </button>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import Swal from "sweetalert2";

export default {
    name: "Files",
    props: ['sanction'],
    data() {
        return {
            errors: null,
            dropzone: null,
            uploadUrl: null,
            uploadedFilesUrl: null,
            uploadedFiles: [],
            title: null,
        }
    },
    methods: {
        initDropzone() {
            let self = this;
            if (self.dropzone !== null) {
                self.dropzone.destroy();
            }

            self.dropzone = new Dropzone('#dpz-multiple-files', {
                url: self.uploadUrl,
                paramName: 'file',
                acceptedFiles: '.pdf,.png,.jpg,.jpeg,.ppt,.pptx,.doc,.dox,.xls,.xlxs',
                maxFilesize: 20,
                clickable: true,
                parallelUploads: 1,
                addRemoveLinks: true
            });

            self.dropzone.on('complete', function (file) {
                self.dropzone.removeFile(file);
            });

            self.dropzone.on('success', function (file) {
                self.title = null;
                self.errors = null;
                self.getSanctionFiles();
            });

            self.dropzone.on('error', function (file, message) {
                self.errors = message?.errors;
            });
        },
        getSanctionFiles() {
            let self = this;
            axios.get(self.uploadedFilesUrl)
                .then(function (response) {
                    self.uploadedFiles = response.data;
                    self.$nextTick(() => {
                        feather.replace();
                    });
                })
                .catch(function (error) {

                });
        },
        deleteFile(id) {
            let self = this;
            const swal = Swal.mixin({
                customClass: {
                    confirmButton: 'btn btn-gradient-danger me-1',
                    cancelButton: 'btn btn-gradient-secondary'
                },
                buttonsStyling: false
            });

            swal.fire({
                title: `${self.$parent.messages.delete_confirm}`,
                icon: 'error',
                showCancelButton: true,
                confirmButtonText: `${self.$parent.messages.ok}`,
                cancelButtonText: `${self.$parent.messages.cancel}`,
                buttonsStyling: false
            }).then(result => {
                if (result.value === true) {
                    axios.delete(`/${self.$parent.locale}/axios/sanctions/${self.sanction.id}/files/${id}`)
                        .then(function (response) {
                            self.getSanctionFiles();
                        })
                        .catch(function (error) {

                        });
                }
            });
        }
    },
    computed: {
        csrfToken() {
            return $('meta[name="csrf-token"]').attr('content');
        }
    },
    created() {
        Dropzone.autoDiscover = false;
    }
}
</script>

<style scoped>

</style>
