<template>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-datatable table-responsive pt-0">
                    <table class="table" id="datatable">
                        <thead>
                        <tr>
                            <th>{{ messages.id }}</th>
                            <th>{{ messages.name_en }}</th>
                            <th>{{ messages.name_se }}</th>
                            <th>{{ messages.color }}</th>
                            <th>{{ messages.sortOrder }}</th>
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

export default {
    name: 'TaskStatuses',
    props: ['locale', 'messages', 'canUpdate'],
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
                    url: `/${self.locale}/axios/task_statuses`,
                    dataSrc: null
                },
                columns: [
                    {data: 'id'},
                    {data: 'name_en'},
                    {data: 'name_se'},
                    {data: 'color'},
                    {data: 'sort_order'},
                    {data: null, defaultContent: ''}
                ],
                columnDefs: [
                    {
                        // color
                        targets: 3,
                        render: function (data, type, full, meta) {
                            let r = `
                                    <div style="width: 30px; height: 30px; border-radius: 0.15rem; background-color: ${full.color}"></div>
                                `;

                            return r;
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
                                r += `<a href="/${self.locale}/task_statuses/${full.id}/edit" class="btn btn-primary waves-effect waves-float waves-light text-nowrap">${self.messages.edit}</a>`;
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
        }
    },
    mounted() {
        this.initDatatable();
    }
}
</script>

<style scoped>

</style>
