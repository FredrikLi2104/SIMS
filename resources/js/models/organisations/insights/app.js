window.axios = require('axios');

import {createApp} from 'vue'
import OrganisationInsights from './OrganisationInsights.vue'

const app = createApp({})

app
    .component('OrganisationInsights', OrganisationInsights)

app.mount('#app');
