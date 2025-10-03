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
export default {
    name: 'Configs',
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
                    url: `/${self.locale}/axios/configs`,
                    dataSrc: null
                },
                columns: [
                    {data: 'id'},
                    {data: 'name_en'},
                    {data: 'name_se'},
                    {data: null, defaultContent: ''}
                ],
                columnDefs: [
                    {
                        // actions
                        targets: -1,
                        title: self.messages.actions,
                        orderable: false,
                        searchable: false,
                        render: function (data, type, full, meta) {
                            let r = '<div class="d-flex">';

                            if (self.canUpdate) {
                                r += `<a href="/${self.locale}/configs/${full.id}/edit" class="btn btn-primary waves-effect waves-float waves-light text-nowrap">${self.messages.edit}</a>`;
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
