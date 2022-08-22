window.axios = require('axios');

import {createApp} from 'vue'
import Kpis from './Kpis.vue'

const app = createApp({})

app
    .component('Kpis', Kpis)

app.mount('#app');
