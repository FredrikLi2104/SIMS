window.axios = require('axios');

import {createApp} from 'vue'
import OrganisationDo from './OrganisationDo.vue'

const app = createApp({})

app
    .component('OrganisationDo', OrganisationDo)

app.mount('#app');
