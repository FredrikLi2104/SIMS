import {createApp} from 'vue'
import OrganisationCheck from './OrganisationCheck.vue'

const app = createApp({})

app
    .component('OrganisationCheck', OrganisationCheck)

app.mount('#app');
