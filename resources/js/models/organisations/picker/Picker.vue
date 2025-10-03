<script>
export default {
    name: "Picker",
    props: ['locale', 'list', 'selectedOrg'],
    methods: {
        changeOrg(id) {
            axios.get(`/${this.locale}/axios/organisations/change/${id}`)
                .then(function (response) {
                    window.location.reload();
                })
                .catch(function (error) {

                });
        }
    }
}
</script>

<template>
    <div class="btn-group">
        <button type="button" class="btn btn-outline-secondary dropdown-toggle waves-effect fw-bolder"
                data-bs-toggle="dropdown">{{ selectedOrg?.name }}
        </button>
        <div class="dropdown-menu">
            <a v-for="item in list" :key="item.id" class="dropdown-item" href="#"
               @click="changeOrg(item.id)">{{ item.name }}</a>
        </div>
    </div>
</template>

<style scoped>

</style>
