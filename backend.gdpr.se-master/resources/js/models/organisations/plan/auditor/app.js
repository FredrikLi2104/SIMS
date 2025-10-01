window.axios = require('axios');

import {createApp} from 'vue'
import OrganisationAuditorPlan from './OrganisationAuditorPlan.vue'

const app = createApp({})

app
    .component('OrganisationAuditorPlan', OrganisationAuditorPlan)

app.mount('#app');
