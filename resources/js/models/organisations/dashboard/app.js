window.axios = require('axios');

import {createApp} from 'vue'
import OrganisationDashboard from './OrganisationDashboard.vue'

const app = createApp({})

app
    .component('OrganisationDashboard', OrganisationDashboard)

app.mount('#app');
