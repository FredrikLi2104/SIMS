<template>
    <div class="col-md-6 col-12">
        <div class="mb-1">
            <label class="form-label" for="country_id">{{ collection?.messages?.country }}</label>
            <select id="country_id" :class="`select2 form-select form-control ${errors?.country_id ? `is-invalid` : ``}`" name="country_id">
                <option value="">{{ pleaseSelect }}</option>
                <option v-for="country in collection?.countries" :value="country.id">{{country.name}}</option>
            </select>
            <country-flag country="fr" size="small" />
            <div v-if="errors?.country_id" class="invalid-feedback">{{ errors?.country_id?.message }}</div>
        </div>
    </div>
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
            errors: null,
            pleaseSelect: null,
        };
    },
    methods: {
        load() {
            var thisComponent = this;
            axios
                .get("/" + thisComponent.locale + "/axios/countries", {})
                .then(function (response) {
                    console.log(response.data);
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
        if (this.locale == "en") {
            this.pleaseSelect = "Please Select";
        } else {
            this.pleaseSelect = "Vänligen välj";
        }
    },
    props: ["locale"],
};
</script>
