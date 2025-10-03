<template>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-datatable table-responsive pt-0">
                    <table class="table" id="datatable">
                        <thead>
                        <tr>
                            <th>{{ messages.id }}</th>
                            <th>{{ messages.content_en }}</th>
                            <th>{{ messages.content_se }}</th>
                            <th>{{ messages.sortOrder }}</th>
                            <th>Live</th>
                            <th>{{ messages.actions }}</th>
                        </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import Swal from "sweetalert2";
import {QuillDeltaToHtmlConverter} from 'quill-delta-to-html';

export default {
    name: 'Links',
    props: ['locale', 'messages', 'canUpdate', 'canDelete'],
    data() {
        return {
            datatable: null
        }
    },
    methods: {
        initDatatable() {
            let self = this;
            self.datatable = $('#datatable').DataTable({
                ajax: {
                    url: `/${self.locale}/axios/links`,
                    dataSrc: null
                },
                columns: [
                    {data: 'id'},
                    {data: 'content_en'},
                    {data: 'content_se'},
                    {data: 'sort_order'},
                    {data: 'live'},
                    {data: null, defaultContent: ''}
                ],
                columnDefs: [
                    {
                        // content en
                        targets: 1,
                        render: function (data, type, full, meta) {
                            return self.deltaToHtml(full.content_en);
                        }
                    },
                    {
                        // content se
                        targets: 2,
                        render: function (data, type, full, meta) {
                            return self.deltaToHtml(full.content_se);
                        }
                    },
                    {
                        // live
                        targets: 4,
                        render: function (data, type, full, meta) {
                            if (full.live === 1) {
                                return `<span class="badge badge-light-success">${self.messages.yes}</span>`;
                            } else {
                                return `<span class="badge badge-light-secondary">${self.messages.no}</span>`;
                            }
                        }
                    },
                    {
                        // actions
                        targets: -1,
                        title: self.messages.actions,
                        orderable: false,
                        searchable: false,
                        render: function (data, type, full, meta) {
                            let r = '<div class="d-flex">';

                            if (self.canUpdate) {
                                r += `<a href="/${self.locale}/links/${full.id}/edit" class="btn btn-primary waves-effect waves-float waves-light text-nowrap">${self.messages.edit}</a>`;
                            }

                            if (self.canDelete) {
                                r += `<button type="button" class="btn btn-danger waves-effect waves-float del-btn text-nowrap ms-50" onclick="deleteLink(${full.id})">${self.messages.delete}</button>`;
                            }

                            r += '</div>';
                            return r;
                        }
                    }
                ],
                drawCallback: function (settings) {
                    feather.replace()
                },
                dom: '<"d-flex justify-content-between align-items-center mx-2 row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>t<"d-flex justify-content-between mx-2 row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
            })
        },
        deleteLink(linkId) {
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
                        .delete(`/${self.locale}/links/${linkId}`, {})
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

                            self.$nextTick(() => {
                                self.datatable.destroy();
                                self.datatable = null;
                                self.initDatatable();
                            });
                        })
                        .catch(function (error) {

                        });
                }
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
        }
    },
    mounted() {
        let self = this;
        window.deleteLink = function (linkId) {
            self.deleteLink(linkId);
        }
        this.initDatatable();
    }
}
</script>

<style scoped>

</style>
