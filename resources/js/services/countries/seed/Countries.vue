<template>
    <div>{{collection?.messages?.key}}</div>
</template>
<script>
import CountryFlag from "vue-country-flag-next";
export default {
    components: {
        CountryFlag,
    },
    data() {
        return {
            collection: null,
        };
    },
    methods: {
        load() {
            var thisComponent = this;
            axios
                .get("/" + thisComponent.locale + "/axios/messages", {})
                .then(function (response) {
                    // console.log(response.data);
                    thisComponent.collection = response.data;
                })
                .catch(function (error) {
                    console.log(error);
                    console.log(error.response);
                });
        },
    },
    mounted() {
        this.load();
    },
    props: ["locale"],
};
</script>