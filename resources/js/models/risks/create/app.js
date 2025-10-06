window.axios = require('axios');

import {createApp} from 'vue'
import RiskMatrix from './RiskMatrix.vue'

const app = createApp({})

app
    .component('RiskMatrix', RiskMatrix)

app.mount('#app');
