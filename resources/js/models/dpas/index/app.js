window.axios = require('axios');

import {createApp} from 'vue'
import Dpas from './Dpas.vue'

const app = createApp({})

app
    .component('Dpas', Dpas)

app.mount('#app');
