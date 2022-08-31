window.axios = require('axios');

import {createApp} from 'vue'
import OrganisationAct from './OrganisationAct.vue'

const app = createApp({})

app
    .component('OrganisationAct', OrganisationAct)

app.mount('#app');
