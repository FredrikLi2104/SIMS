window.axios = require('axios');

import {createApp} from 'vue'
import OrganisationPlan from './OrganisationPlan.vue'

const app = createApp({})

app
    .component('OrganisationPlan', OrganisationPlan)

app.mount('#app');
