import {createApp} from 'vue'
import OrganisationKnowledge from './OrganisationKnowledge.vue'

const app = createApp({})

app
    .component('OrganisationKnowledge', OrganisationKnowledge)

app.mount('#app');
